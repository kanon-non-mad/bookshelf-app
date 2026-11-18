<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class BookController extends Controller
{
    /**
     * 書籍一覧を表示する
     */
    public function index(Request $request): View
    {
        $keyword = $request->query('keyword');

        $genreId = $request->query('genre');

        $sort = $request->query('sort');

        $isbn = $request->query('isbn');

        $query = Book::with('genres');

        if ($keyword) {
            $query->where(function ($query) use ($keyword) {
                $query->where('title', 'LIKE', "%{$keyword}%")
                    ->orWhere('author', 'LIKE', "%{$keyword}%");
            });
        }

        if ($isbn) {
            $query->where('isbn', $isbn);
        }

        if ($genreId) {
            $query->whereHas('genres', function ($query) use ($genreId) {
                $query->where('genres.id', $genreId);
            });
        }

        if ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } elseif ($sort === 'title') {
            $query->orderBy('title', 'asc');
        } elseif ($sort === 'rating') {
            $query->withAvg('reviews', 'rating')
                ->orderByDesc('reviews_avg_rating');
        } else {
            $query->latest();
        }

        $books = $query->paginate(10)
            ->withQueryString();

        $genres = Genre::all();

        return view('books.index', compact('books', 'genres'));
    }

    /**
     * ISBNからGoogle Books APIで書籍情報を取得する
     */
    public function searchIsbn(string $isbn): JsonResponse
    {
        $response = Http::get(config('services.google_books.url'), [
            'q' => 'isbn:'.$isbn,
            'key' => config('services.google_books.key'),
        ]);

        if ($response->failed()) {
            return response()->json([
                'error' => '書籍情報の取得に失敗しました。時間をおいて再度お試しください。',
            ], $response->status());
        }

        $data = $response->json();

        if (empty($data['items'])) {
            return response()->json([
                'error' => '書籍が見つかりませんでした。',
            ], 404);
        }

        $volumeInfo = $data['items'][0]['volumeInfo'];

        return response()->json([
            'title' => $volumeInfo['title'] ?? '',
            'author' => $volumeInfo['authors'][0] ?? '',
            'description' => $volumeInfo['description'] ?? '',
            'published_at' => $volumeInfo['publishedDate'] ?? '',
            'book_image' => $volumeInfo['imageLinks']['thumbnail'] ?? '',
        ]);
    }

    /**
     * 書籍登録画面を表示する
     */
    public function create(): View
    {
        $genres = Genre::all();

        return view('books.create', compact('genres'));
    }

    /**
     * 書籍を登録する
     */
    public function store(StoreBookRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $genreIds = $data['genres'];
        unset($data['genres']);
        $data['user_id'] = $request->user()->id;
        $book = Book::create($data);
        $book->genres()->sync($genreIds);

        return redirect()->route('books.index');
    }

    /**
     * 書籍詳細を表示する
     */
    public function show(Book $book): View
    {
        $book->load([
            'genres', 'reviews.user', 'reviews.likes',
        ]);

        return view('books.show', compact('book'));
    }

    /**
     * 書籍編集画面を表示する
     */
    public function edit(Book $book): View
    {
        $genres = Genre::all();

        return view('books.edit', compact('book', 'genres'));
    }

    /**
     * 書籍を更新する
     */
    public function update(StoreBookRequest $request, Book $book): RedirectResponse
    {
        $this->authorize('update', $book);
        $data = $request->validated();
        $genreIds = $data['genres'];
        unset($data['genres']);
        $book->update($data);
        $book->genres()->sync($genreIds);

        return redirect()->route('books.index');
    }

    /**
     * 書籍を削除する
     */
    public function destroy(Book $book): RedirectResponse
    {
        $this->authorize('delete', $book);
        foreach ($book->reviews as $review) {
            $review->likes()->delete();
        }
        $book->reviews()->delete();
        $book->favorites()->delete();
        $book->genres()->detach();
        $book->delete();

        return redirect()->route('books.index');
    }
}
