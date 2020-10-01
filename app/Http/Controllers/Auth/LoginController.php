<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;

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

    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider()
    {
        return Socialite::with('envato')->stateless()->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback()
    {
        $envatoUser = Socialite::driver('envato')->stateless()->user();

        $userToken = $envatoUser->token;

        // Get or Add user to our database
        // YADO: try-catch the userId call for Envato API
        $userEnvatoId = Http::withToken($userToken)->get('https://api.envato.com/whoami')['userId'];
        $user = User::where('provider_id', $userEnvatoId)->first();

        if (!$user) {
            // add user to database
            $user = User::create([
                'email' => $envatoUser->getEmail(),
                'name' => $envatoUser->getName(),
                //'provider_id' => $envatoUser->getId(),
                'provider_id' => $userEnvatoId,
            ]);
        }

        // Login the user
        Auth::login($user, true);

        return redirect($this->redirectTo);
    }
}
