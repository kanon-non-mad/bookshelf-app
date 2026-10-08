<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Models\Book;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(StoreReviewRequest $request, Book $book)
    {
        $data = $request->validated(); 
        $data['user_id'] = Auth::id();
        $data['book_id'] = $book->id;
        Review::create($data);
    
        return redirect()->route('books.show', $book);
    }

    public function create( Book $book )
    {
        return view('reviews.create',compact('book'));
    }

    public function edit(Review $review)
    {
       	return view('reviews.edit',compact('review'));

    } 

    public function update(StoreReviewRequest $request, Review $review)
    {
  	    $data = $request->validated();
	    $review->update($data);
  	
	    return redirect()->route('books.show', $review->book_id);
    }

    public function destroy(Review $review)
    {
        $review->likes()->delete();
	    $review->delete();
	
	    return redirect() ->route('books.show',$review->book_id);
    }
}
