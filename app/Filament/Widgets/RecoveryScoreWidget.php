<?php

namespace App\Filament\Widgets;

use App\Models\NutritionLog;
use App\Models\SleepLog;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class RecoveryScoreWidget extends StatsOverviewWidget
{
    protected function getCards(): array
    {
        $sleepLog = SleepLog::orderBy('date', 'desc')->first();
        $nutritionLog = NutritionLog::orderBy('date', 'desc')->first();

        $sleepScore = $sleepLog ? min(100, ($sleepLog->hours / 8) * 50 + ($sleepLog->quality ?? 5) * 5) : 0;
        $nutritionScore = $nutritionLog && $nutritionLog->protein >= 150 ? 50 : ($nutritionLog && $nutritionLog->protein >= 100 ? 35 : 20);
        $totalScore = min(100, $sleepScore + $nutritionScore);

        $color = match (true) {
            $totalScore >= 70 => 'success',
            $totalScore >= 40 => 'warning',
            default => 'danger',
        };

        return [
            Card::make('Recovery Score', $totalScore.'%')
                ->description('Based on sleep & nutrition')
                ->color($color),
        ];
    }
}
