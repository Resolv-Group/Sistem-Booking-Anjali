<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement(
            "SELECT setval(pg_get_serial_sequence('kolaborasi', 'id'), (SELECT COALESCE(MAX(id), 1) FROM kolaborasi))"
        );
    }

    public function down(): void
    {
        // Sequence correction is not reversible.
    }
};
