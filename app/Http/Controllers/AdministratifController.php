<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Administratif;
class AdministratifController extends Controller
{
    public function index()
    {
        return view('administratifs.index');
    }

    public function create()
    {
        return view('administratifs.create');
    }

    public function store(Request $request)
    {
        // Validation + création
    }

    public function show(Administratif $administratif)
    {
        return view('administratifs.show', compact('administratif'));
    }

    public function edit(Administratif $administratif)
    {
        return view('administratifs.edit', compact('administratif'));
    }

    public function update(Request $request, Administratif $administratif)
    {
        // Validation + update
    }

    public function destroy(Administratif $administratif)
    {
        // Suppression
    }
}
