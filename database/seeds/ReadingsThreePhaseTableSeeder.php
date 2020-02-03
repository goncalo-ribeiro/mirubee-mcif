<?php

use Illuminate\Database\Seeder;

class ReadingsThreePhaseTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {      
        $path = 'database/seeds/readings_three_phase.sql';
        DB::unprepared(file_get_contents($path));
    }
}
