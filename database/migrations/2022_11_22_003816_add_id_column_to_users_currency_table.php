<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('users_currency', function (Blueprint $table) {
            // ALTER TABLE atomcms.users_currency DROP INDEX `PRIMARY`;
          $table->id();
        });
    }

    public function down()
    {
        Schema::table('user_currencies', function (Blueprint $table) {
            //
        });
    }
};