Sistema de gestión de citas médicas

Una clínica necesita un sistema donde se registren pacientes, médicos y citas. Cada paciente puede tener varias citas y cada médico atiende múltiples pacientes. Se requiere controlar disponibilidad de horarios y evitar que un médico tenga dos citas al mismo tiempo.

# Requerimientos:

Autenticación de usuarios (login/logout).

CRUD de pacientes (nombre, teléfono, email).

CRUD de médicos (nombre, especialidad, teléfono).

CRUD de citas (fecha, hora, paciente, médico, estado: programada, cancelada, completada).

Validar que un médico no tenga dos citas en el mismo horario.

Mostrar un dashboard con las citas del día.


# Flujo - Paciente : 

Paciente inicia sesion

Paciente ve sus citas

Paciente crea una cita

Paciente cancela una cita

Paciente completa una cita (opcional)

Paciente ve su historial de citas


# Flujo - Médico : 

Médico inicia sesion

Médico ve sus citas

Médico crea una cita

Médico cancela una cita

Médico completa una cita (opcional)

Médico ve su historial de citas

# Datos de prueba

Usar terminal: **php artisan tinker**, y luego escribir los comandos de prueba

```php
App\Models\Paciente::create(['nombre' => 'Carlos Andres Lopez','telefono' => '3001234567','email' => 'carlos.lopez@email.com','password' => '123456','fecha_nacimiento' => '1988-03-10','sexo' => 'masculino','direccion' => 'Calle 15 # 8-32, Pereira']);
```

```php
App\Models\Paciente::create(['nombre' => 'Maria Fernanda Torres','telefono' => '3157654321','email' => 'maria.torres@email.com','password' => '123456','fecha_nacimiento' => '1993-11-25','sexo' => 'femenino','direccion' => 'Carrera 12 # 45-67, Pereira']);
```

```php
App\Models\Medico::create(['nombre' => 'Jorge Herrera','especialidad' => 'Cardiología','telefono' => '3209876543','email' => 'jorge.herrera@clinica.com','password' => '123456','numero_licencia' => 'MED-2024-003','activo' => true]);
```

```php
App\Models\Medico::create(['nombre' => 'Valentina Ruiz','especialidad' => 'Dermatología','telefono' => '3124567890','email' => 'valentina.ruiz@clinica.com','password' => '123456','numero_licencia' => 'MED-2024-004','activo' => true]);

```

Documentación: https://github-55.mintlify.app/
