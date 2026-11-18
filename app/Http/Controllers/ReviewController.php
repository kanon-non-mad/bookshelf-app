<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Models\Book;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReviewController extends Controller
{
    /**
     * レビューを登録する
     */
    public function store(StoreReviewRequest $request, Book $book): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        $data['book_id'] = $book->id;
        Review::create($data);

        return redirect()->route('books.show', $book);
    }

    /**
     * レビュー投稿画面を表示する
     */
    public function create(Book $book): View
    {
        return view('reviews.create', compact('book'));
    }

    /**
     * レビュー編集画面を表示する
     */
    public function edit(Review $review): View
    {
        return view('reviews.edit', compact('review'));
    }

    /**
     * レビューを更新する
     */
    public function update(StoreReviewRequest $request, Review $review): RedirectResponse
    {
        $this->authorize('update', $review);

        $data = $request->validated();
        $review->update($data);

        return redirect()->route('books.show', $review->book_id);
    }

    /**
     * レビューを削除する
     */
    public function destroy(Review $review): RedirectResponse
    {
        $this->authorize('delete', $review);

        $review->likes()->delete();
        $review->delete();

        return redirect()->route('books.show', $review->book_id);
    }
}
