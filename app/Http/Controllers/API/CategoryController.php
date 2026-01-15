<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index() {
        return response()->json(Category::all());
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string|max:100'
        ]);

        return Category::create($request->all());
    }

    public function update(Request $request, $id) {
        $category = Category::findOrFail($id);
        $category->update($request->all());
        return $category;
    }

    public function destroy($id) {
        Category::destroy($id);
        return response()->json(['message'=>'Category deleted']);
    }
}
