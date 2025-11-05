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
        Schema::create('friends', function (Blueprint $table) {
            $table->id();

            // users 
             $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade');
             // friend id
             $table->foreignId('friend_id')
                  ->constrained('users')
                  ->onDelete('cascade');

             $table->timestamp('friends_since')->useCurrent();
            $table->timestamps();

             $table->unique(['user_id', 'friend_id']);
              $table->index(['user_id', 'friend_id']);
            $table->index('friends_since');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('friends');
    }
};