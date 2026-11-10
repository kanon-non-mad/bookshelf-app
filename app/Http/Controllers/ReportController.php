<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Enums\ReadingPlanStatus;

class ReportController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $completedCount = $user->readingPlans()
            ->where('status',ReadingPlanStatus::Completed)
            ->distinct('book_id')
            ->count('book_id');

        $averageRating = $user->reviews()
            ->avg('rating');

        $ratingDistribution = $user->reviews()
            ->get()
            ->groupBy('rating')
            ->map->count();

        $topRatedBooks = $user->reviews()
            ->with('book')
            ->where('rating', '>=', 4)
            ->orderBy('rating','desc')
            ->limit(5)
            ->get()
            ->map(function ($review) {
                return[
                    'id' => $review->book->id,
                    'title' => $review->book->title,
                    'author' => $review->book->author,
                    'rating' => $review->rating,
                    ];
                });
        
        $reviews = $user->reviews()
            ->with('book.genres')
            ->get();

        $genreRatings = $reviews
            ->flatMap(function ($review) {
                return $review->book->genres->map(function ($genre) use ($review) {
                    return[
                        'genre' => $genre,
                        'rating' => $review->rating,
                    ];
                });
            })
            ->groupBy(function ($item) {
                return $item['genre']->id;
            })
            ->map(function ($items) {
                return [
                    'id' => $items->first()['genre']->id,
                    'name' => $items->first()['genre']->name,
                    'count' => $items->count(),
                    'average_rating' => $items->avg('rating'),
                ];
            });
        
        $stats = [
            'summary' => [
                'total_reviews' => $user->reviews()->count(),
                'books_read' => $completedCount,
                'average_rating' => $averageRating,
            ],
            'rating_distribution' => $ratingDistribution,
            'top_rated_books' => $topRatedBooks,
            'genre_ratings' => $genreRatings,
        ];

        return view('reports.index',[
            'stats' => $stats,
            ]);
    }
}
