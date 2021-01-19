<?php

use App\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            ['name' => 'Admin','created_at' => Carbon::now(),'updated_at' => Carbon::now()],
            ['name' => 'Cashier','created_at' => Carbon::now(),'updated_at' => Carbon::now()],
        ]);

        User::updateOrCreate([
            'email' => 'admin@sitaku.dev'
        ],[
            'name' => 'Master Admin',
            'password' => bcrypt('admin'),
            'role_id' => 1
        ]);
    }
}
