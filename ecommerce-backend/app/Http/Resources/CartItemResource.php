<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $product = $this->whenLoaded('product');
        $subtotal = 0;

        if ($product && $this->product) {
            $subtotal = (float) ($this->product->price * $this->quantity);
        }

        return [
            'id' => $this->id,
            'product' => new ProductResource($product),
            'quantity' => $this->quantity,
            'subtotal' => $subtotal,
        ];
    }
}
