<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\SavingCategory;
use Illuminate\Http\Request;

class SavingCategoryController extends Controller
{
    // create saving category
public function create(Request $request)
{
    $admin = $request->user();

    if ($admin->role !== 'admin' && $admin->role !== 'super_admin') {
        abort(403, 'Unauthorised action');
    }

    $request->validate([
        'name'        => 'required|string|max:45',
        'description' => 'nullable|string|max:100',
    ]);

    $category = SavingCategory::create([
        'name'        => $request->name,
        'description' => $request->description,
    ]);

    return response()->json($category);
}

    // update category
  public function update(Request $request, SavingCategory $category)
{
    $admin = $request->user();

    if ($admin->role !== 'admin' && $admin->role !== 'super_admin') {
        abort(403, 'Unauthorised action');
    }

    $request->validate([
        'name'        => 'sometimes|string|max:45',
        'description' => 'sometimes|nullable|string|max:100',
    ]);

    $category->update([
        'name'        => $request->name ?? $category->name,
        'description' => $request->description ?? $category->description,
    ]);

    return response()->json([
        'message'  => 'Category updated successfully',
        'category' => $category,
    ]);
}
    // delete category
    public function destroy(Request $request,SavingCategory $category)
    {
                $admin=$request->user();
            if($admin->role !== 'admin' && $admin->role !== 'super_admin'){
                abort(403,'Unauthorised action');
            }
            // delete 
            $category->update([
                'is_active'=>false
            ]);
            // response
            return response()->json([
                'message' => 'Category deleted successifully',
                'category'=> $category
            ]);
    }

    // saving category
    public function index(Request $request)
    {
        $category = SavingCategory::where('is_active',true)->latest()->get();
        // response
        return response()->json($category);
    }
}
