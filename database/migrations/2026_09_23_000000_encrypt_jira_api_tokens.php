<?php

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Encrypted payloads exceed 255 chars, so widen the column
     * and encrypt tokens that were stored in plain text.
     */
    public function up(): void
    {
        Schema::table('jira_settings', function (Blueprint $table) {
            $table->text('jira_api_token')->nullable()->change();
        });

        DB::table('jira_settings')->whereNotNull('jira_api_token')->get(['id', 'jira_api_token'])
            ->each(function (object $row) {
                try {
                    Crypt::decryptString($row->jira_api_token);
                } catch (DecryptException) {
                    DB::table('jira_settings')->where('id', $row->id)
                        ->update(['jira_api_token' => Crypt::encryptString($row->jira_api_token)]);
                }
            });
    }

    public function down(): void
    {
        DB::table('jira_settings')->whereNotNull('jira_api_token')->get(['id', 'jira_api_token'])
            ->each(fn (object $row) => DB::table('jira_settings')->where('id', $row->id)
                ->update(['jira_api_token' => Crypt::decryptString($row->jira_api_token)]));

        Schema::table('jira_settings', function (Blueprint $table) {
            $table->string('jira_api_token')->nullable()->change();
        });
    }
};
