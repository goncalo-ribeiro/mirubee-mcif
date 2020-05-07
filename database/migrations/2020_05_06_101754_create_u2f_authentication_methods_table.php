<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateU2FAuthenticationMethodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('u2f_authentication_methods', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->binary('credentialId')->nullable();
            $table->string('credentialPublicKey');	
            $table->text('certificate');
            $table->integer('signatureCounter')->nullable();
            $table->binary('AAGUID')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('user_id');
        });

        Schema::table('u2f_authentication_methods', function($table) {
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('u2f_authentication_methods');
    }
}
