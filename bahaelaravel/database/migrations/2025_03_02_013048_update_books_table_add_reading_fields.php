<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            // Add new columns if they don't exist
            if (!Schema::hasColumn('books', 'current_page')) {
                $table->integer('current_page')->default(0);
            }
            if (!Schema::hasColumn('books', 'status')) {
                $table->enum('status', ['want_to_read', 'reading', 'completed', 'dropped'])->default('want_to_read');
            }
            if (!Schema::hasColumn('books', 'time_spent')) {
                $table->integer('time_spent')->default(0);
            }
            if (!Schema::hasColumn('books', 'total_pages')) {
                $table->integer('total_pages')->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn(['current_page', 'status', 'time_spent', 'total_pages']);
        });
    }
};