<?php
// process_login.php
session_start();
include('db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_email'] = $email;
            $_SESSION['user_role'] = $user['role'];
            if ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: user_dashboard.php");
            }
            exit;
        } else {
            echo "Contraseña incorrecta. <a href='index.php'>Volver</a>";
        }
    } else {
        echo "Usuario no encontrado. <a href='index.php'>Volver</a>";
    }
}
$conn->close();
?>
