<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookRequest;
use App\Http\Resources\BookDetailResource;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class BookController extends Controller
{
    /**
     * 書籍一覧を取得する。
     *
     * @return AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $keyword = $request->query('keyword');
        $genreId = $request->query('genre') ?? $request->query('genre_id');
        $query = Book::with('genres');

        if (! empty($keyword)) {
            $query->where(function ($query) use ($keyword) {
                $query->where('title', 'LIKE', "%{$keyword}%")
                    ->orWhere('author', 'LIKE', "%{$keyword}%");
            });
        }
        if (! empty($genreId)) {
            $query->whereHas('genres', function ($query) use ($genreId) {
                $query->where('genres.id', $genreId);
            });
        }

        $sort = $request->query('sort', 'latest');

        if ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        }

        if ($sort === 'latest') {
            $query->orderBy('created_at', 'desc');
        }

        if ($sort === 'title') {
            $query->orderBy('title', 'asc');
        }

        $query->withAvg('reviews', 'rating');

        if ($sort === 'rating') {
            $query->orderByDesc('reviews_avg_rating');
        }

        $books = $query
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->paginate(10);

        return BookResource::collection($books);
    }

    /**
     * 書籍を登録する。
     *
     * @return BookResource
     */
    public function store(StoreBookRequest $request)
    {
        $data = $request->validated();
        $genreIds = $data['genres'];
        unset($data['genres']);
        $data['user_id'] = $request->user()->id;
        DB::transaction(function () use ($data, $genreIds, &$book) {
            $book = Book::create($data);
            $book->genres()->sync($genreIds);
        });

        return response()->json([
            'message' => 'Book created successfully.',
            'data' => new BookResource(
                $book->load('genres')
            ),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        $book->loadCount('reviews')
            ->loadAvg('reviews', 'rating');

        $book->load([
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
     * 書籍情報を更新する。
     */
    public function update(StoreBookRequest $request, Book $book): BookResource|JsonResponse
    {
        $this->authorize('update', $book);
        $data = $request->validated();
        $genreIds = $data['genres'] ?? [];
        unset($data['genres']);
        $book->update($data);
        $book->genres()->sync($genreIds);

        return response()->json([
            'message' => 'Book updated successfully.',
            'data' => new BookResource(
                $book->load('genres')
            ),
        ], 200);
    }

    /**
     * 書籍を削除する。
     */
    public function destroy(Book $book): JsonResponse
    {
        $this->authorize('delete', $book);
        DB::transaction(function () use ($book) {
            foreach ($book->reviews as $review) {
                $review->likes()->delete();
            }
            $book->reviews()->delete();
            $book->favorites()->delete();
            $book->genres()->detach();
            $book->delete();
        });

        return response()->json([
            'message' => 'Book deleted successfully.',
        ], Response::HTTP_OK);
    }
}
