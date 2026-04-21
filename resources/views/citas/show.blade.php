<!DOCTYPE html>
<html lang='es'>

<head>
    <meta charset='UTF-8'>
    <title>Detalle de Cita</title>
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

        .badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
        }

        .badge-programada {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .badge-cancelada {
            background: #fee2e2;
            color: #b91c1c;
        }

        .badge-completada {
            background: #dcfce7;
            color: #15803d;
        }

        .seccion {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .seccion h3 {
            margin: 0 0 15px 0;
            font-size: 15px;
            color: #475569;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 8px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .info-item span {
            display: block;
            font-size: 12px;
            color: #94a3b8;
            margin-bottom: 3px;
        }

        .info-item strong {
            font-size: 15px;
            color: #1e293b;
        }

        .info-full {
            margin-top: 15px;
        }

        .info-full span {
            display: block;
            font-size: 12px;
            color: #94a3b8;
            margin-bottom: 3px;
        }

        .info-full p {
            font-size: 15px;
            color: #1e293b;
            margin: 0;
            line-height: 1.6;
        }

        .acciones {
            display: flex;
            gap: 10px;
            margin-top: 5px;
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

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .btn-warning {
            background: #d97706;
            color: white;
        }

        .btn-danger {
            background: #dc2626;
            color: white;
        }

        .btn:hover {
            opacity: 0.85;
        }
    </style>
</head>

<body>
    <div class="container">

        <div class="header">
            <h2>Detalle de Cita</h2>
            <a href="{{ route('citas.index') }}" class="btn btn-secondary">Volver</a>
        </div>

        <div style="margin-bottom: 20px;">
            <span class="badge badge-{{ $cita->estado }}">
                {{ ucfirst($cita->estado) }}
            </span>
        </div>

        <div class="seccion">
            <h3>Información de la cita</h3>
            <div class="info-grid">
                <div class="info-item">
                    <span>Fecha</span>
                    <strong>{{ $cita->fecha->format('d/m/Y') }}</strong>
                </div>
                <div class="info-item">
                    <span>Hora</span>
                    <strong>{{ \Carbon\Carbon::parse($cita->hora)->format('H:i') }}</strong>
                </div>
                <div class="info-item">
                    <span>Duración</span>
                    <strong>{{ $cita->duracion_minutos }} minutos</strong>
                </div>
                <div class="info-item">
                    <span>Creada el</span>
                    <strong>{{ $cita->created_at->format('d/m/Y H:i') }}</strong>
                </div>
            </div>

            @if($cita->motivo)
            <div class="info-full">
                <span>Motivo</span>
                <p>{{ $cita->motivo }}</p>
            </div>
            @endif

            @if($cita->notas)
            <div class="info-full">
                <span>Notas</span>
                <p>{{ $cita->notas }}</p>
            </div>
            @endif
        </div>

        @if($tipo === 'paciente')
        <div class="seccion">
            <h3>Médico</h3>
            <div class="info-grid">
                <div class="info-item">
                    <span>Nombre</span>
                    <strong>{{ $cita->medico->nombre }}</strong>
                </div>
                <div class="info-item">
                    <span>Especialidad</span>
                    <strong>{{ $cita->medico->especialidad }}</strong>
                </div>
                <div class="info-item">
                    <span>Teléfono</span>
                    <strong>{{ $cita->medico->telefono ?? 'No registrado' }}</strong>
                </div>
                <div class="info-item">
                    <span>Email</span>
                    <strong>{{ $cita->medico->email }}</strong>
                </div>
            </div>
        </div>
        @else
        <div class="seccion">
            <h3>Paciente</h3>
            <div class="info-grid">
                <div class="info-item">
                    <span>Nombre</span>
                    <strong>{{ $cita->paciente->nombre }}</strong>
                </div>
                <div class="info-item">
                    <span>Teléfono</span>
                    <strong>{{ $cita->paciente->telefono ?? 'No registrado' }}</strong>
                </div>
                <div class="info-item">
                    <span>Email</span>
                    <strong>{{ $cita->paciente->email }}</strong>
                </div>
                <div class="info-item">
                    <span>Fecha de nacimiento</span>
                    <strong>{{ $cita->paciente->fecha_nacimiento?->format('d/m/Y') ?? 'No registrada' }}</strong>
                </div>
            </div>
        </div>
        @endif

        @if($cita->estado === 'programada')
        <div class="acciones">
            <a href="{{ route('citas.edit', $cita->id) }}" class="btn btn-warning">
                Editar cita
            </a>

            <form action="{{ route('citas.cancelar', $cita->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-danger"
                    onclick="return confirm('¿Estás seguro de cancelar esta cita?')">
                    Cancelar cita
                </button>
            </form>
        </div>
        @endif

    </div>
</body>

</html>