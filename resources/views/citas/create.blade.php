<!DOCTYPE html>
<html lang='es'>

<head>
    <meta charset='UTF-8'>
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
            max-width: 600px;
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
                <select name="medico_id" required>
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
                    <input type="date" name="fecha" value="{{ old('fecha') }}" min="{{ date('Y-m-d') }}" required>
                    @error('fecha')
                    <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Hora</label>
                    <input type="time" name="hora" value="{{ old('hora') }}" required>
                    @error('hora')
                    <p class="error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label>Duración (minutos)</label>
                <select name="duracion_minutos">
                    @foreach([15, 30, 45, 60, 90, 120] as $duracion)
                    <option value="{{ $duracion }}" {{ old('duracion_minutos', 30)==$duracion ? 'selected' : '' }}>
                        {{ $duracion }} minutos
                    </option>
                    @endforeach
                </select>
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
</body>

</html>