<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Contact_Details;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendResetLinkEmail(Request $request)
{
    $this->validate($request, ['email' => 'required|email']);

    // Look for the Contact_Details by email
    $contact = Contact_Details::where('email', $request->email)->first();

    if (!$contact) {
        return back()->withErrors(['email' => trans('User not found.')]);
    }

    // Fetch the user associated with the contact details
    $userJoined = DB::table('user')
                    ->join('credentials', 'user.credential_id', '=', 'credentials.credential_id')
                    ->join('contact_details', 'user.contact_id', '=', 'contact_details.contact_id')
                    ->select('user.*', 'credentials.*', 'contact_details.*')
                    ->where('contact_details.email', '=', $request->email) // Filter by the email provided
                    ->first();

    if (!$userJoined) {
        return back()->withErrors(['email' => trans('User not found.')]);
    }

    // Send the reset link using the user's email through the Contact_Details model
    $sent = Contact_Details::sendResetLink($contact->email);

    if ($sent) {
        return back()->with('status', trans('A password reset link has been sent to your email.'));
    } else {
        return back()->withErrors(['email' => trans('Failed to send password reset link.')]);
    }
}





    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\View\View
     */
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }
}
