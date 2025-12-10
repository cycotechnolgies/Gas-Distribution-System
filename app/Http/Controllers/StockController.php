<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index()
    {
        $stocks = \App\Models\Stock::with('gasType')->get();
        return view('stocks.index', compact('stocks'));
    }

}
