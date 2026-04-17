<?php

use App\Models\User;
use App\Models\Paciente;
use App\Models\Medico;

return [

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
        'paciente' => [
            'driver' => 'session',
            'provider' => 'pacientes',
        ],
        'medico' => [
            'driver' => 'session',
            'provider' => 'medicos',
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => User::class ,
        ],
        'pacientes' => [
            'driver' => 'eloquent',
            'model' => Paciente::class ,
        ],
        'medicos' => [
            'driver' => 'eloquent',
            'model' => Medico::class ,
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];