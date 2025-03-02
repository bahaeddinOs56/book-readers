<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\OpenLibraryService;
use Illuminate\Http\Request;
use App\Models\Book;

class BookSearchController extends Controller
{
    protected $openLibraryService;

    public function __construct(OpenLibraryService $openLibraryService)
    {
        $this->openLibraryService = $openLibraryService;
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $limit = $request->input('limit', 10);

        if (empty($query)) {
            return response()->json([
                'success' => false,
                'message' => 'Search query is required'
            ], 400);
        }

        $results = $this->openLibraryService->searchBooks($query, $limit);

        return response()->json($results);
    }

    public function addToLibrary(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'author' => 'required|string',
            'total_pages' => 'nullable|integer',
            'isbn' => 'nullable|string',
            'cover_url' => 'nullable|string',
            'description' => 'nullable|string',
            'publication_year' => 'nullable|integer',
            'genres' => 'nullable|array'
        ]);

        try {
            $book = Book::create([
                'user_id' => auth()->id(),
                'title' => $request->title,
                'author' => $request->author,
                'total_pages' => $request->total_pages ?? 0,
                'current_page' => 0,
                'status' => 'want_to_read',
                'isbn' => $request->isbn,
                'cover_url' => $request->cover_url,
                'description' => $request->description,
                'publication_year' => $request->publication_year,
                'genres' => $request->genres,
                'rating' => null,
                'time_spent' => 0
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Book added to your library',
                'book' => $book
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add book to library'
            ], 500);
        }
    }
}