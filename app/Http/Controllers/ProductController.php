<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductListResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function home()
    {
        $products = Product::query()->forWebsite()->paginate(12);
        // dd(["products" => ProductListResource::collection($products)]);
        return Inertia::render("Home", ["products" => ProductListResource::collection($products)]);
    }
    public function show(Product $product)
    {

        $product->load([
            'user',
            'department',
            'variationTypes.options.media',
            'variations',
            'media'
        ]);
        // dd(new ProductResource($product));
        // for this "variationsOptions" see the helper files in js and u will get it

        return Inertia::render('Product/Show', [
            'product' => new ProductResource($product),
            'variationsOptions' => request('options', []),
        ]);
    }
}
