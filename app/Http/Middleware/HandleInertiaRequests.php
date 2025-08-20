<?php

namespace App\Http\Middleware;

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

        // dd($totalPrice, $toatalQuantity, $cartItems);
        return [
            'csrf_token'=>csrf_token(),
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'ziggy' => fn () => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),

            ],
            'seccess'=> session('success'),
            'miniCartItems'=>$cartItems,
            'totalQuantity'=> $toatalQuantity,
            'totalPrice'=> $totalPrice,
        ];
    }
}
