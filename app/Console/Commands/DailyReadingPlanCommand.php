<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ReadingPlan;
use App\Enums\ReadingPlanStatus;
use Carbon\Carbon;
use App\Notifications\ExpiredReadingPlanNotification;

class DailyReadingPlanCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:daily-reading-plan-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update expired reading plans';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();

        $expiredPlans = ReadingPlan::whereDate('target_date', '<', $today)
        ->whereIn('status', [
            ReadingPlanStatus::NotStarted,
            ReadingPlanStatus::Reading,
        ])
        ->get();

        foreach($expiredPlans as $expiredPlan) {
                $expiredPlan->status = ReadingPlanStatus::Expired;

                $expiredPlan->save();

                $expiredPlan->user->notify(
                    new ExpiredReadingPlanNotification($expiredPlan)
                );
            }
    }
}
