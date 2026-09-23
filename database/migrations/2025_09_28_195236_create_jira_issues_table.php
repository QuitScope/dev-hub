<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jira_issues', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('jira_id')->unique();
            $table->string('jira_key')->unique();
            $table->text('summary');
            $table->longText('description')->nullable();
            $table->string('status', 100);
            $table->string('priority', 50);
            $table->string('issue_type', 100);
            $table->string('assignee')->nullable();
            $table->timestamp('jira_created_at')->nullable();
            $table->timestamp('jira_updated_at')->nullable();
            $table->text('url')->nullable();
            $table->timestamps();

            $table->index(['status', 'priority']);
            $table->index(['assignee']);
            $table->index(['jira_key']);
        });
    }
};
