<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductImageResource extends JsonResource
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
            'is_primary' => $this->is_primary,
            'name' => $this->name,
            'link' => asset($this->path),
            'routes' => [
                'destroy' => route('api.product-images.destroy',[$this->id]),
                'update' => route('api.product-images.update',[$this->id]),
            ]
        ];
    }
}
