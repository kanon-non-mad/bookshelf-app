<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
	public function index()
	{
		$books = Auth::user()
			->favoriteBooks()
			->paginate(12);
		
		return view('favorites.index', compact('books'));
	}

    public function toggle(Book $book)
    {
        if (Favorite::where([
    		'user_id' => Auth::id(),
    		'book_id' => $book->id,
		])->exists()){

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
