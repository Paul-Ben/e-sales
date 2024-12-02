<?php

namespace App\Helpers;

use App\Models\Product;
use Illuminate\Support\Facades\Cookie;

class CartManagement
{
    //add items to cart
    public static function addItemToCart($product_id)
    {
        $cart_items = self::getCartItemsFromCookie();

        $existing_item = null;

        foreach ($cart_items as $key => $item) {
            if ($item['product_id'] == $product_id) {
                $existing_item = $key;
                break;
            }
        }
        if ($existing_item !== null) {
            $cart_items[$existing_item]['quantity']++;
            $cart_items[$existing_item]['total_price'] = $cart_items[$existing_item]['quantitu'] * $cart_items[$existing_item]['unit_price'];
        } else {
            $product = Product::where('id', $product_id)->first(['id', 'name', 'price', 'images']);
            if ($product) {
                $cart_items[] = [
                    'product_id' => $product_id,
                    'name' => $product->name,
                    'image' => $product->image,
                    'quantity' => 1,
                    'unit_price' => $product->price,
                    'total_price' => $product->price
                ];
            }
        }
        self::addCartItemsToCookie($cart_items);
        return count($cart_items);
    }

    //remove items from cart

    public static function removeCartItem($product_id)
    {
        $cart_items = self::getCartItemsFromCookie();

        foreach ($cart_items as $key => $item) {
            if ($item['product_id'] == $product_id) {
                unset($cart_items[$key]);
            }
        }

        self::addCartItemsToCookie($cart_items);
        return $cart_items;
    }


    //add cart items to coockie
    static public function addCartItemsToCookie($cart_items)
    {
        Cookie::queue('cartItems', json_encode($cart_items), 60 * 24 * 30);
    }

    //clear cart items from coockie
    static public function clearCartItemsFromCookie()
    {
        Cookie::queue(Cookie::forget('cart_items'));
    }
    //get all cart items from coockie
    static public function getCartItemsFromCookie()
    {
        $cart_items = json_decode(Cookie::get('cart_items'), true);
        if (!$cart_items) {
            $cart_items = [];
        }

        return $cart_items;
    }
    //increment item quantity

    public static function incrementQuantityToCartItem($product_id)
    {
        $cart_items = self::getCartItemsFromCookie();

        foreach ($cart_items as $key => $item) {
            if ($item['product_id'] == $product_id) {
                $cart_items[$key][$quantity]++;
                $cart_items[$key]['total_price'] = $cart_items[$key]['quantity'] * $cart_items[$key]['unit_price'];
            }
        }
        self::addCartItemsToCookie($cart_items);
        return $cart_items;
    }

    //decrement item quantity

    public static function decrementQuantityToCartItem($product_id)
    {
    }

    //calculate grand total
}
