<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // dd($this->user->vendor);
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'price' => $this->getPriceForFirstOptions(),
            'description' => $this->description,
            'quantity' => $this->quantity,
            'image' => $this->getFirstImageUrl(), // using Spatie Media Library

            'user' => [
                'id' => $this->user->id ?? null,
                'name' => $this->user->name ?? null,
                'store_name' => $this->user->vendor->store_name ?? null,
            ],
            'department' => [
                'id' => $this->department->id ?? null,
                'name' => $this->department->name ?? null,
            ],
        ];
    }
}
