<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Http\Resources\OrderResource;
use App\Http\Resources\UserResource;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{

// Jib ga3 les utilisateurs clients
    public function getAllUsers(Request $request): JsonResponse
    {
        $users = User::where('role', 'client')->paginate(15);

        return response()->json([
            'message' => 'Users retrieved successfully',
            // Transformation des utilisateurs avant envoi au Frontend
            'data' => UserResource::collection($users),
            'meta' => [
                'current_page' => $users->currentPage(),
                'total' => $users->total(),
                'per_page' => $users->perPage(),
                'last_page' => $users->lastPage(),
            ],
        ]);
    }


    //Katjib ga3 les commandes ou t9der tfilterihom b statut (Pending, Delivered...).
    public function getAllOrders(Request $request): JsonResponse
    {
        $query = Order::with('items.product', 'user');
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        $orders = $query->paginate(15);
        return response()->json([
            'message' => 'Orders retrieved successfully',
            'data' => OrderResource::collection($orders),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'total' => $orders->total(),
                'per_page' => $orders->perPage(),
                'last_page' => $orders->lastPage(),
            ],
        ]);
    }


//Katbdel statut dyal commande (Pending, Shipped, Delivered...).
    public function updateOrderStatus(UpdateOrderStatusRequest $request, Order $order): JsonResponse
    {
        $order->update(['status' => $request->status]);

        return response()->json([
            'message' => 'Order status updated successfully',
            'data' => new OrderResource($order->load('items.product')),// fesh status kitbdel kn3awdo njino les donnes dyal produit
        ]);
    }


    //Katjib état stock dyal produits ou wach kayn f stock wla la.
    public function getStockStatus(Request $request): JsonResponse
    {
        $products = Product::select('id', 'name', 'stock', 'category_id')
            ->with('category')
            ->paginate(15);

        return response()->json([
            'message' => 'Stock status retrieved successfully',
            'data' => $products->map(fn($product) => [
                'id' => $product->id,
                'name' => $product->name,
                'stock' => $product->stock,
                'category' => $product->category->name,
                'in_stock' => $product->stock > 0,
            ]),
            'meta' => [
                'current_page' => $products->currentPage(),
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'last_page' => $products->lastPage(),
            ],
        ]);
    }
}
