<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agendamento extends Model
{
    protected $table = 'agendamentos';
    protected $fillable = [
        'id_paciente',
        'id_filial',
        'data_hora',
        'status_pagamento',
        'status_agendamento',
        'ativo',
        'observacoes',
    ];

    protected $casts = [
        'data_hora' => 'datetime',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'id_paciente');
    }

    public function filial()
    {
        return $this->belongsTo(Filial::class, 'id_filial');
    }

    public function servicoTratamentos()
    {
        return $this->hasMany(ServicoTratamento::class, 'id_agendamento');
    }
}
