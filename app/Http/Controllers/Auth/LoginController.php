<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Components\Traits\ApiController;
use App\Role;
use Auth;
use JWTAuth;

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

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function authenticated(Request $request,$user)
    {
        // if(in_array($user->role_id,[Role::GUEST,Role::MIGRANT])){
        //     $this->guard()->logout();
        //     $request->session()->invalidate();

        //     return redirect()->back()
        //         ->withInput($request->only('email', 'remember'))
        //         ->withErrors([
        //             'email' => 'Sorry, only admin can open this page',
        //         ]);
        // }else{
            return redirect()->intended($this->redirectPath());
        // }
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            if($token = JWTAuth::attempt(['email' => $request->email, 'password' => $request->password])){ 
                session(['token' => $token]);
                $request->session()->regenerate();
                return $this->authenticated($request, $this->guard()->user());
            }else{ 
                return $this->sendError('Unauthorized.', ['error'=>'Unauthorized']);
            } 
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }
}
