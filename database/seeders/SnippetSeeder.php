<?php

namespace Database\Seeders;

use Domain\Models\Snippet;
use Illuminate\Database\Seeder;

class SnippetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Snippet::factory()
            ->count(5)
            ->create();
    }
}
