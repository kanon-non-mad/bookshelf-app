<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreBookRequest;
use App\Http\Resources\BookResource;
use App\Http\Resources\BookDetailResource;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\JsonResponse;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $keyword = $request->query('keyword');
        $genreId = $request->query('genre_id');
        $query = Book::with('genres');
            
        if (!empty($keyword)) {
            $query->where(function($query) use ($keyword) {
                $query->where('title', 'LIKE', "%{$keyword}%")
                ->orWhere('author', 'LIKE', "%{$keyword}%");
            });
        }
        if(!empty($genreIds)) {
            $query->whereHas('genres', function ($query) use ($genreIds) {
                $query->whereIn('genres.id',$genreIds);
            });
        }
        $books = $query
            ->withCount('reviews')
            ->withAvg('reviews','rating')
            ->paginate(10);
        return BookResource::collection($books);
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
                'data' => new BookResource(
                $book->load('genres')
                )
            ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        $book->loadCount('reviews')
             ->loadAvg('reviews','rating');

        $book -> load([
            'genres',
            'reviews' => function ($query) {
                $query
                    ->with('user')
                    ->withCount('likes');
            },
        ]);

        return new BookDetailResource($book);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreBookRequest $request, Book $book):BookResource|JsonResponse
    {
        $this->authorize('update', $book);
        $data = $request->validated();
	    $genreIds = $data['genres']??[];
	    unset($data['genres']);
	    $book->update($data);
	    $book->genres()->sync($genreIds);

        return response()->json([
            'message' => 'Book updated successfully.',
            'data' => new BookResource(
                $book->load('genres')
            )
        ],200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book):JsonResponse
    {
        $this->authorize('delete', $book);
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
