<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\ReadingPlanController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('books.index');
});


//BookController
Route::middleware('auth')->group(function () {
    Route::resource('books', BookController::class)
        ->only([
           'create','store','edit','update','destroy'
        ]);
    
    Route::resource('genres', GenreController::class);


//ReviewController
    Route::post('books/{book}/reviews',[ReviewController::class,'store'])->name('reviews.store');
    Route::resource('reviews',ReviewController::class)
        ->only(['edit','update','destroy']);
    Route::get('books/{book}/reviews/create',[ReviewController::class,'create'])->name('reviews.create');

//FavoriteController
    Route::get('/favorites',[FavoriteController::class, 'index'])
        ->name('favorites.index');
    Route::post('/books/{book}/favorites',[FavoriteController::class,'toggle'])
        ->name('favorites.toggle');

//LikeController
    Route::post('reviews/{review}/likes',[LikeController::class,'toggle'])
        ->name('likes.store');

//ReadingPlanController
    Route::resource('reading-plans',ReadingPlanController::class)
        ->except(['show']);
    Route::post('reading-plans/{reading_plan}/complete', [ReadingPlanController::class, 'complete'])
    ->name('reading-plans.complete');

//RankingController
    Route::get('/ranking', [RankingController::class, 'index'])
        ->name('ranking.index');

//ReportController
    Route::get('/reports', [ReportController::class, 'index'])
        ->name('reports.index');
});


Route::resource('books', BookController::class)
    ->only(['index','show']);