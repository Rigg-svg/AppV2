<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Paciente extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $fillable = [
        'nombre',
        'telefono',
        'email',
        'password',
        'fecha_nacimiento',
        'sexo',
        'direccion',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'fecha_nacimiento' => 'date',
        ];
    }

    public function citas()
    {
        return $this->hasMany(Cita::class);
    }
}