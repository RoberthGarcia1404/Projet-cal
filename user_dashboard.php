<?php
// user_dashboard.php
session_start();
if (!isset($_SESSION['user_email'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard del Usuario</title>
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
    <header>
        <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['user_email']); ?></h1>
    </header>
    <nav class="dashboard-menu">
        <ul>
            <li><a href="user_dashboard.php">Home</a></li>
            <li><a href="user_appointments.php">Ver Citas</a></li>
            <!-- Puedes agregar más módulos -->
        </ul>
    </nav>
    <main>
        <p>Aquí podrás gestionar tu perfil y ver tus citas.</p>
        <a href="logout.php">Cerrar sesión</a>
    </main>
    <script src="js/dashboard.js"></script>
</body>
</html>
