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
        Schema::create('bvns', function (Blueprint $table) {
            $table->id();
            $table->string('firstName', 50);
            $table->string('middleName', 50);
            $table->string('lastName', 50);
            $table->string('dateOfBirth');
            $table->string('phoneNumber1', 14);
            $table->string('phoneNumber2', 14);
            $table->string('registrationDate');
            $table->string('enrollmentBank', 50);
            $table->string('enrollmentBranch', 50);
            $table->string('email');
            $table->string('gender', 50);
            $table->string('levelOfAccount', 50);
            $table->string('lgaOfOrigin', 50);
            $table->string('lgaOfResidence', 50);
            $table->string('maritalStatus');
            $table->string('nin', 50);
            $table->string('nameOnCard', 50);
            $table->string('nationality', 50);
            $table->string('residentialAddress');
            $table->string('stateOfOrigin');
            $table->string('stateOfResidence');
            $table->string('title');
            $table->string('watchListed', 50);
            $table->string('bvn', 12);
            $table->longText('base64Image');
            $table->string('provider', 15);
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
        Schema::dropIfExists('bvns');
    }
};
