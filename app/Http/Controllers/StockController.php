<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock;

class StockController extends Controller
{
    public function index()
    {
        // Si tu utilises Livewire pour la page, cette vue devrait juste monter le composant.
        return view('stocks.index'); // dossier en minuscules
    }

    public function create()
    {
        return view('stocks.create'); // stocks/create.blade.php
    }

    public function store(Request $request)
    {
        // Validation + création
    }

    public function show(Stock $stock)
    {
        return view('stocks.show', compact('stock'));
    }

    public function edit(Stock $stock)
    {
        return view('stocks.edit', compact('stock'));
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
