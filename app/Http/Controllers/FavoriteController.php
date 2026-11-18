<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Favorite;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FavoriteController extends Controller
{
    /**
     * お気に入り一覧を表示する
     */
    public function index(): View
    {
        $books = Auth::user()
            ->favoriteBooks()
            ->paginate(12);

        return view('favorites.index', compact('books'));
    }

    /**
     * お気に入り登録・解除を切り替える
     */
    public function toggle(Book $book): RedirectResponse
    {
        if (Favorite::where([
            'user_id' => Auth::id(),
            'book_id' => $book->id,
        ])->exists()) {

            Favorite::where([
                'user_id' => Auth::id(),
                'book_id' => $book->id,
            ])->delete();

        } else {
            Favorite::create([
                'user_id' => Auth::id(),
                'book_id' => $book->id,
            ]);
        }

        return redirect()->route('books.show', $book);

    }
}
