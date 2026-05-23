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

Paciente: [laura@email.com] - 123456
Médico: [carlos@email.com] - 123456


App\Models\Medico::create([
    'nombre'           => 'Dra. Laura Gómez',
    'email'            => 'laura@email.com',
    'password'         => bcrypt('123456'),
    'especialidad'     => 'Medicina General',
    'telefono'         => '3109876543',
    'numero_licencia'  => 'MED-00123'
]);

App\Models\Paciente::create([
    'nombre'           => 'Carlos Pérez',
    'email'            => 'carlos@email.com',
    'password'         => bcrypt('123456'),
    'telefono'         => '3001234567',
    'fecha_nacimiento' => '1990-01-01',
    'sexo'             => 'masculino',
    'direccion'        => 'Calle 10 # 5-20'
]);


Documentación: https://mintlify.wiki/Rigg-svg/AppV2/introduction