<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class offer extends Model
{
    use HasFactory;
    protected $fillable = ['start_date','percentage','promocode', 'product_id','exp_date'];

    
    public function product()
    {
        return $this->hasMany(product::class);
    }
    
}
