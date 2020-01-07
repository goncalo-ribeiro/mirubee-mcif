<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('charts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->unsignedBigInteger('view_id');
            $table->text('title');              //
            $table->text('type');               // [note: "define o tipo de chart (linhas, barras, circular)"]
            $table->text('metric');             // [note: "define o qual o campo da tabela readings a exibir"]
        });

        Schema::table('charts', function($table) {
            $table->foreign('view_id')->references('id')->on('views')->onDelete('cascade');;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('charts');
    }
}
