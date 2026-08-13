<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['nome', 'email', 'senha'])]
#[Hidden(['senha', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
    protected $table = 'usuarios';
    protected $fillable = [
        'nome',
        'email',
        'senha',
        'tipo',
        'id_filial',
        'ativo',
    ];

    public function filial()
    {
        return $this->belongsTo(Filial::class, 'id_filial');
    }
    public function getAuthPasswordName() //sem isso login não autenticava por causa do nome senha ao invés de password
    {
        return 'senha';
    }
}