<?php

namespace App\Filament\Widgets;

use App\Models\ExerciseSet;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class WeightProgressionChart extends ApexChartWidget
{
    protected static ?string $heading = 'Weight Progression';

    protected function getChartData(): array
    {
        $exercises = ExerciseSet::whereNotNull('weight')
            ->where('weight', '>', 0)
            ->select('exercise_name')
            ->distinct()
            ->pluck('exercise_name');

        $series = [];
        $categories = [];

        foreach ($exercises as $exercise) {
            $data = ExerciseSet::where('exercise_name', $exercise)
                ->whereNotNull('weight')
                ->where('weight', '>', 0)
                ->orderBy('created_at')
                ->get()
                ->map(fn ($item) => $item->weight);

            if ($data->isNotEmpty()) {
                $series[] = [
                    'name' => $exercise,
                    'data' => $data->toArray(),
                ];
                $categories = ExerciseSet::where('exercise_name', $exercise)
                    ->orderBy('created_at')
                    ->pluck('created_at')
                    ->map(fn ($date) => $date->format('M d'))
                    ->toArray();
            }
        }

        return [
            'chart' => [
                'type' => 'line',
                'height' => 300,
            ],
            'series' => $series,
            'xaxis' => [
                'categories' => $categories,
            ],
            'colors' => ['#f59e0b', '#10b981', '#3b82f6', '#ef4444'],
        ];
    }
}
