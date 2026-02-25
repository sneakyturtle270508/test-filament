<?php

namespace App\Filament\Widgets;

use App\Models\SleepLog;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class SleepChart extends ApexChartWidget
{
    protected static ?string $heading = 'Sleep Hours';

    protected function getChartData(): array
    {
        $sleepLogs = SleepLog::orderBy('date', 'desc')
            ->limit(14)
            ->get();

        return [
            'chart' => [
                'type' => 'area',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => 'Hours',
                    'data' => $sleepLogs->pluck('hours')->toArray(),
                ],
            ],
            'xaxis' => [
                'categories' => $sleepLogs->pluck('date')->map(fn ($date) => \Carbon\Carbon::parse($date)->format('M d'))->toArray(),
            ],
            'colors' => ['#8b5cf6'],
            'fill' => [
                'type' => 'gradient',
                'gradient' => [
                    'shadeIntensity' => 1,
                    'opacityFrom' => 0.7,
                    'opacityTo' => 0.3,
                ],
            ],
        ];
    }
}
