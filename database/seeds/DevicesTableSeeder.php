<?php

use Illuminate\Database\Seeder;

class DevicesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {      
        $path = 'database/seeds/devices.sql';
        DB::unprepared(file_get_contents($path));
    }
}
