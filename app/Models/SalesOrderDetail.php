<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class SalesOrderDetail extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

     // If you want to change table name change 'reservation'
    protected $table = 'sales_order_detail';
    // Primary Key can be change here('id')
    public $primaryKey = 'sales_order_detail_id';
    protected $fillable = [
        'sales_order_detail_id',
        'sale_price_per_unit',
        'quantity',
        'product_id',
        'sales_order_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function sales_order()
    {
        return $this->belongsTo(SalesOrder::class, 'sales_order_id');
    }
}
