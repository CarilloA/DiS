<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // User Model
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    /**
     * Handle login requests to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
{
    // Validate the form data
    $credentials = $request->validate([
        'username' => 'required|string',
        'password' => 'required|string',
    ]);

    // Fetch the credential based on the username
    $credential = User::where('username', $credentials['username'])->first();
    
    if ($credential && Hash::check($credentials['password'], $credential->password)) {
        // Fetch the user based on the credential_id
        $user = User::where('user_id', $credential->user_id)->first();
        $userFKey = DB::table('user')
                    ->select('user.*')
                    ->where('user_id', '=',  $credential->user_id)
                    ->first();

                    if ($userFKey) { // Check if the userFKey is not null before accessing its properties
                        // Check if the user is an Administrator or has a verified email
                        if ($credential->role === 'Administrator' || 
                            ($userFKey->email_verified_at !== null && $userFKey->email_verified_at !== '0000-00-00 00:00:00')) {
                            // Log in the user
                            Auth::login($user);
                            return redirect()->intended('/dashboard')->with('success', 'You have successfully logged in.');
                        } else {
                // Redirect with an error message for unverified email
                return back()->with('error', 'Your email is not verified. Please check your inbox.');
            }
        }
    }

    // If login fails, redirect back with an error message
    return back()->with('error', 'The provided credentials do not match our records.');
}


    public function showLoginForm()
    {
        return view('auth.login'); // Adjust the view path as necessary
    }

    public function logout(Request $request)
    {
        Auth::logout(); // Log out the user

        $request->session()->invalidate(); // Invalidate the session
        $request->session()->regenerateToken(); // Regenerate CSRF token

        return redirect('/login'); // Redirect to login or any desired route
    }
}
