<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePacienteRequest;
use App\Http\Requests\UpdatePacienteRequest;
use App\Models\Paciente;
use Illuminate\Http\Request;

class PacienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pacientes = Paciente::all();
        return view("pacientes.index", compact("pacientes"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("pacientes.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePacienteRequest $request)
    {
        if (!$this->isValidate('cpf', $request->validated()['cpf'])) {
            return redirect()->back()
                ->withErrors(['cpf' => 'O CPF informado não é válido.'])
                ->withInput();
        }

        $paciente = Paciente::create($request->validated());
        return redirect()->route('pacientes.index')->with('success', 'Paciente cadastrado com sucesso.');
    }

    protected function isValidate($attribute, $value) //https://www.guj.com.br/t/laravel-validar-cpf/382494/
    {
        $c = preg_replace('/\D/', '', $value);
        if (strlen($c) != 11 || preg_match("/^{$c[0]}{11}$/", $c)) {
            return false;
        }
        for ($s = 10, $n = 0, $i = 0; $s >= 2; $n += $c[$i++] * $s--);
        if ($c[9] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
            return false;
        }
        for ($s = 11, $n = 0, $i = 0; $s >= 2; $n += $c[$i++] * $s--);
        if ($c[10] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
            return false;
        }
        return true;
    }

    /**
     * Display the specified resource.
     */
    public function show(Paciente $paciente)
    {
        $paciente = Paciente::with(['planoTratamento'])->findOrFail($paciente->id);
        return view('pacientes.show', compact('paciente'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Paciente $paciente)
    {
        $paciente = Paciente::with(['planoTratamento'])->findOrFail($paciente->id);
        return view('pacientes.edit', compact('paciente'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePacienteRequest $request, Paciente $paciente)
    {
        $paciente->update($request->validated());
        return redirect()->route('pacientes.index')->with('success', 'Paciente atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Paciente $paciente)
    {
        $paciente = Paciente::findOrFail($paciente->id);
        $paciente->update(['ativo' => false]);
        return redirect()->route('pacientes.index')->with('success', 'Paciente desativado com sucesso.');
    }

    public function webhook(Request $request) {}
}
