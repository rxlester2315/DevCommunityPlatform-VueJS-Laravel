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
         Schema::table('users', function (Blueprint $table) {
            // Change google_token to TEXT to handle long tokens
            $table->text('google_token')->nullable()->change();
            
            // Also update google_refresh_token to be safe
            $table->text('google_refresh_token')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
         Schema::table('users', function (Blueprint $table) {
            // Revert back to string if needed (not recommended)
            $table->string('google_token', 512)->nullable()->change();
            $table->string('google_refresh_token', 512)->nullable()->change();
        });
    }
};