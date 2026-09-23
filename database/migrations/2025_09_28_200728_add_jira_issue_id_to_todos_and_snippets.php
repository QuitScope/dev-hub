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
        Schema::table('todos', function (Blueprint $table) {
            $table->uuid('jira_issue_id')->nullable()->after('jira_issue');
            $table->foreign('jira_issue_id')->references('id')->on('jira_issues')->onDelete('set null');
        });

        Schema::table('snippets', function (Blueprint $table) {
            $table->uuid('jira_issue_id')->nullable()->after('jira_issue');
            $table->foreign('jira_issue_id')->references('id')->on('jira_issues')->onDelete('set null');
        });
    }
};
