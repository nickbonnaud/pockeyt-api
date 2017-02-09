<?php

use Illuminate\Database\Migrations\Migration;

class RemoveLatLongFromProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profiles', function ($table) {
            $table->dropColumn('lat', 'lng');
        });
    }
}
