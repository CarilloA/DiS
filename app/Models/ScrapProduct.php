<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class ScrapProduct extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

     // If you want to change table name change 'reservation'
    protected $table = 'scrapproduct';
    // Primary Key can be change here('id')
    public $primaryKey = 'scrap_product_id';
    protected $fillable = [
        'scrap_product_id',
        'scrap_quanity', 
        'scrap_reason',
        'scrap_date',
        'product_id',
        'stockroom_id',
    ];

    public $timestamps = false;

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function invetory(){ // Contact_Details is a foreignkey of User
        return $this->hasOne(Inventory::class, 'stockroom_id');
    }

    public function updateQuantity($amount)
    {
        $this->product_quantity += $amount;
        $this->save();
    }

}