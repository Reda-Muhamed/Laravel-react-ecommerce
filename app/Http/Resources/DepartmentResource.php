<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentResource extends JsonResource
{
    public static $wrap = false; // ✅ Keep Inertia clean (no data wrapper)
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=> $this->id,
            "name"=> $this->name,
            "slug"=> $this->slug,
            'image' => $this->getFirstMediaUrl('department_image', 'large') ?: null,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'categries'=>$this->categories->map(fn($category)=>[
                'id'=>$category->id,
                'name'=>$category->name,
                'image' => $category->getFirstMediaUrl('image', 'large') ?: null,

            ]),
        ];
    }
}
