<?php

namespace App\Http\Controllers;

use App\User;
use App\Invitation;

use App\Notifications\InvitationCreated;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
	 * Update the specified resource in storage.
	 *
     * @param  Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return array
	 */
	public function update(Request $request, $id)
	{
		// START: Validate profile fields
        $this->validate($request, [
            'name'              => 'required',
            'user_name'         => 'required|min:4|max:20',
            'user_role'         => 'required'
        ]);

        $user = User::find($id);
        if(!$user) {
            return [
                'status'    => 'ERROR',
                'message'   => 'User not found.'
            ];
        }
        // END: Validate profile fields

        $user->name         = $request->name;
        $user->user_name    = $request->user_name;
        $user->user_role    = $request->user_role;

        if ($request->avatar) {
            $user->avatar = $request->avatar;
        }

        $user->save();

        return [
            'status'    => 'OK',
            'message'   => 'Successfully updated profile.',
            'user'      => $user
        ];
	}

    /**
	 * Handler or /users/{user}/send-invite route.
	 *
     * @param  Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return array
	 */
    public function sendInvite (Request $request, $id) {
        // START: Validate invitation fields
        $this->validate($request, [
            'email'              => 'required'
        ]);

        $invitee = User::where('email', $request->email)->first();
        if($invitee && strpos($invitee->user_name, 'invited') === false) {
            return [
                'status'    => 'ERROR',
                'message'   => 'Email '. $request->email . ' is already registered.'
            ];
        }

        // END: Validate invitation fields

        // START: Authenticate/Validate invitation sender
        $user = User::find($id);

        if (!$user) {
            return [
                'status'    => 'ERROR',
                'message'   => 'User not found.'
            ];
        }
        // END: Authenticate/Validate invitation sender

        // generate invitation code if needed
        if (!$user->invitation_code) {
            $user->invitation_code = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $user->save();
        }
        
        $invitation_code = $user->invitation_code;
        $recipient_email = $request->email;

        // START: Store the invitation data to database
        $invitation_data = [
            "email_sent_at"         => date("Y-m-d H:i:s"),
            "notification_sent_at"  => date("Y-m-d H:i:s")
        ];

        $invitation = Invitation::updateOrCreate([
            "inviter_id"    => $user->id,
            "invitee_email" => $recipient_email
        ], $invitation_data);

        $latest_user_id = 1;
        $latest_user = User::latest()->first();
        if ($latest_user) {
            $latest_user_id = $latest_user->id + 1;
        }

        $user_data = [
            "name"              => "Invited Via Email",
            "user_name"         => "invited_" . $latest_user_id,
            "email"             => $recipient_email,
            "password"          => Hash::make("password")
        ];

        $invitee = User::updateOrCreate([
            "email" => $recipient_email
        ], $user_data);
        // END: Store the invitation data to database

        // send the invitation notification
        $invitee->notify(new InvitationCreated($invitation));

        return [
            'status'            => 'OK',
            'message'           => 'Successfully sent email.',
            'email'             => $recipient_email,
            'invitation_code'   => $invitation_code,
        ];
    }
}
