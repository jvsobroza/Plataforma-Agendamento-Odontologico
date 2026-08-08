<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Agendamento;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        $agendamento = Agendamento::all();
        return view("user.index", compact("users", "agendamento"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("user.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $user = User::create($request->validated());
        return redirect()->route('usuarios.index')->with('success', 'Usuário cadastrado com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);
        return view('user.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        return view('user.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, string $id)
    {
        $user = User::findOrFail($id);
        $user->update($request->validated());
        return redirect()->route('usuarios.index')->with('success', 'Usuário atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->update(['ativo' => false]);
        return redirect()->route('usuarios.index')->with('success', 'Usuário desativado com sucesso.');
    }
}
