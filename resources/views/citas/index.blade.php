<!DOCTYPE html>
<html lang='es'>

<head>
    <meta charset='UTF-8'>
    <title>Mis Citas</title>
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
            max-width: 900px;
            margin: auto;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .btn {
            padding: 9px 18px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-success {
            background: #16a34a;
            color: white;
        }

        .btn-primary {
            background: #111827;
            color: white;
        }

        .btn-info {
            background: #2563eb;
            color: white;
        }

        .btn-warning {
            background: #2563eb;
            color: white;
        }

        .btn-danger {
            background: #dc2626;
            color: white;
        }

        .btn:hover {
            opacity: 0.85;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        th {
            background: #111827;
            color: white;
            padding: 12px 15px;
            text-align: left;
        }

        td {
            padding: 12px 15px;
            border-bottom: 1px solid #e2e8f0;
            color: #1e293b;
        }

        tr:hover td {
            background: #f8fafc;
        }

        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
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
            color: #1d4ed8;
        }

        .acciones {
            display: flex;
            gap: 6px;
        }

        .success {
            color: green;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .error {
            color: red;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .empty {
            text-align: center;
            padding: 40px;
            color: #94a3b8;
            font-size: 15px;
        }
    </style>
</head>

<body>
    <div class="container">

        <div class="header">
            <h2>Mis Citas</h2>
            <div style="display:flex; gap:10px;">
                @if($tipo === 'medico')
                    <a href="{{ route('calendario.index') }}" class="btn" style="background: #4f46e5; color: white;">Ver Calendario</a>
                @endif
                <a href="{{ route('citas.create') }}" class="btn btn-primary">Nueva cita</a>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger">Cerrar sesión</button>
                </form>
            </div>
        </div>

        @if(session('success'))
        <p class="success">{{ session('success') }}</p>
        @endif

        @if(session('error'))
        <p class="error">{{ session('error') }}</p>
        @endif

        @if($citas->isEmpty())
        <div class="empty">No tienes citas registradas.</div>
        @else
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Hora</th>
                    @if($tipo === 'paciente')
                    <th>Médico</th>
                    <th>Especialidad</th>
                    @else
                    <th>Paciente</th>
                    @endif
                    <th>Motivo</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($citas as $cita)
                <tr>
                    <td>{{ $cita->fecha->format('d/m/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($cita->hora)->format('H:i') }}</td>

                    @if($tipo === 'paciente')
                    <td>{{ $cita->medico->nombre }}</td>
                    <td>{{ $cita->medico->especialidad }}</td>
                    @else
                    <td>{{ $cita->paciente->nombre }}</td>
                    @endif

                    <td>{{ Str::limit($cita->motivo, 40) }}</td>

                    <td>
                        <span class="badge badge-{{ $cita->estado }}">
                            {{ ucfirst($cita->estado) }}
                        </span>
                    </td>

                    <td>
                        <div class="acciones">
                            <a href="{{ route('citas.show', $cita->id) }}" class="btn btn-info">Ver</a>

                            @if($cita->estado === 'programada')
                            <a href="{{ route('citas.edit', $cita->id) }}" class="btn btn-warning">Editar</a>

                            <form action="{{ route('citas.cancelar', $cita->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-danger"
                                    onclick="return confirm('¿Cancelar esta cita?')">
                                    Cancelar
                                </button>
                            </form>

                            @if($tipo === 'medico')
                            <form action="{{ route('citas.completar', $cita->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success"
                                    onclick="return confirm('¿Marcar esta cita como completada?')">
                                    Completar
                                </button>
                            </form>
                            @endif
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

    </div>
</body>

</html>