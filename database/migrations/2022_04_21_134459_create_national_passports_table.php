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
        Schema::create('national_passports', function (Blueprint $table) {
            $table->id();
            $table->string('request_number',50);
            $table->string('first_name', 50);
            $table->string('middle_name', 50);
            $table->string('last_name');
            $table->string('mobile');
            $table->longText('photo');
            $table->string('gender');
            $table->string('dob');
            $table->string('issued_at');
            $table->string('issued_date');
            $table->string('expiry_date');
            $table->string('reference_id');
            $table->string('date_created')->nullable();
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
        Schema::dropIfExists('national_passports');
    }
};
