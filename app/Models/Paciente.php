<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    protected $table = 'pacientes';
    protected $fillable = [
        'nome',
        'cpf',
        'telefone',
        'observacoes_medicas',
        'data_nascimento',
        'ativo',
    ];

    public function agendamentos()
    {
        return $this->hasMany(Agendamento::class, 'id_paciente');
    }

    public function planos()
    {
        return $this->hasMany(PlanoTratamento::class, 'id_paciente');
    }
}
