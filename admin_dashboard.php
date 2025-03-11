<?php
// admin_dashboard.php
session_start();
if (!isset($_SESSION['user_email']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: index.php");
    exit;
}
include('db.php');

// Obtener información del usuario para mostrar en el dashboard
$user_email = $_SESSION['user_email'];
$sql = "SELECT * FROM users WHERE email='$user_email' LIMIT 1";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

$user_id = $user['id'];

// Función para obtener el nombre del día
function dayName($dayNum) {
    $names = [1 => "Lunes", 2 => "Martes", 3 => "Miércoles", 4 => "Jueves", 5 => "Viernes", 6 => "Sábado", 7 => "Domingo"];
    return $names[$dayNum];
}

// Procesar actualización de información del negocio
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_business'])) {
    $business_name = $conn->real_escape_string($_POST['business_name']);
    $facebook = $conn->real_escape_string($_POST['facebook']);
    $instagram = $conn->real_escape_string($_POST['instagram']);
    $twitter = $conn->real_escape_string($_POST['twitter']);
    $scheduling_min_days = $conn->real_escape_string($_POST['scheduling_min_days']);
    $scheduling_max_days = $conn->real_escape_string($_POST['scheduling_max_days']);
    
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
        $updateQuery = "UPDATE business_info SET business_name='$business_name', facebook='$facebook', instagram='$instagram', twitter='$twitter', scheduling_min_days='$scheduling_min_days', scheduling_max_days='$scheduling_max_days'";
        if ($photoPath != '') {
            $updateQuery .= ", photo='$photoPath'";
        }
        $updateQuery .= " WHERE user_id=$user_id";
        $conn->query($updateQuery);
    } else {
        $conn->query("INSERT INTO business_info (user_id, business_name, photo, facebook, instagram, twitter, scheduling_min_days, scheduling_max_days) VALUES ($user_id, '$business_name', '$photoPath', '$facebook', '$instagram', '$twitter', '$scheduling_min_days', '$scheduling_max_days')");
    }
}

// Procesar actualización de horarios
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

// Obtener las citas agendadas ordenadas por fecha y hora
$appointments = [];
$result = $conn->query("SELECT * FROM appointments WHERE user_id = $user_id ORDER BY appointment_date, appointment_time");
while ($row = $result->fetch_assoc()) {
    $appointments[] = $row;
}

// Generar el link del calendario personalizado para este admin
$calendarLink = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/schedule.php?user_id=" . $user_id;

// Agrupar citas por fecha
$groupedAppointments = [];
foreach ($appointments as $appt) {
    $date = $appt['appointment_date'];
    if (!isset($groupedAppointments[$date])) {
        $groupedAppointments[$date] = [];
    }
    $groupedAppointments[$date][] = $appt;
}
ksort($groupedAppointments);

// Obtener formato de hora (por GET, default '24')
$timeFormat = isset($_GET['time_format']) ? $_GET['time_format'] : '24';
if ($timeFormat !== '12' && $timeFormat !== '24') {
    $timeFormat = '24';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - Control Panel</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <!-- Incluir FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <h1>Dashboard Admin</h1>
        <div class="user-info">
            <p><strong>Nombre:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
            <p><strong>Correo:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
        </div>
        <nav class="dashboard-menu">
            <ul>
                <li><a href="#" data-section="home"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="#" data-section="business"><i class="fas fa-building"></i> Configuración del Negocio</a></li>
                <li><a href="#" data-section="working"><i class="fas fa-clock"></i> Configuración de Horarios</a></li>
                <li><a href="#" data-section="appointments"><i class="fas fa-calendar-alt"></i> Citas Programadas</a></li>

            </ul>
        </nav>
    </header>
    <main>
        <!-- Sección Home -->
        <section id="section-home" class="dashboard-section">
            <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION['user_email']); ?></h2>
            <p>Aquí puedes gestionar tu negocio y citas.</p>
        <!-- Sección Link del Calendario -->

            <h2>Link del Calendario</h2>
            <p>Comparte este enlace para que tus clientes puedan agendar citas:</p>
            <input type="text" id="calendar-link-input" value="<?php echo $calendarLink; ?>" readonly>
            <button id="copy-link-btn" class="btn-primary"><i class="fas fa-copy"></i> Copiar Link</button>


        </section>
        
        <!-- Sección Configuración del Negocio -->
        <section id="section-business" class="dashboard-section hidden">
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
                <label>Días mínimos de antelación para reservar:</label>
                <input type="number" name="scheduling_min_days" value="<?php echo isset($business['scheduling_min_days']) ? $business['scheduling_min_days'] : 0; ?>" min="0" required>
                <label>Días máximos para reservar:</label>
                <input type="number" name="scheduling_max_days" value="<?php echo isset($business['scheduling_max_days']) ? $business['scheduling_max_days'] : 30; ?>" min="1" required>
                <button type="submit" class="btn-primary">Actualizar Negocio</button>
            </form>
        </section>
        
        <!-- Sección Configuración de Horarios -->
        <section id="section-working" class="dashboard-section hidden">
            <h2>Configuración de Horarios</h2>
            <form method="POST">
                <input type="hidden" name="update_working" value="1">
                <table>
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
                <button type="submit" class="btn-primary">Actualizar Horarios</button>
            </form>
        </section>
        
        <!-- Sección Citas Programadas -->
        <section id="section-appointments" class="dashboard-section hidden">
            <h2>Citas Programadas</h2>
            <div class="time-format-buttons">
                <button class="time-format-btn <?php echo ($timeFormat==='12') ? 'active' : ''; ?>" onclick="window.location.href='admin_dashboard.php?time_format=12'">12h</button>
                <button class="time-format-btn <?php echo ($timeFormat==='24') ? 'active' : ''; ?>" onclick="window.location.href='admin_dashboard.php?time_format=24'">24h</button>
            </div>
            <?php if (!empty($groupedAppointments)): ?>
                <?php foreach ($groupedAppointments as $date => $appts): 
                    $formattedDate = date("l, d F Y", strtotime($date));
                ?>
                    <h3><?php echo $formattedDate; ?></h3>
                    <ul>
                        <?php 
                        usort($appts, function($a, $b) {
                            return strtotime($a['appointment_time']) - strtotime($b['appointment_time']);
                        });
                        foreach ($appts as $appt): 
                            $time = strtotime($appt['appointment_time']);
                            if ($timeFormat === '12') {
                                $formattedTime = date("g:i A", $time);
                            } else {
                                $formattedTime = date("H:i", $time);
                            }
                        ?>
                            <li class="appointment-item">
                                <span class="appointment-time"><strong><?php echo $formattedTime; ?></strong></span>
                                <span class="appointment-info">
                                    <?php echo htmlspecialchars($appt['customer_name']); ?> - 
                                    <?php echo htmlspecialchars($appt['customer_email']); ?>, 
                                    <?php echo htmlspecialchars($appt['customer_phone']); ?>
                                </span>
                                <div class="action-dropdown">
                                    <button class="btn-primary action-btn"><i class="fas fa-ellipsis-v"></i></button>
                                    <div class="action-dropdown-content">
                                        <a href="process_appointment_action.php?action=complete&id=<?php echo $appt['id']; ?>">Cita Culminada</a>
                                        <a href="process_appointment_action.php?action=cancel&id=<?php echo $appt['id']; ?>">Cancelar Cita</a>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No hay citas programadas.</p>
            <?php endif; ?>
        </section>
        

    </main>
    <footer>
        <a href="logout.php">Cerrar sesión</a>
    </footer>
    <script src="js/dashboard.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const menuLinks = document.querySelectorAll('.dashboard-menu a');
            const sections = document.querySelectorAll('.dashboard-section');

            menuLinks.forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const sectionName = link.getAttribute('data-section');
                    const sectionId = "section-" + sectionName;
                    sections.forEach(sec => {
                        sec.classList.toggle('hidden', sec.id !== sectionId);
                    });
                });
            });
            
            // Dropdown para acciones de cita
            document.querySelectorAll('.action-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    document.querySelectorAll('.action-dropdown').forEach(dd => {
                        if (dd !== btn.parentElement) {
                            dd.classList.remove('show');
                        }
                    });
                    btn.parentElement.classList.toggle('show');
                });
            });
            window.addEventListener('click', () => {
                document.querySelectorAll('.action-dropdown').forEach(dd => dd.classList.remove('show'));
            });
            
            // Copiar el link del calendario
            document.getElementById('copy-link-btn').addEventListener('click', () => {
                const copyText = document.getElementById('calendar-link-input');
                copyText.select();
                copyText.setSelectionRange(0, 99999);
                document.execCommand("copy");
                alert("Link copiado: " + copyText.value);
            });
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>
