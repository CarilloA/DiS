<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalesOrder;
use App\Models\SalesOrderDetail;
use App\Models\Product;

class SalesOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required',
            'products' => 'required|array',
        ]);

        // Create a new sales order
        $salesOrder = SalesOrder::create([
            'user_id' => $validated['user_id'],
            'total_amount' => $this->calculateTotal($validated['products']),
            'sales_date' => now(),
        ]);

        foreach ($validated['products'] as $product) {
            $productModel = Product::find($product['product_id']);
            
            // Create sales order detail
            SalesOrderDetail::create([
                'sales_order_id' => $salesOrder->id,
                'product_id' => $product['product_id'],
                'quantity' => $product['quantity'],
                'sales_price_per_unit' => $productModel->inventory->sales_price_per_unit,
            ]);

            // Decrease the in_stock amount in the inventory
            $productModel->inventory->decrement('in_stock', $product['quantity']);
        }

        return response()->json(['message' => 'Sales Order created successfully']);
    }

    private function calculateTotal($products)
    {
        $total = 0;
        foreach ($products as $product) {
            $productModel = Product::find($product['product_id']);
            $total += $product['quantity'] * $productModel->inventory->sales_price_per_unit;
        }
        return $total;
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
