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
use Carbon\Carbon;

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
    { //55 + DDD + número numero armazenado assim
        $mensagem = $request->input('payload.body');
        $remetente = $request->input('payload.from');
        $fromMe = $request->input('payload.fromMe');
        if ($fromMe) { //ignora se for o mesmo número mandando mensagem
            return response()->json([
                'status' => 'ok'
            ]);
        }
        if (str_ends_with($remetente, '@lid')) { //@lid identificador próprio do whatsapp
            $resposta = Http::withHeaders([
                'X-Api-Key' => env('WAHA_API_KEY')
            ])->get("http://localhost:3000/api/tcc/lids/{$remetente}");

            if ($resposta->successful()) {
                $remetente = $resposta->json('pn');
            }
        }
        $numero = str_replace('@c.us', '', $remetente); //@c.us identificador de contato
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
            if (!$this->dataValidate($mensagem)) {
                sleep(2);
                $this->enviarMensagem(
                    $numero,
                    "Data de nascimento inválida. Por favor, informe uma data válida no formato DD/MM/AAAA."
                );
                return response()->json([
                    'status' => 'ok'
                ]);
            }
            Log::info('Dados para cadastro do paciente', [
                'nome' => Cache::get('chatbot_nome_' . $numero),
                'cpf' => Cache::get('chatbot_cpf_' . $numero),
                'data_nascimento_recebida' => $mensagem,
                'telefone' => $numero,
                'observacoes_medicas' => "Nenhuma observação médica",
            ]);
            $dataNascimento = Carbon::createFromFormat('d/m/Y', $mensagem)
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
                "Olá, {$paciente->nome}!\n\nComo posso ajudar?\n\n1 - Agendar consulta\n2 - Consultar agendamento(s)\n3 - Cancelar agendamento\n4 - Falar com a clínica"
            );
            return response()->json([
                'status' => 'ok'
            ]);
        }
        if ($estado == 'atendimento_humano') {
            return response()->json([
                'status' => 'ok'
            ]);
        }
        if ($estado == 'cancelando_agendamento') {
            sleep(2);
            $cancelamento = Cache::get('cancelamento_' . $numero);
            $indice = (int) trim($mensagem) - 1;
            if (isset($cancelamento[$indice])) {
                Cache::put(
                    'agendamento_cancelar_' . $numero,
                    $cancelamento[$indice],
                    now()->addMinutes(30)
                );
                Cache::put($chaveConversa, 'confirmando_cancelamento', now()->addMinutes(30));
                $this->enviarMensagem(
                    $numero,
                    "Deseja realmente cancelar este agendamento?\n\nResponda SIM ou NÃO."
                );
            } else {
                $this->enviarMensagem($numero, "Número inválido. Escolha um agendamento da lista.");
            }
            return response()->json([
                'status' => 'ok'
            ]);
        }
        if ($estado == 'confirmando_cancelamento') {
            sleep(2);
            $resposta = mb_strtoupper(trim($mensagem));
            $agendamentoId = Cache::get('agendamento_cancelar_' . $numero);
            if ($resposta == 'SIM') {
                $agendamento = Agendamento::where('id', $agendamentoId)
                    ->where('id_paciente', $paciente->id)
                    ->where('data_hora', '>=', now()->addHours(48))
                    ->whereNotIn('status_agendamento', ['cancelado', 'concluido'])
                    ->first();
                if ($agendamento) {
                    $agendamento->update([
                        'status_agendamento' => 'cancelado'
                    ]);
                    $this->enviarMensagem(
                        $numero,
                        "Agendamento cancelado com sucesso!"
                    );
                } else {
                    $this->enviarMensagem(
                        $numero,
                        "Não foi possível cancelar o agendamento. Ele pode ter sido alterado ou não respeitar mais o prazo mínimo de 48 horas. /nPor favor, verifique seus agendamentos e tente novamente."
                    );
                }
                Cache::forget('agendamento_cancelar_' . $numero);
                Cache::put($chaveConversa, 'menu_principal', now()->addMinutes(30));
            } elseif ($resposta == 'NÃO' || $resposta == 'NAO') {
                Cache::forget('agendamento_cancelar_' . $numero);
                Cache::put($chaveConversa, 'menu_principal', now()->addMinutes(30));
                $this->enviarMensagem(
                    $numero,
                    "Cancelamento interrompido. Seu agendamento continua ativo. /nComo posso ajudar?\n\n1 - Agendar consulta\n2 - Consultar agendamento(s)\n3 - Cancelar agendamento\n4 - Falar com a clínica"
                );
            } else {
                $this->enviarMensagem(
                    $numero,
                    "Resposta inválida. Digite SIM para confirmar ou NÃO para desistir."
                );
            }
            return response()->json([
                'status' => 'ok'
            ]);
        }
        if ($estado == 'menu_principal') {
            switch (trim($mensagem)) {
                case '1': //agendar consulta
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
                case '2': //visualizar agendamentos
                    $agendamentos = Agendamento::where('id_paciente', $paciente->id)
                        ->where('data_hora', '>=', now())
                        ->orderBy('data_hora')
                        ->orderBy('status_agendamento')
                        ->get();
                    $agendamentosAntes = Agendamento::where('id_paciente', $paciente->id)->with(['servicoTratamentos.servico'])
                        ->where('data_hora', '<', now())
                        ->orderBy('data_hora')
                        ->orderBy('status_agendamento')
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
                                "*Data:* " . Carbon::parse($agendamento->data_hora)->format('d/m/Y') . "\n" .
                                "*Hora:* " . Carbon::parse($agendamento->data_hora)->format('H:i') . "\n" .
                                "*Filial:* " . $agendamento->filial->cidade . "\n" .
                                "*Endereço:* " . $agendamento->filial->endereco . "\n" .
                                "*Pagamento:* " . $agendamento->status_pagamento . "\n" .
                                "*Status:* " . ucfirst($agendamento->status_agendamento) . "\n" .
                                "*Observações:* " . ($agendamento->observacoes ?? 'Nenhuma') . "\n\n";
                        }
                        if ($agendamentosAntes->isNotEmpty()) {
                            $texto .= "Seus agendamentos passados:\n\n";
                            foreach ($agendamentosAntes as $agendamento) {
                                $texto .=
                                    "*Data:* " . Carbon::parse($agendamento->data_hora)->format('d/m/Y') . "\n" .
                                    "*Hora:* " . Carbon::parse($agendamento->data_hora)->format('H:i') . "\n" .
                                    "*Filial:* " . $agendamento->filial->cidade . "\n" .
                                    "*Endereço:* " . $agendamento->filial->endereco . "\n" .
                                    "*Pagamento:* " . $agendamento->status_pagamento . "\n" .
                                    "*Status:* " . ucfirst($agendamento->status_agendamento) . "\n" .
                                    "*Observações:* " . ($agendamento->observacoes ?? 'Nenhuma') . "\n";
                                $servicos = $agendamento->servicoTratamentos;
                                if ($servicos->isNotEmpty()) {
                                    $texto .=
                                        "*Serviço realizado:* " . $servicos->pluck('servico.nome')->join(', ') . "\n" .
                                        "*Preço total:* R$ " . number_format($servicos->sum('preco'), 2, ',', '.') . "\n\n";
                                } else {
                                    $texto .=
                                        "*Serviço realizado:* Não informado\n" .
                                        "*Preço total:* Não disponível\n\n";
                                }
                            }
                        }
                        $texto .= "*Para voltar ao menu principal, envie qualquer mensagem.*";
                        $this->enviarMensagem(
                            $numero,
                            $texto
                        );
                    }
                    break;
                case '3': //cancelar agendamento
                    sleep(2);
                    $agendamentos = Agendamento::where('id_paciente', $paciente->id)
                        ->with('filial')
                        ->where('data_hora', '>', now()->addHours(48))
                        ->whereNotIn('status_agendamento', ['cancelado', 'concluido'])
                        ->orderBy('data_hora')
                        ->get();
                    if ($agendamentos->isEmpty()) {
                        $this->enviarMensagem(
                            $numero,
                            "Você não possui agendamentos disponíveis para cancelamento.\n\nLembre-se: alterações precisam ser solicitadas com pelo menos 48 horas de antecedência. \n\nPara voltar ao menu principal, envie qualquer mensagem."
                        );
                        break;
                    }
                    Cache::put(
                        'cancelamento_' . $numero,
                        $agendamentos->pluck('id')->values()->all(), //pega os agendamentos e monta um array com os ids
                        now()->addMinutes(30)
                    );
                    Cache::put(
                        $chaveConversa,
                        'cancelando_agendamento',
                        now()->addMinutes(30)
                    );

                    $texto = "Estes são seus agendamentos disponíveis para cancelamento:\n\n";
                    foreach ($agendamentos as $indice => $agendamento) {
                        $texto .=
                            ($indice + 1) . " - " .
                            Carbon::parse($agendamento->data_hora)->format('d/m/Y \à\s H:i') . "\n" .
                            "Filial: " . $agendamento->filial->cidade . "\n" .
                            "Endereço: " . $agendamento->filial->endereco . "\n\n";
                    }
                    $texto .= "\nDigite o número do agendamento que deseja cancelar.";
                    $this->enviarMensagem($numero, $texto);
                    break;
                case '4': //falar com a clínica
                    Cache::put(
                        $chaveConversa,
                        'atendimento_humano',
                        now()->addMinutes(30)
                    );
                    sleep(2);
                    $this->enviarMensagem(
                        $numero,
                        "Certo! Sua conversa foi encaminhada para a clínica.\n\nAguarde um momento para ser atendido."
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

    protected function dataValidate($data) //verifica se a data está no formato correto, também se não é data futura
    {
        try {
            $data = \Carbon\Carbon::createFromFormat('d/m/Y', $data);
            if ($data->isFuture()) {
                return false;
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
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
}
