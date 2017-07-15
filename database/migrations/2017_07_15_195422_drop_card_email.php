<?php

use Illuminate\Database\Migrations\Migration;

class DropCardEmail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function ($table) {
            $table->dropColumn('card_email');
        });
    }
}
