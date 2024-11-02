<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
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
        if ($user) {
            
            // Check if the logged-in user is an Administrator
            if ($user->role === "Administrator") {
                // Join `user`, `credentials`, and `contact_details` to get user details
                $userSQL = DB::table('user')
                    ->select('user.*')
                    ->where('role', '!=', 'Administrator')
                    ->get();
    
                // Pass the user details to the view
                return view('account_management.accounts_table', [
                    'userSQL' => $userSQL,
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
        'first_name' => ['required', 'string', 'max:15'],
        'last_name' => ['required', 'string', 'max:15'],
        'mobile_number' => ['required', 'digits:11', 'unique:user'],
        'email' => ['required', 'string', 'email', 'max:30', 'unique:user'],
        'role' => ['required'],
        'username' => ['required', 'string', 'max:15', 'unique:user'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
    ]);

    // Handle file upload with a default image if no file is provided
    $fileNameToStore = 'noimage.jpg'; 
    if ($request->hasFile('image_url')) {
        $fileNameToStore = $this->handleFileUpload($request->file('image_url'));
    }

    // Generate a custom user ID
    $userId = $this->generateUserId();

    // Use a transaction to ensure data integrity
    $user = DB::transaction(function () use ($validatedData, $fileNameToStore, $userId) {

        // Create the user
        $user = User::create([
            'user_id' => $userId,
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'image_url' => $fileNameToStore,
            'mobile_number' => $validatedData['mobile_number'],
            'email' => $validatedData['email'],
            'username' => $validatedData['username'],
            'password' => Hash::make($validatedData['password']),
            'role' => $validatedData['role'],
        ]);

        Log::info('New user created with ID: ' . $user->user_id); // Log the new user ID
        return $user; // Return the user object
    });

    // Send confirmation email
    Mail::to($validatedData['email'])->send(new ConfirmRegistration($user));
    Log::info('Sending confirmation email for user: ', $user->toArray()); // Log email sending

    return redirect()->route('accounts_table')->with('success', 'User registered successfully! A confirmation email has been sent.');
}

/**
 * Handle file upload and return the filename.
 *
 * @param  \Illuminate\Http\UploadedFile  $file
 * @return string
 */
private function handleFileUpload($file)
{
    $fileNameWithExt = $file->getClientOriginalName();
    $fileName = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
    $extension = $file->getClientOriginalExtension();
    $fileNameToStore = $fileName . '_' . time() . '.' . $extension;
    $file->storeAs('public/userImage', $fileNameToStore);

    return $fileNameToStore;
}

/**
 * Generate a custom user ID based on the current year and latest user ID.
 *
 * @return string
 */
private function generateUserId()
{
    $currentYear = date('Y');
    $latestUser = DB::table('user')
                    ->where('user_id', 'like', "{$currentYear}%")
                    ->orderBy('user_id', 'desc')
                    ->first();

    Log::info('Latest user found: ', (array)$latestUser); // Log latest user information

    // Initialize newIdNumber to 1
    $newIdNumber = '0000';

    if ($latestUser) {
        // Extract the last four digits and increment them
        $latestIdNumber = substr($latestUser->user_id, -4); // Get the last 4 digits of user_id
        Log::info('Latest ID Number: ' . $latestIdNumber); // Log the latest ID Number
        
        $incrementedIdNumber = (int)$latestIdNumber + 1; // Increment the ID Number
        $newIdNumber = str_pad($incrementedIdNumber, 4, '0', STR_PAD_LEFT); // Format to 4 digits
    }

    // Concatenate year with new ID number
    $generatedUserId = $currentYear . $newIdNumber; // e.g., '20240001'
    Log::info('Generated User ID: ' . $generatedUserId); // Log the generated User ID
    
    return $generatedUserId; // Return the new User ID
}





public function confirmEmail($id)
{
    Log::info('Email confirmation called for user ID: ' . $id); // Log the incoming ID

    try {
        // Find the user by ID
        $user = User::find($id);
        Log::info('User found: ', ['user' => $user]); // Log the found user details

        if (!$user) {
            return redirect()->route('login')->with('error', 'User not found.');
        }
        
        if ($user) {
            $user->email_verified_at = now();
            $user->save();
            return redirect()->route('login')->with('success', 'Email has been confirmed!');
        }

        return redirect()->route('login')->with('error', 'User contact details not found.');
    } catch (Exception $e) {
        Log::error('Email confirmation error: ' . $e->getMessage());
        return redirect()->route('login')->with('error', 'There was an error confirming your email.');
    }
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
    public function destroy(Request $request, $id)
    {
        // Validate admin credentials
        $request->validate([
            'admin_username' => 'required|string',
            'admin_password' => 'required|string',
        ]);

        // Get the authenticated admin user
        $admin = auth()->user();

        // Check if the admin credentials are correct
        if ($admin->username !== $request->admin_username || 
            !Hash::check($request->admin_password, $admin->password)) {
            // return redirect()->route('accounts_table')->with('error', 'Invalid admin credentials.');
            return back()->withErrors(['confirm_password' => 'Invalid username or password'])->withInput(); //can be use for modal errors
        }

        // Find the user to be deleted
        $user = User::find($id);

        // Check if the user exists
        if (!$user) {
            return redirect()->route('accounts_table')->with('error', 'User not found.');
        }

        // Finally, delete the user
        $user->delete();

        return redirect()->route('accounts_table')->with('success', 'User deleted successfully.');
    }

}
