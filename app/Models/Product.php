<?php

namespace App\Models;

use App\Enums\ProductStatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
{
    // this to deal with images
    use InteractsWithMedia;


    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')->width(100);
        $this->addMediaConversion('small')->width(480);
        $this->addMediaConversion('large')->width(1200);
    }
    // public function registerMediaCollections(): void
    // {
    //     $this->addMediaCollection('images');
    // }

    /**
     * Get the department that owns the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
    /**
     * Get the category that owns the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    /**
     * Get all of the variation for the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function variationTypes(): HasMany
    {
        return $this->hasMany(VariationType::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function variations(): HasMany
    {
        return $this->hasMany(ProductVariation::class);
    }

    public function scopeForVendor(Builder $query)
    {
        return $query->where('created_by', Auth::user()->id);
    }
    public function scopeForPublished(Builder $query)
    {
        return $query->where('status', ProductStatusEnum::Published);
    }
    public function scopeForWebsite(Builder $query)
    {
        return $query->forPublished();
    }
    public function getPriceForOptions($optionIds = [])
    {
        $optionIds = array_values($optionIds); // the id for the option for each type
        ksort($optionIds);
        foreach ($this->variations as $variation) {
            $a = $variation->variation_type_option_ids;
            ksort($a);
            if ($optionIds == $a) {
                return $variation->price??$this->price;
            }
        }
        return $this->price;
    }
}
