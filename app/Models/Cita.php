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

    /**
     * Check if there is an overlap with another appointment.
     */
    public static function hasOverlap($medicoId, $fecha, $hora, $duracionMinutos, $excludeCitaId = null): bool
    {
        $fechaStr = \Carbon\Carbon::parse($fecha)->format('Y-m-d');
        $newStart = \Carbon\Carbon::parse($hora);
        $newEnd = $newStart->copy()->addMinutes($duracionMinutos);

        $citas = self::where('medico_id', $medicoId)
            ->whereDate('fecha', $fechaStr)
            ->whereIn('estado', ['programada', 'completada'])
            ->when($excludeCitaId, function ($query) use ($excludeCitaId) {
                $query->where('id', '!=', $excludeCitaId);
            })
            ->get();

        foreach ($citas as $cita) {
            $existingStart = \Carbon\Carbon::parse($cita->hora);
            $existingEnd = $existingStart->copy()->addMinutes($cita->duracion_minutos);

            // Overlap condition: Start1 < End2 AND End1 > Start2
            if ($existingStart->lt($newEnd) && $existingEnd->gt($newStart)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the appointment is within the doctor's working hours.
     */
    public static function isWithinWorkingHours($medico, $hora, $duracionMinutos): bool
    {
        $start = \Carbon\Carbon::parse($hora);
        $end = $start->copy()->addMinutes($duracionMinutos);

        // Parse doctor working hours using the same date part as the start time to compare times
        $workStart = \Carbon\Carbon::parse($start->format('Y-m-d') . ' ' . $medico->hora_inicio_jornada);
        $workEnd = \Carbon\Carbon::parse($start->format('Y-m-d') . ' ' . $medico->hora_fin_jornada);

        return $start->greaterThanOrEqualTo($workStart) && $end->lessThanOrEqualTo($workEnd);
    }
}