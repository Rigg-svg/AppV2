<!DOCTYPE html>
<html lang='es'>

<head>
    <meta charset='UTF-8'>
    <title>Dashboard</title>
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
            max-width: 650px;
            margin: auto;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .badge a {
            padding: 5px 12px;
            padding: 10px 20px;
            background: #030bedff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 25px;
        }

        .info-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
        }

        .info-card span {
            display: block;
            font-size: 12px;
            color: #94a3b8;
            margin-bottom: 4px;
        }

        .info-card strong {
            font-size: 15px;
            color: #1e293b;
        }

        .btn-logout {
            padding: 10px 20px;
            background: #dc2626;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-logout:hover {
            background: #b91c1c;
        }

        hr {
            border: none;
            border-top: 1px solid #e2e8f0;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="container">

        <div class="header">
            <h2 style="margin:0">Bienvenido, {{ $usuario->nombre }}</h2>
            <div style="display: flex; gap: 10px; align-items: center;">
                @if(Auth::guard('medico')->check())
                    <a href="{{ route('calendario.index') }}" style="padding: 10px 20px; background: #4f46e5; color: white; border-radius: 5px; text-decoration: none; font-size: 14px; font-weight: bold; display: inline-block;">Ver Calendario</a>
                @endif
                <a class="bagde" href="{{ route('citas.index') }}" style="padding: 10px 20px; background: #030bedff; color: white; border-radius: 5px; text-decoration: none; font-size: 14px; font-weight: bold; display: inline-block;"> Ver citas </a>
            </div>
        </div>

        <hr>

        @if(Auth::guard('paciente')->check())
        <div class="info-grid">
            <div class="info-card">
                <span>Correo</span>
                <strong>{{ $usuario->email }}</strong>
            </div>
            <div class="info-card">
                <span>Teléfono</span>
                <strong>{{ $usuario->telefono ?? 'No registrado' }}</strong>
            </div>
            <div class="info-card">
                <span>Fecha de nacimiento</span>
                <strong>{{ $usuario->fecha_nacimiento?->format('d/m/Y') ?? 'No registrada' }}</strong>
            </div>
            <div class="info-card">
                <span>Sexo</span>
                <strong>{{ ucfirst($usuario->sexo ?? 'No registrado') }}</strong>
            </div>
        </div>

        @else
        <div class="info-grid">
            <div class="info-card">
                <span>Correo</span>
                <strong>{{ $usuario->email }}</strong>
            </div>
            <div class="info-card">
                <span>Especialidad</span>
                <strong>{{ $usuario->especialidad }}</strong>
            </div>
            <div class="info-card">
                <span>Teléfono</span>
                <strong>{{ $usuario->telefono ?? 'No registrado' }}</strong>
            </div>
            <div class="info-card">
                <span>N° Licencia</span>
                <strong>{{ $usuario->numero_licencia ?? 'No registrado' }}</strong>
            </div>
        </div>
        @endif

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn-logout">Cerrar sesión</button>


        </form>

    </div>
</body>

</html>