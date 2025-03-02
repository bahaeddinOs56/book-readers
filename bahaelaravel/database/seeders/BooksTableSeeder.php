<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use App\Models\Book;
use App\Models\User;

class BooksTableSeeder extends Seeder
{
    public function run(): void
    {
        // Get a test user (you need at least one user in the database)
        $user = User::first();
        
        if (!$user) {
            $this->command->error('Please create a user first!');
            return;
        }

        // Search terms for different types of books
        $searchTerms = [
            'harry potter',
            'lord of the rings',
            'dune',
            'foundation asimov',
            'game of thrones'
        ];

        foreach ($searchTerms as $term) {
            $response = Http::get('https://openlibrary.org/search.json', [
                'q' => $term,
                'limit' => 10
            ]);

            if ($response->successful()) {
                $books = $response->json();
                
                foreach ($books['docs'] as $bookData) {
                    // Skip if required fields are missing
                    if (!isset($bookData['title'], $bookData['author_name'])) {
                        continue;
                    }

                    Book::create([
                        'user_id' => $user->id,
                        'title' => $bookData['title'],
                        'author' => $bookData['author_name'][0] ?? 'Unknown Author',
                        'total_pages' => $bookData['number_of_pages_median'] ?? rand(100, 1000),
                        'current_page' => 0,
                        'status' => 'want_to_read',
                        'isbn' => $bookData['isbn'][0] ?? null,
                        'cover_url' => isset($bookData['cover_i']) 
                            ? "https://covers.openlibrary.org/b/id/{$bookData['cover_i']}-L.jpg"
                            : null,
                        'description' => $bookData['first_sentence'][0] ?? null,
                        'publication_year' => $bookData['first_publish_year'] ?? null,
                        'genres' => $bookData['subject'] ?? [],
                        'rating' => null,
                        'time_spent' => 0
                    ]);

                    // Add a small delay to be nice to the API
                    sleep(1);
                }
            }
        }
    }
}