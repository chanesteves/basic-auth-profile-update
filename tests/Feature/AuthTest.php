<?php

namespace Tests\Feature;

use App\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    /**
     * A basic feature to test login.
     *
     * @return void
     */
    public function testLogin()
    {
        // START: test login failed
        $response = $this->json('POST', '/api/login', [
            'user_name' => 'chan.esteves@gmail.com',
            'password'  => 'password'
        ]);

        $response->assertStatus(200);
        $this->assertEquals('ERROR', $response['status']);
        // END: test login failed

        // START: test login success
        $response = $this->json('POST', '/api/login', [
            'user_name' => 'chanesteves',
            'password'  => 'password'
        ]);

        $response->assertStatus(200);
        $this->assertEquals('OK', $response['status']);
        // END: test login success
    }

    /**
     * A basic feature to test registration.
     *
     * @return void
     */
    public function testRegister()
    {
        $inviter = User::where('email', 'chan.esteves@gmail.com')->first();
        $latest_user_id = 0;
        $latest_user = User::latest()->first();
        if ($latest_user) {
            $latest_user_id = $latest_user->id;
        }
        $latest_active_user = User::where('user_name', 'not like', '%invited%')->latest()->first();

        if ($inviter) {
            // START: test registration failed (no invitation code)
            $response = $this->json('POST', '/api/register', [
                'name'                  => 'Christian Esteves' . $latest_user_id,
                'user_name'             => 'chanesteves' . $latest_user_id,
                'email'                 => 'chan.esteves+' . $latest_user_id . '@gmail.com',
                'password'              => 'password',
                'password_confirmation' => 'password',
                'user_role'             => 'user'
            ]);

            $response->assertStatus(422);
            // END: test registration failed (no invitation code)

            // START: test registration failed (unconfirmed password)
            $response = $this->json('POST', '/api/register', [
                'name'                  => 'Christian Esteves' . $latest_user_id,
                'user_name'             => 'chanesteves' . $latest_user_id,
                'email'                 => 'chan.esteves+' . $latest_user_id . '@gmail.com',
                'password'              => 'password',
                'password_confirmation' => 'passwordXXX',
                'user_role'             => 'user',
                'invitation_code'       => $inviter->invitation_code
            ]);

            $response->assertStatus(422);
            // END: test registration failed (unconfirmed password)

            // START: test registration failed (existing email)
            $response = $this->json('POST', '/api/register', [
                'name'                  => $latest_active_user->name,
                'user_name'             => $latest_active_user->user_name,
                'email'                 => $latest_active_user->email,
                'password'              => 'password',
                'password_confirmation' => 'password',
                'user_role'             => 'user',
                'invitation_code'       => $inviter->invitation_code
            ]);

            $response->assertStatus(200);
            $this->assertEquals('ERROR', $response['status']);
            // END: test registration failed (existing email)

            // START: test registration success
            $response = $this->json('POST', '/api/register', [
                'name'                  => 'Christian Esteves' . $latest_user_id,
                'user_name'             => 'chanesteves' . $latest_user_id,
                'email'                 => 'chan.esteves+' . $latest_user_id . '@gmail.com',
                'password'              => 'password',
                'password_confirmation' => 'password',
                'user_role'             => 'user',
                'invitation_code'       => $inviter->invitation_code
            ]);

            $response->assertStatus(200);
            $this->assertEquals('OK', $response['status']);
            // END: test registration success
        }
    }
}
