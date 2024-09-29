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
use App\Mail\ConfirmRegistration;
use Illuminate\Support\Facades\Mail;
 use Illuminate\Support\Facades\Log;
 use Exception;

class AccountManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() 
    {
        // Check if the user is logged in
        if (!Auth::check()) {
            // If the user is not logged in, redirect to login
            return redirect('/login')->withErrors('You must be logged in.');
        }
    
        // Fetch the logged-in user's credentials
        $user = Auth::user();
        
        // Check if the user has credentials
        if ($user && $user->credential) {
            $user_name = $user->first_name . ' ' . $user->last_name;
            $user_role = $user->credential->role; // Get the role from credentials
            
            // Check if the logged-in user is an Administrator
            if ($user_role === "Administrator") {
                // Join `user`, `credentials`, and `contact_details` to get Inventory Manager details
                $userJoined = DB::table('user')
                    ->join('credentials', 'user.credential_id', '=', 'credentials.credential_id')
                    ->join('contact_details', 'user.contact_id', '=', 'contact_details.contact_id')
                    ->select('user.*', 'credentials.*', 'contact_details.*')
                    ->where('credentials.role', '!=', 'Administrator') // Only select Inventory Managers
                    ->get();
    
                // Pass the inventory managers and user role to the view
                return view('account_management.accounts_table', [
                    'userJoined' => $userJoined,
                    'userRole' => $user_role,
                    'userName' => $user_name
                ]);
            } else {
                // If the user is not an Administrator, redirect with an error
                return redirect('/login')->withErrors('Unauthorized access.');
            }
        }
    
        // If credentials are not found, redirect with an error
        return redirect('/login')->withErrors('Unauthorized access or missing credentials.');
    }
    
    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('account_management.create_account');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
{
    // Validate the incoming request data
    $validatedData = $request->validate([
        'image_url' => ['nullable', 'image'],
        'first_name' => ['required', 'string', 'max:255'],
        'last_name' => ['required', 'string', 'max:255'],
        'mobile_number' => ['required', 'digits:11', 'unique:contact_details'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:contact_details'],
        'role' => ['required'],
        'username' => ['required', 'string', 'max:255', 'unique:credentials'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
    ]);

    // Handle File Upload
    if ($request->hasFile('image_url')) {
        // Get Filename with the extension
        $fileNameWithExt = $request->file('image_url')->getClientOriginalName();
        // Get Just Filename
        $fileName = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
        // Get just extension
        $extension = $request->file('image_url')->getClientOriginalExtension();
        // Filename to store
        $fileNameToStore = $fileName . '_' . time() . '.' . $extension;
        // Upload Image
        $request->file('image_url')->storeAs('public/userImage', $fileNameToStore);
    } else {
        $fileNameToStore = 'noimage.jpg';
    }

    // Use a transaction to ensure data integrity
    $user = DB::transaction(function () use ($validatedData, $fileNameToStore) {
        // Create the Contact_Details first
        $contact_details = Contact_Details::create([
            'mobile_number' => $validatedData['mobile_number'],
            'email' => $validatedData['email'],
        ]);

        // Determine the role based on selected value and username
        $adminPattern = '/admin/i';
        $selectedRole = $validatedData['role'];
        $role = preg_match($adminPattern, $validatedData['username']) ? 'Administrator' : $selectedRole;

        // Store credentials
        $credential = Credentials::create([
            'username' => $validatedData['username'],
            'password' => Hash::make($validatedData['password']),
            'role' => $role,
        ]);

        // Store user
        return User::create([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'image_url' => $fileNameToStore,
            'contact_id' => $contact_details->contact_id,
            'credential_id' => $credential->credential_id,
        ]);
    });

    // Send confirmation email
    Mail::to($validatedData['email'])->send(new ConfirmRegistration($user));

    // Redirect or return response after successful creation
    return redirect()->route('accounts_table')->with('success', 'User registered successfully! A confirmation email has been sent.');
}


public function confirmEmail($id)
{
    Log::info("User ID for email confirmation: {$id}"); // Debugging log

    // Find the user by ID
    $user = User::find($id);
    
    if (!$user) {
        return redirect()->route('login')->withErrors('User not found.');
    }

    // Retrieve the related contact details and update the email_verified_at field
    $contactDetails = $user->contact;
    
    if ($contactDetails) {
        $contactDetails->email_verified_at = now();
        $contactDetails->save();
    }

    // Redirect to the accounts table with a success message
    return redirect()->route('accounts_table')->with('success', 'Email has been confirmed!');
}




    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
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
