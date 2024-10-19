<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
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


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {   
        // Validate user credentials
        $request->validate([
            'confirm_username' => 'required|string',
            'confirm_password' => 'required|string',
        ]);

        // Get the authenticated user
        $user = auth()->user();

        // Check if the user credentials are correct
        if ($user->username !== $request->confirm_username || 
            !Hash::check($request->confirm_password, $user->password)) {
            return redirect()->back()->with('error', 'Invalid credential in the Confirm Update field.');
        }

        // Validate the incoming request data
        $validatedData = $request->validate([
            'image_url' => ['image'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'mobile_number' => ['required', 'digits:11', 'unique:user,mobile_number,' . $id . ',user_id'], //',user_id' is for primary key
            'email' => ['required', 'string', 'email', 'max:255', 'unique:user,email,' . $id . ',user_id'],
            'username' => ['required', 'string', 'max:255', 'unique:user,username,' . $id . ',user_id'],
            'new_password' => ['nullable', 'string', 'min:8', 'confirmed'],
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

             // Start the update array with common fields
            $updateData = [
                'first_name' => $validatedData['first_name'],
                'last_name' => $validatedData['last_name'],
                'image_url' => $fileNameToStore,
                'mobile_number' => $validatedData['mobile_number'],
                'email' => $validatedData['email'],
                'username' => $validatedData['username'],
            ];

            // If there is a new password, hash it and add it to the update array
            if (!empty($validatedData['new_password'])) {
                $updateData['password'] = Hash::make($validatedData['new_password']);
            }

            // Now perform the update
            User::where('user_id', $id)->update($updateData);
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
