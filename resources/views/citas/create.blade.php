<!DOCTYPE html>
<html lang='es'>

<head>
    <meta charset='UTF-8'>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Cita</title>
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
            <h2>Nueva Cita</h2>
            <a href="{{ route('citas.index') }}" class="btn btn-secondary">Volver</a>
        </div>

        <form action="{{ route('citas.store') }}" method="POST">
            @csrf

            @if($tipo === 'paciente')
            <div class="form-group">
                <label>Médico</label>
                <select name="medico_id" id="medico_id" required>
                    <option value="">-- Selecciona un médico --</option>
                    @foreach($medicos as $medico)
                    <option value="{{ $medico->id }}" {{ old('medico_id')==$medico->id ? 'selected' : '' }}>
                        {{ $medico->nombre }} — {{ $medico->especialidad }}
                    </option>
                    @endforeach
                </select>
                @error('medico_id')
                <p class="error">{{ $message }}</p>
                @enderror
            </div>
            @else
            <div class="form-group">
                <label>Paciente</label>
                <select name="paciente_id" required>
                    <option value="">-- Selecciona un paciente --</option>
                    @foreach($pacientes as $paciente)
                    <option value="{{ $paciente->id }}" {{ old('paciente_id')==$paciente->id ? 'selected' : '' }}>
                        {{ $paciente->nombre }}
                    </option>
                    @endforeach
                </select>
                @error('paciente_id')
                <p class="error">{{ $message }}</p>
                @enderror
            </div>
            @endif

            <div class="fila">
                <div class="form-group">
                    <label>Fecha</label>
                    <input type="date" name="fecha" id="fecha" value="{{ old('fecha') }}" min="{{ date('Y-m-d') }}" required>
                    @error('fecha')
                    <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                @if($tipo === 'medico')
                {{-- Médicos conservan el input libre de hora --}}
                <div class="form-group">
                    <label>Hora</label>
                    <input type="time" name="hora" value="{{ old('hora') }}" required>
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
                    <div class="slots-prompt">
                        Selecciona un <strong>médico</strong> y una <strong>fecha</strong> para ver los horarios disponibles.
                    </div>
                </div>

                {{-- Campo oculto que envía el valor real del slot seleccionado --}}
                <input type="hidden" name="hora" id="hora_hidden" value="{{ old('hora') }}">
            </div>
            @endif

            <div class="form-group">
                <p>Duración por cita: 30 minutos</p>
                
            </div>

            <div class="form-group">
                <label>Motivo de la cita</label>
                <textarea name="motivo" required
                    placeholder="Describe el motivo de la consulta...">{{ old('motivo') }}</textarea>
                @error('motivo')
                <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="acciones">
                <button type="submit" class="btn btn-primary">Guardar cita</button>
                <a href="{{ route('citas.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>

        </form>
    </div>

    @if($tipo === 'paciente')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const medicoSelect = document.getElementById('medico_id');
            const fechaInput = document.getElementById('fecha');
            const slotsContainer = document.getElementById('slots-container');
            const horaHidden = document.getElementById('hora_hidden');

            function fetchSlots() {
                const medicoId = medicoSelect.value;
                const fecha = fechaInput.value;

                if (!medicoId || !fecha) {
                    slotsContainer.innerHTML = `
                        <div class="slots-prompt">
                            Selecciona un <strong>médico</strong> y una <strong>fecha</strong> para ver los horarios disponibles.
                        </div>`;
                    return;
                }

                slotsContainer.innerHTML = '<div class="slots-loading">Cargando horarios...</div>';

                fetch(`{{ route('citas.slots') }}?medico_id=${medicoId}&fecha=${fecha}`)
                    .then(r => {
                        if (!r.ok) throw new Error('Error de red');
                        return r.json();
                    })
                    .then(data => renderSlots(data.slots))
                    .catch(() => {
                        slotsContainer.innerHTML = '<div class="slots-prompt">Error al cargar horarios. Intenta de nuevo.</div>';
                    });
            }

            function renderSlots(slots) {
                if (!slots || slots.length === 0) {
                    slotsContainer.innerHTML = '<div class="slots-prompt">No hay horarios configurados para este día.</div>';
                    return;
                }

                const currentValue = horaHidden.value;
                let html = '<div class="slots-grid">';

                slots.forEach(slot => {
                    const isSelected = (currentValue === slot.hora);
                    const isDisabled = slot.ocupado;

                    if (isDisabled) {
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

                // Add click listeners
                slotsContainer.querySelectorAll('.slot-btn:not(.slot-disabled)').forEach(btn => {
                    btn.addEventListener('click', () => {
                        slotsContainer.querySelectorAll('.slot-btn').forEach(b => b.classList.remove('slot-selected'));
                        btn.classList.add('slot-selected');
                        horaHidden.value = btn.getAttribute('data-hora');
                    });
                });
            }

            medicoSelect.addEventListener('change', () => {
                horaHidden.value = '';
                fetchSlots();
            });
            fechaInput.addEventListener('change', () => {
                horaHidden.value = '';
                fetchSlots();
            });

            // If old values exist (validation failure), pre-load slots
            if (medicoSelect.value && fechaInput.value) {
                fetchSlots();
            }
        });
    </script>
    @endif
</body>

</html>