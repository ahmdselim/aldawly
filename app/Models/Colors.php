<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Colors extends Model
{
    use HasFactory;
protected $fillable=[
    'size_id',
    'color_name',
    'out_of_stock'
];
    public function sizes()
    {
        return $this->belongsToMany(size::class, 'colors', 'color_name', 'size_id');
    }
}
