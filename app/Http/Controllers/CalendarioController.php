<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Medico;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalendarioController extends Controller
{
    /**
     * Display the calendar and optionally the slots for a specific day.
     */
    public function index(Request $request)
    {
        $medico = Auth::guard('medico')->user();

        // Parse month and year from query parameters or default to now
        $month = (int) $request->input('month', Carbon::now()->month);
        $year = (int) $request->input('year', Carbon::now()->year);

        // Current month date
        $currentDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();

        // Calculate navigation months
        $prevMonth = $currentDate->copy()->subMonth();
        $nextMonth = $currentDate->copy()->addMonth();

        $daysInMonth = $currentDate->daysInMonth;
        // ISO day of week: 1 (Monday) to 7 (Sunday)
        $firstDayOfWeekIso = $currentDate->dayOfWeekIso;
        $blankDays = $firstDayOfWeekIso - 1;

        // Fetch all appointments for the current month
        $citasDelMes = Cita::where('medico_id', $medico->id)
            ->whereBetween('fecha', [
                $currentDate->copy()->startOfMonth()->toDateString(),
                $currentDate->copy()->endOfMonth()->toDateString()
            ])
            ->whereIn('estado', ['programada', 'completada'])
            ->get();

        // Group by date to get appointment counts
        $citasPorDia = $citasDelMes->groupBy(function ($cita) {
            return Carbon::parse($cita->fecha)->format('Y-m-d');
        })->map(function ($dayGroup) {
            return $dayGroup->count();
        });

        // If a date is requested, calculate slots for that day
        $selectedDate = $request->input('date');
        $slots = [];

        if ($selectedDate) {
            // Validate date format Y-m-d
            try {
                $parsedDate = Carbon::createFromFormat('Y-m-d', $selectedDate);
                $slots = $this->generarSlots($medico, $parsedDate->toDateString());
            } catch (\Exception $e) {
                $selectedDate = null;
            }
        }

        return view('calendario.index', compact(
            'medico',
            'currentDate',
            'prevMonth',
            'nextMonth',
            'daysInMonth',
            'blankDays',
            'citasPorDia',
            'selectedDate',
            'slots'
        ));
    }

    /**
     * Get slots for a day in JSON format (AJAX).
     */
    public function slotsPorDia(Request $request)
    {
        $medico = Auth::guard('medico')->user();
        $date = $request->input('date');

        if (!$date) {
            return response()->json(['error' => 'La fecha es requerida.'], 400);
        }

        try {
            $parsedDate = Carbon::createFromFormat('Y-m-d', $date);
            $slots = $this->generarSlots($medico, $parsedDate->toDateString());
            return response()->json([
                'date' => $parsedDate->toDateString(),
                'slots' => $slots
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Formato de fecha inválido.'], 400);
        }
    }

    /**
     * Helper to generate time slots for a doctor on a specific date.
     */
    private function generarSlots(Medico $medico, string $dateString): array
    {
        $inicio = $medico->hora_inicio_jornada ?? '08:00:00';
        $fin = $medico->hora_fin_jornada ?? '17:00:00';
        $duracion = $medico->duracion_cita_minutos ?? 30;

        $start = Carbon::parse($dateString . ' ' . $inicio);
        $end = Carbon::parse($dateString . ' ' . $fin);
        $slots = [];

        // Fetch doctor's appointments for this specific day
        $citas = Cita::where('medico_id', $medico->id)
            ->whereDate('fecha', $dateString)
            ->whereIn('estado', ['programada', 'completada'])
            ->with('paciente')
            ->orderBy('hora')
            ->get();

        $currentSlot = $start->copy();

        while ($currentSlot->copy()->addMinutes($duracion)->lte($end)) {
            $slotStart = $currentSlot->copy();
            $slotEnd = $currentSlot->copy()->addMinutes($duracion);

            $overlapCita = null;

            foreach ($citas as $cita) {
                // $cita->hora can be Carbon instance or formatted time string.
                $citaStartTimeStr = Carbon::parse($cita->hora)->format('H:i:s');
                $citaStart = Carbon::parse($dateString . ' ' . $citaStartTimeStr);
                $citaEnd = $citaStart->copy()->addMinutes($cita->duracion_minutos);

                // Overlap condition: Start1 < End2 AND End1 > Start2
                if ($citaStart->lt($slotEnd) && $citaEnd->gt($slotStart)) {
                    $overlapCita = $cita;
                    break;
                }
            }

            $slots[] = [
                'hora_inicio' => $slotStart->format('H:i'),
                'hora_fin' => $slotEnd->format('H:i'),
                'ocupado' => !is_null($overlapCita),
                'cita' => $overlapCita ? [
                    'id' => $overlapCita->id,
                    'paciente_nombre' => $overlapCita->paciente->nombre,
                    'motivo' => $overlapCita->motivo,
                    'estado' => $overlapCita->estado,
                ] : null
            ];

            $currentSlot->addMinutes($duracion);
        }

        return $slots;
    }
}
