<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'stripe_session_id',
        'user_id',
        'total_price',
        'status',
        'online_payment_commission',
        'website_commission',
        'vendor_subtotal',
        'payment_intent',
    ];
    /**
     * Get all of the orderItem for the Order
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
    /**
     * Get the user that owns the Order
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    /**
     * Get the vendor that owns the Order
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo( Vendor::class,'vendor_user_id','user_id');
    }
    /**
     * Get the vendorUser that owns the Order
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vendorUser(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_user_id','user_id');
    }
}
