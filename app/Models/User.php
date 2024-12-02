<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    
    protected $table = 'user';
    // Primary Key can be change here('id')
    public $primaryKey = 'user_id';
    protected $fillable = [
        'user_id',
        'first_name', 
        'last_name', 
        'image_url', 
        'mobile_number',
        'email',
        'username',
        'password',
        'role',
        'default_password',
        'email_verified_at',
        'updated_at'
    ];

    protected $hidden = ['password', 'remember_token'];

    public $timestamps = true;
}
