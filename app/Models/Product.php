<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function category(){
        return $this->hasOne(ProductCategory::class, 'id','category_id');
    }

    public function getCategoryNameAttribute(){
        return $this->category->name;
    }

    public function images(){
        return $this->hasMany(ProductImage::class, 'product_id', 'id');
    }

    public function latest_image(){
        return $this->hasOne(ProductImage::class, 'product_id', 'id')->latest();
    }

    public function primary_image(){
        return $this->hasOne(ProductImage::class, 'product_id', 'id')->where('is_primary',1);
    }

    public function getImageAttribute(){
        if($this->primary_image)
            return $this->primary_image->link;
        else return 'https://placehold.co/400';
    }
}
