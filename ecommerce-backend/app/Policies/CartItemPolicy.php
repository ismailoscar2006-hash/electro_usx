<?php

namespace App\Policies;

use App\Models\CartItem;
use App\Models\User;

class CartItemPolicy
{
    public function update(User $user, CartItem $cartItem): bool
    {
        $cart = $cartItem->cart;
        if (!$cart) {
            return false;
        }
        return $user->id === $cart->user_id;
    }

    public function delete(User $user, CartItem $cartItem): bool
    {
        $cart = $cartItem->cart;
        if (!$cart) {
            return false;
        }
        return $user->id === $cart->user_id;
    }
}
