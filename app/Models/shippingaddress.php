<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class shippingaddress extends Model
{
    use HasFactory;

    protected $fillable = 
    [
        'user_id',
        'special_mark',
        'order_id',
        'government_id',
        'price',
        'distnation',
        'street',
        'number_of_billiding',
        'number_of_floor',
        'number_of_flat',
    ];
    
    








}
