<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendario de Citas</title>
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --bg-main: #f8fafc;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --success-bg: #dcfce7;
            --success-text: #16a34a;
            --info-bg: #dbeafe;
            --info-text: #2563eb;
            --completed-bg: #f1f5f9;
            --completed-text: #475569;
            --danger-bg: #fee2e2;
            --danger-text: #dc2626;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: var(--bg-main);
            color: var(--text-dark);
            margin: 0;
            padding: 20px;
            line-height: 1.5;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 10px;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            padding: 15px 30px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            margin-bottom: 25px;
        }

        .navbar h1 {
            font-size: 20px;
            margin: 0;
            font-weight: 700;
            color: var(--text-dark);
        }

        .nav-actions {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .btn {
            padding: 8px 16px;
            border-radius: 6px;
            border: 1px solid var(--border-color);
            background: white;
            color: var(--text-dark);
            cursor: pointer;
            font-weight: 500;
            font-size: 14px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: all 0.2s ease;
        }

        .btn:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            border: none;
        }

        .btn-primary:hover {
            background: var(--primary-hover);
        }

        .btn-danger {
            background: var(--danger-bg);
            color: var(--danger-text);
            border-color: transparent;
        }

        .btn-danger:hover {
            background: #fecaca;
        }

        .layout-grid {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 25px;
        }

        @media (max-width: 992px) {
            .layout-grid {
                grid-template-columns: 1fr;
            }
        }

        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            border: 1px solid var(--border-color);
            padding: 24px;
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .calendar-title {
            font-size: 18px;
            font-weight: 700;
            text-transform: capitalize;
            margin: 0;
        }

        .month-nav {
            display: flex;
            gap: 8px;
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 8px;
        }

        .weekday-label {
            text-align: center;
            font-weight: 600;
            font-size: 12px;
            color: var(--text-muted);
            padding: 8px 0;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .calendar-day-cell {
            aspect-ratio: 1;
            position: relative;
            background: #fff;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 8px;
            text-decoration: none;
            box-sizing: border-box;
        }

        .calendar-day-cell:hover {
            border-color: var(--primary);
            background: #f5f3ff;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.08);
        }

        .calendar-day-cell.blank {
            background: #f8fafc;
            border-color: #f1f5f9;
            cursor: default;
            pointer-events: none;
        }

        .calendar-day-cell.selected {
            border-color: var(--primary);
            background: #e0e7ff;
            box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2);
        }

        .day-num {
            font-weight: 600;
            font-size: 14px;
            color: var(--text-dark);
        }

        .calendar-day-cell.selected .day-num {
            color: var(--primary);
        }

        .day-citas-indicator {
            align-self: flex-end;
            background: var(--primary);
            color: white;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 10px;
            line-height: 1;
        }

        .schedule-header {
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 15px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .schedule-title {
            font-size: 16px;
            font-weight: 700;
            margin: 0;
            color: var(--text-dark);
        }

        .schedule-subtitle {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        .slots-container {
            display: flex;
            flex-direction: column;
            gap: 12px;
            max-height: 550px;
            overflow-y: auto;
            padding-right: 5px;
        }

        .slot-card {
            border-radius: 8px;
            border: 1px solid var(--border-color);
            padding: 14px 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.2s ease;
        }

        .slot-card.available {
            background-color: var(--success-bg);
            border-color: rgba(22, 163, 74, 0.25);
        }

        .slot-card.occupied {
            background-color: var(--info-bg);
            border-color: rgba(37, 99, 235, 0.25);
        }

        .slot-card.completed {
            background-color: var(--completed-bg);
            border-color: rgba(71, 85, 105, 0.25);
        }

        .slot-time {
            font-weight: 700;
            font-size: 15px;
            color: var(--text-dark);
            display: flex;
            flex-direction: column;
        }

        .slot-duration {
            font-weight: 400;
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .slot-info {
            flex-grow: 1;
            margin-left: 20px;
            margin-right: 20px;
        }

        .slot-patient {
            font-weight: 600;
            font-size: 14px;
            color: var(--text-dark);
            margin: 0;
        }

        .slot-reason {
            font-size: 12px;
            color: var(--text-muted);
            margin: 2px 0 0 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 250px;
        }

        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: capitalize;
            white-space: nowrap;
        }

        .badge-available {
            background: #bbf7d0;
            color: #15803d;
        }

        .badge-programada {
            background: #93c5fd;
            color: #1e40af;
        }

        .badge-completada {
            background: #cbd5e1;
            color: #334155;
        }

        .empty-slots-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-muted);
        }

        .empty-slots-state svg {
            width: 48px;
            height: 48px;
            color: #cbd5e1;
            margin-bottom: 12px;
        }

        /* Scrollbar styles for the slots list */
        .slots-container::-webkit-scrollbar {
            width: 6px;
        }
        .slots-container::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }
        .slots-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        .slots-container::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>

<body>
    @php
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        $nombreMes = $meses[$currentDate->month];
    @endphp

    <div class="container">
        <!-- Navbar -->
        <div class="navbar">
            <div>
                <h1>Gestión de Citas - Portal del Médico</h1>
                <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">
                    Médico: <strong>{{ $medico->nombre }}</strong> ({{ $medico->especialidad }})
                </div>
            </div>
            <div class="nav-actions">
                <a href="{{ route('dashboard') }}" class="btn">Dashboard</a>
                <a href="{{ route('citas.index') }}" class="btn">Lista de Citas</a>
                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn btn-danger">Cerrar Sesión</button>
                </form>
            </div>
        </div>

        <!-- Layout Grid -->
        <div class="layout-grid">
            <!-- Calendar Card -->
            <div class="card">
                <div class="calendar-header">
                    <h2 class="calendar-title">{{ $nombreMes }} de {{ $currentDate->year }}</h2>
                    <div class="month-nav">
                        <a href="{{ route('calendario.index', ['month' => $prevMonth->month, 'year' => $prevMonth->year]) }}" class="btn" title="Mes Anterior">&larr;</a>
                        <a href="{{ route('calendario.index') }}" class="btn" title="Mes Actual">Hoy</a>
                        <a href="{{ route('calendario.index', ['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}" class="btn" title="Mes Siguiente">&rarr;</a>
                    </div>
                </div>

                <div class="calendar-grid">
                    <!-- Weekdays -->
                    <div class="weekday-label">Lun</div>
                    <div class="weekday-label">Mar</div>
                    <div class="weekday-label">Mié</div>
                    <div class="weekday-label">Jue</div>
                    <div class="weekday-label">Vie</div>
                    <div class="weekday-label">Sáb</div>
                    <div class="weekday-label">Dom</div>

                    <!-- Blank cells before start of month -->
                    @for ($i = 0; $i < $blankDays; $i++)
                        <div class="calendar-day-cell blank"></div>
                    @endfor

                    <!-- Days in Month -->
                    @for ($day = 1; $day <= $daysInMonth; $day++)
                        @php
                            $dayDateString = sprintf('%04d-%02d-%02d', $currentDate->year, $currentDate->month, $day);
                            $citasCount = $citasPorDia->get($dayDateString, 0);
                            $isSelected = ($selectedDate === $dayDateString);
                        @endphp
                        <a href="{{ route('calendario.index', ['month' => $currentDate->month, 'year' => $currentDate->year, 'date' => $dayDateString]) }}" 
                           class="calendar-day-cell {{ $isSelected ? 'selected' : '' }}"
                           data-date="{{ $dayDateString }}">
                            <span class="day-num">{{ $day }}</span>
                            @if ($citasCount > 0)
                                <span class="day-citas-indicator">{{ $citasCount }} {{ $citasCount == 1 ? 'cita' : 'citas' }}</span>
                            @endif
                        </a>
                    @endfor
                </div>
            </div>

            <!-- Slots Card -->
            <div class="card" id="slots-section">
                @if ($selectedDate)
                    @php
                        $dateFormatted = \Carbon\Carbon::parse($selectedDate)->format('d/m/Y');
                    @endphp
                    <div class="schedule-header">
                        <div>
                            <h3 class="schedule-title">Horarios Disponibles</h3>
                            <div class="schedule-subtitle">Agenda del día {{ $dateFormatted }}</div>
                        </div>
                    </div>

                    <div class="slots-container" id="slots-list">
                        @if (count($slots) > 0)
                            @foreach ($slots as $slot)
                                <div class="slot-card {{ $slot['ocupado'] ? ($slot['cita']['estado'] === 'completada' ? 'completed' : 'occupied') : 'available' }}">
                                    <div class="slot-time">
                                        {{ $slot['hora_inicio'] }}
                                        <span class="slot-duration">{{ $slot['hora_inicio'] }} - {{ $slot['hora_fin'] }}</span>
                                    </div>

                                    @if ($slot['ocupado'])
                                        <div class="slot-info">
                                            <p class="slot-patient">{{ $slot['cita']['paciente_nombre'] }}</p>
                                            <p class="slot-reason" title="{{ $slot['cita']['motivo'] }}">{{ $slot['cita']['motivo'] }}</p>
                                        </div>
                                        <span class="badge badge-{{ $slot['cita']['estado'] }}">
                                            {{ $slot['cita']['estado'] }}
                                        </span>
                                    @else
                                        <div class="slot-info" style="color: var(--success-text); font-style: italic; font-size: 14px;">
                                            Bloque horario disponible para agendamiento
                                        </div>
                                        <span class="badge badge-available">
                                            Disponible
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <div class="empty-slots-state">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p>No se pudieron generar bloques horarios para este día.</p>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="empty-slots-state">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 style="margin: 0 0 8px 0; color: var(--text-dark);">Seleccione un día</h3>
                        <p style="margin: 0;">Haga click en cualquier día del calendario que tenga citas registradas o esté disponible para ver su agenda horaria detallada.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Vanilla JS progressive enhancement for smooth AJAX selection of slots -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const dayCells = document.querySelectorAll('.calendar-day-cell:not(.blank)');
            const slotsSection = document.getElementById('slots-section');

            dayCells.forEach(cell => {
                cell.addEventListener('click', (e) => {
                    // Only enhance with AJAX, fallback to link behavior if anything goes wrong
                    const date = cell.getAttribute('data-date');
                    if (!date) return;

                    e.preventDefault();

                    // Update visually selected day
                    dayCells.forEach(c => c.classList.remove('selected'));
                    cell.classList.add('selected');

                    // Fetch slots for selected date via AJAX
                    fetch(`{{ route('calendario.slots') }}?date=${date}`)
                        .then(response => {
                            if (!response.ok) throw new Error('Network response was not ok');
                            return response.json();
                        })
                        .then(data => {
                            renderSlots(data.date, data.slots);
                            // Update browser URL query string without reloading page
                            const url = new URL(window.location);
                            url.searchParams.set('date', date);
                            window.history.pushState({}, '', url);
                        })
                        .catch(err => {
                            console.error('Error fetching slots:', err);
                            // Fallback: reload the page using the standard href
                            window.location.href = cell.getAttribute('href');
                        });
                });
            });

            function renderSlots(dateStr, slots) {
                // Parse date into readable Spanish format
                const parts = dateStr.split('-');
                const formattedDate = `${parts[2]}/${parts[1]}/${parts[0]}`;

                let html = `
                    <div class="schedule-header">
                        <div>
                            <h3 class="schedule-title">Horarios Disponibles</h3>
                            <div class="schedule-subtitle">Agenda del día ${formattedDate}</div>
                        </div>
                    </div>
                    <div class="slots-container" id="slots-list">
                `;

                if (slots.length > 0) {
                    slots.forEach(slot => {
                        const cardClass = slot.ocupado 
                            ? (slot.cita.estado === 'completada' ? 'completed' : 'occupied') 
                            : 'available';

                        html += `
                            <div class="slot-card ${cardClass}">
                                <div class="slot-time">
                                    ${slot.hora_inicio}
                                    <span class="slot-duration">${slot.hora_inicio} - ${slot.hora_fin}</span>
                                </div>
                        `;

                        if (slot.ocupado) {
                            html += `
                                <div class="slot-info">
                                    <p class="slot-patient">${escapeHtml(slot.cita.paciente_nombre)}</p>
                                    <p class="slot-reason" title="${escapeHtml(slot.cita.motivo || '')}">${escapeHtml(slot.cita.motivo || '')}</p>
                                </div>
                                <span class="badge badge-${slot.cita.estado}">
                                    ${slot.cita.estado}
                                </span>
                            `;
                        } else {
                            html += `
                                <div class="slot-info" style="color: var(--success-text); font-style: italic; font-size: 14px;">
                                    Bloque horario disponible para agendamiento
                                </div>
                                <span class="badge badge-available">
                                    Disponible
                                </span>
                            `;
                        }

                        html += `</div>`;
                    });
                } else {
                    html += `
                        <div class="empty-slots-state">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <p>No se pudieron generar bloques horarios para este día.</p>
                        </div>
                    `;
                }

                html += `</div>`;
                slotsSection.innerHTML = html;
            }

            function escapeHtml(str) {
                if (!str) return '';
                return str
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;");
            }
        });
    </script>
</body>

</html>
