<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTariffsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tariffs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->double('contracted power');
            $table->double('daily_power_price');
            $table->double('tax');
            $table->enum('tariff_type', ['simple', 'bi-hourly', 'tri-hourly']);    //  [note: "define o tipo de tarifário (simples, bi-horário, trihorário)"]
            $table->double('price_simple');                                        //  [note: "preço em €/kWh do tarifario simples"]
            $table->double('price_off-peak_hours');                                //  [note: "preço em €/kWh das horas de vazio nos tarifarios bi e tri horarios"]
            $table->double('price_outside_off-peak_hours');                        //  [note: "preço em €/kWh das horas fora de vazio no tarifario bi horario"]
            $table->double('price_peak_hours');                                    //  [note: "preço em €/kWh das horas de ponta no tarifario tri horario"]
            $table->double('price_full_time_hours');                               //  [note: "preço em €/kWh das horas cheias no tarifario tri horario"]
        });

        Schema::table('tariffs', function($table) {
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tariffs');
    }
}
