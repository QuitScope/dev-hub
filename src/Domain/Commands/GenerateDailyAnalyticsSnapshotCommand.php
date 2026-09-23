<?php

declare(strict_types=1);

namespace Domain\Commands;

use Carbon\Carbon;
use Domain\Actions\GenerateAnalyticsSnapshotAction;
use Domain\Enums\AnalyticsPeriod;
use Illuminate\Console\Command;

class GenerateDailyAnalyticsSnapshotCommand extends Command
{
    protected $signature = 'analytics:generate-daily-snapshot';

    protected $description = 'Generate daily analytics snapshot for yesterday';

    public function __construct(private GenerateAnalyticsSnapshotAction $generateSnapshot)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Generating daily analytics snapshot...');

        $yesterday = Carbon::yesterday();

        try {
            $snapshot = $this->generateSnapshot->generateForPeriod($yesterday, AnalyticsPeriod::Daily);

            $this->info("Daily snapshot generated successfully for {$yesterday->toDateString()}");
            $this->info("Snapshot ID: {$snapshot->id}");

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Failed to generate daily snapshot: {$e->getMessage()}");

            return self::FAILURE;
        }
    }
}
