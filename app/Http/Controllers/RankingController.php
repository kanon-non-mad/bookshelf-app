<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\View\View;

class RankingController extends Controller
{
    /**
     * 書籍ランキングを表示する
     * 
     * @return View
     */
    public function index(): View
    {
        $rankedBooks = Book::whereHas('reviews')
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->orderByDesc('reviews_avg_rating')
            ->limit(10)
            ->get();

        return view('ranking.index', [
            'rankedBooks' => $rankedBooks,
        ]);
    }
}
