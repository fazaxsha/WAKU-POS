<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\QueryException;

class CreateActivityLogTable extends Migration
{
    /**
     * Some managed Postgres poolers can return a connection in an aborted
     * transaction state; running this migration outside the migrator's
     * transaction avoids cascading SQLSTATE[25P02] errors.
     */
    public $withinTransaction = false;

    public function up()
    {
        $connection = config('activitylog.database_connection');
        $tableName = config('activitylog.table_name');

        // Best-effort cleanup if the connection is stuck in an aborted tx.
        try {
            DB::connection($connection)->rollBack();
        } catch (\Throwable) {
            // ignore
        }

        // If the table already exists (e.g. created previously), don't error.
        if (Schema::connection($connection)->hasTable($tableName)) {
            return;
        }

        try {
            Schema::connection($connection)->create($tableName, function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('log_name')->nullable();
                $table->text('description');
                $table->nullableMorphs('subject', 'subject');
                $table->nullableMorphs('causer', 'causer');
                $table->json('properties')->nullable();
                $table->timestamps();
                $table->index('log_name');
            });
        } catch (QueryException $e) {
            // Postgres duplicate table: SQLSTATE 42P07
            if (($e->errorInfo[0] ?? null) === '42P07') {
                return;
            }

            throw $e;
        }
    }

    public function down()
    {
        Schema::connection(config('activitylog.database_connection'))->dropIfExists(config('activitylog.table_name'));
    }
}
