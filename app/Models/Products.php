<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Products extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

     // If you want to change table name change 'reservation'
    protected $table = 'products';
    // Primary Key can be change here('id')
    public $primaryKey = 'product_id';
    protected $fillable = [
        'product_name', 
        'description', 
        'unit_price', 
        'UoM',
        'quantity_in_stock',
        'reorder_level',
        'category_id',
        'stroom_id',
    ];

    public function category()
    {
        return $this->belongsTo(Categories::class, 'category_id');
    }
}
