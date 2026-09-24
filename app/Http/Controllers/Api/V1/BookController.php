<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreBookRequest;
use Symfony\Component\HttpFoundation\Response;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $books = Book::with('genres')
              ->latest()
              ->paginate(10);
        return response()->json($books);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookRequest $request)
    {
        $data = $request->validated();
        $genreIds = $data['genres'];
        unset($data['genres']);
        $data['user_id'] = $request->user()->id;
        DB::transaction(function () use ($data,$genreIds,&$book) {
            $book = Book::create($data);
            $book->genres()->sync($genreIds);
        });

        return response()->json([
                'message' => 'Book created successfully.',
                'data' => $book->load('genres')
            ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        $book -> load([
            'genres','reviews.user','reviews.likes',
        ]);

        return response()->json($book);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreBookRequest $request, Book $book):BookResource|JsonResponse
    {
        $data = $request->validated();
	    $genreIds = $data['genres']??[];
	    unset($data['genres']);
	    $book->update($data);
	    $book->genres()->sync($genreIds);

        return response()->json([
            'message' => 'Book updated successfully.',
            'data' => $book->load('genres'),
        ],200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book):JsonResponse
    {
        DB::transaction(function() use ($book){
        foreach ($book->reviews as $review) {
            $review->likes()->delete();
        }
        $book->reviews()->delete();
	    $book->favorites()-> delete();
	    $book->genres()->detach();
	    $book->delete();
        });

        return response()->json([
            'message' => 'Book deleted successfully.',
        ],Response::HTTP_OK);
    }
}
