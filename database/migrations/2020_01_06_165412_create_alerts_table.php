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
            $table->text('title');
            $table->text('metric');                    //  [note: "define o qual o campo da tabela readings a parametrizar"]
            $table->text('threshold');                 //  [note: "define o valor numérico a partir do qual é despoletado o alerta"]
            $table->text('type');                      //  [note: "define o tipo de alerta (email, sms, website, etc.)"]
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
