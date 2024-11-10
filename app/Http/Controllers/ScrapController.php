<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ScrapProduct;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Mail\ConfirmRegistration;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Exception;

class ScrapController extends Controller
{
    private function generateId($table)
    {
        do {
            $id = random_int(10000000, 99999999);
        } while (DB::table($table)->where("{$table}_id", $id)->exists()); // Ensure unique ID

        return $id;
    }

    public function disposeProduct(Request $request, $id)
    {
        // Validate login credentials
        $request->validate([
            'product_name' => 'required|string',
            'return_quantity' => 'required|number',
            'confirm_username' => 'required|string',
            'confirm_password' => 'required|string',
        ]);

        // Get the authenticated login user
        $user = auth()->user();

        // Check if the user credentials are correct
        if ($user->username !== $request->username || 
            !Hash::check($request->password, $user->password)) {
            return redirect()->route('return_product_table')->with('error', 'Invalid user credentials.');
        }

        // Generate a unique ID for the return product
        $scrapProductId = $this->generateId('scrap_product');

        DB::table('scrap_product')->insert([
            'scrap_product_id' => $scrapProductId,
            'user_id' => $user,
            'scrap_quantity' => $request['return_quantity'],
            'total_return_amount' => $request['total_return_amount'],
            'return_reason' => $request['return_reason'],
            'return_date' => now(), // Current timestamp
            'status' => 'Undisposed',
        ]);

        // Update the return product table
        DB::table('return_product')->update([
            'status' => 'Undisposed',
        ]);

        return redirect()->back()->with('success', 'Product disposed successfully.');
    }
}