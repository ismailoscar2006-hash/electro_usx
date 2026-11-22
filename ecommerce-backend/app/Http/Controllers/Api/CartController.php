<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Http\Resources\CartResource;
use App\Models\CartItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{


/**Jib panier dyal utilisateur connecté mea les produits li fih*/
    public function show(Request $request): JsonResponse
    {
        try {
            $cart = $request->user()->cart()->with('items.product')->first();

            if (!$cart) {
                return response()->json([
                    'message' => 'Cart not found',
                    'data' => null,
                ], 404);
            }

            return response()->json([
                'message' => 'Cart retrieved successfully',
                'data' => new CartResource($cart),
            ]);
        } catch (\Exception $e) {
            \Log::error('ShowCart: Exception occurred', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()?->id ?? null,
                'stack' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }


    /** Ajout produit ou augmentation quantité f panier*/
    public function addItem(AddToCartRequest $request): JsonResponse
    {
        try {
            $cart = $request->user()->cart()->first();

            if (!$cart) {
                return response()->json([
                    'message' => 'Cart not found',
                    'data' => null,
                ], 404);
            }

            $cartItem = $cart->items()->firstOrNew(
                ['product_id' => $request->product_id]
            );

            if ($cartItem->exists) {
                $cartItem->quantity += $request->quantity;
            } else {
                $cartItem->quantity = $request->quantity;
            }

            $cartItem->save();

            $cart->load('items.product');

            return response()->json([
                'message' => 'Item added to cart',
                'data' => new CartResource($cart),
            ]);
        } catch (\Exception $e) {
            \Log::error('AddItem: Exception occurred', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()?->id ?? null,
                'stack' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }


    // Modification quantité produit f panier
    public function updateItem(UpdateCartItemRequest $request, CartItem $cartItem): JsonResponse
    {
        try {
            $this->authorize('update', $cartItem);

            $cartItem->update(['quantity' => $request->quantity]);

            $cart = $cartItem->cart;
            if (!$cart) {
                return response()->json([
                    'message' => 'Cart not found',
                    'data' => null,
                ], 404);
            }

            $cart->load('items.product');

            return response()->json([
                'message' => 'Cart item updated',
                'data' => new CartResource($cart),
            ]);
        } catch (\Exception $e) {
            \Log::error('UpdateItem: Exception occurred', [
                'error' => $e->getMessage(),
                'cartItem_id' => $cartItem->id ?? null,
                'stack' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }


    // Suppression produit men panier
    public function removeItem(CartItem $cartItem, Request $request): JsonResponse
    {
        try {
            \Log::info('RemoveItem: Starting removal', ['cartItem_id' => $cartItem->id]);

            $this->authorize('delete', $cartItem);
            \Log::info('RemoveItem: Authorization passed', ['cartItem_id' => $cartItem->id]);

            $cart = $cartItem->cart;
            \Log::info('RemoveItem: Cart retrieved', ['cart_id' => $cart?->id]);

            if (!$cart) {
                \Log::error('RemoveItem: Cart is null', ['cartItem_id' => $cartItem->id]);
                return response()->json([
                    'message' => 'Cart not found',
                    'data' => null,
                ], 404);
            }

            $cartItem->delete();
            \Log::info('RemoveItem: Item deleted', ['cartItem_id' => $cartItem->id]);

            $cart->load('items.product');
            \Log::info('RemoveItem: Cart reloaded', ['items_count' => $cart->items->count()]);

            return response()->json([
                'message' => 'Item removed from cart',
                'data' => new CartResource($cart),
            ]);
        } catch (\Exception $e) {
            \Log::error('RemoveItem: Exception occurred', [
                'error' => $e->getMessage(),
                'cartItem_id' => $cartItem->id ?? null,
                'stack' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }


    // Suppression ga3 produits men panier
    public function clear(Request $request): JsonResponse
    {
        try {
            $cart = $request->user()->cart()->first();

            if (!$cart) {
                return response()->json([
                    'message' => 'Cart not found',
                    'data' => null,
                ], 404);
            }

            $cart->items()->delete();
            $cart->load('items.product');

            return response()->json([
                'message' => 'Cart cleared',
                'data' => new CartResource($cart),
            ]);
        } catch (\Exception $e) {
            \Log::error('ClearCart: Exception occurred', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()?->id ?? null,
                'stack' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
