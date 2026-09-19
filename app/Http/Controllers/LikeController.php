<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function store(Review $review)
    {
	$data = [
        'user_id' => Auth::id(),
	    'review_id' => $review->id,
		];
	Like::create($data);
	return redirect()->route('books.show',$review->book_id);
    }

    public function destroy(Review $review)
    {
	Like::where([
    		'user_id' => Auth::id(),
    		'review_id' => $review->id,
		])->delete();

	return redirect()->route('books.show',$review->book_id);

    }
}
