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
        Schema::create('profile', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
              
            $table->text('bio')->nullable(); 
            $table->string('location')->nullable(); 
            $table->string('website')->nullable(); 
            $table->string('github_url')->nullable(); 
            $table->unsignedInteger('total_posts')->default(0);
            $table->unsignedInteger('followers_count')->default(0);
            $table->unsignedInteger('following_count')->default(0);
            $table->unsignedInteger('total_karma')->default(0); 
            $table->string('job_title')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile');
    }
};