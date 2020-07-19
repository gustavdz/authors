<?php

use Illuminate\Database\Seeder;
use App\User;
use Illuminate\Support\Facades\Hash;

class usersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = array(
            [
                'display_name'=>'Gustavo Decker',
                'username'=>'gustavdz',
                'email'=> 'gustavdz@gmail.com',
                'password'=>Hash::make('Gustavo123'),
                'email_verified_at'=>date(now())
            ],
        );
        foreach($users as $user){
            User::create($user);
        }
    }
}
