<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductImage extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function getLinkAttribute(){
        return asset($this->path);
    }

    public function product(){
        return $this->hasOne(Product::class, 'id', 'product_id');
    }
}
