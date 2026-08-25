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
        $filiais = Filial::with('servicos')->where('ativo', true)->get();
        $diaSemana = [
            00 => 'Domingo',
            01 => 'Segunda-feira',
            02 => 'Terça-feira',
            03 => 'Quarta-feira',
            04 => 'Quinta-feira',
            05 => 'Sexta-feira',
            06 => 'Sábado',
        ];
        foreach ($filiais as $filial) {
            $numeros = !empty($filial->datas_agenda) ? explode(';', $filial->datas_agenda) : [];
            $nomes = [];
            foreach ($numeros as $num) {
                $num = (int) $num; //erro se não convertesse pra inteiro
                if (isset($diaSemana[$num])) {
                    $nomes[] = $diaSemana[$num];
                }
            }
            $filial->dias_nomes = $nomes;
        }
        return view('filiais.index', compact('filiais'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('filiais.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFilialRequest $request)
    {
        $filial = Filial::create($request->validated());
        if ($request->filled('servicos')) {
            $servicos = explode(',', $request->servicos);
            $filial->servicos()->attach($servicos);
        } //faz insert na tabela pivo filial_servico
        return redirect()->route('filiais.index')->with('success', 'Filial cadastrada com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $filial = Filial::findOrFail($id);
        return view('filiais.show', compact('filial'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $filial = Filial::findOrFail($id);
        return view('filiais.edit', compact('filial'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFilialRequest $request, string $id)
    {
        $filial = Filial::findOrFail($id);
        $filial->update($request->validated());
        $filial->servicos()->sync($request->servicos); //atualiza a tabela pivo filial_servico
        return redirect()->route('filiais.index')->with('success', 'Filial atualizada com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $filial = Filial::findOrFail($id);
        $filial->update(['ativo' => false]);
        return redirect()->route('filiais.index')->with('success', 'Filial desativada com sucesso.');
    }
}
