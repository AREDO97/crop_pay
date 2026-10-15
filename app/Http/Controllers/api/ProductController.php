<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // create product
    public function create(Request $request)
    {
        $admin = $request->user();

    if ($admin->role !== 'admin' && $admin->role !== 'super_admin') {
        abort(403, 'Unauthorised action');
    }

    $request->validate([
        'name'        => 'required|string|max:45',
        'description' => 'nullable|string|max:100',
        'unit'        => 'nullable|string|max:100',
    ]);
    // create product
    $product = Product::create([
        'name'        =>$request->name,
        'description' =>$request->description,
        'unit'        =>$request->unit
    ]);
    // response
    return response()->json([
        'message' =>'Product created successifully',
        'product' =>$product
    ]);
    }
    // update product
    public function update(Request $request,Product $product)
    {
           $admin = $request->user();

    if ($admin->role !== 'admin' && $admin->role !== 'super_admin') {
        abort(403, 'Unauthorised action');
    }

    $request->validate([
        'name'        => 'nullable|string|max:45',
        'description' => 'nullable|string|max:100',
        'unit'        => 'nullable|string|max:100',
    ]);
    // create product
    $product->update([
        'name'        =>$request->name ?? $product->name,
        'description' =>$request->description ?? $product->description,
        'unit'        =>$request->unit ?? $product->unit
    ]);
    // response
    return response()->json([
        'message' =>'Product updated successifully',
        'product' =>$product
    ]);

    }
    // view all products
    public function index(Request $request)
    {
        // all products
        $products = Product::where('is_active',true)->latest()->get();
        // response
        return response()->json($products);
    }
    // soft delete a product
    public function destroy(Request $request,Product $product)
    {
          $admin = $request->user();

    if ($admin->role !== 'admin' && $admin->role !== 'super_admin') {
        abort(403, 'Unauthorised action');
    }
    // change product status
    $product->update([
        'is_active' => false
    ]);
    // response
    return response()->json([
        'message' => 'Product deleted successifully',
        'product' => $product
    ]);
    }
}
