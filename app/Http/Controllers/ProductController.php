<?php

namespace App\Http\Controllers;

use App\Http\Resources\DepartmentResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductListResource;
use App\Models\Department;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    // for home page
    public function home(Request $request)
    {
        $keyword = $request->query('keyword');
        $products = Product::query()->forWebsite()->when($keyword, function ($query, $keyword) {
            $query->where('name', 'like', "%$keyword%")
                ->orWhere('description', 'like', "%$keyword%");
        })->paginate(12);
        // dd($products);
        // dd(["products" => ProductListResource::collection($products)]);
        return Inertia::render("Home", ["products" => ProductListResource::collection($products)]);
    }
    // for single product page
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
    // for Departments filtering
    public function byDepartment(Request $request, Department $department)
    {
        abort_unless($department->active, 404);
        $keyword = $request->query('keyword');

        $products = Product::query()
            ->forWebsite()
            ->where('department_id', $department->id)
            ->when($keyword, function ($query, $keyword) {
                $query->where('name', 'like', "%$keyword%")
                    ->orWhere('description', 'like', "%$keyword%");
            })->paginate();


        // dd(new DepartmentResource($department), $keyword,ProductListResource::collection($products));

        return Inertia::render('Department/Index', [
            'products' => ProductListResource::collection($products),
            'department' => new DepartmentResource($department),
    
        ]);
    }
}
