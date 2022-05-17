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
        Schema::create('sms', function (Blueprint $table) {
            $table->id();
            $table->string('message_from_number');
            $table->string('message_from');
            $table->longText('message_body');
            $table->string('message_to');
            $table->string('status');
            // $table->string('message');
            $table->string('cost')->nullable();
            $table->string('message_id');
            $table->string('currency');
            // $table->string('gateway_used');
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
        Schema::dropIfExists('sms');
    }
};
