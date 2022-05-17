<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('phone_numbers', function (Blueprint $table) {
            $table->id();
            $table->string('nin', 50);
            $table->string('firstname', 50);
            $table->string('middlename', 50);
            $table->string('surname', 50);
            $table->string('maidenname', 50)->nullable();
            $table->string('telephoneno', 13);
            $table->string('state', 50)->nullable();
            $table->string('place', 50)->nullable();
            $table->string('title', 50);
            $table->string('height')->nullable();
            $table->string('email')->nullable();
            $table->string('birthdate');
            $table->string('birthstate', 50);
            $table->string('birthcountry', 50)->nullable();
            $table->string('centralID')->nullable();
            $table->string('documentno')->nullable();
            $table->string('educationallevel', 50);
            $table->string('employmentstatus', 50);
            $table->string('maritalstatus', 50);
            $table->string('nok_firstname', 50);
            $table->string('nok_middlename', 50);
            $table->string('nok_address1', 50)->nullable();
            $table->string('nok_address2', 50)->nullable();
            $table->string('nok_lga', 50);
            $table->string('nok_state', 50);
            $table->string('nok_town', 50)->nullable();
            $table->string('nok_postalcode', 50)->nullable();
            $table->string('othername', 50)->nullable();
            $table->string('pfirstname', 50)->nullable();
            $table->longText('photo');
            $table->string('pmiddlename', 50)->nullable();
            $table->string('psurname', 50)->nullable();
            $table->string('profession', 50);
            $table->string('nspokenlang', 50)->nullable();
            $table->string('ospokenlang', 50);
            $table->string('religion', 50);
            $table->string('residence_town')->nullable();
            $table->string('residence_lga')->nullable();
            $table->string('residence_state', 50);
            $table->string('residencestatus', 50);
            $table->string('residence_AddressLine1')->nullable();
            $table->string('residence_AddressLine2')->nullable();
            $table->string('self_origin_lga');
            $table->string('self_origin_place');
            $table->string('self_origin_state');
            $table->longText('signature')->nullable();
            $table->string('nationality')->nullable();
            $table->string('gender', 50);
            $table->string('trackingId')->nullable();
            $table->string('birthlga')->nullable();
            $table->string('provider');
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
        Schema::dropIfExists('phone_numbers');
    }
};
        
