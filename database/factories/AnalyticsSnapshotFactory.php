<?php

declare(strict_types=1);

namespace Database\Factories;

use Domain\Enums\AnalyticsPeriod;
use Domain\Models\AnalyticsSnapshot;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AnalyticsSnapshot>
 */
class AnalyticsSnapshotFactory extends Factory
{
    protected $model = AnalyticsSnapshot::class;

    public function definition(): array
    {
        $totalTasksCompleted = fake()->numberBetween(20, 80);
        $bugsResolved = fake()->numberBetween(10, 40);
        $todosCompleted = $totalTasksCompleted - $bugsResolved;
        $totalBugs = $bugsResolved + fake()->numberBetween(5, 15);
        $bugsReopened = fake()->numberBetween(0, 5);

        return [
            'date' => fake()->dateTimeBetween('-90 days', 'now'),
            'period' => fake()->randomElement([
                AnalyticsPeriod::Daily,
                AnalyticsPeriod::Weekly,
                AnalyticsPeriod::Monthly,
            ]),
            'metrics' => [
                'period' => [
                    'from' => fake()->dateTimeBetween('-30 days', '-1 day')->format('Y-m-d'),
                    'to' => fake()->dateTimeBetween('-1 day', 'now')->format('Y-m-d'),
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
                    'avg_cycle_time_hours' => round(fake()->randomFloat(1, 12.0, 72.0), 1),
                    'avg_bug_resolution_hours' => round(fake()->randomFloat(1, 8.0, 48.0), 1),
                    'avg_todo_completion_hours' => round(fake()->randomFloat(1, 2.0, 24.0), 1),
                ],
                'team' => [
                    'active_users' => fake()->numberBetween(3, 12),
                    'total_hours_tracked' => round(fake()->randomFloat(1, 100.0, 500.0), 1),
                    'top_performers' => [],
                ],
            ],
        ];
    }

    public function daily(): static
    {
        return $this->state(fn (array $attributes) => [
            'period' => AnalyticsPeriod::Daily,
        ]);
    }

    public function weekly(): static
    {
        return $this->state(fn (array $attributes) => [
            'period' => AnalyticsPeriod::Weekly,
        ]);
    }

    public function monthly(): static
    {
        return $this->state(fn (array $attributes) => [
            'period' => AnalyticsPeriod::Monthly,
        ]);
    }
}
