<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\VariationType;
use App\Models\VariationTypeOption;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Type\Decimal;
use Illuminate\Support\Str;

class CartServices
{
    private ?array $CachedCartItems = null;
    protected const COOKIE_NAME = "cartItems";
    protected const COOKIE_LIFE_TIME = 60 * 24 * 365; //1 year



    public  function addItemToCart(Product $product, int $quantity = 1, $optionIds = null)
    {
        if ($optionIds === null) {
            // if no options are selected, we will select the first option for all type and just add the product to the cart
            $optionIds = $product->variationTypes->mapWithKeys(fn(VariationType $type) => [$type->id => $type->options[0]?->id])->toArray();
        }
        $price = $product->getPriceForOptions($optionIds);
        // dd($price);
        if (Auth::check()) {
            $this->saveItemToDatabase($product->id, $quantity, $price, $optionIds);
        } else {
            $this->saveItemToCookies($product->id, $quantity, $price, $optionIds);
        }
    }

    public  function updateItemQuantity(int $productId, int $quantity, $optionIds = null)
    {
        if (Auth::check()) {
            $this->updateItemQuantityInDatabase($productId, $quantity, $optionIds);
        } else {
            $this->updateItemQuantityInCookies($productId, $quantity, $optionIds);
        }
    }


    public  function removeItemFromCart(int $productId,  $optionIds = null)
    {
        if (Auth::check()) {
            $this->removeItemFromDatabase($productId, $optionIds);
        } else {
            $this->removeItemFromCookie($productId, $optionIds);
        }
    }


    // some helper function
    public function getCartItems(): array
    {
        try {
            // check if cart items are cached it will enter in the first time only
            if ($this->CachedCartItems === null) {
                // check if user is Authenticated or not
                if (Auth::check()) {
                    $cartItems = $this->getCartItemsFromDatabase() ?? [];
                } else {
                    $cartItems = $this->getCartItemsFromCookies() ?? [];
                    // dd($cartItems);
                }
                $cartItems = collect($cartItems)
                    ->map(fn($item) => (array)$item)
                    ->toArray();

                $productIds = collect($cartItems)
                    ->pluck('product_id')
                    ->toArray();



                $products = Product::whereIn("id", $productIds)->with('user.vendor')->forWebsite()->get()->keyBy('id');

                $cartItemData = [];

                foreach ($cartItems as $key => $cartItem) {
                    $product = data_get($products, $cartItem['product_id']);

                    if (!$product) continue;
                    $optionInfo = [];

                    $options = VariationTypeOption::with('variationType')->whereIn('id', $cartItem['option_ids'])->get()->keyBy('id');

                    $imageUrl = null;
                    // dd($cartItem['option_ids']);
                    foreach ($cartItem['option_ids'] as $option_id) {
                        $option = data_get($options, $option_id);

                        if (!$imageUrl) {
                            $imageUrl = $option->getFirstMediaUrl('images', 'small');
                        }

                        $optionInfo[] = [
                            'id' => $option->id,
                            'name' => $option->name, //black
                            'type' => [
                                'id' => $option->variationType->id, //number
                                'name' => $option->variationType->name // color
                            ]
                        ];
                    }
                    $cartItemData[] = [
                        'id' => $cartItem['id'],
                        'product_id' => $product->id,
                        'title' => $product->name,
                        'slug' => $product->slug,
                        'price' => $cartItem['price'],
                        'quantity' => $cartItem['quantity'],
                        'option_ids' => $cartItem['option_ids'],
                        'options' => $optionInfo,
                        'image' => $imageUrl ?: $product->getFirstMediaUrl('images', 'small'),
                        'user' => [
                            'id' => $product->created_by,
                            'name' => $product->user->vendor->store_name,
                        ],
                    ];
                }
                $this->CachedCartItems = $cartItemData;
            }
            // dd($this->CachedCartItems);
            return $this->CachedCartItems;
        } catch (\Exception $e) {
            throw $e;
            Log::error($e->getMessage() . PHP_EOL . $e->getTraceAsString());
        }
        return [];
    }
    public function getTotalQuantity(): int
    {
        $totalQuantity = 0;
        foreach ($this->getCartItems() as $cartItem) {
            $totalQuantity += $cartItem['quantity'];
        }

        return $totalQuantity;
    }
    public function getTotalPrice(): float
    {
        $totalPrice = 0;
        foreach ($this->getCartItems() as $cartItem) {
            $totalPrice += $cartItem['price'] * $cartItem['quantity'];
        }
        return $totalPrice;
    }
    protected function updateItemQuantityInDatabase(int $productId, int $quantity, array $optionIds)
    {
        $userId = Auth::id();

        $cartItem = CartItem::where('user_id', $userId)->where('product_id', $productId)->where('variation_type_option_ids', json_encode($optionIds))->first();

        if ($cartItem) {
            $cartItem->update([
                'quantity' => $quantity,
            ]);
        }
    }
    protected function updateItemQuantityInCookies(int $productId, int $quantity, array $optionIds)
    {
        $cartItems = $this->getCartItemsFromCookies();
        ksort($optionIds);
        // use a unique key for each product and option combination
        $itemKey = $productId . '_' . json_encode($optionIds);
        if (isset($cartItems[$itemKey])) {
            $cartItems[$itemKey]['quantity'] = $quantity;
        }
        //save the cart items to cookies
        Cookie::queue(self::COOKIE_NAME, json_encode($cartItems), self::COOKIE_LIFE_TIME);
    }
    protected function saveItemToDatabase(int $productId, int $quantity, float $price, array $optionIds)
    {
        $userId = Auth::id();
        ksort($optionIds);
        $encodedOptions = json_encode($optionIds);

        $cartItem = CartItem::where('user_id', $userId)
            ->where('product_id', $productId)
            ->where('variation_type_option_ids', $encodedOptions)
            ->first();


        if ($cartItem) {
            $cartItem->update([
                'quantity' => DB::raw('quantity + ' . $quantity),
            ]);
        } else {
            $cartItem = CartItem::create([
                'user_id' => $userId,
                'product_id' => $productId,
                'quantity' => $quantity,
                'price' => $price,
                'variation_type_option_ids' => $optionIds,
            ]);
        }
    }
    protected function saveItemToCookies(int $productId, int $quantity, float $price, array $optionIds)
    {
        $cartItems = $this->getCartItemsFromCookies();
        ksort($optionIds);
        $itemKey = $productId . '_' . json_encode($optionIds);

        if (isset($cartItems[$itemKey])) {

           $cartItems[$itemKey]['quantity'] += $quantity;
        //    $cartItems[$itemKey]['price'] += $price;
        //    dd($cartItems[$itemKey]['quantity'] ,$cartItems[$itemKey]['price'],$quantity,$price);
        } else {
            $cartItems[$itemKey] = [
                'id' => Str::uuid(),
                'product_id' => $productId,
                'quantity' => $quantity,
                'price' => $price,
                'option_ids' => $optionIds,
            ];
        }
        // save the cart items to cookies
        // dd($cartItems);
        Cookie::queue(self::COOKIE_NAME, json_encode($cartItems), self::COOKIE_LIFE_TIME);
    }
    protected function removeItemFromDatabase(
        int $productId,
        array $optionIds
    ) {
        $userId = Auth::id();
        ksort($optionIds);
        CartItem::where('user_id', $userId)->where('product_id', $productId)->where('variation_type_option_ids', json_encode($optionIds))->delete();
    }
    protected function removeItemFromCookie(
        int $productId,
        array $optionIds
    ) {
        $cartItems = $this->getCartItemsFromCookies();
        ksort($optionIds);
        $itemKey = $productId . '_' . json_encode($optionIds);

        if (isset($cartItems[$itemKey])) {
            unset($cartItems[$itemKey]);
            Cookie::queue(self::COOKIE_NAME, json_encode($cartItems), self::COOKIE_LIFE_TIME);
        }
    }
    protected function getCartItemsFromDatabase()
    {
        $userId = Auth::id();
        $cartItems = CartItem::where('user_id', $userId)->get()->map(fn($cartItem) => [
            'id' => $cartItem->id,
            'product_id' => $cartItem->product_id,
            'quantity' => $cartItem->quantity,
            'price' => $cartItem->price,
            'option_ids' => $cartItem->variation_type_option_ids
        ])->toArray();
        return $cartItems;
    }
    protected function getCartItemsFromCookies()
    {
        $cartItems = json_decode(Cookie::get(self::COOKIE_NAME, '[]'), true);
        return $cartItems;
    }
    public function getCartItemsGrouped(): array
    {
        $cartItems = $this->getCartItems();

        return collect($cartItems)->groupBy(fn($cartItem) => $cartItem['user']['id'])->map(fn($items, $userId) => [
            'user' => $items->first()['user'],
            'items' => $items->toArray(),
            'totalQuantity' => $items->sum('quantity'),
            'totalPrice' => $items->sum(fn($item) => $item['price'] * $item['quantity']),

        ])->toArray();
    }
    public function moveCartItemFromCookiesToDatabase($userId)
    {
        $cartItems = $this->getCartItemsFromCookies();
        $cartItems= array_values($cartItems);
        // dd($cartItems);
        foreach ($cartItems as $cartItem) {
            $existingItem = CartItem::where('user_id', $userId)->where('product_id', $cartItem['product_id'])->where('variation_type_option_ids', json_encode($cartItem['option_ids']))->first();
            // dd($existingItem);
            if ($existingItem) {
                $existingItem->update([
                    'quantity' => $existingItem->quantity + $cartItem['quantity'],
                ]);
            } else {

                CartItem::create([
                    'user_id' => $userId,
                    'product_id' => $cartItem['product_id'],

                    'quantity' => $cartItem['quantity'],
                    'price' => $cartItem['price'],
                    'variation_type_option_ids' => $cartItem['option_ids'],
                ]);
            }
        }
        Cookie::queue(self::COOKIE_NAME,'',-1);//delete cookie after send it when the user logined
    }
}
