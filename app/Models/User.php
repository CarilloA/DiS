<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

     // If you want to change table name change 'reservation'
    protected $table = 'user';
    // Primary Key can be change here('id')
    public $primaryKey = 'user_id';
    protected $fillable = [
        'user_id',
        'first_name', 
        'last_name', 
        'image_url', 
        'contact_id',
        'credential_id',
    ];

    public function credential()
    {
        // return $this->belongsTo(Credentials::class, 'credential_id', 'credential_id');
        return $this->belongsTo(Credentials::class, 'credential_id');
    }

    public function contact()
    {
        return $this->belongsTo(Contact_Details::class, 'contact_id');
    }
}
