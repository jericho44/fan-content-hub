<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE events ADD COLUMN search_vector tsvector");
        DB::statement("UPDATE events SET search_vector = to_tsvector('english', coalesce(name, '') || ' ' || coalesce(description, ''))");
        DB::statement("CREATE INDEX events_search_vector_idx ON events USING GIN(search_vector)");

        DB::statement("ALTER TABLE contents ADD COLUMN search_vector tsvector");
        DB::statement("UPDATE contents SET search_vector = to_tsvector('english', coalesce(title, ''))");
        DB::statement("CREATE INDEX contents_search_vector_idx ON contents USING GIN(search_vector)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP INDEX IF EXISTS events_search_vector_idx");
        DB::statement("ALTER TABLE events DROP COLUMN IF EXISTS search_vector");

        DB::statement("DROP INDEX IF EXISTS contents_search_vector_idx");
        DB::statement("ALTER TABLE contents DROP COLUMN IF EXISTS search_vector");
    }
};
