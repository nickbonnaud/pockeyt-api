<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TableProfilesAddApproved extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('profiles', function(Blueprint $table) {
            $table->boolean('approved')->default(false)->after('hero_photo_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('profiles', function(Blueprint $table) {
            $table->dropColumn('approved');
        });
    }
}
