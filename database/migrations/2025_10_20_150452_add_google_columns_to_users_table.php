<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('google_id')->nullable()->unique();
            $table->string('google_token')->nullable();
            $table->string('google_refresh_token')->nullable();
            $table->string('avatar')->nullable();
            // Make password nullable for Google users
            $table->string('password')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['google_id', 'google_token', 'google_refresh_token', 'avatar']);
            $table->string('password')->nullable(false)->change();
        });
    }
};