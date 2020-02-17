<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlertsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alerts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();

            $table->unsignedBigInteger('user_id'); 
            $table->text('name');
            $table->enum('unit', ['voltage', 'current', 'apparent power', 'active power',  'frequency', 'power factor']); //  [note: "define o qual o campo da tabela readings a parametrizar"]
            $table->double('threshold');                   //  [note: "define o valor numérico a partir do qual é despoletado o alerta"]
            $table->double('threshold2')->nullable();      //  [note: "define o valor numérico a partir do qual é despoletado o alerta"]
            $table->enum('type', ['email', 'website']);    //  [note: "define o tipo de alerta (email, sms, website, etc.)"]

            $table->enum('condition', ['bigger than', 'lesser than', 'between', 'equal']);
        });


        Schema::table('alerts', function($table) {
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
        Schema::dropIfExists('alerts');
    }
}
