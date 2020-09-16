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
            $table->text('v1');
            $table->text('v2');
            $table->text('v3');
            $table->text('vt');
            $table->text('i1');
            $table->text('i2');
            $table->text('i3');
            $table->text('it');
            $table->text('p1');
            $table->text('p2');
            $table->text('p3');
            $table->text('pt');
            $table->text('a1');
            $table->text('a2');
            $table->text('a3');
            $table->text('at');
            $table->text('r1');
            $table->text('r2');
            $table->text('r3');
            $table->text('rt');
            $table->text('q1');
            $table->text('q2');
            $table->text('q3');
            $table->text('qt');
            $table->text('f1');
            $table->text('f2');
            $table->text('f3');
            $table->text('ft');
            $table->text('e1');
            $table->text('e2');
            $table->text('e3');
            $table->text('et');
            $table->text('o1');
            $table->text('o2');
            $table->text('o3');
            $table->text('ot');
            $table->text('ps');
            $table->enum('calc_day_week', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']);
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
