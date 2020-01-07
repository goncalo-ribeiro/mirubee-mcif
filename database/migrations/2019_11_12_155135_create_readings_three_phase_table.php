<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReadingsThreePhaseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('readings_three_phase', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->ipAddress('ip');
            $table->double('time');
            $table->boolean('calc_time');
            $table->double('v1');
            $table->double('v2');
            $table->double('v3');
            $table->double('vt');
            $table->double('i1');
            $table->double('i2');
            $table->double('i3');
            $table->double('it');
            $table->double('p1');
            $table->double('p2');
            $table->double('p3');
            $table->double('pt');
            $table->double('a1');
            $table->double('a2');
            $table->double('a3');
            $table->double('at');
            $table->double('r1');
            $table->double('r2');
            $table->double('r3');
            $table->double('rt');
            $table->double('q1');
            $table->double('q2');
            $table->double('q3');
            $table->double('qt');
            $table->double('f1');
            $table->double('f2');
            $table->double('f3');
            $table->double('ft');
            $table->double('e1');
            $table->double('e2');
            $table->double('e3');
            $table->double('et');
            $table->double('o1');
            $table->double('o2');
            $table->double('o3');
            $table->double('ot');
            $table->double('ps');
            $table->enum('calc_day_week', ['monday', 'tuesday'. 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']);
            $table->integer('calc_day_month');
            $table->integer('calc_year');
            $table->integer('calc_month');
            $table->integer('calc_hour');
            $table->integer('calc_minute');

        });

        Schema::table('readings_three_phase', function($table) {
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
        Schema::dropIfExists('readings_three_phase');
    }
}
