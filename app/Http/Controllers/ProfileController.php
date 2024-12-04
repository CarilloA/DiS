<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\ConfirmRegistration;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        // Check if the user is logged in
        if (!Auth::check()) {
            // If the user is not logged in, redirect to login
            return redirect('/login')->withErrors('You must be logged in.');
        }

        $user = Auth::user();
        return view('profile.show_profile', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Get the authenticated user
        $userAuth = Auth::user();
        
        // Find the user by the provided ID
        $user = User::find($id);
        
        // Check if the user is logged in
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Unauthorized Page');
        }

        // Pass user data to the view
        return view('profile.update_profile', [
            'user' => $user,
            'userAuth' => $userAuth,
        ]);
    }

    public function update(Request $request, $field)
    {
        /** @var User $user */
        $user = Auth::user(); // Explicitly cast Auth::user() as an instance of the User model

        // Handle email update separately
        if ($field === 'email') {
            // Validate the email
            $request->validate([
                'email' => ['required', 'string', 'email', 'max:30', 'unique:user'],
            ]);

            // Update the email
            $user->update([
                'email' => $request['email'], // Hash the new password before storing
                'email_verified_at' => null,
                'email_verification_sent_at' => now(),
            ]);

            // Send confirmation email
            Mail::to($request['email'])->send(new ConfirmRegistration($user));

            return redirect('/login')->with('success', 'Email updated successfully! A confirmation email has been sent to your email address.');
        }

        // Handle password update separately
        if ($field === 'password') {
            // Validate the new password and confirm new password
            $request->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8|confirmed', // Ensure passwords match
            ]);

            // Check if the current password is correct
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Error: Current password is incorrect.']);
            }

            // Check if the new password is similar to current password
            if ($request->current_password === $request->new_password) {
                return back()->withErrors(['new_password' => 'Error: New password should not be similar to your current password.']);
            }

            // Update the password
            $user->update([
                'password' => Hash::make($request->new_password), // Hash the new password before storing
            ]);

            return redirect('/login')->with('success', 'Password updated successfully!');
        }

        // Validate the input based on the field being updated
        // $request->validate([
        //     $field => 'required|string|max:255',
        // ]);

        // Handle the image field separately
        if ($field === 'image_url' && $request->hasFile('image_url')) {

            $request->validate([
                'image_url' => ['image'],
            ]);

            $fileNameWithExt = $request->file('image_url')->getClientOriginalName();
            $fileName = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('image_url')->getClientOriginalExtension();
            $fileNameToStore = $fileName . '_' . time() . '.' . $extension;
            $request->file('image_url')->storeAs('public/userImage', $fileNameToStore);

            $user->update(['image_url' => $fileNameToStore]);
        } else {

            // Validate the input based on the field being updated
            $request->validate([
                $field => 'required|string|max:255',
            ]);

            // Update other fields
            $user->update([$field => $request->$field]);
        }

        return redirect()->back()->with('success', ucfirst($field) . ' updated successfully!');
    }





    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request, $id)
    // {   
    //     // Validate user credentials
    //     $request->validate([
    //         'confirm_username' => 'required|string',
    //         'confirm_password' => 'required|string',
    //     ]);

    //     // Get the authenticated user
    //     $user = auth()->user();

    //     // Check if the user credentials are correct
    //     if ($user->username !== $request->confirm_username || 
    //         !Hash::check($request->confirm_password, $user->password)) {
    //         return redirect()->back()->with('error', 'Invalid credential in the Confirm Update field.');
    //     }

    //     // Validate the incoming request data
    //     $validatedData = $request->validate([
    //         'image_url' => ['image'],
    //         'first_name' => ['required', 'string', 'max:255'],
    //         'last_name' => ['required', 'string', 'max:255'],
    //         'mobile_number' => ['required', 'digits:11', 'unique:user,mobile_number,' . $id . ',user_id'], //',user_id' is for primary key
    //         'email' => ['required', 'string', 'email', 'max:255', 'unique:user,email,' . $id . ',user_id'],
    //         'username' => ['required', 'string', 'max:255', 'unique:user,username,' . $id . ',user_id'],
    //         'new_password' => ['nullable', 'string', 'min:8', 'confirmed',
    //             function ($attribute, $value, $fail) use ($user) {
    //                 if (Hash::check($value, $user->password)) {
    //                     $fail('The new password cannot be the same as the current password.');
    //                 }
    //             },
    //         ],
    //     ]);
        

    //     // Handle file upload (if a new image is uploaded)
    //     if ($request->hasFile('image_url')) {
    //         $fileNameWithExt = $request->file('image_url')->getClientOriginalName();
    //         $fileName = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
    //         $extension = $request->file('image_url')->getClientOriginalExtension();
    //         $fileNameToStore = $fileName . '_' . time() . '.' . $extension;
    //         $request->file('image_url')->storeAs('public/userImage', $fileNameToStore);
    //     } else {
    //         // Keep the existing image
    //         $existingUser = User::find($id);
    //         $fileNameToStore = $existingUser->image_url;
    //     }

    //     // Update the data using a transaction for data integrity
    //     DB::transaction(function () use ($validatedData, $fileNameToStore, $id) {

    //          // Start the update array with common fields
    //         $updateData = [
    //             'first_name' => $validatedData['first_name'],
    //             'last_name' => $validatedData['last_name'],
    //             'image_url' => $fileNameToStore,
    //             'mobile_number' => $validatedData['mobile_number'],
    //             'email' => $validatedData['email'],
    //             'username' => $validatedData['username'],
    //         ];

    //         // If there is a new password, hash it and add it to the update array
    //         if (!empty($validatedData['new_password'])) {
    //             $updateData['password'] = Hash::make($validatedData['new_password']);
    //         }

    //         // Now perform the update
    //         User::where('user_id', $id)->update($updateData);
    //     });

    //     // Redirect back to profile with success message
    //     return redirect()->route('show_profile')->with('success', 'User profile updated successfully.');
    // }


}
