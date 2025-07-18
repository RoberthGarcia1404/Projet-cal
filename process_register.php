<?php
// process_register.php
session_start();
include('db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $country_code = $conn->real_escape_string($_POST['country_code']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        die("Las contraseñas no coinciden. <a href='index.php'>Volver</a>");
    }

    // Hashear la contraseña
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // En este ejemplo, el primer usuario se considerará admin (puedes ajustar según tu lógica)
    $sql = "INSERT INTO users (name, email, phone, country_code, password, role) VALUES ('$name', '$email', '$phone', '$country_code', '$hashed_password', 'admin')";
    if ($conn->query($sql) === TRUE) {
        $_SESSION['user_email'] = $email;
        $_SESSION['user_role'] = 'admin';
        header("Location: admin_dashboard.php");
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}
$conn->close();
?>
