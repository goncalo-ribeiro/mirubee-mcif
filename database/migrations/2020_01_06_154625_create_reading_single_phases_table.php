<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReadingSinglePhasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('readings_single_phases', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();

            $table->ipAddress('ip');
            $table->double('time');
            $table->boolean('calc_time');
            $table->double('vt');
            $table->double('it');
            $table->double('pt');
            $table->double('at');
            $table->double('qt');
            $table->double('ft');
            $table->double('et');
            $table->double('ot');
            $table->double('ps');
            $table->enum('calc_day_week', ['monday', 'tuesday'. 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']);
            $table->integer('calc_day_month');
            $table->integer('calc_year');
            $table->integer('calc_month');
            $table->integer('calc_hour');
            $table->integer('calc_minute');
        });

        Schema::table('readings_single_phases', function($table) {
            $table->unsignedBigInteger('device_id');
            $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('readings_single_phases');
    }
}
