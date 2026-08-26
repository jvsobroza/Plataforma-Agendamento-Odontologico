<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFilialRequest;
use App\Http\Requests\UpdateFilialRequest;
use App\Models\Filial;
use App\Models\Servico;

class FilialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $filiais = Filial::with('servicosFilial')->where('ativo', true)->get();
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
        $servicos = Servico::where('ativo', true)->get();

        return view('filiais.create', compact('servicos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFilialRequest $request)
    {
        $dados = $request->validated();
        $dados['datas_agenda'] = implode(';', $dados['datas_agenda']);
        $dados['servicos'] = implode(';', $dados['servicos']);
        $filial = Filial::create($dados);
        $filial->servicosFilial()->attach(explode(';', $dados['servicos'])); //insere na tabela pivot
        return redirect()->route('dentista.filiais.index')->with('success', 'Filial cadastrada com sucesso.');
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
        $servicos = Servico::where('ativo', true)->get();
        $servicosSelecionados = [];
        foreach ($filial->servicosFilial as $servico) {
            $servicosSelecionados[] = $servico->id;
        }
        $diasSelecionados = [];
        if (!empty($filial->datas_agenda)) {
            foreach (explode(';', $filial->datas_agenda) as $dia) {
                $diasSelecionados[] = (int) $dia;
            }
        }
        return view('filiais.edit', compact('filial', 'servicos', 'diasSelecionados', 'servicosSelecionados'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFilialRequest $request, string $id)
    {
        $filial = Filial::findOrFail($id);
        $dados = $request->validated();
        $servicos = $dados['servicos'];
        $dados['datas_agenda'] = implode(';', $dados['datas_agenda']);
        $dados['servicos'] = implode(';', $servicos);
        $filial->update($dados);
        $filial->servicosFilial()->sync($servicos);
        return redirect()->route('dentista.filiais.index')->with('success', 'Filial atualizada com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $filial = Filial::findOrFail($id);
        $filial->update(['ativo' => false]);
        return redirect()->route('dentista.filiais.index')->with('success', 'Filial desativada com sucesso.');
    }
}
