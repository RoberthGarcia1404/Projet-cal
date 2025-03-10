<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Bienvenido - Calendario de Citas</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Enlace al CSS específico para login/registro -->
  <link rel="stylesheet" href="css/login.css">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
  <div class="welcome-container">
    <div class="form-toggle">
      <button id="login-toggle" class="active">Login</button>
      <button id="register-toggle">Crear Cuenta</button>
    </div>
    <!-- Login Form -->
    <div id="login-form" class="form-panel">
      <h2>Iniciar Sesión</h2>
      <form action="process_login.php" method="POST">
        <label for="login_email">Correo Electrónico</label>
        <input type="email" id="login_email" name="email" placeholder="Ingresa tu correo" required>
        
        <label for="login_password">Contraseña</label>
        <input type="password" id="login_password" name="password" placeholder="Ingresa tu contraseña" required>
        
        <button type="submit" class="btn-primary">Iniciar Sesión</button>
      </form>
    </div>
    <!-- Register Form -->
    <div id="register-form" class="form-panel hidden">
      <h2>Crear Cuenta</h2>
      <form action="process_register.php" method="POST" id="registerForm">
        <!-- Campo Nombre -->
        <label for="reg_name">Nombre</label>
        <input type="text" id="reg_name" name="name" placeholder="Ingresa tu nombre" required>
        
        <label for="reg_email">Correo Electrónico</label>
        <input type="email" id="reg_email" name="email" placeholder="Ingresa tu correo" required>
        
        <label for="reg_phone">Teléfono</label>
        <div class="phone-input">
          <select name="country_code" id="country_code" required>
            <option value="+1">+1 (USA)</option>
            <option value="+44">+44 (UK)</option>
            <option value="+34">+34 (España)</option>
            <option value="+52">+52 (México)</option>
          </select>
          <input type="text" id="reg_phone" name="phone" placeholder="Ingresa tu teléfono" required>
        </div>
        
        <label for="reg_password">Contraseña</label>
        <input type="password" id="reg_password" name="password" placeholder="Ingresa tu contraseña" required>
        
        <label for="reg_confirm_password">Confirmar Contraseña</label>
        <input type="password" id="reg_confirm_password" name="confirm_password" placeholder="Confirma tu contraseña" required>
        
        <button type="submit" class="btn-primary">Crear Cuenta</button>
      </form>
    </div>
  </div>
  <script src="js/login.js"></script>
</body>
</html>
