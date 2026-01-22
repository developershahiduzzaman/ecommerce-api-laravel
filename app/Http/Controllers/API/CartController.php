<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // Add to cart
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $cart = Cart::where('user_id', Auth::id())
            ->where('product_id', $request->product_id)
            ->first();

        if ($cart) {
            $cart->increment('quantity');
        } else {
            Cart::create([
                'user_id' => Auth::id(),
                'product_id' => $request->product_id,
                'quantity' => 1
            ]);
        }

        return response()->json(['message' => 'Added to cart']);
    }

    // Cart list
    public function index()
    {
        return Cart::where('user_id', Auth::id())
            ->with('product')
            ->get();
    }

    // Update quantity
    public function update(Request $request, $id)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);

        $cart = Cart::where('user_id', Auth::id())->findOrFail($id);
        $cart->update(['quantity' => $request->quantity]);

        return response()->json(['message' => 'Cart updated']);
    }

    // Remove item
    public function destroy($id)
    {
        Cart::where('user_id', Auth::id())->findOrFail($id)->delete();
        return response()->json(['message' => 'Item removed']);
    }
}
