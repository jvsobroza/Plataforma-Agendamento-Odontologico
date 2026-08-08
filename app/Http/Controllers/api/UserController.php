<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(
            User::where('ativo', true)->with('filial')->paginate(15)
        );
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $data['senha'] = Hash::make($data['senha']);

        $user = User::create($data);

        return response()->json($user, 201);
    }

    public function show(string $id)
    {
        return response()->json(User::with('filial')->findOrFail($id));
    }

    public function update(UpdateUserRequest $request, string $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validated();

        if (isset($data['senha'])) {
            $data['senha'] = Hash::make($data['senha']);
        }

        $user->update($data);

        return response()->json($user);
    }

    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->update(['ativo' => false]);

        return response()->json(['message' => 'Usuário desativado com sucesso.']);
    }
}