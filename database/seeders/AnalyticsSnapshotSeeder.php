<?php

declare(strict_types=1);

namespace Database\Seeders;

use Carbon\Carbon;
use Domain\Models\AnalyticsSnapshot;
use Illuminate\Database\Seeder;

class AnalyticsSnapshotSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Analytics Snapshots...');

        // Generate Daily Snapshots for the last 30 days
        $this->command->info('Creating daily snapshots for the last 30 days...');
        for ($i = 30; $i >= 1; $i--) {
            $date = Carbon::now()->subDays($i);

            AnalyticsSnapshot::factory()
                ->daily()
                ->create([
                    'date' => $date,
                    'metrics' => $this->generateMetrics($i, 'daily'),
                ]);
        }

        // Generate Weekly Snapshots for the last 12 weeks
        $this->command->info('Creating weekly snapshots for the last 12 weeks...');
        for ($i = 12; $i >= 1; $i--) {
            $date = Carbon::now()->subWeeks($i)->endOfWeek();

            AnalyticsSnapshot::factory()
                ->weekly()
                ->create([
                    'date' => $date,
                    'metrics' => $this->generateMetrics($i, 'weekly'),
                ]);
        }

        // Generate Monthly Snapshots for the last 12 months
        $this->command->info('Creating monthly snapshots for the last 12 months...');
        for ($i = 12; $i >= 1; $i--) {
            $date = Carbon::now()->subMonths($i)->endOfMonth();

            AnalyticsSnapshot::factory()
                ->monthly()
                ->create([
                    'date' => $date,
                    'metrics' => $this->generateMetrics($i, 'monthly'),
                ]);
        }

        $this->command->info('Analytics Snapshots seeded successfully!');
        $this->command->info('Total snapshots created: '.(30 + 12 + 12));
    }

    private function generateMetrics(int $periodsAgo, string $periodType): array
    {
        // Create realistic trending data (improving over time)
        $baseMultiplier = match ($periodType) {
            'daily' => 1.0,
            'weekly' => 5.0,
            'monthly' => 20.0,
            default => 1.0,
        };

        // Older data has lower values, newer data has higher values (showing improvement)
        $trendFactor = 1 + ((1 / $periodsAgo) * 0.3);

        $totalTasksCompleted = (int) (fake()->numberBetween(20, 50) * $baseMultiplier * $trendFactor);
        $bugsResolved = (int) (fake()->numberBetween(8, 20) * $baseMultiplier * $trendFactor);
        $todosCompleted = $totalTasksCompleted - $bugsResolved;
        $totalBugs = $bugsResolved + fake()->numberBetween(2, 8);
        $bugsReopened = (int) (fake()->numberBetween(0, 3) / $trendFactor);

        $from = match ($periodType) {
            'daily' => Carbon::now()->subDays($periodsAgo)->startOfDay(),
            'weekly' => Carbon::now()->subWeeks($periodsAgo)->startOfWeek(),
            'monthly' => Carbon::now()->subMonths($periodsAgo)->startOfMonth(),
        };

        $to = match ($periodType) {
            'daily' => Carbon::now()->subDays($periodsAgo)->endOfDay(),
            'weekly' => Carbon::now()->subWeeks($periodsAgo)->endOfWeek(),
            'monthly' => Carbon::now()->subMonths($periodsAgo)->endOfMonth(),
        };

        return [
            'period' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
            ],
            'velocity' => [
                'total_tasks_completed' => $totalTasksCompleted,
                'bugs_resolved' => $bugsResolved,
                'todos_completed' => $todosCompleted,
            ],
            'quality' => [
                'bug_resolution_rate' => $totalBugs > 0 ? round($bugsResolved / $totalBugs, 2) : 0,
                'bugs_reopened' => $bugsReopened,
                'first_time_fix_rate' => $totalBugs > 0 ? round(($bugsResolved - $bugsReopened) / $totalBugs, 2) : 0,
            ],
            'performance' => [
                'avg_cycle_time_hours' => round(fake()->randomFloat(1, 20.0, 50.0) / $trendFactor, 1),
                'avg_bug_resolution_hours' => round(fake()->randomFloat(1, 15.0, 40.0) / $trendFactor, 1),
                'avg_todo_completion_hours' => round(fake()->randomFloat(1, 3.0, 15.0) / $trendFactor, 1),
            ],
            'team' => [
                'active_users' => fake()->numberBetween(4, 10),
                'total_hours_tracked' => round(fake()->randomFloat(1, 150.0, 400.0) * $baseMultiplier, 1),
                'top_performers' => [],
            ],
        ];
    }
}
