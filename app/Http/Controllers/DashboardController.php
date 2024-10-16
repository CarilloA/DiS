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

        // Join `user`, `credentials`, and `contact_details` using the foreign keys
        $userSQL = DB::table('user')
            ->select('user.*')
            ->where('user_id', '=', $user_id) // Correctly filter using `user_id`
            ->first(); // Get only one user (since it's based on logged-in user)

        // Check if the user is an Administrator (role is in `credentials` table)
        if ($userSQL && $userSQL->role === "Administrator" || $userSQL->role === "Inventory Manager" || $userSQL->role === "Auditor") {
            // Pass the inventory managers and user role to the view
            return view('dashboard', [
                'userSQL' => $userSQL,
            ]);
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
    if (auth()->user()->role === "Administrator") {  // Assuming role is in the credentials table
        // Check if the user being deleted is not an Administrator or Customer
        if ($userAccount->role != "Administrator") {
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