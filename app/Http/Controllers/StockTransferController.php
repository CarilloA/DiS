<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockTransfer;
use App\Models\Stockroom;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class StockTransferController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transfers = StockTransfer::with('from_stockroom', 'to_stockroom', 'product')->get();
        return view('inventory.stock_transfers', ['transfers' => $transfers]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $stockrooms = Stockroom::all();
        $products = Product::all();
        return view('inventory.create_transfer', compact('stockrooms', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'transfer_quantity' => 'required|numeric|min:1',
            'from_stockroom_id' => 'required|exists:stockroom,stockroom_id',
            'to_stockroom_id' => 'required|exists:stockroom,stockroom_id|different:from_stockroom_id',
            'product_id' => 'required|exists:product,product_id',
        ]);

        DB::transaction(function () use ($validatedData) {
            // Reduce stock from source stockroom
            $fromStockroom = Stockroom::find($validatedData['from_stockroom_id']);
            $fromStockroom->product_quantity -= $validatedData['transfer_quantity'];
            $fromStockroom->save();

            // Increase stock in destination stockroom
            $toStockroom = Stockroom::find($validatedData['to_stockroom_id']);
            $toStockroom->product_quantity += $validatedData['transfer_quantity'];
            $toStockroom->save();

            // Record the stock transfer
            StockTransfer::create([
                'stock_transfer_id' => $this->generateId('stock_transfer'),
                'transfer_quantity' => $validatedData['transfer_quantity'],
                'transfer_date' => now(),
                'from_stockroom_id' => $validatedData['from_stockroom_id'],
                'to_stockroom_id' => $validatedData['to_stockroom_id'],
                'product_id' => $validatedData['product_id'],
            ]);
        });

        return redirect()->route('stock_transfers')->with('success', 'Stock transfer successful.');
    }

    private function generateId($table)
    {
        // Similar to your custom ID generation logic
        $currentMaxId = StockTransfer::max('stock_transfer_id');
        $newId = ($currentMaxId !== null) ? $currentMaxId + 1 : 1;
        return $newId;
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
