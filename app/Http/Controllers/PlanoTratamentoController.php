<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlanoTratamentoRequest;
use App\Http\Requests\UpdatePlanoTratamentoRequest;
use App\Models\PlanoTratamento;
use Illuminate\Http\Request;

class PlanoTratamentoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $planotratamentos = PlanoTratamento::all();
        return view("planotratamento.index", compact("planotratamentos"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("planotratamento.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePlanoTratamentoRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(PlanoTratamento $planoTratamento)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PlanoTratamento $planoTratamento)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePlanoTratamentoRequest $request, PlanoTratamento $planoTratamento)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PlanoTratamento $planoTratamento)
    {
        //
    }
}
