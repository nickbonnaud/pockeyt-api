<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('profile_id')->unsigned();
            $table->foreign('profile_id')->references('id')->on('profiles')->onDelete('cascade');
            $table->string('accountUserFirst');
            $table->string('accountUserLast');
            $table->string('accountEmail');
            $table->date('dateOfBirth');
            $table->integer('last4');
            $table->string('indivStreetAdress');
            $table->string('indivCity');
            $table->string('indivState');
            $table->string('indivZip');
            $table->string('legalBizName');
            $table->integer('bizTaxId');
            $table->string('bizStreetAdress')->nullable();
            $table->string('bizCity')->nullable();
            $table->string('bizState')->nullable();
            $table->string('bizZip')->nullable();
            $table->integer('accountNumber4');
            $table->integer('routingNumber4');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('accounts');
    }
}
