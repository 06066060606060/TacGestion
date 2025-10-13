<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feu;

class FeuController extends Controller
{
    public function index()
    {
        return view('feux.index');
    }

    public function create()
    {
        return view('feux.create');
    }

    public function store(Request $request)
    {
        // Validation + création
    }

    public function show(Feu $feu)
    {
        return view('feux.show', compact('feu'));
    }

    public function edit(Feu $feu)
    {
        return view('feux.edit', compact('feu'));
    }

    public function update(Request $request, Feu $feu)
    {
        // Validation + update
    }

    public function destroy(Feu $feu)
    {
        // Suppression
    }
}

