<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use App\Models\Contact_Details;
use App\Models\User;
use App\Models\Credentials; // Ensure you import the Credentials model
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/login';

    /**
     * Display the password reset form.
     */
    public function showResetForm(Request $request, $token = null)
    {
        $email = $request->email;
        return view('auth.passwords.reset')->with([
            'token' => $token,
            'email' => $email,
        ]);
    }

    /**
     * Handle resetting the user's password.
     */
    public function reset(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Find the contact details by email
        $contact = Contact_Details::where('email', $request->email)->first();

        if (!$contact) {
            return back()->withErrors(['email' => trans('User not found.')]);
        }

        // Find the user associated with the contact details
        $user = User::where('contact_id', $contact->contact_id)->first();

        if (!$user) {
            return back()->withErrors(['email' => trans('User not found.')]);
        }

        // Now, retrieve the credentials associated with the user
        $credentials = Credentials::where('credential_id', $user->credential_id)->first();

        if (!$credentials) {
            return back()->withErrors(['email' => trans('Credentials not found.')]);
        }

        // Update the password in the credentials table
        $credentials->password = Hash::make($request->password);
        $credentials->save();

        // Redirect with success message
        return redirect($this->redirectTo)->with('success', trans('Password has been reset.'));
    }
}
