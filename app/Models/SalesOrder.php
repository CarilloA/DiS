<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class SalesOrder extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

     // If you want to change table name change 'reservation'
    protected $table = 'sales_order';
    // Primary Key can be change here('id')
    public $primaryKey = 'sales_order_id';
    protected $fillable = [
        'sales_order_id',
        'total_amount', 
        'sales_date', 
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sales_order_detail()
    {
        return $this->hasMany(SalesOrderDetail::class, 'sales_order_detail_id');
    }
}
