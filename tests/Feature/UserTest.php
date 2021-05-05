<?php

namespace Tests\Feature;

use App\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * A basic feature to test send invites.
     *
     * @return void
     */
    public function testSendInvite()
    {
        $user = User::whereNotNull('email_verified_at')->first();
        $latest_user_id = 0;
        $latest_user = User::latest()->first();
        if ($latest_user) {
            $latest_user_id = $latest_user->id;
        }
        $latest_active_user = User::where('user_name', 'not like', '%invited%')->latest()->first();

        if ($user) {
            // START: test invite failed (no email)
            $response = $this->json('POST', '/api/users/' . $user->id . '/send-invite', [
                'email'     => '',
                'api_token' => $user->api_token
            ]);

            $response->assertStatus(422);
            // END: test invite failed (no email)

            // START: test invite failed (email already registered)
            $response = $this->json('POST', '/api/users/' . $user->id . '/send-invite', [
                'email'     => $latest_active_user->email,
                'api_token' => $user->api_token
            ]);

            $response->assertStatus(200);
            $this->assertEquals('ERROR', $response['status']);
            // END: test invite failed (email already registered)

            // START: test invite success
            $response = $this->json('POST', '/api/users/' . $user->id . '/send-invite', [
                'email'     => 'chan.esteves+' . $latest_user_id . '@gmail.com',
                'api_token' => $user->api_token
            ]);

            $response->assertStatus(200);
            $this->assertEquals('OK', $response['status']);
            // END: test invite success
        }
    }

    /**
     * A basic feature to test profile update.
     *
     * @return void
     */
    public function testUpdate()
    {
        $user = User::where('email', 'chan.esteves@gmail.com')->first();

        if ($user) {
            // START: test update failed
            $response = $this->json('PUT', '/api/users/' . $user->id . '', [
                'name'      => 'Chris Esteves',
                'user_name' => 'chanesteves',
                'api_token' => $user->api_token
            ]);

            $response->assertStatus(422);
            // END: test update failed

            // START: test update success
            $response = $this->json('PUT', '/api/users/' . $user->id . '', [
                'name'      => 'Chris Esteves',
                'user_name' => 'chanesteves',
                'user_role' => 'admin',
                'avatar'    => 'https://avatars.githubusercontent.com/u/5785059?v=4',
                'api_token' => $user->api_token
            ]);

            $response->assertStatus(200);
            $this->assertEquals('OK', $response['status']);
            // END: test update success
        }
    }
}
