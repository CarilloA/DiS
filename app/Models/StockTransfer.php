<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class StockTransfer extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

     // If you want to change table name change 'reservation'
    protected $table = 'stock_transfer';
    // Primary Key can be change here('id')
    public $primaryKey = 'stock_transfer_id';
    protected $fillable = [
        'stock_transfer_id',
        'transfer_quantity', 
        'transfer_date',
        'from_stockroom_id',
        'to_stockroom_id',
        'product_id',
    ];

    public $timestamps = false;

    public function from_stockroom()
    {
        return $this->belongsTo(Stockroom::class, 'from_stockroom_id');
    }

    public function to_stockroom()
    {
        return $this->belongsTo(Stockroom::class, 'to_stockroom_id');
    }

    public function product()
    {
        return $this->hasOne(Product::class, 'product_id');
    }

    public function invetory(){ // Contact_Details is a foreignkey of User
        return $this->hasOne(Inventory::class, 'stock_transfer_id');
    }
}
