<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Medico;
use App\Models\Paciente;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CitaController extends Controller
{
    // Rango de horarios permitido para pacientes
    private const PACIENTE_HORA_INICIO = '08:00';
    private const PACIENTE_HORA_FIN = '16:30';
    private const PACIENTE_DURACION_SLOT = 30;

    // Determina el usuario autenticado y su tipo
    private function usuarioActual(): array
    {
        if (Auth::guard('paciente')->check()) {
            return ['usuario' => Auth::guard('paciente')->user(), 'tipo' => 'paciente'];
        }
        return ['usuario' => Auth::guard('medico')->user(), 'tipo' => 'medico'];
    }

    public function index()
    {
        ['usuario' => $usuario, 'tipo' => $tipo] = $this->usuarioActual();

        if ($tipo === 'paciente') {
            $citas = $usuario->citas()->with('medico')->orderBy('fecha')->orderBy('hora')->get();
        }
        else {
            $citas = $usuario->citas()->with('paciente')->orderBy('fecha')->orderBy('hora')->get();
        }

        return view('citas.index', compact('citas', 'tipo'));
    }

    // Formulario para crear cita
    public function create()
    {
        ['tipo' => $tipo] = $this->usuarioActual();

        $medicos = Medico::all();
        $pacientes = Paciente::all();

        return view('citas.create', compact('medicos', 'pacientes', 'tipo'));
    }

    // Guardar nueva cita
    public function store(Request $request)
    {
        ['usuario' => $usuario, 'tipo' => $tipo] = $this->usuarioActual();

        $request->validate([
            'fecha' => ['required', 'date', 'after_or_equal:today'],
            'hora' => ['required'],
            'motivo' => ['required', 'string', 'max:500'],
            'duracion_minutos' => ['nullable', 'integer', 'min:15', 'max:180'],
            'paciente_id' => ['required_if:tipo,medico', 'exists:pacientes,id'],
            'medico_id' => ['required_if:tipo,paciente', 'exists:medicos,id'],
        ]);

        $medicoId = $tipo === 'medico' ? $usuario->id : $request->medico_id;
        $medico = $tipo === 'medico' ? $usuario : Medico::findOrFail($medicoId);
        $duracion = $request->duracion_minutos ?? 30;

        // Validación estricta de rango horario para pacientes (08:00 - 16:30)
        if ($tipo === 'paciente') {
            $horaReq = Carbon::parse($request->hora);
            $limiteInicio = Carbon::parse(self::PACIENTE_HORA_INICIO);
            $limiteFin = Carbon::parse(self::PACIENTE_HORA_FIN);
            $horaFinCita = $horaReq->copy()->addMinutes($duracion);

            if ($horaReq->lt($limiteInicio) || $horaFinCita->gt($limiteFin->copy()->addMinutes($duracion))) {
                return back()->withErrors([
                    'hora' => 'Solo puedes reservar citas entre las ' . self::PACIENTE_HORA_INICIO . ' y las ' . self::PACIENTE_HORA_FIN . '.'
                ])->withInput();
            }
        }

        // Validar si la cita está dentro de la jornada laboral del médico
        if (!Cita::isWithinWorkingHours($medico, $request->hora, $duracion)) {
            $jornada = substr($medico->hora_inicio_jornada, 0, 5) . ' - ' . substr($medico->hora_fin_jornada, 0, 5);
            return back()->withErrors([
                'hora' => "La hora seleccionada está fuera de la jornada laboral de este médico ({$jornada})."
            ])->withInput();
        }

        // Validar que no haya solapamiento (doble reserva)
        if (Cita::hasOverlap($medicoId, $request->fecha, $request->hora, $duracion)) {
            return back()->withErrors([
                'hora' => 'El médico ya tiene otra cita programada en este horario (doble reserva).'
            ])->withInput();
        }

        Cita::create([
            'paciente_id' => $tipo === 'paciente' ? $usuario->id : $request->paciente_id,
            'medico_id' => $medicoId,
            'fecha' => $request->fecha,
            'hora' => $request->hora,
            'motivo' => $request->motivo,
            'duracion_minutos' => $duracion,
            'estado' => 'programada',
        ]);

        return redirect()->route('citas.index')->with('success', 'Cita creada correctamente.');
    }

    //Ver detalle de una cita
    public function show(Cita $cita)
    {
        ['usuario' => $usuario, 'tipo' => $tipo] = $this->usuarioActual();

        // Verifica que la cita pertenezca al usuario autenticado
        if ($tipo === 'paciente' && $cita->paciente_id !== $usuario->id) {
            abort(403);
        }
        if ($tipo === 'medico' && $cita->medico_id !== $usuario->id) {
            abort(403);
        }

        $cita->load('paciente', 'medico');

        return view('citas.show', compact('cita', 'tipo'));
    }

    // Formulario para editar cita
    public function edit(Cita $cita)
    {
        ['usuario' => $usuario, 'tipo' => $tipo] = $this->usuarioActual();

        if ($tipo === 'paciente' && $cita->paciente_id !== $usuario->id) {
            abort(403);
        }
        if ($tipo === 'medico' && $cita->medico_id !== $usuario->id) {
            abort(403);
        }

        if ($cita->estado !== 'programada') {
            return redirect()->route('citas.index')->with('error', 'Solo se pueden editar citas programadas.');
        }

        $medicos = Medico::all();
        $pacientes = Paciente::all();

        return view('citas.edit', compact('cita', 'medicos', 'pacientes', 'tipo'));
    }

    // Guardar cambios de la cita
    public function update(Request $request, Cita $cita)
    {
        ['usuario' => $usuario, 'tipo' => $tipo] = $this->usuarioActual();

        if ($tipo === 'paciente' && $cita->paciente_id !== $usuario->id) {
            abort(403);
        }
        if ($tipo === 'medico' && $cita->medico_id !== $usuario->id) {
            abort(403);
        }

        $request->validate([
            'fecha' => ['required', 'date', 'after_or_equal:today'],
            'hora' => ['required'],
            'motivo' => ['required', 'string', 'max:500'],
            'duracion_minutos' => ['nullable', 'integer', 'min:15', 'max:180'],
        ]);

        $medico = $cita->medico;
        $duracion = $request->duracion_minutos ?? $cita->duracion_minutos;

        // Validación estricta de rango horario para pacientes (08:00 - 16:30)
        if ($tipo === 'paciente') {
            $horaReq = Carbon::parse($request->hora);
            $limiteInicio = Carbon::parse(self::PACIENTE_HORA_INICIO);
            $limiteFin = Carbon::parse(self::PACIENTE_HORA_FIN);
            $horaFinCita = $horaReq->copy()->addMinutes($duracion);

            if ($horaReq->lt($limiteInicio) || $horaFinCita->gt($limiteFin->copy()->addMinutes($duracion))) {
                return back()->withErrors([
                    'hora' => 'Solo puedes reservar citas entre las ' . self::PACIENTE_HORA_INICIO . ' y las ' . self::PACIENTE_HORA_FIN . '.'
                ])->withInput();
            }
        }

        // Validar si la cita está dentro de la jornada laboral del médico
        if (!Cita::isWithinWorkingHours($medico, $request->hora, $duracion)) {
            $jornada = substr($medico->hora_inicio_jornada, 0, 5) . ' - ' . substr($medico->hora_fin_jornada, 0, 5);
            return back()->withErrors([
                'hora' => "La hora seleccionada está fuera de la jornada laboral de este médico ({$jornada})."
            ])->withInput();
        }

        // Validar que no haya solapamiento (doble reserva), excluyendo esta misma cita
        if (Cita::hasOverlap($medico->id, $request->fecha, $request->hora, $duracion, $cita->id)) {
            return back()->withErrors([
                'hora' => 'El médico ya tiene otra cita programada en este horario (doble reserva).'
            ])->withInput();
        }

        $cita->update([
            'fecha' => $request->fecha,
            'hora' => $request->hora,
            'motivo' => $request->motivo,
            'duracion_minutos' => $duracion,
        ]);

        return redirect()->route('citas.index')->with('success', 'Cita actualizada correctamente.');
    }

    public function completar(Cita $cita)
    {
        ['usuario' => $usuario, 'tipo' => $tipo] = $this->usuarioActual();

        if ($tipo !== 'medico') {
            abort(403);
        }

        if ($cita->medico_id !== $usuario->id) {
            abort(403);
        }

        if ($cita->estado !== 'programada') {
            return redirect()->route('citas.index')
                ->with('error', 'Solo se pueden completar citas programadas.');
        }

        $cita->update(['estado' => 'completada']);

        return redirect()->route('citas.index')
            ->with('success', 'Cita marcada como completada.');
    }

    // Cancelar cita (no se elimina, cambia el estado)
    public function cancelar(Cita $cita)
    {
        ['usuario' => $usuario, 'tipo' => $tipo] = $this->usuarioActual();

        if ($tipo === 'paciente' && $cita->paciente_id !== $usuario->id) {
            abort(403);
        }
        if ($tipo === 'medico' && $cita->medico_id !== $usuario->id) {
            abort(403);
        }

        if ($cita->estado !== 'programada') {
            return redirect()->route('citas.index')->with('error', 'Solo se pueden cancelar citas programadas.');
        }

        $cita->update(['estado' => 'cancelada']);

        return redirect()->route('citas.index')->with('success', 'Cita cancelada correctamente.');
    }
    /**
     * Devuelve los slots horarios disponibles para un médico en una fecha (AJAX).
     * Usado por pacientes para seleccionar horarios válidos.
     */
    public function slotsDisponibles(Request $request)
    {
        $request->validate([
            'medico_id' => ['required', 'exists:medicos,id'],
            'fecha' => ['required', 'date'],
        ]);

        $medico = Medico::findOrFail($request->medico_id);
        $fecha = $request->fecha;
        $duracion = self::PACIENTE_DURACION_SLOT;

        $inicio = Carbon::parse(self::PACIENTE_HORA_INICIO);
        $fin = Carbon::parse(self::PACIENTE_HORA_FIN);

        // Consulta Eloquent: citas activas del médico en esa fecha
        $citasDelDia = Cita::where('medico_id', $medico->id)
            ->whereDate('fecha', $fecha)
            ->whereIn('estado', ['programada', 'completada'])
            ->get();

        $slots = [];
        $cursor = $inicio->copy();

        while ($cursor->copy()->addMinutes($duracion)->lte($fin->copy()->addMinutes($duracion))) {
            $slotInicio = $cursor->format('H:i');
            $slotFin = $cursor->copy()->addMinutes($duracion)->format('H:i');

            // Verificar si este slot está ocupado
            $ocupado = false;
            $citaInfo = null;

            foreach ($citasDelDia as $cita) {
                $citaStart = Carbon::parse($cita->hora);
                $citaEnd = $citaStart->copy()->addMinutes($cita->duracion_minutos);

                // Solapamiento: slot_inicio < cita_fin AND slot_fin > cita_inicio
                if ($cursor->lt($citaEnd) && $cursor->copy()->addMinutes($duracion)->gt($citaStart)) {
                    $ocupado = true;
                    $citaInfo = $cita->estado;
                    break;
                }
            }

            $slots[] = [
                'hora' => $slotInicio,
                'hora_fin' => $slotFin,
                'ocupado' => $ocupado,
                'estado' => $citaInfo,
            ];

            $cursor->addMinutes($duracion);
        }

        return response()->json(['slots' => $slots]);
    }
}