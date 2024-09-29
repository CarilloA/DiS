<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Credentials; // Credentials Model
use App\Models\User; // User Model
use Illuminate\Support\Facades\Hash;

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

        // Fetch the user based on the username from the credentials table
        $credential = Credentials::where('username', $credentials['username'])->first();

        if ($credential && Hash::check($credentials['password'], $credential->password)) {
            // Fetch the user based on the credential_id
            $user = User::where('credential_id', $credential->credential_id)->first();

            if ($user) {
                Auth::login($user);
                return redirect()->intended('/dashboard');
            }
        }

        // If login fails, redirect back with an error message
        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ]);
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
