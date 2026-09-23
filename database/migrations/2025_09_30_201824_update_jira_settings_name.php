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
        Schema::table('jira_settings', function (Blueprint $table) {
            $table->string('jira_url')->nullable()->after('user_id');
            $table->string('jira_email')->nullable()->after('jira_url');
            $table->string('jira_api_token')->nullable()->after('jira_email');
            $table->dropColumn(['base_url', 'username', 'api_token']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
