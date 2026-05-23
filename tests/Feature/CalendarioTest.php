<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\Medico;
use App\Models\Paciente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalendarioTest extends TestCase
{
    use RefreshDatabase;

    private $medico;
    private $paciente;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a doctor
        $this->medico = Medico::create([
            'nombre' => 'Dr. House',
            'especialidad' => 'Diagnóstico',
            'telefono' => '123456789',
            'email' => 'house@example.com',
            'numero_licencia' => 'LIC12345',
            'password' => bcrypt('password'),
            'hora_inicio_jornada' => '08:00:00',
            'hora_fin_jornada' => '17:00:00',
            'duracion_cita_minutos' => 30,
        ]);

        // Create a patient
        $this->paciente = Paciente::create([
            'nombre' => 'John Doe',
            'telefono' => '987654321',
            'email' => 'john@example.com',
            'fecha_nacimiento' => '1990-05-15',
            'sexo' => 'masculino',
            'direccion' => 'Calle Falsa 123',
            'password' => bcrypt('password'),
        ]);
    }

    public function test_guest_cannot_access_calendar()
    {
        $response = $this->get(route('calendario.index'));

        // Guest is redirected to login
        $response->assertRedirect(route('login.form'));
    }

    public function test_patient_cannot_access_calendar()
    {
        $response = $this->actingAs($this->paciente, 'paciente')->get(route('calendario.index'));

        // Patient is redirected to login because the guard is auth:medico
        $response->assertRedirect(route('login.form'));
    }

    public function test_doctor_can_access_calendar()
    {
        $response = $this->actingAs($this->medico, 'medico')->get(route('calendario.index'));

        $response->assertStatus(200);
        $response->assertViewIs('calendario.index');
        $response->assertSee('Dr. House');
    }

    public function test_cannot_book_appointment_outside_working_hours()
    {
        // Act as patient to book an appointment
        $response = $this->actingAs($this->paciente, 'paciente')->post(route('citas.store'), [
            'fecha' => now()->addDay()->format('Y-m-d'),
            'hora' => '07:30', // Before start of workday (08:00)
            'motivo' => 'Dolor de cabeza',
            'duracion_minutos' => 30,
            'medico_id' => $this->medico->id,
        ]);

        $response->assertSessionHasErrors('hora');
        $this->assertDatabaseEmpty('citas');
    }

    public function test_cannot_book_overlapping_appointment()
    {
        $fechaStr = now()->addDay()->format('Y-m-d');

        // Create first appointment (09:00 - 09:30)
        Cita::create([
            'paciente_id' => $this->paciente->id,
            'medico_id' => $this->medico->id,
            'fecha' => $fechaStr,
            'hora' => '09:00:00',
            'motivo' => 'Cita original',
            'duracion_minutos' => 30,
            'estado' => 'programada',
        ]);

        // Attempt to create second overlapping appointment (09:15 - 09:45)
        $response = $this->actingAs($this->paciente, 'paciente')->post(route('citas.store'), [
            'fecha' => $fechaStr,
            'hora' => '09:15',
            'motivo' => 'Cita solapada',
            'duracion_minutos' => 30,
            'medico_id' => $this->medico->id,
        ]);

        $response->assertSessionHasErrors('hora');
        // Only 1 appointment in DB
        $this->assertDatabaseCount('citas', 1);
    }

    public function test_dynamic_slots_generation_works_correctly()
    {
        $fechaStr = now()->addDay()->format('Y-m-d');

        // Create one booked slot (10:00 - 10:30)
        Cita::create([
            'paciente_id' => $this->paciente->id,
            'medico_id' => $this->medico->id,
            'fecha' => $fechaStr,
            'hora' => '10:00:00',
            'motivo' => 'Cita reservada',
            'duracion_minutos' => 30,
            'estado' => 'programada',
        ]);

        // Call the AJAX endpoint for slots
        $response = $this->actingAs($this->medico, 'medico')
            ->getJson(route('calendario.slots', ['date' => $fechaStr]));

        $response->assertStatus(200);

        $data = $response->json();
        $this->assertEquals($fechaStr, $data['date']);
        $this->assertNotEmpty($data['slots']);

        // Check 10:00 slot is marked occupied
        $slot10 = collect($data['slots'])->firstWhere('hora_inicio', '10:00');
        $this->assertNotNull($slot10);
        $this->assertTrue($slot10['ocupado']);
        $this->assertEquals('John Doe', $slot10['cita']['paciente_nombre']);

        // Check 08:00 slot is marked available
        $slot08 = collect($data['slots'])->firstWhere('hora_inicio', '08:00');
        $this->assertNotNull($slot08);
        $this->assertFalse($slot08['ocupado']);
    }
}
