<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class size extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'size', 'Coach_price','Store_price', 'Player_price' ,'old_Coach_price',
                    'old_Store_price',
                    'old_Player_price',

    ];
    public function colors()
    {
        return $this->hasMany(Colors::class);
    }

}
