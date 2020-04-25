<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Report;

class CreateReportsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('month');
            $table->integer('year');
            $table->double('time');
            $table->timestamps();
        });

        Schema::table('reports', function($table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        $this->populate();
    }

    public function populate()
    {
        $months =  array('january','february','march','april','may','june','july ','august','september','october','november','december');
        $reports = DB::table('users')->select('user_id','calc_month', 'calc_year', DB::raw('MIN(time) as time'))->rightJoin('devices', 'users.id', '=', 'devices.user_id')->rightJoin('readings_three_phase', 'devices.id', '=', 'readings_three_phase.device_id')->groupBy('user_id', 'calc_month', 'calc_year')->get();

        
        foreach ($reports as $rep) {
            $report = new Report;
            $report->year = $rep->calc_year;
            $report->month = $months[$rep->calc_month-1];
            $report->user_id = $rep->user_id;
            $report->time = $rep->time;
            $report->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reports');
    }
}
