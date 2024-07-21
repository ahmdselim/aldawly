<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class order extends Model
{
    use HasFactory;

    protected $fillable =
    [
        'user_id',
        'amount',
        'status',
        'payment_way'

    ];
    public function orderdetails()
    {
        return $this->hasMany(orderdetails::class);
    }
}
