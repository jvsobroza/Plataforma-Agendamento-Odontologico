<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFilialRequest;
use App\Http\Requests\UpdateFilialRequest;
use App\Models\Filial;
use Illuminate\Http\Request;

class FilialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $filials = Filial::all();
        return view("filial.index", compact("filials"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("filial.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFilialRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Filial $filial)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Filial $filial)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFilialRequest $request, Filial $filial)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Filial $filial)
    {
        //
    }
}
