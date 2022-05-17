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
        Schema::create('voters_cards', function (Blueprint $table) {
            $table->id();
            $table->string('gender');
            $table->string('vin');
            $table->string('first_name', 50)->nullable();
            $table->string('last_name', 50)->nullable();
            $table->string('date_of_birth')->nullable();
            $table->string('fullName');
            $table->string('occupation');
            $table->string('timeOfRegistration');
            $table->string('lga');
            $table->string('state');
            $table->string('registrationAreaWard');
            $table->string('pollingUnit');
            $table->string('pollingUnitCode');
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
        Schema::dropIfExists('voters_cards');
    }
};
