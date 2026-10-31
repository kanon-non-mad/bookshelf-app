<?php

namespace App\Enums;

enum ReadingPlanStatus: string
{
    case NotStarted = 'not_started';
    case Reading = 'in_progress';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            ReadingPlanStatus::NotStarted => '未着手',
            ReadingPlanStatus::InProgress => '読書中',
            ReadingPlanStatus::Completed => '完了',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            ReadingPlanStatus::NotStarted => 'bg-gray-100 text-gray-800',
            ReadingPlanStatus::Reading => 'bg-blue-100 text-blue-800',
            ReadingPlanStatus::Completed => 'bg-green-100 text-green-800',
        };
    }
}
