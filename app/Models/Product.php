<?php

namespace App\Models;

use App\Enums\ProductStatusEnum;
use App\Enums\VendorStatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
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
        return $query->where('products.status', ProductStatusEnum::Published);
    }
    public function scopeForWebsite(Builder $query)
    {
        return $query->forPublished()->vendorApproved();
    }
    public function scopeVendorApproved(Builder $query)
    {
        return $query->join('vendors', 'products.created_by', '=', 'vendors.user_id')
            ->where('vendors.status', VendorStatusEnum::Approved->value);

    }
    public function getPriceForOptions($optionIds = [])
    {
        $optionIds = array_values($optionIds); // the id for the option for each type
        ksort($optionIds);
        foreach ($this->variations as $variation) {
            $a = $variation->variation_type_option_ids;
            ksort($a);
            if ($optionIds == $a) {
                return $variation->price ?? $this->price;
            }
        }
        return $this->price;
    }
    public function getImageForOptions($optionIds = null)
    {
        if ($optionIds) {
            // dd($optionIds);
            $optionIds = json_decode($optionIds, true);
            $optionIds = array_values($optionIds);
            sort($optionIds);
            $options = VariationTypeOption::whereIn('id', $optionIds)->get();
            foreach ($options as $option) {
                $image = $option->getFirstMediaUrl('images', 'small');
                if ($image) {
                    return $image;
                }
            }
        }
        return $this->getFirstMediaUrl('images', 'small');
    }
    public function options(): HasManyThrough
    {
        return $this->hasManyThrough(
            VariationTypeOption::class,
            VariationType::class,
            'product_id', // Foreign key on VariationType table...
            'variation_type_id', // Foreign key on VariationTypeOption table...
            'id', // Local key on Product table...
            'id' // Local key on VariationType table...
        );
    }
    public function getFirstImageUrl($collectionName  = 'images', $conversion = 'small'): string
    {
        if ($this->options()->count() > 0) {
            foreach ($this->options as $option) {
                $image = $option->getFirstMediaUrl($collectionName, $conversion);
                if ($image) {
                    return $image;
                }
            }
        }
        return $this->getFirstMediaUrl($collectionName, $conversion);
    }
    public function getPriceForFirstOptions(): float
    {
        $firstOption = $this->getFirstOptionsMap();

        if ($firstOption) {
            return $this->getPriceForOptions($firstOption);
        }
        return $this->price;
    }
    public function getFirstOptionsMap(): array{
        //  dd($this->variationTypes->mapWithKeys(function($item){
        //     return [$item->id => $item->options[0]?->id];
        // })->toArray());
        return $this->variationTypes->mapWithKeys(function($item){
            return [$item->id => $item->options[0]?->id];
        })->toArray();
    }
    public function getImages():MediaCollection{
        if ($this->options()->count() > 0) {
            foreach ($this->options as $option) {
                $images = $option->getMedia('images');
                if ($images->count() > 0) {
                    return $images;
                }
            }
        }
        return $this->getMedia('images');
    }
    public function getImagesForOptions($optionIds = []):MediaCollection{
        if ($optionIds) {
            $optionIds = array_values($optionIds);
            sort($optionIds);
            $options = VariationTypeOption::whereIn('id', $optionIds)->get();
            foreach ($options as $option) {
                $images = $option->getMedia('images');
                if ($images->count() > 0) {
                    return $images;
                }
            }
        }
        return $this->getMedia('images');
    }
}
