<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RefactorBetterSupportForLogosAndHeroes extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        $all_photos = \DB::table('photos')->get();

        echo "Serialized photos follow as backup:\n" . serialize($all_photos) . "\n";

        Schema::table('photos', function(Blueprint $table) {
            $table->dropForeign('photos_profile_id_foreign');
            $table->dropColumn('profile_id');
            $table->dropColumn('logo');
        });

        Schema::table('profiles', function(Blueprint $table) {
            $table->integer('logo_photo_id')->unsigned()->nullable()->after('description');
            $table->integer('hero_photo_id')->unsigned()->nullable()->after('logo_photo_id');

            $table->foreign('logo_photo_id')->references('id')->on('photos');
            $table->foreign('hero_photo_id')->references('id')->on('photos');
        });

        DB::transaction(function() use (&$all_photos) {
            foreach(array_reverse($all_photos) as $photo) {
                DB::table('profiles')->where('id', $photo->profile_id)->update(['logo_photo_id' => $photo->id]);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        $all_profiles = \DB::table('profiles')->get();

        echo "Serialized profiles follow as backup:\n" . serialize($all_profiles) . "\n";

        Schema::table('photos', function(Blueprint $table) {
            $table->boolean('logo')->after('thumbnail_path');
            $table->integer('profile_id')->unsigned()->after('id');
        });

        Schema::table('profiles', function(Blueprint $table) {
            $table->dropForeign('profiles_hero_photo_id_foreign');
            $table->dropForeign('profiles_logo_photo_id_foreign');

            $table->dropColumn('hero_photo_id');
            $table->dropColumn('logo_photo_id');
        });

        DB::transaction(function() use (&$all_profiles) {
            foreach($all_profiles as $profile) {
                DB::table('photos')->where('id', $profile->logo_photo_id)->update(['profile_id' => $profile->id, 'logo' => 1]);
            }
        });

        Schema::table('photos', function(Blueprint $table) {
            $table->foreign('profile_id')->references('id')->on('profiles')->onDelete('cascade');
        });
    }
}
