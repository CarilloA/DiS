<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ScrapProduct;
use App\Models\ReturnProduct;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
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

    public function disposeProduct(Request $request)
    {
        // Validate login credentials
        $request->validate([
            // 'product_name' => 'required|string',
            'return_product_id' => 'required|numeric',
            'return_quantity' => 'required|numeric',
            'confirm_username' => 'required|string',
            'confirm_password' => 'required|string',
        ]);

        // Get the authenticated login user
        $user = Auth::user();

        if ($user->username !== $request->confirm_username || !Hash::check($request->confirm_password, $user->password)) {
            return back()->withErrors(['confirm_password' => 'Invalid user credentials'])->withInput();
        }

        // Use DB transaction to ensure data integrity
        DB::transaction(function () use ($request, $user) {

            // Generate a unique ID for the return product
            $scrapProductId = $this->generateId('scrap_product');

            DB::table('scrap_product')->insert([
                'scrap_product_id' => $scrapProductId,
                'user_id' => $user->user_id,
                'scrap_quantity' => $request['return_quantity'],
                'scrap_date' => now(), // Current timestamp
            ]);

             // Find the ReturnProduct entry
             $returnProduct = ReturnProduct::where('return_product_id', $request->return_product_id)->firstOrFail();

             // Update ReturnProduct with the `scrap_product_id`
             $returnProduct->update([
                 'scrap_product_id' => $scrapProductId,
             ]);
        });

        return redirect()->back()->with('success', 'Product disposed successfully.');
    }
}