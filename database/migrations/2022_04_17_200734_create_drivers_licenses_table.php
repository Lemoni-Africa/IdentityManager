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
        Schema::create('drivers_licenses', function (Blueprint $table) {
            $table->id();
            $table->string('gender', 50);
            $table->string('licenseNo');
            $table->string('firstName', 50);
            $table->string('lastName', 50);
            $table->string('middleName', 50);
            $table->string('issuedDate');
            $table->string('expiryDate');
            $table->string('stateOfIssue');
            $table->string('birthDate');
            $table->longText('photo');
            $table->string('uuid')->nullable();
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
        Schema::dropIfExists('drivers_licenses');
    }
};
