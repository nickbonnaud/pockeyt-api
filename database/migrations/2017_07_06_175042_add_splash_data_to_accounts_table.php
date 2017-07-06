<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSplashDataToAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->integer('businessType')->nullable()->unsigned()->after('legalBizName');
            $table->date('established')->nullable()->after('businessType');
            $table->integer('annualCCSales')->nullable()->unsigned()->after('established');
            $table->integer('phone')->nullable()->unsigned()->after('bizZip');
            $table->integer('ownership')->nullable()->unsigned()->after('accountUserLast');
            $table->string('ownerEmail')->nullable()->after('ownership');
            $table->integer('method')->nullable()->unsigned()->after('routing');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn('method');
            $table->dropColumn('ownerEmail');
            $table->dropColumn('ownership');
            $table->dropColumn('phone');
            $table->dropColumn('annualCCSales');
            $table->dropColumn('established');
            $table->dropColumn('businessType');
        });
    }
}
