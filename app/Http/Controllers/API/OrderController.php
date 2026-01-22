<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;

class OrderController extends Controller
{

    public function placeOrder(): \Illuminate\Http\JsonResponse
    {
        $cartItems = Cart::where('user_id', Auth::id())->with('product')->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item->product->price * $item->quantity;
        }

        $order = Order::create([
            'user_id' => Auth::id(),
            'total_amount' => $total,
            'status' => 'pending'
        ]);

        foreach ($cartItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->product->price
            ]);
        }

        Cart::where('user_id', Auth::id())->delete();

        return response()->json([
            'message' => 'Order placed successfully',
            'order' => $order
        ]);
    }

    public function history()
    {
        return Order::where('user_id', Auth::id())
            ->with('items.product')
            ->get();
    }

}
