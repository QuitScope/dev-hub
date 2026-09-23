<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bugs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('priority');
            $table->string('status');
            $table->timestamp('reported_date');
            $table->timestamp('processed_date')->nullable();
            $table->timestamp('resolved_date')->nullable();
            $table->string('reporter');
            $table->string('assignee')->nullable();
            $table->string('jira_issue')->nullable();
            $table->json('tags')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('priority');
            $table->index('reporter');
            $table->index('assignee');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bugs');
    }
};
