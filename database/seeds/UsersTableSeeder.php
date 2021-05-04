<?php

use App\User;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
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
        // START: Create initial admin user

        // encrypt default password
        $password = Hash::make("password");

        // generate invitation code
        $invitation_code = Str::random(60);

        // generate API token
        $api_token = Str::random(60);

        $data = [
            "name"              => "Christian Esteves",
            "user_name"         => "chanesteves",
            "email"             => "chan.esteves@gmail.com",
            "password"          => $password,
            "user_role"         => "admin",
            "email_verified_at" => date("Y-m-d H:i:s"),
            "invitation_code"   => $invitation_code,
            "api_token"         => $api_token
        ];

        User::create($data, ["email" => "chan.esteves@gmail.com"]);

        // END: Create initial admin user
    }
}
