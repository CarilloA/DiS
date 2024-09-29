<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Credentials extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $table = 'credentials';  // Specify the table name
     // Primary Key can be change here('id')
    public $primaryKey = 'credential_id';
    protected $fillable = [
        'username', 
        'password',
        'role',
        
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public $timestamps = false; // false = to enable customization on timestamp at DB, true = automatic timestamp
    public function User(){ // Credentials is a foregnkey of User
         return $this->hasOne(User::class, 'credential_id');
        //  return $this->belongsTo('App\Models\User');
    }
}
