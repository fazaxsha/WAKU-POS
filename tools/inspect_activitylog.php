<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$conn = config('activitylog.database_connection');
$table = config('activitylog.table_name');

echo 'conn=' . ($conn === null ? 'null' : $conn) . PHP_EOL;
echo 'table=' . $table . PHP_EOL;

$schema = Illuminate\Support\Facades\Schema::connection($conn);
echo 'hasTable=' . ($schema->hasTable($table) ? 'yes' : 'no') . PHP_EOL;

$driver = Illuminate\Support\Facades\DB::connection($conn)->getDriverName();
echo 'driver=' . $driver . PHP_EOL;

try {
    $db = Illuminate\Support\Facades\DB::connection($conn);

    if ($driver === 'pgsql') {
        $rows = $db->select("
            select schemaname, tablename
            from pg_tables
            where tablename = ?
            order by schemaname, tablename
        ", [$table]);

        echo "tables:\n";
        foreach ($rows as $r) {
            echo "- {$r->schemaname}.{$r->tablename}\n";
        }
    } elseif ($driver === 'sqlite') {
        $rows = $db->select("select name from sqlite_master where type='table' and name = ?", [$table]);
        echo "tables:\n";
        foreach ($rows as $r) {
            echo "- {$r->name}\n";
        }
    } else {
        echo "tables: (skipped listing for driver $driver)\n";
    }
} catch (Throwable $e) {
    echo 'db_error=' . $e->getMessage() . PHP_EOL;
}

