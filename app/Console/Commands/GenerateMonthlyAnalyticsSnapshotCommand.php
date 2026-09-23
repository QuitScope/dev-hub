<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Carbon\Carbon;
use Domain\Actions\GenerateAnalyticsSnapshotAction;
use Domain\Enums\AnalyticsPeriod;
use Illuminate\Console\Command;

class GenerateMonthlyAnalyticsSnapshotCommand extends Command
{
    protected $signature = 'analytics:generate-monthly-snapshot';

    protected $description = 'Generate monthly analytics snapshot for last month';

    public function __construct(private GenerateAnalyticsSnapshotAction $generateSnapshot)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Generating monthly analytics snapshot...');

        $lastMonth = Carbon::now()->subMonth();

        try {
            $snapshot = $this->generateSnapshot->generateForPeriod($lastMonth, AnalyticsPeriod::Monthly);

            $this->info("Monthly snapshot generated successfully for {$lastMonth->format('Y-m')}");
            $this->info("Snapshot ID: {$snapshot->id}");

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Failed to generate monthly snapshot: {$e->getMessage()}");

            return self::FAILURE;
        }
    }
}
