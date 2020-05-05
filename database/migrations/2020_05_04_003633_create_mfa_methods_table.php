<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\MfaMethod;

class CreateMfaMethodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mfa_methods', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();

            $table->string('google_code')->nullable();
                
            $table->string('u2f_code')->nullable();
                
            $table->string('sqrl_code')->nullable();
                
            $table->string('email_code')->nullable();
            $table->boolean('email_activated')->default(false);
            $table->string('email_temp_code')->nullable();
            $table->timestamp('email_time')->nullable()->default(null);
    
            $table->string('remember_mfa_token')->nullable();

            $table->unsignedBigInteger('user_id');

        });
        
        Schema::table('mfa_methods', function($table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        $this->populate();
    }

    public function populate()
    {
        $users =  App\User::all();
            
        foreach ($users as $user) {
            $mfaMethod = new MfaMethod;
            $mfaMethod->user_id = $user->id;
            $mfaMethod->save();
        }
    }   

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mfa_methods');
    }
}
