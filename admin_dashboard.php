<?php
// admin_dashboard.php
session_start();
if (!isset($_SESSION['user_email']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: index.php");
    exit;
}
include('db.php');

// Para este ejemplo usamos un user_id fijo; en producción, usa el id del usuario autenticado.
$user_id = 1;

// Procesamiento de actualización de información del negocio
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_business'])) {
    $business_name = $conn->real_escape_string($_POST['business_name']);
    $facebook = $conn->real_escape_string($_POST['facebook']);
    $instagram = $conn->real_escape_string($_POST['instagram']);
    $twitter = $conn->real_escape_string($_POST['twitter']);
    
    // Subida de foto
    $photoPath = '';
    if (isset($_FILES['photo_file']) && $_FILES['photo_file']['error'] == 0) {
        $uploadDir = "uploads/";
        $fileName = uniqid() . "_" . basename($_FILES['photo_file']['name']);
        $uploadFile = $uploadDir . $fileName;
        if (move_uploaded_file($_FILES['photo_file']['tmp_name'], $uploadFile)) {
            $photoPath = $conn->real_escape_string($uploadFile);
        } else {
            echo "Error al subir la foto.";
        }
    }
    
    $result = $conn->query("SELECT id FROM business_info WHERE user_id = $user_id LIMIT 1");
    if ($result->num_rows > 0) {
        if ($photoPath != '') {
            $conn->query("UPDATE business_info SET business_name='$business_name', photo='$photoPath', facebook='$facebook', instagram='$instagram', twitter='$twitter' WHERE user_id=$user_id");
        } else {
            $conn->query("UPDATE business_info SET business_name='$business_name', facebook='$facebook', instagram='$instagram', twitter='$twitter' WHERE user_id=$user_id");
        }
    } else {
        $conn->query("INSERT INTO business_info (user_id, business_name, photo, facebook, instagram, twitter) VALUES ($user_id, '$business_name', '$photoPath', '$facebook', '$instagram', '$twitter')");
    }
}

// Procesamiento de actualización de horarios
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_working'])) {
    for ($day = 1; $day <= 7; $day++) {
        $is_working = isset($_POST["working_$day"]) ? 1 : 0;
        $start_time = $_POST["start_time_$day"];
        $end_time = $_POST["end_time_$day"];
        $duration = $_POST["duration_$day"];
        
        $result = $conn->query("SELECT id FROM working_hours WHERE user_id = $user_id AND day_of_week = $day");
        if ($result->num_rows > 0) {
            $conn->query("UPDATE working_hours SET is_working=$is_working, start_time='$start_time', end_time='$end_time', appointment_duration=$duration WHERE user_id = $user_id AND day_of_week=$day");
        } else {
            $conn->query("INSERT INTO working_hours (user_id, day_of_week, is_working, start_time, end_time, appointment_duration) VALUES ($user_id, $day, $is_working, '$start_time', '$end_time', $duration)");
        }
    }
}

// Obtener información actual del negocio y horarios
$business = $conn->query("SELECT * FROM business_info WHERE user_id = $user_id LIMIT 1")->fetch_assoc();
$working = [];
$result = $conn->query("SELECT * FROM working_hours WHERE user_id = $user_id");
while ($row = $result->fetch_assoc()) {
    $working[$row['day_of_week']] = $row;
}

// Obtener citas agendadas ordenadas por fecha y hora
$appointments = [];
$result = $conn->query("SELECT * FROM appointments WHERE user_id = $user_id ORDER BY appointment_date, appointment_time");
while ($row = $result->fetch_assoc()) {
    $appointments[] = $row;
}

function dayName($dayNum) {
    $names = [1 => "Lunes", 2 => "Martes", 3 => "Miércoles", 4 => "Jueves", 5 => "Viernes", 6 => "Sábado", 7 => "Domingo"];
    return $names[$dayNum];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Admin - Agendamiento de Citas</title>
  <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
  <header>
    <h1>Dashboard Admin</h1>
    <nav class="dashboard-menu">
      <ul>
        <li><a href="admin_dashboard.php">Home</a></li>
        <li><a href="admin_appointments.php">Admin Citas</a></li>
        <li><a href="admin_view_appointments.php">Ver Citas</a></li>
      </ul>
    </nav>
  </header>
  <main>
    <!-- Aquí se pueden incluir módulos según la opción del menú -->
    <section>
      <h2>Configuración del Negocio</h2>
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="update_business" value="1">
        <label>Nombre del negocio:</label>
        <input type="text" name="business_name" value="<?php echo isset($business['business_name']) ? $business['business_name'] : ''; ?>" required>
        <label>Subir Foto del Negocio:</label>
        <input type="file" name="photo_file" accept="image/*">
        <label>Facebook:</label>
        <input type="text" name="facebook" value="<?php echo isset($business['facebook']) ? $business['facebook'] : ''; ?>">
        <label>Instagram:</label>
        <input type="text" name="instagram" value="<?php echo isset($business['instagram']) ? $business['instagram'] : ''; ?>">
        <label>Twitter:</label>
        <input type="text" name="twitter" value="<?php echo isset($business['twitter']) ? $business['twitter'] : ''; ?>">
        <button type="submit">Actualizar Negocio</button>
      </form>
    </section>
    
    <section>
      <h2>Configuración de Horarios</h2>
      <form method="POST">
        <input type="hidden" name="update_working" value="1">
        <table border="1">
          <tr>
            <th>Día</th>
            <th>¿Trabaja?</th>
            <th>Hora Inicio</th>
            <th>Hora Fin</th>
            <th>Duración de Cita (min)</th>
          </tr>
          <?php for ($day = 1; $day <= 7; $day++): 
            $data = isset($working[$day]) ? $working[$day] : ['is_working'=>0, 'start_time'=>'09:00:00', 'end_time'=>'17:00:00', 'appointment_duration'=>60];
          ?>
          <tr>
            <td><?php echo dayName($day); ?></td>
            <td><input type="checkbox" name="working_<?php echo $day; ?>" <?php echo $data['is_working'] ? 'checked' : ''; ?>></td>
            <td><input type="time" name="start_time_<?php echo $day; ?>" value="<?php echo substr($data['start_time'], 0, 5); ?>"></td>
            <td><input type="time" name="end_time_<?php echo $day; ?>" value="<?php echo substr($data['end_time'], 0, 5); ?>"></td>
            <td><input type="number" name="duration_<?php echo $day; ?>" value="<?php echo $data['appointment_duration']; ?>" min="5"></td>
          </tr>
          <?php endfor; ?>
        </table>
        <button type="submit">Actualizar Horarios</button>
      </form>
    </section>
    
    <section>
      <h2>Citas Programadas</h2>
      <?php if(count($appointments) > 0): ?>
        <table border="1">
          <tr>
            <th>Fecha</th>
            <th>Hora</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Teléfono</th>
          </tr>
          <?php foreach($appointments as $appt): ?>
            <tr>
              <td><?php echo $appt['appointment_date']; ?></td>
              <td><?php echo $appt['appointment_time']; ?></td>
              <td><?php echo $appt['customer_name']; ?></td>
              <td><?php echo $appt['customer_email']; ?></td>
              <td><?php echo $appt['customer_phone']; ?></td>
            </tr>
          <?php endforeach; ?>
        </table>
      <?php else: ?>
        <p>No hay citas programadas.</p>
      <?php endif; ?>
    </section>
  </main>
  <footer>
    <a href="logout.php">Cerrar sesión</a>
  </footer>
</body>
</html>
<?php $conn->close(); ?>
