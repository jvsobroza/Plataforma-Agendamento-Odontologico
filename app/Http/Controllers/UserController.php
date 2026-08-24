<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Agendamento;
use App\Models\Filial;
use App\Models\Paciente;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $hoje = Carbon::today();
        $agora = Carbon::now();
        $consultasHoje = Agendamento::whereDate('data_hora', $hoje)->where('ativo', true)->count();
        $totalPacientes = Paciente::where('ativo', true)->count();
        $consultasNoMes = Agendamento::whereMonth('data_hora', $hoje->month)->whereYear('data_hora', $hoje->year)->where('ativo', true)->count();
        $filials = Filial::where('ativo', true)->get();
        $filial = [];
        $diaHoje = now()->dayOfWeek - 1; //00 domingo, 01 segunda, 02 terça, 03 quarta, 04 quinta, 05 sexta, 06 sábado
        foreach ($filials as $f) {
            $filialData = explode(';', $f->datas_agenda);
            foreach ($filialData as $fd) {
                $data = strtolower($fd);
                if ($data == strtolower($diaHoje)) {
                    $filial[] = [
                        'id' => $f->id,
                        'cidade' => $f->cidade,
                        'endereco' => $f->endereco,
                        'datas_agenda' => $f->datas_agenda,
                        'servicos' => $f->servicos,
                    ];
                }
            }
        }
        $agendamentosHoje = Agendamento::with(['paciente', 'servicoTratamentos.servico'])
            ->whereDate('data_hora', $hoje)
            ->where('ativo', true)
            ->where('id_filial', $filial[0]['id'] ?? null) //filtra pelo ID da filial
            ->orderBy('data_hora')
            ->get();

        $agendaHoje = [];
        //agenda de hoje na dashboard
        foreach ($agendamentosHoje as $ag) {
            $status = strtolower($ag->status_agendamento);

            if ($status === 'cancelado') {
                $status = 'agendado';
            }

            if ($status == 'pendente' && $ag->data_hora->lessThanOrEqualTo($agora)) {
                $status = 'andamento';
            }

            $paciente = $ag->paciente->nome;
            $servicoTratamento = $ag->servicoTratamentos->first();
            $servico = 'Consulta';

            if ($servicoTratamento && $servicoTratamento->servico) {
                $servico = $servicoTratamento->servico->nome;
            }

            $agendaHoje[] = [
                'id' => $ag->id,
                'hora' => $ag->data_hora->format('H:i'),
                'paciente' => $paciente,
                'servico' => $servico,
                'status' => $status,
            ];
        } //parte da direita, próximos agendamentos
        $agendamentosProximos = Agendamento::with('paciente', 'servicoTratamentos.servico')
            ->whereDate('data_hora', '>', $hoje)
            ->where('ativo', true)
            ->orderBy('data_hora')
            ->take(6)
            ->get();

        $proximos = [];

        foreach ($agendamentosProximos as $ag) {
            $servicoTratamento = $ag->servicoTratamentos->first();
            $servico = 'Consulta';

            if ($servicoTratamento && $servicoTratamento->servico) { // se existe altera o nome da consulta
                $servico = $servicoTratamento->servico->nome;
            }
            //mais fácil de se organizar
            $proximos[] = [
                'dia'      => $ag->data_hora->format('d'),
                'mes'      => mb_strtoupper($ag->data_hora->translatedFormat('M')),
                'paciente' => $ag->paciente->nome,
                'hora'     => $ag->data_hora->format('H:i'),
                'servico'  => $servico,
            ];
        }

        return view('dentista.index', compact(
            'consultasHoje',
            'totalPacientes',
            'consultasNoMes',
            'agendaHoje',
            'proximos',
            'filial',
        ));
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
