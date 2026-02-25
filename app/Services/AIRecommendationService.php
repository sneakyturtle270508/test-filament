<?php

namespace App\Services;

use App\Models\AirRecommendation;
use App\Models\ExerciseSet;
use App\Models\NutritionLog;
use App\Models\SleepLog;
use App\Models\User;
use Carbon\Carbon;

class AIRecommendationService
{
    public function analyzeAndRecommend(User $user): void
    {
        $this->checkStagnation($user);
        $this->checkRecovery($user);
        $this->checkNutrition($user);
    }

    private function checkStagnation(User $user): void
    {
        $recentWorkouts = ExerciseSet::where('user_id', $user->id)
            ->where('created_at', '>=', Carbon::now()->subWeeks(2))
            ->get()
            ->groupBy('exercise_name');

        foreach ($recentWorkouts as $exerciseName => $sets) {
            if ($sets->count() < 4) {
                continue;
            }

            $volumes = $sets->map(fn ($set) => $this->calculateVolume($set));

            if ($this->isStagnating($volumes)) {
                $this->createRecommendation(
                    $user,
                    'progressive_overload',
                    "Stagnasjon oppdaget på {$exerciseName}. Prøv å øke vekten med 2.5kg eller legg til 1-2 reps."
                );
            }
        }
    }

    private function checkRecovery(User $user): void
    {
        $recentSleep = SleepLog::where('user_id', $user->id)
            ->where('date', '>=', Carbon::now()->subDays(3))
            ->get();

        $avgSleep = $recentSleep->avg('hours');

        if ($avgSleep && $avgSleep < 6) {
            $this->createRecommendation(
                $user,
                'recovery',
                "Din søvn ({$avgSleep}t) er under anbefalt nivå. Vurder en restitusjonsdag for optimal progresjon."
            );
        }
    }

    private function checkNutrition(User $user): void
    {
        $recentNutrition = NutritionLog::where('user_id', $user->id)
            ->where('date', '>=', Carbon::now()->subDays(3))
            ->get();

        $avgProtein = $recentNutrition->avg('protein');

        if ($avgProtein && $avgProtein < 100) {
            $this->createRecommendation(
                $user,
                'nutrition',
                "Proteininntaket ditt ({$avgProtein}g) er lavt. Prøv å øke til 150g+ for bedre muskelvekst."
            );
        }
    }

    private function calculateVolume(ExerciseSet $set): float
    {
        $sets = is_array($set->sets) ? $set->sets : [];
        $totalVolume = 0;

        foreach ($sets as $s) {
            $reps = $s['reps'] ?? 0;
            $weight = $s['weight'] ?? 0;
            $totalVolume += $reps * $weight;
        }

        return $totalVolume;
    }

    private function isStagnating($volumes): bool
    {
        if ($volumes->count() < 3) {
            return false;
        }

        $first = $volumes->first();
        $last = $volumes->last();

        return $last <= $first * 1.05;
    }

    private function createRecommendation(User $user, string $type, string $message): void
    {
        AirRecommendation::create([
            'user_id' => $user->id,
            'type' => $type,
            'message' => $message,
        ]);
    }

    public function calculate1RM(float $weight, int $reps): float
    {
        if ($reps == 0) {
            return 0;
        }

        if ($reps == 1) {
            return $weight;
        }

        return $weight * (1 + $reps / 30);
    }

    public function calculateRecoveryScore(User $user): int
    {
        $sleepLog = SleepLog::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->first();

        $nutritionLog = NutritionLog::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->first();

        $score = 50;

        if ($sleepLog) {
            if ($sleepLog->hours >= 7) {
                $score += 25;
            } elseif ($sleepLog->hours >= 6) {
                $score += 15;
            }

            if ($sleepLog->quality >= 7) {
                $score += 15;
            } elseif ($sleepLog->quality >= 5) {
                $score += 10;
            }
        }

        if ($nutritionLog && $nutritionLog->protein >= 150) {
            $score += 10;
        }

        return min(100, $score);
    }
}
