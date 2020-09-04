<?php

use App\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
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
            ['name' => 'Guest','created_at' => Carbon::now(),'updated_at' => Carbon::now()],
        ]);

        User::updateOrCreate([
            'email' => 'admin@gmail.com'
        ],[
            'name' => 'Admin 1',
            'password' => bcrypt('admin'),
            'role_id' => 1
        ]);
    }
}
