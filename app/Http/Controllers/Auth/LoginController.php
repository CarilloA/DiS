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
        'username' => 'nullable|string',
        'email' => 'nullable|string',
        'password' => 'required|string',
    ]);

    // Fetch the user by email or username
    $credential = User::where('email', $credentials['email'])
        ->orWhere('username', $credentials['username'])
        ->first();

    // Check if user exists
    if ($credential) {
        if (Hash::check($credentials['password'], $credential->password)) {

            // Fetch the user and check if the user has valid email verification or is an administrator
            $user = User::where('user_id', $credential->user_id)->first();
            $userFKey = DB::table('user')
                ->select('user.*')
                ->where('user_id', '=',  $credential->user_id)
                ->first();

            if ($userFKey) {
                // Check if the email is verified
                if ($credential->email_verified_at || $credential->user_roles === 'Administrator') {
                    if ($credential->user_roles) {
                        // Log the user in
                        Auth::login($user);
                        
                        // Get all roles associated with the user
                        $roles = explode(', ', $user->user_roles); // Converts the comma-separated string back into an array

                        // If the user has only one role, log them in directly
                        if (count($roles) === 1) {

                            // Perform the role update within a transaction
                            DB::transaction(function () use ($user, $roles) {
                                // Prepare the update data
                                $updateData = [
                                    'role' => $roles[0], //update the role fieild if the user's user_role is only 1
                                ];
                        
                                // Update the user's role
                                User::where('user_id', $user->user_id)->update($updateData);
                            });

                            Auth::login($user);
                            return redirect('/dashboard')->with('success', "Successfully logged in as {$roles[0]}.");
                        }
                        
                        // Pass roles to the view for selection
                        return view('auth.login', [
                            'roles' => $roles,
                            'user' => $credential,
                        ]);
                    } else {
                        return back()->with('error', 'Your account is not verified yet.');
                    }
                } else {
                    return back()->with('error', 'Your email is not verified. Please check your inbox.');
                }
            } else {
                return back()->with('error', 'The provided credentials do not match our records.');
            }
        } else {
            return back()->with('error', 'Incorrect Entered Password. Please login again.');
        }
    } else {
        // If login fails, redirect back with an error message
        return back()->with('error', 'Incorrect Entered Email. Please login again.');
    }
}




    public function showLoginForm()
    {
        return view('auth.login'); // Adjust the view path as necessary
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        // Perform the role update within a transaction
        DB::transaction(function () use ($user) {
            // Prepare the update data
            $updateData = [
                'role' => null,
            ];
    
            // Update the user's role
            User::where('user_id', $user->user_id)->update($updateData);
        });

        Auth::logout(); // Log out the user

        $request->session()->invalidate(); // Invalidate the session
        $request->session()->regenerateToken(); // Regenerate CSRF token

        return redirect('/login'); // Redirect to login or any desired route
    }

    public function selectRole(Request $request)
{
    $request->validate([
        'role' => 'required|string',
        'password' => 'required|string',
    ]);

    $user = Auth::user();

    if ($user) {
        // Check if password matches
        if (Hash::check($request->password, $user->password)) {
            // Verify the selected role is part of the user's roles
            $roles = explode(', ', $user->user_roles); // Convert stored roles into an array
            if (in_array($request->role, $roles)) {

                // Perform the role update within a transaction
                DB::transaction(function () use ($request, $user) {
                    // Prepare the update data
                    $updateData = [
                        'role' => $request->role,
                    ];
            
                    // Update the user's role
                    User::where('user_id', $user->user_id)->update($updateData);
                });

                //session(['selected_role' => $request->role]); // Store the role in the session
                return redirect('/dashboard')->with('success', "Successfully Logged in.");
            } else {
                return back()->with('error', 'Invalid role selection.');
            }
        } else {
            return back()->with('error', 'Incorrect password. Please try again.');
        }
    } else {
        return redirect()->route('login')->with('error', 'Invalid session. Please login again.');
    }
}

}
