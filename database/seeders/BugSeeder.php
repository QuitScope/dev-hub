<?php

declare(strict_types=1);

namespace Database\Seeders;

use Domain\Models\Bug;
use Illuminate\Database\Seeder;

class BugSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Bugs...');

        // Create a mix of bugs with different statuses and priorities
        Bug::factory()->count(5)->reported()->create();
        Bug::factory()->count(3)->reported()->critical()->create();
        Bug::factory()->count(8)->inProgress()->create();
        Bug::factory()->count(2)->inProgress()->high()->create();
        Bug::factory()->count(12)->resolved()->create();
        Bug::factory()->count(5)->create(); // Random status

        $this->command->info('Bugs seeded successfully!');
        $this->command->info('Total bugs created: '.Bug::count());
    }
}
