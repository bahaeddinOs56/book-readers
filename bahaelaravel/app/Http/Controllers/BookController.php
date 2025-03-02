<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class BookController extends Controller
{
    // Update reading progress
    public function updateProgress(Request $request, $id)
    {
        $book = Book::findOrFail($id);
        
        // Add validation with proper rules
        $validated = $request->validate([
            'current_page' => ['required', 'integer', 'min:0', 'max:'.$book->total_pages]
        ]);

        $book->current_page = $validated['current_page'];
        $book->save();

        return response()->json([
            'success' => true,
            'book' => $book
        ]);
    }

    // Update book status
    public function updateStatus(Request $request, Book $book): JsonResponse
    {
        $request->validate([
            'status' => [
                'required',
                'string',
                'in:' . implode(',', Book::VALID_STATUSES)
            ]
        ]);

        if ($book->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $book->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => true,
            'book' => $book->fresh()
        ]);
    }

    // Update reading time
    public function updateReadingTime(Request $request, Book $book): JsonResponse
    {
        $request->validate([
            'time_spent' => 'required|integer|min:0'
        ]);

        if ($book->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $book->update([
            'time_spent' => $request->time_spent
        ]);

        return response()->json([
            'success' => true,
            'book' => $book->fresh()
        ]);
    }
    public function index(): JsonResponse
    {
        $books = Book::where('user_id', auth()->id())
                    ->orderBy('created_at', 'desc')
                    ->get();

        return response()->json([
            'success' => true,
            'books' => $books
        ]);
    }
    public function destroy(Book $book): JsonResponse
    {
        if ($book->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $book->delete();

        return response()->json([
            'success' => true,
            'message' => 'Book deleted successfully'
        ]);
    }
}