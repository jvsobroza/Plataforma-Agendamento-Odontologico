<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Filial extends Model
{
    protected $table = 'filiais';
    protected $fillable = [
        'cidade',
        'endereco',
        'datas_agenda',
        'servicos',
        'ativo',
    ];

    public function usuarios()
    {
        return $this->hasMany(User::class, 'id_filial');
    }

    public function agendamentos()
    {
        return $this->hasMany(Agendamento::class, 'id_filial');
    }
}
