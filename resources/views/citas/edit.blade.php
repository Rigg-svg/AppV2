<!DOCTYPE html>
<html lang='es'>

<head>
    <meta charset='UTF-8'>
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
                    <input type="date" name="fecha" value="{{ old('fecha', $cita->fecha->format('Y-m-d')) }}"
                        min="{{ date('Y-m-d') }}" required>
                    @error('fecha')
                    <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Hora</label>
                    <input type="time" name="hora"
                        value="{{ old('hora', \Carbon\Carbon::parse($cita->hora)->format('H:i')) }}" required>
                    @error('hora')
                    <p class="error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

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
</body>

</html>