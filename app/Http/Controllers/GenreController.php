<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGenreRequest;
use App\Http\Requests\UpdateGenreRequest;
use App\Models\Genre;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GenreController extends Controller
{
    /**
     * ジャンル一覧を表示する
     * 
     * @return View
     */
    public function index(): View
    {
        $genres = Genre::withCount('books')->get();

        return view('genres.index', compact('genres'));
    }

    /**
     * ジャンル登録画面を表示する
     * 
     * @return View
     */
    public function create(): View
    {
        return view('genres.create');
    }

    /**
     * ジャンルを登録する
     * 
     * @param StoreGenreRequest $request
     * @return RedirectResponse
     */
    public function store(StoreGenreRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        Genre::create($validated);

        return redirect()->route('genres.index')
            ->with('success', 'ジャンルを登録しました');
    }

    /**
     * ジャンル詳細を表示する
     * 
     * @param Genre $genre
     * @return View
     */
    public function show(Genre $genre): View
    {
        $books = $genre->books()
            ->with('genres')
            ->paginate(12);

        return view('genres.show', compact('genre', 'books'));
    }

    /**
     * ジャンル編集画面を表示する
     *
     * @param Genre $genre
     * @return View
     */
    public function edit(Genre $genre): View
    {
        return view('genres.edit', compact('genre'));
    }

    /**
     * ジャンルを更新する
     *
     * @param UpdateGenreRequest $request
     * @param Genre $genre
     * @return RedirectResponse
     */
    public function update(UpdateGenreRequest $request, Genre $genre): RedirectResponse
    {
        $data = $request->validated();
        $genre->update($data);

        return redirect()->route('genres.index')
            ->with('success', 'ジャンルを更新しました');
    }

    /**
     * ジャンルを削除する
     *
     * @param Genre $genre
     * @return RedirectResponse
     */
    public function destroy(Genre $genre): RedirectResponse
    {
        if ($genre->books()->exists()) {
            return back()->with('error', '使用中のジャンルは削除できません');
        }

        $genre->delete();

        return redirect()->route('genres.index')
            ->with('success', 'ジャンルを削除しました');
    }
}
