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


        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('post_id');
            $table->unsignedBigInteger('user_id');
            
            $table->text('content');
            $table->string('image')->nullable();
            $table->timestamps();
        // the meaning of this the post_id  is referencing in the id column of the post table
            $table->foreign('post_id')
                  ->references('id')
                  ->on('post')
                  // if the author delete the post all comments related to that post will be deleted too
                  // and also if the user is deleted all his comments will be deleted too
                  ->onDelete('cascade');
                  
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->index(['post_id', 'created_at']);
        });
       
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comment');
    }
};