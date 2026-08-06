<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServicoTratamentoRequest;
use App\Http\Requests\UpdateServicoTratamentoRequest;
use App\Models\ServicoTratamento;
use Illuminate\Http\Request;

class ServicoTratamentoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $servicostratamentos = ServicoTratamento::all();
        return view("servicotratamento.index", compact("servicostratamentos"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("servicotratamento.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreServicoTratamentoRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ServicoTratamento $servicoTratamento)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ServicoTratamento $servicoTratamento)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateServicoTratamentoRequest $request, ServicoTratamento $servicoTratamento)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServicoTratamento $servicoTratamento)
    {
        //
    }
}
