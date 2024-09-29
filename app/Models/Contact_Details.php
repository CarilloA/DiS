<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

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
    public function User(){ // Contact_Details is a foreignkey of User
        //  return $this->belongsTo('App\Models\User');
         return $this->hasOne(User::class, 'contact_id');
    }
}
