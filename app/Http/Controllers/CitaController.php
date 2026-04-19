<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Medico;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CitaController extends Controller
{

    // Determina el usuario autenticado y su tipo
    private function usuarioActual(): array
    {
        if (Auth::guard('paciente')->check()) {
            return ['usuario' => Auth::guard('paciente')->user(), 'tipo' => 'paciente'];
        }
        return ['usuario' => Auth::guard('medico')->user(), 'tipo' => 'medico'];
    }

    // Listar citas según el tipo de usuario
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

        Cita::create([
            'paciente_id' => $tipo === 'paciente' ? $usuario->id : $request->paciente_id,
            'medico_id' => $tipo === 'medico' ? $usuario->id : $request->medico_id,
            'fecha' => $request->fecha,
            'hora' => $request->hora,
            'motivo' => $request->motivo,
            'duracion_minutos' => $request->duracion_minutos ?? 30,
            'estado' => 'programada',
        ]);

        return redirect()->route('citas.index')->with('success', 'Cita creada correctamente.');
    }

    // Ver detalle de una cita
    // public function show(Cita $cita)
    // {
    //     ['usuario' => $usuario, 'tipo' => $tipo] = $this->usuarioActual();

    //     // Verifica que la cita pertenezca al usuario autenticado
    //     if ($tipo === 'paciente' && $cita->paciente_id !== $usuario->id) {
    //         abort(403);
    //     }
    //     if ($tipo === 'medico' && $cita->medico_id !== $usuario->id) {
    //         abort(403);
    //     }

    //     $cita->load('paciente', 'medico');

    //     return view('citas.show', compact('cita', 'tipo'));
    // }

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

        $cita->update([
            'fecha' => $request->fecha,
            'hora' => $request->hora,
            'motivo' => $request->motivo,
            'duracion_minutos' => $request->duracion_minutos ?? 30,
        ]);

        return redirect()->route('citas.index')->with('success', 'Cita actualizada correctamente.');
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
}