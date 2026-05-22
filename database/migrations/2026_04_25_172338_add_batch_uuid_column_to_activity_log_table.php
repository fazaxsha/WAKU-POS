<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBatchUuidColumnToActivityLogTable extends Migration
{
    public $withinTransaction = false;

    public function up()
    {
        $connection = config('activitylog.database_connection');
        $tableName = config('activitylog.table_name');

        try {
            DB::connection($connection)->rollBack();
        } catch (\Throwable) {
            // ignore
        }

        if (!Schema::connection($connection)->hasTable($tableName)) {
            return;
        }

        if (Schema::connection($connection)->hasColumn($tableName, 'batch_uuid')) {
            return;
        }

        Schema::connection($connection)->table($tableName, function (Blueprint $table) {
            $table->uuid('batch_uuid')->nullable()->after('properties');
        });
    }

    public function down()
    {
        $connection = config('activitylog.database_connection');
        $tableName = config('activitylog.table_name');

        try {
            DB::connection($connection)->rollBack();
        } catch (\Throwable) {
            // ignore
        }

        if (!Schema::connection($connection)->hasTable($tableName)) {
            return;
        }

        if (!Schema::connection($connection)->hasColumn($tableName, 'batch_uuid')) {
            return;
        }

        Schema::connection($connection)->table($tableName, function (Blueprint $table) {
            $table->dropColumn('batch_uuid');
        });
    }
}
