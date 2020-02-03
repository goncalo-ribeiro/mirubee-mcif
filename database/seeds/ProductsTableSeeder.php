<?php

use Illuminate\Database\Seeder;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {      
        DB::table('products')->insert($this->mirubeeSinglePhase());
        DB::table('products')->insert($this->mirubeeThreePhase());
    }

    private function mirubeeSinglePhase (){
        return [
            'type' => 'mirubee single phase',
            'desc' => 'A single phase energy monitor developed by mirubee',
            'image' => 'images/products/mirubee.jpg',
        ];
    }

    private function mirubeeThreePhase(){
        return [
            'type' => 'mirubee three phase',
            'desc' => 'A three phase energy monitor developed by mirubee',
            'image' => 'images/products/mirubee.jpg',
        ];
    }
}
