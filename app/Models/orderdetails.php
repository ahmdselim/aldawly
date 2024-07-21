<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class orderdetails extends Model
{
    use HasFactory;
    protected $fillable =
    [
        'user_id',
        'order_id',
        'product_id',
        'price',
        'sizeid',
        'quantity',
        'color'
    ];

    public function product()
    {
        return $this->belongsTo(product::class);
    }

    public function size()
    {
        return $this->belongsTo(size::class, 'sizeid');
    }
}
