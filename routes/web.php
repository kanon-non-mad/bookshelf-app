<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\LikeController;


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


//ReviewController
    Route::post('books/{book}/reviews',[ReviewController::class,'store'])->name('reviews.store');
    Route::resource('reviews',ReviewController::class)
        ->only(['edit','update','destroy']);
    Route::get('books/{book}/reviews/create',[ReviewController::class,'create'])->name('reviews.create');

//FavoriteController
    Route::post('/books/{book}/favorites',[FavoriteController::class,'toggle'])
        ->name('favorites.toggle');

//LikeController
    Route::post('reviews/{review}/likes',[LikeController::class,'store'])->name('likes.store');
    Route::delete('reviews/{review}/likes',[LikeController::class,'destroy'])->name('likes.destroy');
});

Route::resource('books', BookController::class)
    ->only(['index','show']);