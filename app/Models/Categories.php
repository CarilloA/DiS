<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Categories extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

     // If you want to change table name change 'reservation'
    protected $table = 'categories';
    // Primary Key can be change here('id')
    public $primaryKey = 'category_id';
    protected $fillable = [
        'category_name', 
    ];

    public $timestamps = false; // false = to enable customization on timestamp at DB, true = automatic timestamp
    public function product(){ // Contact_Details is a foreignkey of User
        return $this->hasOne(Products::class, 'category_id');
    }
}
