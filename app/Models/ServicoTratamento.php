<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicoTratamento extends Model
{
    protected $table = 'servico_tratamentos';
    protected $fillable = [
        'id_planos',
        'id_servico',
        'id_agendamento',
        'tempo',
        'preco',
    ];

    public function servico()
    {
        return $this->belongsTo(Servico::class, 'id_servico');
    }

    public function planoTratamento()
    {
        return $this->belongsTo(PlanoTratamento::class, 'id_planos');
    }

    public function agendamento()
    {
        return $this->belongsTo(Agendamento::class, 'id_agendamento');
    }
}
