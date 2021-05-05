<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Invitation;

use App\Notifications\UserCreated;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name'      => 'required',
            'user_name' => 'required|min:4|max:20',
            'email'     => 'required|email|max:255|unique:users',
            'password'  => 'required',
            'user_role' => 'required'
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::updateOrCreate([
            'email' => $data['email']
        ], [
            'name'                      => $data['name'],
            'user_name'                 => $data['user_name'],
            'password'                  => Hash::make($data['password']),
            'user_role'                 => $data['user_role'],
            'invitation_code'           => str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT),
            'api_token'                 => Str::random(60),
            'email_verification_code'   => str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT)
        ]);
    }

    /**
     * Handler or /register route
     *
     * @param  Illuminate\Http\Request  $request
     * @return array
     */
    public function register(Request $request){
        // START: Validate registration fields
        $this->validate($request, [
            'name'              => 'required',
            'user_name'         => 'required|min:4|max:20',
            'email'             => 'required|email|max:255',
            'password'          => 'required|confirmed',
            'user_role'         => 'required',
            'invitation_code'   => 'required'
        ]);

        // check if username or email already exists
        $user = User::where('user_name', $request->user_name)->orWhere('email', $request->email)->first();
        if($user && strpos($user->user_name, 'invited') === false) {
            if ($user->user_name == $request->user_name) {
                return [
                    'status'    => 'ERROR',
                    'message'   => 'Username '. $request->user_name . ' already exists.'
                ];
            }
            if ($user->email == $request->email) {
                return [
                    'status'    => 'ERROR',
                    'message'   => 'Email '. $request->email . ' already exists.'
                ];
            }
        }

        // validate invitation code
        $inviter = User::where('invitation_code', $request->invitation_code)->first();
        if (!$inviter) {
            return [
                'status'    => 'ERROR',
                'message'   => 'Invalid invitation code.'
            ];
        }

        // END: Validate registration fields

        $data = $request->all();
        $user = $this->create($data);

        // mark invitation as accepted
        $invitation = Invitation::where(['inviter_id' => $inviter->id, 'invitee_email' => $request->email])->first();
        if ($invitation) {
            $invitation->accepted_at = date('Y-m-d H:i:s');
            $invitation->save();
        }

        // send email verification code
        $user->notify(new UserCreated($invitation));

        return [
            'status'    => 'OK',
            'message'   => 'Successfully registered.',
            'user'      => $user
        ];
    }

    /**
     * Handler or /verify route
     *
     * @param  Illuminate\Http\Request  $request
     * @return array
     */
    public function verify(Request $request){
        // START: Validate verification fields
        $this->validate($request, [
            'user_name'                 => 'required',
            'email_verification_code'   => 'required'
        ]);

        $user = User::where(array('user_name' => $request->user_name))->first();
        if(!$user) {
            return [
                'status'    => 'ERROR',
                'message'   => 'User not found.'
            ];
        }
        if ($request->email_verification_code != $user->email_verification_code) {
            return [
                'status'    => 'ERROR',
                'message'   => 'Invalid verification code.'
            ];
        }
        // END: Validate verification fields

        return [
            'status'    => 'OK',
            'message'   => 'Successfully verified.',
            'user'      => $user
        ];
    }
}