<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'category' => new ProductCategoryResource($this->category),
            'description' => $this->description,
            'date' => Carbon::parse($this->datetime_at)->format('Y-m-d h:i A'),
            'images' => ProductImageResource::collection($this->images),
            'image' => $this->image,
            'routes' => [
                'edit' => route('products.edit',[$this->id]),
                'destroy' => route('products.destroy',[$this->id]),
                'uploadImage' => route('api.product-images.store',['product_id'=>$this->id]),
            ],
        ];
    }
}
