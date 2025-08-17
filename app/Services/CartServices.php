<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Type\Decimal;

class CartServices
{
    private ?array $CachedCartItems = null;
    protected const COOKIE_NAME = "cartItems";
    protected const COOKIE_LIFE_TIME = 60 * 24 * 365; //1 year



    public  function addItemToCart(Product $product, int $quantity = 1, $optionIds = null) {}

    public  function updateItemQuantity(int $productId, int $quantity, $optionIds = null) {}


    public  function removeItemFromCart(int $productId, int $optionIds = null) {}


    // some helper function
    public function getCartItems(): array
    {
        try {
            // check if user is Authenticated or not
            if ($this->CachedCartItems === null) {
                if (Auth::check()) {
                    $cartItems = $this->getCartItemsFromDatabase();
                } else {
                    $cartItems = $this->getCartItemsFromCookies();
                }
                $productIds = collect($cartItems)->map(fn($item) => $item["product_id"]);
                $products = Product::whereIn("id", $productIds)->with('user.vendor');

            }
            return $this->CachedCartItems;
        } catch (\Exception $e) {
        }
        return [];
    }
    public function getTotalQuantity(): int
    {
        return 1;
    }
    public function getTotalPrice(): float
    {
        return 1;
    }
    protected function updateItemQuantityInDatabase(int $productId, int $quantity, array $optionIds) {}
    protected function updateItemQuantityInCookies(int $productId, int $quantity, array $optionIds) {}
    protected function saveItemToDatabase(int $productId, int $quantity, array $optionIds) {}
    protected function saveItemToCookies(int $productId, int $quantity, array $optionIds) {}
    protected function removeItemFromCookies(
        int $productId,
        int $quantity,
        array $optionIds
    ) {}
    protected function removeItemFromCookie(
        int $productId,
        int $quantity,
        array $optionIds
    ) {}
    protected function getCartItemsFromDatabase() {}
    protected function getCartItemsFromCookies() {}
}
