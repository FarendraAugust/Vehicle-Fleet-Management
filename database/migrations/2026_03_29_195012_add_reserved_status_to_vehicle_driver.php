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
        Schema::table('vehicles', function (Blueprint $table) {
            $table->enum('status', [
                'available',
                'in_use',
                'maintenance',
                'reserved'
            ])->default('available')->change();
        });
        Schema::table('drivers', function (Blueprint $table) {
            $table->enum('status', [
                'available',
                'assigned',
                'inactive',
                'reserved'
            ])->default('available')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->enum('status', [
                'available',
                'in_use',
                'maintenance',
            ])->default('available')->change();
        });
        Schema::table('drivers', function (Blueprint $table) {
            $table->enum('status', [
                'available',
                'assigned',
                'inactive',
            ])->default('available')->change();
        });
    }
};
