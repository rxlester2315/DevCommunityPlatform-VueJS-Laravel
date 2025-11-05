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
        Schema::create('follows', function (Blueprint $table) {
            $table->id();

            // The user who is following (follower)
            $table->foreignId('follower_id')
                  ->constrained('users')
                  ->onDelete('cascade');

                // The user who is being followed (followed)
            $table->foreignId('followed_id')
                  ->constrained('users')
                  ->onDelete('cascade');  
            $table->timestamp('followed_at')->useCurrent();
            $table->unique(['follower_id', 'followed_id']);
            $table->index(['follower_id', 'followed_at']);
            $table->index(['followed_id', 'followed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('follows');
    }
};