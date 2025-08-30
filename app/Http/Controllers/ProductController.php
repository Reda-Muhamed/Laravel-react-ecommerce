<?php

namespace App\Http\Controllers;

use App\Http\Resources\DepartmentResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductListResource;
use App\Models\Category;
use App\Models\Department;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    // for home page
    // public function home(Request $request)
    // {
    //     $filters = $request->input('products_index', []);


    //     $keyword = $filters['query']?? '';
    //     $products = Product::query()->forWebsite()->when($keyword, function ($query, $keyword) {
    //         $query->where('name', 'like', "%$keyword%")
    //             ->orWhere('description', 'like', "%$keyword%");
    //     })->paginate(12);
    //     // dd($products);
    //     // dd(["products" => ProductListResource::collection($products)]);
    //     return Inertia::render("Home", ["products" => ProductListResource::collection($products)]);
    // }

     public function home(Request $request)
    {
        // Parse nested query parameters
        $filters = $request->input('products_index', []);
        $departmentNames = $filters['refinementList']['department_name'] ?? []; // Default to current department
        $categoryNames = $filters['refinementList']['category_name'] ?? [];
        $priceMin = $filters['numericMenu']['price']['min'] ?? null;
        $priceMax = $filters['numericMenu']['price']['max'] ?? null;

        $keyword = $filters['query'] ?? '';
        // dd($filters['query'] ?? $request->query('keyword'));

        // dd($keyword);
        // dd($keyword,$categoryNames);
        // Map names to IDs
        $departmentIds = Department::whereIn('name', $departmentNames)->pluck('id')->toArray();
        $categoryIds = Category::whereIn('name', $categoryNames)
            ->whereIn('department_id', $departmentIds) // Only categories in selected department(s)
            ->pluck('id')
            ->toArray();

        // Build product query
         $query = Product::query()
            ->forWebsite() ;
            // dd($departmentIds);
        if(!empty($departmentIds)){
            $query = $query ->whereIn('department_id', $departmentIds); // Support multiple departments
        }
        // dd($query);


        // Apply filters
        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%$keyword%")
                  ->orWhere('description', 'like', "%$keyword%");
            });
        }

        if (!empty($categoryIds)) {
            $query->whereIn('category_id', $categoryIds);
        }

        if ($priceMin !== null) {
            $query->where('price', '>=', $priceMin);
        }

        if ($priceMax !== null) {
            $query->where('price', '<=', $priceMax);
        }

        // Paginate products
        $products = $query->paginate(12)->appends($request->query());

        // Fetch departments and categories with counts
        $departments = Department::all()->map(function ($dep) use ($keyword, $categoryIds, $priceMin, $priceMax) {
            $countQuery = Product::where('department_id', $dep->id);
            if ($keyword) {
                $countQuery->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', "%$keyword%")
                      ->orWhere('description', 'like', "%$keyword%");
                });
            }
            if (!empty($categoryIds)) {
                $countQuery->whereIn('category_id', $categoryIds);
            }
            if ($priceMin !== null) {
                $countQuery->where('price', '>=', $priceMin);
            }
            if ($priceMax !== null) {
                $countQuery->where('price', '<=', $priceMax);
            }
            return [
                'id' => $dep->id,
                'name' => $dep->name,
                'count' => $countQuery->count(),
            ];
        });
        // need the only categories that belong to the selected departments
        $categories = $departmentIds ? Category::whereIn('department_id', $departmentIds)->select('id', 'name')->get()->map(function ($cat) use ($keyword, $departmentIds, $priceMin, $priceMax) {
            $countQuery = Product::where('category_id', $cat->id)->whereIn('department_id', $departmentIds);
            if ($keyword) {
                $countQuery->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', "%$keyword%")
                      ->orWhere('description', 'like', "%$keyword%");
                });
            }
            if ($priceMin !== null) {
                $countQuery->where('price', '>=', $priceMin);
            }
            if ($priceMax !== null) {
                $countQuery->where('price', '<=', $priceMax);
            }
            return [
                'id' => $cat->id,
                'name' => $cat->name,
                'count' => $countQuery->count(),
            ];
        }) : Category::all();


        // Return Inertia response

        return Inertia::render('Home', [
            'products' => ProductListResource::collection($products),
            'departments' => $departments,
            'categories' => $categories,

            'filters' => [
                'keyword' => $keyword,
                'department_names' => $departmentNames,
                'category_names' => $categoryNames,
                'price_min' => $priceMin,
                'price_max' => $priceMax,
            ],
        ]);
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
    // public function byDepartment(Request $request, Department $department)
    // {
    //     abort_unless($department->active, 404);
    //     $keyword = $request->query('keyword');
    //     $selectedCategories = $request->query('categories', []);
    //     // dd($department->categories());
    //     $categories = $department->categories()->select('id', 'name')->get();
    //     // dd($categories);
    //     $products = Product::query()
    //         ->forWebsite()
    //         ->where('department_id', $department->id)
    //         ->when($keyword, function ($query, $keyword) {
    //             $query->where('name', 'like', "%$keyword%")
    //                 ->orWhere('description', 'like', "%$keyword%");
    //         })->when(count($selectedCategories) > 0, function ($query) use ($selectedCategories) {
    //             $query->whereIn('category_id', $selectedCategories);
    //         })
    //         ->paginate(12)
    //         ->appends($request->query());


    //     // dd(new DepartmentResource($department), $keyword,ProductListResource::collection($products));

    //     return Inertia::render('Department/Index', [
    //         'products' => ProductListResource::collection($products),
    //         'department' => new DepartmentResource($department),
    //         'categories'  => $categories,
    //         'filters'     => [
    //             'keyword'    => $keyword,
    //             'categories' => $selectedCategories,
    //         ]

    //     ]);
    // }





    public function byDepartment(Request $request, Department $department)
    {
        // Ensure department is active
        abort_unless($department->active, 404);
        // Parse nested query parameters
        $filters = $request->input('products_index', []);
        $departmentNames = $filters['refinementList']['department_name'] ?? [$department->name]; // Default to current department
        $categoryNames = $filters['refinementList']['category_name'] ?? [];
        $priceMin = $filters['numericMenu']['price']['min'] ?? null;
        $priceMax = $filters['numericMenu']['price']['max'] ?? null;

        $keyword = $filters['query'] ?? '';
        // dd($filters['query'] ?? $request->query('keyword'));

        // dd($keyword);
        // dd($keyword,$categoryNames);
        // Map names to IDs
        $departmentIds = Department::whereIn('name', $departmentNames)->pluck('id')->toArray();
        $categoryIds = Category::whereIn('name', $categoryNames)
            ->whereIn('department_id', $departmentIds) // Only categories in selected department(s)
            ->pluck('id')
            ->toArray();

        // Build product query
        $query = Product::query()
            ->forWebsite() // Assuming this scopes active products
            ->whereIn('department_id', $departmentIds); // Support multiple departments

        // Apply filters
        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%$keyword%")
                  ->orWhere('description', 'like', "%$keyword%");
            });
        }

        if (!empty($categoryIds)) {
            $query->whereIn('category_id', $categoryIds);
        }

        if ($priceMin !== null) {
            $query->where('price', '>=', $priceMin);
        }

        if ($priceMax !== null) {
            $query->where('price', '<=', $priceMax);
        }

        // Paginate products
        $products = $query->paginate(12)->appends($request->query());

        // Fetch departments and categories with counts
        $departments = Department::all()->map(function ($dep) use ($keyword, $categoryIds, $priceMin, $priceMax) {
            $countQuery = Product::where('department_id', $dep->id);
            if ($keyword) {
                $countQuery->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', "%$keyword%")
                      ->orWhere('description', 'like', "%$keyword%");
                });
            }
            if (!empty($categoryIds)) {
                $countQuery->whereIn('category_id', $categoryIds);
            }
            if ($priceMin !== null) {
                $countQuery->where('price', '>=', $priceMin);
            }
            if ($priceMax !== null) {
                $countQuery->where('price', '<=', $priceMax);
            }
            return [
                'id' => $dep->id,
                'name' => $dep->name,
                'count' => $countQuery->count(),
            ];
        });

        $categories = $department->categories()->select('id', 'name')->get()->map(function ($cat) use ($keyword, $departmentIds, $priceMin, $priceMax) {
            $countQuery = Product::where('category_id', $cat->id)->whereIn('department_id', $departmentIds);
            if ($keyword) {
                $countQuery->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', "%$keyword%")
                      ->orWhere('description', 'like', "%$keyword%");
                });
            }
            if ($priceMin !== null) {
                $countQuery->where('price', '>=', $priceMin);
            }
            if ($priceMax !== null) {
                $countQuery->where('price', '<=', $priceMax);
            }
            return [
                'id' => $cat->id,
                'name' => $cat->name,
                'count' => $countQuery->count(),
            ];
        });

        // Return Inertia response

        return Inertia::render('Department/Index', [
            'products' => ProductListResource::collection($products),
            'department' => new DepartmentResource($department),
            'departments' => $departments,
            'categories' => $categories,

            'filters' => [
                'keyword' => $keyword,
                'department_names' => $departmentNames,
                'category_names' => $categoryNames,
                'price_min' => $priceMin,
                'price_max' => $priceMax,
            ],
        ]);
    }
}
