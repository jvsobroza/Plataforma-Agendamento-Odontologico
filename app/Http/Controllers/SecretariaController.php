<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Filial;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;

class SecretariaController extends Controller
{
    public function index()
    {
        $secretaria = User::with('filial')->where('tipo', 2)->where('ativo', true)->get();
        $secretariaDesativada = User::with('filial')->where('tipo', 2)->where('ativo', false)->get();
        return view('secretarias.index', compact('secretaria', 'secretariaDesativada'));
    }

    public function create()
    {
        $filiais = Filial::where('ativo', true)->get();
        return view('secretarias.create', compact('filiais'));
    }

    public function edit(string $id)
    {
        $secretaria = User::with('filial')->where('tipo', 2)->findOrFail($id);
        $filiais = Filial::where('ativo', true)->get();
        return view('secretarias.edit', compact('secretaria', 'filiais'));
    }

    public function update(UpdateUserRequest $request, string $id)
    {
        $secretaria = User::findOrFail($id);
        $dados = $request->validated();
        if (!isset($dados['senha'])) { //se não vem senha, não será atualizada
            unset($dados['senha']);
        }
        $secretaria->update($dados);
        return redirect()->route('dentista.secretarias.index')->with('success', 'Secretária atualizada com sucesso.');
    }

    public function store(StoreUserRequest $request)
    {
        $user = User::create($request->validated());
        return redirect()->route('dentista.secretarias.index')->with('success', 'Secretária cadastrada com sucesso.');
    }

    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->update(['ativo' => false]);
        return redirect()->route('dentista.secretarias.index')->with('success', 'Secretária desativada com sucesso.');
    }

    public function restore(string $id)
    {
        $user = User::findOrFail($id);
        $user->update(['ativo' => true]);
        return redirect()->route('dentista.secretarias.index')->with('success', 'Secretária reativada com sucesso.');
    }
}