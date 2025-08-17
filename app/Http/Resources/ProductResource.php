<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public static $wrap = false; // ✅ Keep Inertia clean (no data wrapper)

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name, // ✅ Use correct DB column
            'slug' => $this->slug,
            'price' => $this->price,
            'description' => $this->description,
            'quantity' => $this->quantity,

            // ✅ Main image
            'image' => $this->getFirstMediaUrl('images'),

            // ✅ All images
            'images' => $this->getMedia('images')->map(fn($image) => [
                'id' => $image->id,
                'thumb' => $image->getUrl('thumb'),
                'small' => $image->getUrl('small'),
                'large' => $image->getUrl('large'),
            ]),

            // ✅ User details
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],

            // ✅ Department details
            'department' => [
                'id' => $this->department->id,
                'name' => $this->department->name,
            ],

            // ✅ Variation Types (with options + images)
            'variationTypes' => $this->variationTypes->map(fn($variationType) => [
                'id' => $variationType->id,
                'name' => $variationType->name,
                'type' => $variationType->type,
                'options' => $variationType->options->map(fn($option) => [
                    'id' => $option->id,
                    'name' => $option->name,
                    'images' => $option->getMedia('images')->map(fn($image) => [
                        'id' => $image->id,
                        'thumb' => $image->getUrl('thumb'),
                        'small' => $image->getUrl('small'),
                        'large' => $image->getUrl('large'),
                    ]),
                ]),
            ]),

            // ✅ Variations
            'variations' => $this->variations->map(fn($variation) => [
                'id' => $variation->id,
                'variation_type_option_ids' => $variation->variation_type_option_ids,
                'quantity' => $variation->quantity,
                'price' => $variation->price,
            ]),
        ];
    }
}
