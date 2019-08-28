<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

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
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
      * Redirect the user to the Google authentication page.
      *
      * @return \Illuminate\Http\Response
      */
    public function redirectToProvider()
    {
        $client = new \Google_Client();
        $client->setApplicationName('Sponsor Checker');
        $client->setScopes([
            'https://www.googleapis.com/auth/youtube.readonly',
        ]);
        $client->setAuthConfig('../../../../client_secret.json');
        $client->setAccessType('offline');
        $authUrl = $client->createAuthUrl();
        return redirect($authUrl);
    }

    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback()
    {
        try {
            $user = \Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect('/redirect');
        }
        //Socialite::shouldReceive('driver')->with($this->providerName)->andReturn($this->provider);
        $luser = \User::where(['email' => $user->email])->firstOrFail();
        //$luser = User::where(['email' => $this->user->getEmail()])->firstOrFail();
        $luser->access_token = $user->token;
        $luser->refresh_token = $user->refreshToken;
        return redirect()->to('/registermc'); // not api
    }
}
