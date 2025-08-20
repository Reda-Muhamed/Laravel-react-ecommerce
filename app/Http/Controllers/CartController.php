<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartServices;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(CartServices $cartService)
    {
        // dd($cartService->getCartItems());
        return Inertia::render("Cart/Index", [
            "cartItems" => $cartService->getCartItemsGrouped(),
        ]);
    }
    public function store(Request $request, Product $product, CartServices $cartService)
    {
        $request->mergeIfMissing([
            "quantity" => 1,
        ]);
        $data = $request->validate([
            "option_ids" => ["nullable", 'array'],
            'quantity' => ['required', 'integer', 'min:1'],

        ]);
        $cartService->addItemToCart($product, $data['quantity'], $data['option_ids']?:[]);
        return back()->with('success', 'Product added to cart successfully!');
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product, CartServices $cartService)
    {
        $request->validate([
            'quantity' => ['integer', 'min:1'],
        ]);
        $optionsIds = $request->input('option_ids')?:[];
        $quantity = $request->input('quantity');
        // dd($optionsIds , $quantity);
        $cartService->updateItemQuantity($product->id, $quantity, $optionsIds);
        return back()->with('success', 'Quantity updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Product $product, CartServices $cartService)
    {
        $optionIds = $request->input('option_ids');
        $cartService->removeItemFromCart($product->id, $optionIds);
        return back()->with('success', 'Product was removed from cart');
    }



    public function checkout(){

    }

}
