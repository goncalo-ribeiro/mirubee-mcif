<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    private $numberOfUsers = 30;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        $faker = Faker\Factory::create('pt_PT');
      
        DB::table('users')->insert($this->staticUser());
        $this->command->info('Creating '.$this->numberOfUsers.'  users...');
        $bar = $this->command->getOutput()->createProgressBar($this->numberOfUsers);
        for ($i = 0; $i < $this->numberOfUsers; ++$i) {
            DB::table('users')->insert($this->fakeUser($faker));
            $bar->advance();
        }
        $bar->finish();
        $this->command->info('');
    }

    private function fakeUser(Faker\Generator $faker)
    {
        return [
            'name' => $faker->name,
            'email' => $faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];


        /*
        static $password;
        $createdAt = Carbon\Carbon::now()->subDays(30);
        $updatedAt = $faker->dateTimeBetween($createdAt);
        $nickname = $admin ? 'admin' : $faker->unique()->userName;
        $email = $admin ? 'admin@iot.ipl' : $faker->unique()->safeEmail;
        $pathToPic = $faker->randomElement([null, $faker->image(storage_path('app/'.$this->photoPath), 180, 180, 'people', false)]);
        $pathToPic = ($pathToPic == NULL ? 'storage/profiles/default.jpg' : 'storage/profiles/'.$pathToPic);
        return [
            'name' => $faker->name,
            'email' => $email,
            'admin' => $admin,
            'password' => $password ?: $password = bcrypt('secret'),
            'avatar' => $pathToPic,
            'active' => 1,
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
            'remember_token' => str_random(10),
        ];*/
    }

    private function staticUser (){
        return [
            'name' => 'username',
            'email' => 'username@email.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];
    }
}