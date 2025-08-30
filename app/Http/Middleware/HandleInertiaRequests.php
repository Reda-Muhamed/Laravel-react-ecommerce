<?php

namespace App\Http\Middleware;

use App\Http\Resources\AuthUserResource;
use App\Http\Resources\DepartmentResource;
use App\Models\Department;
use App\Services\CartServices;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {

        // to use a service in middleware
         $cartServices = app(CartServices::class);
        $toatalQuantity = $cartServices->getTotalQuantity();
        $totalPrice = $cartServices->getTotalPrice();
        $cartItems = $cartServices->getCartItems();
        $departments = Department::published()->with('categories')->get();


        // dd($totalPrice, $toatalQuantity, $cartItems);
        return [

            ...parent::share($request),
            'csrf_token'=>csrf_token(),
            'appName'=>config('app.name'),
            'auth' => [
                'user' => $request->user() ? new AuthUserResource($request->user()) : null,
            ],
            'ziggy' => fn () => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),

            ],
            'success'=> [
                'message'=>session('success'),
                'time'=>microtime(true) - LARAVEL_START,
            ],
            'error'=> session('error'),
            'miniCartItems'=>$cartItems,
            'totalQuantity'=> $toatalQuantity,
            'totalPrice'=> $totalPrice,
            'sharedDepartments'=> DepartmentResource::collection($departments)->collection->toArray(), // to use a resource collection in middleware
            'keyword'=> $request->input('products_index', [])['query'] ?? '', // to use a resource collection in middleware
        ];
    }
}
