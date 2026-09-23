<?php

declare(strict_types=1);

namespace Domain\Commands;

use Carbon\Carbon;
use Domain\Actions\GenerateAnalyticsSnapshotAction;
use Domain\Enums\AnalyticsPeriod;
use Illuminate\Console\Command;

class GenerateWeeklyAnalyticsSnapshotCommand extends Command
{
    protected $signature = 'analytics:generate-weekly-snapshot';

    protected $description = 'Generate weekly analytics snapshot for last week';

    public function __construct(private GenerateAnalyticsSnapshotAction $generateSnapshot)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Generating weekly analytics snapshot...');

        $lastWeek = Carbon::now()->subWeek();

        try {
            $snapshot = $this->generateSnapshot->generateForPeriod($lastWeek, AnalyticsPeriod::Weekly);

            $this->info("Weekly snapshot generated successfully for week ending {$lastWeek->endOfWeek()->toDateString()}");
            $this->info("Snapshot ID: {$snapshot->id}");

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Failed to generate weekly snapshot: {$e->getMessage()}");

            return self::FAILURE;
        }
    }
}
