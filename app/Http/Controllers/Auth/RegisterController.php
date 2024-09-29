<?php 

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Models\Contact_Details;
use App\Models\Credentials;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/login'; // Use a direct string or route name

    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'mobile_number' => ['required', 'digits:11', 'unique:contact_details'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:contact_details'],
            'username' => ['required', 'string', 'max:255', 'unique:credentials'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    protected function create(array $data)
    {
        // 1. Create the Contact_Details first
        $contact_details = Contact_Details::create([
            'mobile_number' => $data['mobile_number'],
            'email' => $data['email'],
        ]);

        // 2. Handle credentials and determine role
        $adminPattern = '/admin/i';
        $role = preg_match($adminPattern, $data['username']) ? 'Administrator' : 'Inventory Manager';

        // 3. Store credentials
        $credential = Credentials::create([
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
            'role' => $role,
        ]);

        // 4. Store user
        return User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'image_url' => 'noimage.png',
            'contact_id' => $contact_details->contact_id,
            'credential_id' => $credential->credential_id,
        ]);
    }
}
