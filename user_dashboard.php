<?php
// user_dashboard.php
session_start();
if (!isset($_SESSION['user_email']) || $_SESSION['user_role'] !== 'customer') {
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
            <li><a href="user_dashboard.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="user_appointments.php"><i class="fas fa-calendar-alt"></i> Ver Citas</a></li>
        </ul>
    </nav>
    <main>
        <section>
            <h2>Inicio</h2>
            <p>Aquí podrás ver y gestionar tus citas.</p>
        </section>
    </main>
    <footer>
        <a href="logout.php">Cerrar sesión</a>
    </footer>
    <script src="js/dashboard.js"></script>
</body>
</html>
