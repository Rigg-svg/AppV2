<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Medico extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $fillable = [
        'nombre',
        'especialidad',
        'telefono',
        'email',
        'password',
        'numero_licencia',
        'activo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'activo' => 'boolean',
        ];
    }
}