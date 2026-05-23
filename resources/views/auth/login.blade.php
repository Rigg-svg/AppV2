<!DOCTYPE html>
<html lang='es'>

<head>
    <meta charset='UTF-8'>
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            width: 350px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        label {
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }

        input[type='email'],
        input[type='password'] {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            margin-bottom: 15px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .tipo-selector {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .tipo-selector input[type='radio'] {
            display: none;
        }

        .tipo-selector label {
            flex: 1;
            text-align: center;
            padding: 10px;
            border: 2px solid #ccc;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            color: #555;
            transition: all 0.2s;
        }

        .tipo-selector input[type='radio']:checked+label {
            border-color: #111827;
            background: #111827;
            color: white;
        }

        button {
            width: 100%;
            padding: 10px;
            background: #111827;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-size: 15px;
        }

        button:hover {
            background: #374151;
        }

        .error {
            color: red;
            font-size: 13px;
            margin-top: -10px;
            margin-bottom: 10px;
        }

        .success {
            color: green;
            font-size: 14px;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class='card'>
        <h2>Iniciar sesión</h2>

        @if(session('success'))
        <p class='success'>{{ session('success') }}</p>
        @endif

        @if(session('error'))
        <p class='error'>{{ session('error') }}</p>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf

            <label>Tipo de usuario</label>
            <div class="tipo-selector" style="margin-top: 8px;">
                <input type="radio" name="tipo" id="tipo_paciente" value="paciente" {{ old('tipo', 'paciente'
                    )=='paciente' ? 'checked' : '' }}>
                <label for="tipo_paciente">Paciente</label>

                <input type="radio" name="tipo" id="tipo_medico" value="medico" {{ old('tipo')=='medico' ? 'checked'
                    : '' }}>
                <label for="tipo_medico">Médico</label>
            </div>
            @error('tipo')
            <p class='error'>{{ $message }}</p>
            @enderror

            <label>Correo electrónico</label>
            <input type='email' name='email' value="{{ old('email') }}" required>
            @error('email')
            <p class='error'>{{ $message }}</p>
            @enderror

            <label>Contraseña</label>
            <input type='password' name='password' required>
            @error('password')
            <p class='error'>{{ $message }}</p>
            @enderror

            <button type='submit'>Ingresar</button>
        </form>
    </div>
</body>

</html>