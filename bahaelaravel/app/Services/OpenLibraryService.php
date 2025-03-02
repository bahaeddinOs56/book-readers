<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class OpenLibraryService
{
    private $baseUrl = 'https://openlibrary.org';

    public function searchBooks(string $query, int $limit = 10)
    {
        // Cache results for 1 hour to be nice to the API
        $cacheKey = 'book_search_' . md5($query . $limit);

        return Cache::remember($cacheKey, 3600, function () use ($query, $limit) {
            $response = Http::get("{$this->baseUrl}/search.json", [
                'q' => $query,
                'limit' => $limit,
                'fields' => 'key,title,author_name,cover_i,first_publish_year,number_of_pages_median,isbn,subject'
            ]);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => 'Failed to fetch books from OpenLibrary'
                ];
            }

            $books = $response->json();
            
            return [
                'success' => true,
                'total' => $books['numFound'] ?? 0,
                'books' => collect($books['docs'] ?? [])->map(function ($book) {
                    return [
                        'title' => $book['title'] ?? 'Unknown Title',
                        'author' => $book['author_name'][0] ?? 'Unknown Author',
                        'cover_url' => isset($book['cover_i']) 
                            ? "https://covers.openlibrary.org/b/id/{$book['cover_i']}-L.jpg"
                            : null,
                        'publication_year' => $book['first_publish_year'] ?? null,
                        'total_pages' => $book['number_of_pages_median'] ?? null,
                        'isbn' => $book['isbn'][0] ?? null,
                        'genres' => array_slice($book['subject'] ?? [], 0, 5) ?? [],
                        // Add fields needed for your Book model
                        'current_page' => 0,
                        'status' => 'want_to_read',
                        'rating' => null,
                        'time_spent' => 0
                    ];
                })->toArray()
            ];
        });
    }

    public function getBookDetails(string $isbn)
    {
        $cacheKey = 'book_details_' . $isbn;

        return Cache::remember($cacheKey, 3600, function () use ($isbn) {
            $response = Http::get("{$this->baseUrl}/isbn/{$isbn}.json");

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => 'Book not found'
                ];
            }

            $book = $response->json();

            return [
                'success' => true,
                'book' => [
                    'title' => $book['title'] ?? 'Unknown Title',
                    'author' => $book['authors'][0]['name'] ?? 'Unknown Author',
                    'description' => $book['description'] ?? null,
                    // ... other fields
                ]
            ];
        });
    }
}