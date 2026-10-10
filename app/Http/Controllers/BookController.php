<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::with('genres')
		      ->latest()
		      ->paginate(10);

	return view('books.index',compact('books'));
    }

    public function create()
    {
        $genres = Genre::all();

	    return view('books.create',compact('genres'));
    }

    public function store(StoreBookRequest $request)
    {
        $data = $request->validated();
	    $genreIds = $data['genres'];
	    unset($data['genres']);
	    $data['user_id'] = $request->user()->id;
	    $book = Book::create($data);
	    $book->genres()->sync($genreIds);

	    return redirect()->route('books.index');
    }

    public function show(Book $book)
    {
        $book -> load([
		'genres','reviews.user','reviews.likes',
			]);

	    return view ('books.show',compact('book'));
    }

    public function edit(Book $book)
    {
        $genres = Genre::all();

	    return view('books.edit',compact('book','genres'));

    }

    public function update(StoreBookRequest $request, Book $book)
    {
        $this->authorize('update', $book);
        $data = $request->validated();
	    $genreIds = $data['genres'];
	    unset($data['genres']);
	    $book->update($data);
	    $book->genres()->sync($genreIds);

	    return redirect()->route('books.index');
    }

    public function destroy(Book $book)
    {
        $this->authorize('delete', $book);
        foreach ($book->reviews as $review) {
            $review->likes()->delete();
        }
        $book->reviews()->delete();
	    $book->favorites()-> delete();
	    $book->genres()->detach();
	    $book->delete();
	
	return redirect() ->route('books.index');
    }
}
