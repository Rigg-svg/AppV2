<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cita extends Model
{
    // id paciente_id medico_id fecha hora estado motivo notas duracion_minutos created_at updated_at deleted_at
    use SoftDeletes;

    protected $fillable = [
        'paciente_id',
        'medico_id',
        'fecha',
        'hora',
        'estado',
        'motivo',
        'notas',
        'duracion_minutos',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'hora' => 'datetime:H:i',
        ];
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }
    public function medico()
    {
        return $this->belongsTo(Medico::class);
    }

    public function scopeProgramadas($query)
    {
        return $query->where('estado', 'programada');
    }

    public function scopeCanceladas($query)
    {
        return $query->where('estado', 'cancelada');
    }

    public function scopeCompletadas($query)
    {
        return $query->where('estado', 'completada');
    }
}