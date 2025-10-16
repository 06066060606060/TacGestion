<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Colis;

class ColisController extends Controller
{
 public function index()
    {
        return view('colis.index');
    }

    public function create()
    {
        return view('colis.create');
    }

    public function store(Request $request)
    {
        // Validation + création
    }

    public function show(Colis $colis)
    {
        return view('colis.show', compact('colis'));
    }

    public function edit(Colis $colis)
    {
        return view('colis.edit', compact('colis'));
    }

    public function update(Request $request, Colis $colis)
    {
        // Validation + update
    }

    public function destroy(Colis $colis)
    {
        // Suppression
    }
}