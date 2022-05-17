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
        Schema::create('nins', function (Blueprint $table) {
            $table->id();
            $table->string('employmentstatus');
            $table->string('gender');
            $table->integer('heigth')->nullable();
            $table->string('height')->nullable();
            $table->string('maritalstatus')->nullable();
            $table->string('title');
            $table->string('birthcountry');
            $table->string('birthdate');
            $table->string('birthlga')->nullable();
            $table->string('birthstate');
            $table->string('educationallevel');
            $table->string('email');
            $table->string('firstname', 50);
            $table->string('surname', 50);
            $table->string('nin');
            $table->string('nok_address1');
            $table->string('nok_address2');
            $table->string('nok_firstname', 50);
            $table->string('nok_lga');
            $table->string('nok_middlename', 50);
            $table->string('nok_postalcode', 50);
            $table->string('nok_state', 50);
            $table->string('nok_surname', 50);
            $table->string('nok_town');
            $table->string('spoken_language', 50)->nullable();
            $table->string('ospokenlang', 50);
            $table->string('pfirstname', 50);
            $table->longText('photo');
            $table->string('middlename', 50);
            $table->string('pmiddlename', 50);
            $table->string('profession');
            $table->string('psurname', 50);
            $table->string('religion');
            $table->string('residence_address')->nullable();
            $table->string('residence_town');
            $table->string('residence_lga');
            $table->string('residence_state', 50);
            $table->string('residencestatus');
            $table->string('self_origin_lga');
            $table->string('self_origin_place');
            $table->string('self_origin_state');
            $table->longText('signature');
            $table->string('telephoneno');
            $table->string('trackingId');
            $table->string('maidenname')->nullable();
            $table->string('state')->nullable();
            $table->string('place')->nullable();
            $table->string('centralID')->nullable();
            $table->string('documentno')->nullable();
            $table->string('othername')->nullable();
            $table->string('nspokenlang')->nullable();
            $table->string('residence_AddressLine1')->nullable();
            $table->string('residence_AddressLine2')->nullable();
            $table->string('nationality')->nullable();
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
        Schema::dropIfExists('nins');
    }
};
