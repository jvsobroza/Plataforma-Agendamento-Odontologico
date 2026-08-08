<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servico extends Model
{
    protected $table = 'servicos';
    protected $fillable = [
        'nome',
        'ativo',
    ];

    public function servicosTratamento()
    {
        return $this->hasMany(ServicoTratamento::class, 'id_servico');
    }
    public function filiais()
    {
        return $this->belongsToMany(Filial::class, 'filial_servico', 'id_servico', 'id_filial');
    }
}
