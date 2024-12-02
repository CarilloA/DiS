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

        // Fetch the user by username
        $credential = User::where('username', $credentials['username'])->first();

        // Check if user exists and if password is not null (i.e., default password exists)
        if ($credential) {
            // Check if the user has not updated their default password
            if ($credential->password === null && $credential->default_password !== null) {
                // Check if the user entered the default password
                if (Hash::check($credentials['password'], $credential->default_password)) {

                    // Fetch the user and check if the user has valid email verification or is an administrator
                    $user = User::where('user_id', $credential->user_id)->first();
                    // Log the user in
                    Auth::login($user);

                    // After login, redirect to password change page
                    return redirect()->route('password.change')->with('info', 'Please change your default password.');
                }
            }

            // Now, check if the password entered by the user matches the stored password
            if (Hash::check($credentials['password'], $credential->password)) {
                // Fetch the user and check if the user has valid email verification or is an administrator
                $user = User::where('user_id', $credential->user_id)->first();
                $userFKey = DB::table('user')
                    ->select('user.*')
                    ->where('user_id', '=',  $credential->user_id)
                    ->first();

                // Make sure the user exists and handle email verification or admin check
                if ($userFKey) {
                    if ($credential->role === 'Administrator' || 
                        ($userFKey->email_verified_at !== null && $userFKey->email_verified_at !== '0000-00-00 00:00:00')) {
                        // Log the user in
                        Auth::login($user);

                        return redirect()->intended('/dashboard')->with('success', 'You have successfully logged in.');
                    } else {
                        // Redirect if the email is not verified
                        return back()->with('error', 'Your email is not verified. Please check your inbox.');
                    }
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
