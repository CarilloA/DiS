<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class Contact_Details extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

     // If you want to change table name change 'reservation'
    protected $table = 'contact_details';
    // Primary Key can be change here('id')
    public $primaryKey = 'contact_id';
    protected $fillable = [
        'mobile_number', 
        'email',
        'email_verified_at',
    ];

    public $timestamps = false; // false = to enable customization on timestamp at DB, true = automatic timestamp
    // public function User(){ // Contact_Details is a foreignkey of User
    //     return $this->hasOne(User::class, 'contact_id');
    // }

    public function user() { 
        return $this->hasOne(User::class, 'contact_id');
    }

    public static function sendResetLink($email)
    {
        // Logic to send the password reset link to the email.
        // You can customize this according to your requirements.

        $token = Str::random(60); // Generate a random token (ensure you have appropriate logic)
        
        // Here you would save the token to a database or send the link via email.
        // This is just a simple example of sending an email.
        Mail::send('emails.password_reset', ['token' => $token], function ($message) use ($email) {
            $message->to($email);
            $message->subject('Password Reset Link');
        });

        return true; // Return true on success
    }
}
