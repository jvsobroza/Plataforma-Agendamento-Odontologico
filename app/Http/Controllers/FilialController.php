<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFilialRequest;
use App\Http\Requests\UpdateFilialRequest;
use App\Models\Filial;

class FilialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $filials = Filial::all();
        return view('filial.index', compact('filials'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('filial.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFilialRequest $request)
    {
        $filial = Filial::create($request->validated());
        $filial->servicos()->attach($request->servicos); //faz insert na tabela pivo filial_servico
        return redirect()->route('filial.index')->with('success', 'Filial cadastrada com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $filial = Filial::findOrFail($id);
        return view('filial.show', compact('filial'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $filial = Filial::findOrFail($id);
        return view('filial.edit', compact('filial'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFilialRequest $request, string $id)
    {
        $filial = Filial::findOrFail($id);
        $filial->update($request->validated());
        $filial->servicos()->sync($request->servicos); //atualiza a tabela pivo filial_servico
        return redirect()->route('filial.index')->with('success', 'Filial atualizada com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $filial = Filial::findOrFail($id);
        $filial->update(['ativo' => false]);
        return redirect()->route('filial.index')->with('success', 'Filial desativada com sucesso.');
    }
}
