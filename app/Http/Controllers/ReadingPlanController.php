<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReadingPlanRequest;
use App\Http\Requests\UpdateReadingPlanRequest;
use Illuminate\Http\Request;
use App\Models\ReadingPlan;
use App\Models\Book;
use App\Enums\ReadingPlanStatus;
use Illuminate\Support\Facades\Auth;

class ReadingPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $readingPlans = auth()->user()
                ->readingPlans()
                ->with('book')
                ->get();

        $currentStatus = $request->input('status');

        return view('reading-plans.index',compact('readingPlans','currentStatus'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $books = Book::orderBy('title')->get();

        return view('reading-plans.create',compact('books'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReadingPlanRequest $request)
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
     * Show the form for editing the specified resource.
     */
    public function edit(ReadingPlan $readingPlan)
    {
        $this->authorize('update', $readingPlan);
        
        return view ('reading-plans.edit',[
            'readingPlan' => $readingPlan,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReadingPlanRequest $request, ReadingPlan $readingPlan)
    {
        $this->authorize('update', $readingPlan);

        $readingPlan->target_date = $request->target_date;

        $readingPlan->save();

        return redirect()->route('reading-plans.index');
    }

    public function complete(ReadingPlan $readingPlan)
    {
        $this->authorize('update', $readingPlan);

        $readingPlan->status = ReadingPlanStatus::Completed;
        $readingPlan->save();

        return redirect()->route('reading-plans.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ReadingPlan $readingPlan)
    {
        $this->authorize('delete', $readingPlan);

        $readingPlan->delete();
    
        return redirect()->route('reading-plans.index');
    }
}
