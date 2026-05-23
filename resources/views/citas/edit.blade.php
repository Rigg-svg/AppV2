<!DOCTYPE html>
<html lang='es'>

<head>
    <meta charset='UTF-8'>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cita</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #eef2ff;
            padding: 40px;
        }

        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            max-width: 700px;
            margin: auto;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 14px;
        }

        textarea {
            resize: vertical;
            height: 100px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .fila {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: #111827;
            color: white;
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .btn:hover {
            opacity: 0.85;
        }

        .acciones {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .error {
            color: red;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .info-readonly {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .info-readonly span {
            display: block;
            font-size: 12px;
            color: #94a3b8;
            margin-bottom: 3px;
        }

        .info-readonly strong {
            font-size: 15px;
            color: #1e293b;
        }

        /* ====== Slot Grid Styles ====== */
        .slots-section {
            margin-bottom: 18px;
        }

        .slots-section label {
            margin-bottom: 10px;
        }

        .slots-prompt {
            text-align: center;
            color: #94a3b8;
            font-size: 14px;
            padding: 25px 15px;
            border: 2px dashed #e2e8f0;
            border-radius: 8px;
            background: #f8fafc;
        }

        .slots-loading {
            text-align: center;
            color: #64748b;
            font-size: 14px;
            padding: 25px;
        }

        .slots-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
            gap: 10px;
        }

        .slot-btn {
            padding: 12px 8px;
            border: 2px solid #d1d5db;
            border-radius: 8px;
            background: white;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            text-align: center;
            transition: all 0.2s ease;
            color: #1e293b;
        }

        .slot-btn:hover:not(.slot-disabled):not(.slot-selected) {
            border-color: #4f46e5;
            background: #eef2ff;
            transform: translateY(-1px);
            box-shadow: 0 2px 6px rgba(79, 70, 229, 0.15);
        }

        .slot-btn.slot-selected {
            border-color: #4f46e5;
            background: #4f46e5;
            color: white;
            box-shadow: 0 2px 8px rgba(79, 70, 229, 0.3);
        }

        .slot-btn.slot-disabled {
            background: #fee2e2;
            border-color: #fecaca;
            color: #b91c1c;
            cursor: not-allowed;
            opacity: 0.7;
        }

        .slot-btn .slot-label {
            display: block;
            font-size: 15px;
            font-weight: 700;
        }

        .slot-btn .slot-status {
            display: block;
            font-size: 11px;
            font-weight: 400;
            margin-top: 3px;
        }

        .slot-btn:not(.slot-disabled) .slot-status {
            color: #16a34a;
        }

        .slot-btn.slot-selected .slot-status {
            color: #c7d2fe;
        }
    </style>
</head>

<body>
    <div class="container">

        <div class="header">
            <h2>Editar Cita</h2>
            <a href="{{ route('citas.index') }}" class="btn btn-secondary">Volver</a>
        </div>

        @if($tipo === 'paciente')
        <div class="info-readonly">
            <span>Médico asignado</span>
            <strong>{{ $cita->medico->nombre }} — {{ $cita->medico->especialidad }}</strong>
        </div>
        @else
        <div class="info-readonly">
            <span>Paciente asignado</span>
            <strong>{{ $cita->paciente->nombre }}</strong>
        </div>
        @endif

        <form action="{{ route('citas.update', $cita->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="fila">
                <div class="form-group">
                    <label>Fecha</label>
                    <input type="date" name="fecha" id="fecha" value="{{ old('fecha', $cita->fecha->format('Y-m-d')) }}"
                        min="{{ date('Y-m-d') }}" required>
                    @error('fecha')
                    <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                @if($tipo === 'medico')
                <div class="form-group">
                    <label>Hora</label>
                    <input type="time" name="hora"
                        value="{{ old('hora', \Carbon\Carbon::parse($cita->hora)->format('H:i')) }}" required>
                    @error('hora')
                    <p class="error">{{ $message }}</p>
                    @enderror
                </div>
                @endif
            </div>

            @if($tipo === 'paciente')
            {{-- Selector visual de horarios para pacientes --}}
            <div class="slots-section">
                <label>Horario disponible <span style="font-weight:400; color:#64748b;">(08:00 - 16:30)</span></label>
                @error('hora')
                <p class="error">{{ $message }}</p>
                @enderror

                <div id="slots-container">
                    <div class="slots-loading">Cargando horarios...</div>
                </div>

                <input type="hidden" name="hora" id="hora_hidden" value="{{ old('hora', \Carbon\Carbon::parse($cita->hora)->format('H:i')) }}">
            </div>
            @endif

            <div class="form-group">
                <label>Duración (minutos)</label>
                <select name="duracion_minutos">
                    @foreach([15, 30, 45, 60, 90, 120] as $duracion)
                    <option value="{{ $duracion }}" {{ old('duracion_minutos', $cita->duracion_minutos) == $duracion ?
                        'selected' : '' }}>
                        {{ $duracion }} minutos
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Motivo de la cita</label>
                <textarea name="motivo" required
                    placeholder="Describe el motivo de la consulta...">{{ old('motivo', $cita->motivo) }}</textarea>
                @error('motivo')
                <p class="error">{{ $message }}</p>
                @enderror
            </div>

            @if($tipo === 'medico')
            <div class="form-group">
                <label>Notas de la consulta</label>
                <textarea name="notas"
                    placeholder="Observaciones, diagnóstico, tratamiento...">{{ old('notas', $cita->notas) }}</textarea>
                @error('notas')
                <p class="error">{{ $message }}</p>
                @enderror
            </div>
            @endif

            <div class="acciones">
                <button type="submit" class="btn btn-primary">Guardar cambios</button>
                <a href="{{ route('citas.show', $cita->id) }}" class="btn btn-secondary">Cancelar</a>
            </div>

        </form>
    </div>

    @if($tipo === 'paciente')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const medicoId = {{ $cita->medico_id }};
            const citaId = {{ $cita->id }};
            const fechaInput = document.getElementById('fecha');
            const slotsContainer = document.getElementById('slots-container');
            const horaHidden = document.getElementById('hora_hidden');

            function fetchSlots() {
                const fecha = fechaInput.value;
                if (!fecha) {
                    slotsContainer.innerHTML = '<div class="slots-prompt">Selecciona una <strong>fecha</strong> para ver los horarios.</div>';
                    return;
                }

                slotsContainer.innerHTML = '<div class="slots-loading">Cargando horarios...</div>';

                fetch(`{{ route('citas.slots') }}?medico_id=${medicoId}&fecha=${fecha}`)
                    .then(r => {
                        if (!r.ok) throw new Error('Error');
                        return r.json();
                    })
                    .then(data => renderSlots(data.slots))
                    .catch(() => {
                        slotsContainer.innerHTML = '<div class="slots-prompt">Error al cargar horarios.</div>';
                    });
            }

            function renderSlots(slots) {
                if (!slots || slots.length === 0) {
                    slotsContainer.innerHTML = '<div class="slots-prompt">No hay horarios para este día.</div>';
                    return;
                }

                const currentValue = horaHidden.value;
                let html = '<div class="slots-grid">';

                slots.forEach(slot => {
                    // The current appointment's own slot should appear as selectable, not occupied
                    const isCurrentSlot = (currentValue === slot.hora);
                    const isOccupied = slot.ocupado && !isCurrentSlot;
                    const isSelected = isCurrentSlot;

                    if (isOccupied) {
                        html += `
                            <div class="slot-btn slot-disabled" title="Horario ocupado">
                                <span class="slot-label">${slot.hora}</span>
                                <span class="slot-status">Ocupado</span>
                            </div>`;
                    } else {
                        html += `
                            <div class="slot-btn ${isSelected ? 'slot-selected' : ''}" data-hora="${slot.hora}">
                                <span class="slot-label">${slot.hora}</span>
                                <span class="slot-status">Disponible</span>
                            </div>`;
                    }
                });

                html += '</div>';
                slotsContainer.innerHTML = html;

                slotsContainer.querySelectorAll('.slot-btn:not(.slot-disabled)').forEach(btn => {
                    btn.addEventListener('click', () => {
                        slotsContainer.querySelectorAll('.slot-btn').forEach(b => b.classList.remove('slot-selected'));
                        btn.classList.add('slot-selected');
                        horaHidden.value = btn.getAttribute('data-hora');
                    });
                });
            }

            fechaInput.addEventListener('change', () => {
                horaHidden.value = '';
                fetchSlots();
            });

            // Auto-load slots on page load since the doctor and date are known
            fetchSlots();
        });
    </script>
    @endif
</body>

</html>