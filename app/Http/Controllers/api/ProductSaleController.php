<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\ProductSale;
use App\Models\User;
use App\Notifications\productSaleNotification;
use App\Notifications\saleReceivedNotification;
use Illuminate\Http\Request;

class ProductSaleController extends Controller
{
    //create a product sale 
   
        public function create(Request $request)
        {
            $user = $request->user();
            //validation
            $request->validate([
               'location'            => 'required|string|max:45',
               'brief_notes'         => 'nullable|string|max:100',
               'quantity'            => 'numeric', 
               'expected_unit_price' =>'required|numeric'
            ]);
            // create product sale
            $productSale = ProductSale::create([
                'user_id' =>$user->id,
                'product_id' =>$request->product_id,
                'quantity' =>$request->quantity,
                'expected_unit_price' =>$request->expected_unit_price,
                'expected_total_amount' =>($request->quantity * $request->expected_unit_price),
                'location' =>$request->location,
                'unit'=>$request->unit,
                'ready_at_date'=>$request->ready_at_date,
                'brief_notes' =>$request->brief_notes
            ]);
            // notify admins
            $admins = User::whereIn('role',['admin','super_admin'])->get();
            foreach($admins as $admin)
                {
                    $admin->notify(
                     new productSaleNotification($user->name)
                    );
                }
                // notify user that sell request is received
                $user->notify(
                    new saleReceivedNotification($user->name)
                );
            // response
            return response()->json([
                'message'      => "Product placed on sale",
                'product_sale' =>$productSale
            ]);
        }
        // farmer product sales
        public function index(Request $request)
        {
            // current user
            $user = $request->user();
            $sales = $user->productSales;
            //response
            return response()->json($sales);
        }
        // all product sales 
        public function allProductSales(Request $request)
        {
            // current user
             $admin = $request->user();

    if ($admin->role !== 'admin' && $admin->role !== 'super_admin') {
        abort(403, 'Unauthorised action');
    }
        // sales
        $sales = ProductSale::latest()->paginate(10);
        // response
        return response()->json($sales);
        }
}
