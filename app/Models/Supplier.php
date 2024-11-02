<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Supplier extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

     // If you want to change table name change 'reservation'
    protected $table = 'supplier';
    // Primary Key can be change here('id')
    public $primaryKey = 'supplier_id';
    protected $fillable = [
        'supplier_id',
        'company_name', 
        'contact_person', 
        'mobile_number', 
        'email',
        'address',
    ];

    public $timestamps = false;

    public function product()
    {
        return $this->hasMany(Product::class, 'supplier_id');
    }
}
