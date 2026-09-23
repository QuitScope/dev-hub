<?php

declare(strict_types=1);

namespace Database\Factories;

use Domain\Enums\BugPriority;
use Domain\Enums\BugStatus;
use Domain\Models\Bug;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Bug>
 */
class BugFactory extends Factory
{
    protected $model = Bug::class;

    public function definition(): array
    {
        $status = fake()->randomElement(BugStatus::cases());
        $reportedDate = fake()->dateTimeBetween('-60 days', '-1 day');

        $processedDate = null;
        $resolvedDate = null;

        if (in_array($status, [BugStatus::InProgress, BugStatus::Resolved, BugStatus::Closed])) {
            $processedDate = fake()->dateTimeBetween($reportedDate, 'now');
        }

        if (in_array($status, [BugStatus::Resolved, BugStatus::Closed])) {
            $resolvedDate = fake()->dateTimeBetween($processedDate ?? $reportedDate, 'now');
        }

        return [
            'title' => fake()->randomElement([
                'Button funktioniert nicht',
                'Layout bricht auf Mobilgeräten',
                'API Timeout beim Laden',
                'Formular sendet falsche Daten',
                'Fehler beim Datei-Upload',
                'Login schlägt fehl',
                'Seite lädt nicht vollständig',
                'Performance-Problem bei großen Datensätzen',
                'Fehlerhafte Validierung',
                'Dark Mode zeigt falsche Farben',
            ]),
            'description' => fake()->paragraph(),
            'priority' => fake()->randomElement(BugPriority::cases()),
            'status' => $status,
            'reported_date' => $reportedDate,
            'processed_date' => $processedDate,
            'resolved_date' => $resolvedDate,
            'reporter' => fake()->name(),
            'assignee' => fake()->optional(0.8)->name(),
            'jira_issue' => fake()->optional(0.6)->regexify('DEV-[0-9]{3,4}'),
            'tags' => fake()->randomElements(['frontend', 'backend', 'ui', 'api', 'database', 'critical', 'security', 'performance'], rand(1, 3)),
        ];
    }

    public function reported(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BugStatus::Reported,
            'processed_date' => null,
            'resolved_date' => null,
        ]);
    }

    public function inProgress(): static
    {
        return $this->state(function (array $attributes) {
            $reportedDate = $attributes['reported_date'];

            return [
                'status' => BugStatus::InProgress,
                'processed_date' => fake()->dateTimeBetween($reportedDate, 'now'),
                'resolved_date' => null,
            ];
        });
    }

    public function resolved(): static
    {
        return $this->state(function (array $attributes) {
            $reportedDate = $attributes['reported_date'];
            $processedDate = fake()->dateTimeBetween($reportedDate, 'now');

            return [
                'status' => BugStatus::Resolved,
                'processed_date' => $processedDate,
                'resolved_date' => fake()->dateTimeBetween($processedDate, 'now'),
            ];
        });
    }

    public function critical(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => BugPriority::Critical,
        ]);
    }

    public function high(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => BugPriority::High,
        ]);
    }
}
