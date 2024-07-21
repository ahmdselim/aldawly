<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class product extends Model
{
    use HasFactory;

    protected $fillable = ['productName', 'productDescription', 'productImage','color' ,'subcat','cat_id','has_offer','name_ar','description_ar'];
    public function cartItem()
    {
        return $this->belongsTo(cartItem::class, 'product_id');
    }
    public function size()
    {
        return $this->hasMany(size::class);
    }

    public function category()
    {
        return $this->belongsTo(category::class, 'cat_id');
    }
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }


}
