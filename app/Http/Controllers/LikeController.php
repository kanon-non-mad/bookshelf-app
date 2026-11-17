<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    /**
     * いいね登録・解除を切り替える
     * 
     * @param Review $review
     * @return RedirectResponse
     */
    public function toggle(Review $review): RedirectResponse
    {
        if (Like::where([
            'user_id' => Auth::id(),
            'review_id' => $review->id,
        ])->exists()) {

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

        return redirect()->route('books.show', $review->book_id);

    }
}
