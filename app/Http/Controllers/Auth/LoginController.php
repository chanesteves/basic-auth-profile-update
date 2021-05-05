<?php

namespace App\Http\Controllers\Auth;

use Auth;
use Session;

use App\User;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    protected $loginPath = '/auth/login';
    protected $redirectAfterLogout = '/auth/login';
    protected $redirectPath = '/';

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * Handler or /login route
     *
     * @param  Illuminate\Http\Request  $request
     * @return array
     */
    public function login(Request $request)
    {
        // START: Validate login fields
        $this->validate($request, [
            'user_name' => 'required',
            'password'  => 'required'
        ]);
        // END: Validate login fields

        $user = User::where(array('user_name' => $request->get('user_name')))->first();

        if ($user && (Hash::check($request->get('password'), $user->password))) {   
            if (!$user->email_verified_at ) {
                return [
                    'status'    => 'ERROR',
                    'message'   => 'Your email address is in pending verification status.'
                ];
            }

            // re-generate API token if needed
            if (!$user->api_token) {
                $user->api_token = Str::random(60);
                $user->save();
            }

            Auth::login($user); // log user in

            return [
                'status'    => 'OK',
                'message'   => 'Successfully logged in.',
                'api_token' => $user->api_token
            ];
        }

        return [
            'status'    => 'ERROR',
            'message'   => 'Invalid username or password.'
        ];
    }
}