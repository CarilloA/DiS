<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class ReturnProduct extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

     // If you want to change table name change 'reservation'
    protected $table = 'return_product';
    // Primary Key can be change here('id')
    public $primaryKey = 'return_product_id';
    protected $fillable = [
        'return_product_id',
        'return_quantity', 
        'return_reason',
        'return_date',
        'user_id',
    ];

    public $timestamps = false; // false = to enable customization on timestamp at DB, true = automatic timestamp
    public function user(){ // Contact_Details is a foreignkey of User
        return $this->belongsTo(User::class, 'user_id');
    }
}
