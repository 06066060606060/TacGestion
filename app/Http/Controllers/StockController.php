<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock;

class StockController extends Controller
{
    public function index()
    {
        return view('Stocks.index');
    }

    public function create()
    {
        return view('Stocks.create');
    }

    public function store(Request $request)
    {
        // Validation + création
    }

    public function show(Stock $stock)
    {
        return view('Stocks.show', compact('stock'));
    }

    public function edit(Stock $stock)
    {
        return view('Stocks.edit', compact('stock'));
    }

    public function update(Request $request, Stock $stock)
    {
        // Validation + update
    }

    public function destroy(Stock $stock)
    {
        // Suppression
    }
}

