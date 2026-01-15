<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function index() {
        return response()->json(Product::with('category')->get());
    }

    public function store(Request $request) {
        $request->validate([
            'name'=>'required',
            'price'=>'required|numeric',
            'category_id'=>'required'
        ]);

        return Product::create($request->all());
    }

    public function update(Request $request, $id) {
        $product = Product::findOrFail($id);
        $product->update($request->all());
        return $product;
    }

    public function destroy($id) {
        Product::destroy($id);
        return response()->json(['message'=>'Product deleted']);
    }
}
