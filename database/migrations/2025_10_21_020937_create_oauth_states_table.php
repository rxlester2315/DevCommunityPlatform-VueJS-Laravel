<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('oauth_states', function (Blueprint $table) {
            $table->id();
            $table->string('state')->unique();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('oauth_states');
    }
};