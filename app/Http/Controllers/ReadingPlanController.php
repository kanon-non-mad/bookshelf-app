<?php

namespace App\Http\Controllers;

use App\Enums\ReadingPlanStatus;
use App\Http\Requests\StoreReadingPlanRequest;
use App\Http\Requests\UpdateReadingPlanRequest;
use App\Models\Book;
use App\Models\ReadingPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReadingPlanController extends Controller
{
    /**
     *読書計画一覧を表示する
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $currentStatus = $request->input('status');

        $readingPlans = auth()->user()
            ->readingPlans()
            ->with('book')
            ->when($currentStatus, function ($query) use ($currentStatus) {
                $query->where('status', $currentStatus);
            })
            ->get();

        return view('reading-plans.index', compact('readingPlans', 'currentStatus'));
    }

    /**
     * 読書計画登録画面を表示する
     * 
     * @return View
     */
    public function create(): View
    {
        $books = Book::orderBy('title')->get();

        return view('reading-plans.create', compact('books'));
    }

    /**
     * 読書計画を登録する
     * 
     * @param StoreReadingPlanRequest $request
     * @return RedirectResponse
     */
    public function store(StoreReadingPlanRequest $request): RedirectResponse
    {
        ReadingPlan::create([
            'user_id' => Auth::id(),
            'book_id' => $request->book_id,
            'target_date' => $request->target_date,
            'status' => ReadingPlanStatus::NotStarted,
        ]);

        return redirect()->route('reading-plans.index')
            ->with('success', '読書計画を登録しました');
    }

    /**
     * 読書計画編集画面を表示する
     * 
     * @param ReadingPlan $readingPlan
     * @return View
     */
    public function edit(ReadingPlan $readingPlan): View
    {
        $this->authorize('update', $readingPlan);

        return view('reading-plans.edit', [
            'readingPlan' => $readingPlan,
        ]);
    }

    /**
     * 読書計画を更新する
     * 
     * @param UpdateReadingPlanRequest $request
     * @param ReadingPlan $readingPlan
     * @return RedirectResponse
     */
    public function update(UpdateReadingPlanRequest $request, ReadingPlan $readingPlan): RedirectResponse
    {
        $this->authorize('update', $readingPlan);

        $readingPlan->target_date = $request->target_date;

        $readingPlan->save();

        return redirect()->route('reading-plans.index');
    }

    /**
     * 読書計画を完了状態に変更する
     * 
     * @param ReadingPlan $readingPlan
     * @return RedirectResponse
     */
    public function complete(ReadingPlan $readingPlan): RedirectResponse
    {
        $this->authorize('update', $readingPlan);

        $readingPlan->status = ReadingPlanStatus::Completed;
        $readingPlan->save();

        return redirect()->route('reading-plans.index');
    }

    /**
     * 読書計画を削除する
     *
     * @param ReadingPlan $readingPlan
     * @return RedirectResponse
     */
    public function destroy(ReadingPlan $readingPlan): RedirectResponse
    {
        $this->authorize('delete', $readingPlan);

        $readingPlan->delete();

        return redirect()->route('reading-plans.index');
    }
}
