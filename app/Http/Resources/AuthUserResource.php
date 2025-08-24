<?php

namespace App\Http\Resources;

use App\Enums\VendorStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthUserResource extends JsonResource
{
    public static $wrap = false;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'email_verified_at'=> $this->email_verified_at,
            'roles' => $this->getRoleNames(),
            'permissions' => $this->getAllPermissions()->map(fn($perm) => $perm->name),
            'stripe_account_active' => $this->isStripeAccountActive(),
            'vendor' => $this->vendor ? [
                'id' => $this->vendor->id,
                'status' => $this->vendor->status,
                'status_label' => VendorStatusEnum::from($this->vendor->status)->label(),
                'store_name' => $this->vendor->stor_name,
                'store_address' => $this->vendor->store_address,
                'cover_image' => $this->vendor->cover_image,
            ] : null,
            ];
    }
}
