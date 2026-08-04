<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanoTratamento extends Model
{
    protected $table = 'planos_tratamento';
    protected $fillable = [
        'id_paciente',
        'status',
        'servicos_planejados',
        'servicos_concluidos',
        'ativo',
];

    public function paciente()
    {
        return $this->hasOne(Paciente::class, 'id_paciente');
    }

    public function servicoTratamentos(){
        return $this->hasMany(ServicoTratamento::class, 'id_planos');
    }
}
