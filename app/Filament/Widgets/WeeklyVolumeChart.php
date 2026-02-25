<?php

namespace App\Filament\Widgets;

use App\Models\ExerciseSet;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class WeeklyVolumeChart extends ApexChartWidget
{
    protected static ?string $heading = 'Weekly Volume';

    protected function getChartData(): array
    {
        $volumes = ExerciseSet::selectRaw('DATE(created_at) as date, SUM(weight * reps) as volume')
            ->where('created_at', '>=', now()->subDays(14))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => 'Volume',
                    'data' => $volumes->pluck('volume')->toArray(),
                ],
            ],
            'xaxis' => [
                'categories' => $volumes->pluck('date')->map(fn ($date) => \Carbon\Carbon::parse($date)->format('M d'))->toArray(),
            ],
            'colors' => ['#3b82f6'],
        ];
    }
}
