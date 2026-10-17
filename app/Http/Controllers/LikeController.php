<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function toggle(Review $review)
    {
		if (Like::where([
    		'user_id' => Auth::id(),
    		'review_id' => $review->id,
		])->exists()){

	        Like::where([
	         'user_id' => Auth::id(),
	         'review_id' => $review->id,
		    ])->delete();

   	    } else {
	        Like::create([
	            'user_id' => Auth::id(),
	            'review_id' => $review->id,
		    ]);
    	}
	return redirect()->route('books.show',$review->book_id);

    }
}
