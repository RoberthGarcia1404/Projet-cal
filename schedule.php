<?php
// schedule.php
include('db.php');

// Obtener la información del negocio
$business = $conn->query("SELECT * FROM business_info LIMIT 1")->fetch_assoc();

// Obtener la configuración de horarios (donde is_working = 1)
$working_hours = [];
$result = $conn->query("SELECT * FROM working_hours WHERE is_working = 1");
while ($row = $result->fetch_assoc()) {
    // La clave es el día de la semana (1 = lunes, …, 7 = domingo)
    $working_hours[$row['day_of_week']] = $row;
}

// Obtener todas las citas agendadas para el admin (aquí user_id = 1)
$appointments = [];
$result = $conn->query("SELECT appointment_date, appointment_time FROM appointments WHERE user_id = 1");
while ($row = $result->fetch_assoc()) {
    $appointments[] = $row;
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Agendar Cita</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Enlaza el CSS para el calendario -->
  <link rel="stylesheet" href="css/calendar.css">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
  <header>
    <div class="header-container">
      <h1><?php echo isset($business['business_name']) ? $business['business_name'] : "Agendar Cita"; ?></h1>
    </div>
  </header>
  <main>
    <div class="app-container">
      <!-- Sección del Calendario -->
      <section class="calendar-section">
        <div class="calendar-header">
          <button id="prev-month" class="nav-btn">&lt;</button>
          <div id="month-year" class="month-year"></div>
          <button id="next-month" class="nav-btn">&gt;</button>
        </div>
        <!-- Días de la semana -->
        <div class="weekdays">
          <div>Lun</div>
          <div>Mar</div>
          <div>Mié</div>
          <div>Jue</div>
          <div>Vie</div>
          <div>Sáb</div>
          <div>Dom</div>
        </div>
        <!-- Grid de días -->
        <div id="calendar" class="calendar"></div>
      </section>
      <!-- Sección de Horarios -->
      <aside class="time-section">
        <h2>Horarios Disponibles</h2>
        <div id="time-list" class="time-list">
          <p>Selecciona un día para ver horarios</p>
        </div>
      </aside>
    </div>
  </main>
  <!-- Modal para confirmar la cita -->
  <div id="appointment-modal" class="modal hidden">
    <div class="modal-content">
      <button class="modal-close" id="modal-close">&times;</button>
      <h2>Confirmar Cita</h2>
      <p id="appointment-summary">Detalles de la cita</p>
      <form id="appointment-form" action="process_appointment.php" method="POST">
        <!-- Campos ocultos para fecha y hora -->
        <input type="hidden" name="appointment_date" id="appointment_date">
        <input type="hidden" name="appointment_time" id="appointment_time">
        <label for="name">Tu nombre</label>
        <input type="text" id="name" name="name" placeholder="Ingresa tu nombre" required>
        <label for="phone">Teléfono</label>
        <input type="text" id="phone" name="phone" placeholder="Ingresa tu teléfono" required>
        <label for="email">Correo electrónico</label>
        <input type="email" id="email" name="email" placeholder="Ingresa tu correo" required>
        <div class="modal-buttons">
          <button type="submit" class="btn-primary">Confirmar</button>
        </div>
      </form>
    </div>
  </div>
  <!-- Pasar configuración de horarios y citas ya agendadas a JavaScript -->
  <script>
    const workingHours = <?php echo json_encode($working_hours); ?>;
    const bookedAppointments = <?php echo json_encode($appointments); ?>;
  </script>
  <script src="js/calendar.js"></script>
</body>
</html>
