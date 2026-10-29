<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePacienteRequest;
use App\Http\Requests\UpdatePacienteRequest;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\Agendamento;

class PacienteController extends Controller
{
    public function index()
    {
        return response()->json(
            Paciente::where('ativo', true)->paginate(15)
        );
    }

    public function store(StorePacienteRequest $request)
    {
        $data = $request->validated();
        $data['data_nascimento'] = \Carbon\Carbon::createFromFormat('d/m/Y', $request->input('data_nascimento'))
            ->format('Y-m-d');
        $paciente = Paciente::create($data);
        return response()->json($paciente, 201);
    }

    public function show(string $id)
    {
        return response()->json(
            Paciente::with(['agendamentos', 'planosTratamento'])->findOrFail($id)
        );
    }

    public function update(UpdatePacienteRequest $request, string $id)
    {
        $paciente = Paciente::findOrFail($id);
        $paciente->update($request->validated());

        return response()->json($paciente);
    }

    public function destroy(string $id)
    {
        $paciente = Paciente::findOrFail($id);
        $paciente->update(['ativo' => false]);

        return response()->json(['message' => 'Paciente desativado com sucesso.']);
    }
    public function webhook(Request $request)
    {
        $mensagem = $request->input('payload.body');
        $remetente = $request->input('payload.from');
        $fromMe = $request->input('payload.fromMe');
        if ($fromMe) {
            return response()->json([
                'status' => 'ok'
            ]);
        }
        if (str_ends_with($remetente, '@lid')) {
            $resposta = Http::withHeaders([
                'X-Api-Key' => env('WAHA_API_KEY')
            ])->get("http://localhost:3000/api/tcc/lids/{$remetente}");

            if ($resposta->successful()) {
                $remetente = $resposta->json('pn');
            }
        }
        $numero = str_replace('@c.us', '', $remetente);
        $chaveConversa = 'chatbot_' . $numero;
        $estado = Cache::get($chaveConversa);
        Log::info('Estado atual do chatbot', [
            'numero' => $numero,
            'estado' => $estado,
            'mensagem' => $mensagem,
        ]);
        if ($estado == 'aguardando_nome') {
            Cache::put(
                'chatbot_nome_' . $numero,
                $mensagem,
                now()->addMinutes(30)
            );
            Cache::put(
                $chaveConversa,
                'aguardando_cpf',
                now()->addMinutes(30)
            );
            sleep(2);
            $this->enviarMensagem(
                $numero,
                "Obrigado! Agora informe seu CPF."
            );
            return response()->json([
                'status' => 'ok'
            ]);
        }
        if ($estado == 'aguardando_cpf') {
            $cpf = preg_replace('/\D/', '', $mensagem);
            if (!$this->isValidate('cpf', $cpf)) {
                sleep(2);
                $this->enviarMensagem(
                    $numero,
                    "CPF inválido. Por favor, informe um CPF válido."
                );
                return response()->json([
                    'status' => 'ok'
                ]);
            }
            Cache::put(
                'chatbot_cpf_' . $numero,
                $mensagem,
                now()->addMinutes(30)
            );
            Cache::put(
                $chaveConversa,
                'aguardando_data_nascimento',
                now()->addMinutes(30)
            );
            sleep(2);
            $this->enviarMensagem(
                $numero,
                "Certo! Agora informe sua data de nascimento no formato DD/MM/AAAA."
            );
            return response()->json([
                'status' => 'ok'
            ]);
        }
        if ($estado == 'aguardando_data_nascimento') {
            Log::info('Dados para cadastro do paciente', [
                'nome' => Cache::get('chatbot_nome_' . $numero),
                'cpf' => Cache::get('chatbot_cpf_' . $numero),
                'data_nascimento_recebida' => $mensagem,
                'telefone' => $numero,
                'observacoes_medicas' => "Nenhuma observação médica",
            ]);
            $dataNascimento = \Carbon\Carbon::createFromFormat('d/m/Y', $mensagem)
                ->format('Y-m-d');

            Log::info('Data convertida', [
                'data_nascimento' => $dataNascimento,
            ]);
            $paciente = Paciente::create([
                'nome' => Cache::get('chatbot_nome_' . $numero),
                'cpf' => Cache::get('chatbot_cpf_' . $numero),
                'data_nascimento' => $dataNascimento,
                'telefone' => $numero,
                'observacoes_medicas' => null,
            ]);
            sleep(2);
            $this->enviarMensagem(
                $numero,
                "Cadastro realizado com sucesso, {$paciente->nome}!\n\nComo posso ajudar?\n\n1 - Agendar consulta\n2 - Consultar agendamento\n3 - Cancelar agendamento\n4 - Falar com a clínica"
            );
            Log::info('Paciente cadastrado', [
                'paciente' => $paciente->toArray()
            ]);
            return response()->json([
                'status' => 'ok'
            ]);
        }
        $paciente = Paciente::where('telefone', $numero)->first();
        if (!$paciente) {
            Cache::put(
                $chaveConversa,
                'aguardando_nome',
                now()->addMinutes(30)
            );
            sleep(2);
            $this->enviarMensagem(
                $numero,
                "Olá! Não encontramos um cadastro vinculado a este número.\n\nPara continuar, informe seu nome completo."
            );
            return response()->json([
                'status' => 'ok'
            ]);
        }
        if (!Cache::has($chaveConversa)) {
            Cache::put(
                $chaveConversa,
                'menu_principal',
                now()->addMinutes(30)
            );
            sleep(2);
            $this->enviarMensagem(
                $numero,
                "Olá, {$paciente->nome}!\n\nComo posso ajudar?\n\n1 - Agendar consulta\n2 - Consultar agendamento\n3 - Cancelar agendamento\n4 - Falar com a clínica"
            );
            return response()->json([
                'status' => 'ok'
            ]);
        }
        if ($estado == 'menu_principal') {
            switch (trim($mensagem)) {
                case '1':
                    Cache::put(
                        $chaveConversa,
                        'agendando_consulta',
                        now()->addMinutes(30)
                    );
                    sleep(2);
                    $this->enviarMensagem(
                        $numero,
                        "Certo! Vamos iniciar seu agendamento."
                    );
                    break;
                case '2':
                    $agendamentos = Agendamento::where('id_paciente', $paciente->id)
                        ->where('data_hora', '>=', now())
                        ->orderBy('data_hora')
                        ->get();
                    sleep(2);
                    if ($agendamentos->isEmpty()) {
                        $this->enviarMensagem(
                            $numero,
                            "Você não possui agendamentos futuros."
                        );
                    } else {
                        $texto = "Seus próximos agendamentos:\n\n";
                        foreach ($agendamentos as $agendamento) {
                            $texto .=
                                "Data: " . \Carbon\Carbon::parse($agendamento->data_hora)->format('d/m/Y') . "\n" .
                                "Pagamento: " . $agendamento->status_pagamento . "\n" .
                                "Status: " . $agendamento->status_agendamento . "\n" .
                                "Observações: " . ($agendamento->observacoes ?? 'Nenhuma') . "\n\n";
                        }
                        $this->enviarMensagem(
                            $numero,
                            $texto
                        );
                    }

                    break;
                case '3':
                    sleep(2);
                    $this->enviarMensagem(
                        $numero,
                        "Certo! Vamos verificar seus agendamentos para cancelamento."
                    );
                    break;
                case '4':
                    sleep(2);
                    $this->enviarMensagem(
                        $numero,
                        "Certo! Aguarde para falar com a clínica."
                    );
                    break;
                default:
                    sleep(2);
                    $this->enviarMensagem(
                        $numero,
                        "Opção inválida.\n\n1 - Agendar consulta\n2 - Consultar agendamento\n3 - Cancelar agendamento\n4 - Falar com a clínica"
                    );
                    break;
            }
            return response()->json([
                'status' => 'ok'
            ]);
        }
        Log::info('Mensagem recebida', [
            'numero' => $numero,
            'mensagem' => $mensagem,
            'estado' => $estado,
        ]);
        return response()->json([
            'status' => 'ok'
        ]);
    }

    private function enviarMensagem(string $numero, string $mensagem): void
    {
        Http::withHeaders([
            'X-Api-Key' => env('WAHA_API_KEY')
        ])->post(env('WAHA_API_URL') . '/api/sendText', [
                    'session' => env('WAHA_SESSION'),
                    'chatId' => $numero . '@c.us',
                    'text' => $mensagem
                ]);
    }

    protected function isValidate($attribute, $value) //https://www.guj.com.br/t/laravel-validar-cpf/382494/
    {
        $c = preg_replace('/\D/', '', $value);
        if (strlen($c) != 11 || preg_match("/^{$c[0]}{11}$/", $c)) {
            return false;
        }
        for ($s = 10, $n = 0, $i = 0; $s >= 2; $n += $c[$i++] * $s--)
            ;
        if ($c[9] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
            return false;
        }
        for ($s = 11, $n = 0, $i = 0; $s >= 2; $n += $c[$i++] * $s--)
            ;
        if ($c[10] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
            return false;
        }
        return true;
    }
}
