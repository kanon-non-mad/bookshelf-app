<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookController extends Controller
{
    /**
     * 書籍一覧を表示する
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $keyword = $request->query('keyword');

        $genreId = $request->query('genre');

        $sort = $request->query('sort');

        $query = Book::with('genres');

        if ($keyword) {
            $query->where(function ($query) use ($keyword) {
                $query->where('title', 'LIKE', "%{$keyword}%")
                    ->orWhere('author', 'LIKE', "%{$keyword}%");
            });
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
     * 書籍登録画面を表示する
     *
     * @return View
     */
    public function create(): View
    {
        $genres = Genre::all();

        return view('books.create', compact('genres'));
    }

    /**
     * 書籍を登録する
     *
     * @param StoreBookRequest $request
     * @return RedirectResponse
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
     *
     * @param Book $book
     * @return View
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
     *
     * @param Book $book
     * @return View
     */
    public function edit(Book $book): View
    {
        $genres = Genre::all();

        return view('books.edit', compact('book', 'genres'));
    }

    /**
     * 書籍を更新する
     *
     * @param StoreBookRequest $request
     * @param Book $book
     * @return RedirectResponse
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
     *
     * @param Book $book
     * @return RedirectResponse
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
