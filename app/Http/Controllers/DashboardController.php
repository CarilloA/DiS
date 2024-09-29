<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isNull;

class DashboardController extends Controller
{
    /** Page Access Authentication
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

     public function index()
{ 
    // Check if the user is logged in and is an Administrator
    if (Auth::check()) {
        // Fetch the logged-in user's ID
        $user = auth()->user();
        $user_id = $user->user_id;
        $user_role = $user->credential->role; // Get the role from credentials
        $user_name = $user->first_name . ' ' . $user->last_name;

        // Join `user`, `credentials`, and `contact_details` using the foreign keys
        $userJoined = DB::table('user')
            ->join('credentials', 'user.credential_id', '=', 'credentials.credential_id')
            ->join('contact_details', 'user.contact_id', '=', 'contact_details.contact_id')
            ->select('user.*', 'contact_details.*', 'credentials.*')
            ->where('user.user_id', '=', $user_id) // Correctly filter using `user_id`
            ->first(); // Get only one user (since it's based on logged-in user)

        // Check if the user is an Administrator (role is in `credentials` table)
        if ($userJoined && $userJoined->role === "Administrator" || $userJoined->role === "Inventory Manager" || $userJoined->role === "Auditor") {
            // Pass the inventory managers and user role to the view
            return view('dashboard', [
                'userJoined' => $userJoined,
                'userRole' => $user_role,
                'userName' => $user_name
            ]);
            // return view('dashboard')->with('userJoined', $userJoined);
        } else {
            return redirect('/login')->withErrors('Unauthorized access.');
        }
    }

    return redirect('/login')->withErrors('You must be logged in.');
}


public function destroy(int $id)
{
    $userAccount = User::find($id);

    if (!$userAccount) {
        return redirect('dashboard')->with('error', 'User not found');
    }

    // Check if the logged-in user is an Administrator
    if (auth()->user()->credential->role === "Administrator") {  // Assuming role is in the credentials table
        // Check if the user being deleted is not an Administrator or Customer
        if ($userAccount->credential->role != "Administrator" && $userAccount->credential->role != "Customer") {
            $userAccount->delete();
            return redirect('dashboard')->with('success', 'User account deleted successfully');
        } else {
            return redirect('dashboard')->with('error', 'You cannot delete an administrator or customer account');
        }
    } else {
        return redirect('dashboard')->with('error', 'Unauthorized access');
    }
}


}