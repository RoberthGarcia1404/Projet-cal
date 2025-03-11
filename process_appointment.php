<?php
// process_appointment.php
include('db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $name = $conn->real_escape_string($_POST['name']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $email = $conn->real_escape_string($_POST['email']);
    
    // En este ejemplo, el calendario pertenece al admin con user_id = 1
    $user_id = 1;
    
    $stmt = $conn->prepare("INSERT INTO appointments (user_id, appointment_date, appointment_time, customer_name, customer_phone, customer_email) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $user_id, $appointment_date, $appointment_time, $name, $phone, $email);
    if ($stmt->execute()) {
        echo "Cita confirmada. <a href='schedule.php'>Volver</a>";
    } else {
        echo "Error al agendar la cita: " . $conn->error;
    }
    $stmt->close();
}
$conn->close();
?>