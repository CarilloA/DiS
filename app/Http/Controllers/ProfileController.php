<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Contact_Details;
use App\Models\Credentials;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

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
    $user = User::with(['contact', 'credential'])->find($id);
    
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


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
{
    // Validate the incoming request data
    $validatedData = $request->validate([
        'image_url' => ['image'],
        'first_name' => ['required', 'string', 'max:255'],
        'last_name' => ['required', 'string', 'max:255'],
        'mobile_number' => ['required', 'digits:11', 'unique:contact_details,mobile_number,'.auth()->user()->contact->contact_id.',contact_id'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:contact_details,email,'.auth()->user()->contact->contact_id.',contact_id'],
        'username' => ['required', 'string', 'max:255', 'unique:credentials,username,'.auth()->user()->credential->credential_id.',credential_id'],
    ]);

    // Handle file upload (if a new image is uploaded)
    if ($request->hasFile('image_url')) {
        $fileNameWithExt = $request->file('image_url')->getClientOriginalName();
        $fileName = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
        $extension = $request->file('image_url')->getClientOriginalExtension();
        $fileNameToStore = $fileName . '_' . time() . '.' . $extension;
        $request->file('image_url')->storeAs('public/userImage', $fileNameToStore);
    } else {
        // Keep the existing image
        $existingUser = User::find($id);
        $fileNameToStore = $existingUser->image_url;
    }

    // Update the data using a transaction for data integrity
    DB::transaction(function () use ($validatedData, $fileNameToStore, $id) {
        // Update contact details
        Contact_Details::where('contact_id', auth()->user()->contact->contact_id)->update([
            'mobile_number' => $validatedData['mobile_number'],
            'email' => $validatedData['email'],
        ]);

        // Update credentials
        Credentials::where('credential_id', auth()->user()->credential->credential_id)->update([
            'username' => $validatedData['username'],
        ]);

        // Update user
        User::where('user_id', $id)->update([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'image_url' => $fileNameToStore,
        ]);
    });

    // Redirect back to profile with success message
    return redirect()->route('show_profile')->with('success', 'User updated successfully.');
}



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
