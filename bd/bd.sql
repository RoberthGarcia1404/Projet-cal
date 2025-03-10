-- Crear la base de datos si no existe y seleccionarla
CREATE DATABASE IF NOT EXISTS appointments_db;
USE appointments_db;

-- ====================================================
-- Tabla: users
-- Esta tabla almacena la información de los usuarios que se registran,
-- incluyendo el nombre, correo, teléfono, código de país, contraseña y rol.
-- ====================================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone VARCHAR(50) NOT NULL,
    country_code VARCHAR(10) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'customer') DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ====================================================
-- Tabla: business_info
-- Almacena la información del negocio de cada usuario (admin),
-- incluyendo nombre, foto, redes sociales y la configuración del rango de reserva:
-- scheduling_min_days: días mínimos de antelación para agendar (0 = hoy)
-- scheduling_max_days: días máximos a futuro permitidos para agendar.
-- Se relaciona con la tabla users mediante user_id.
-- ====================================================
CREATE TABLE IF NOT EXISTS business_info (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    business_name VARCHAR(255) NOT NULL,
    photo VARCHAR(255) DEFAULT NULL,
    facebook VARCHAR(255) DEFAULT NULL,
    instagram VARCHAR(255) DEFAULT NULL,
    twitter VARCHAR(255) DEFAULT NULL,
    scheduling_min_days INT DEFAULT 0,
    scheduling_max_days INT DEFAULT 30,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_business_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ====================================================
-- Tabla: working_hours
-- Define, para cada usuario y para cada día de la semana (1 = lunes, …, 7 = domingo),
-- si se atiende, el horario de inicio, el horario de fin y la duración de cada cita en minutos.
-- Se impone una restricción UNIQUE en (user_id, day_of_week) para evitar duplicados.
-- ====================================================
CREATE TABLE IF NOT EXISTS working_hours (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    day_of_week TINYINT NOT NULL,  -- 1 = lunes, ... 7 = domingo
    is_working TINYINT(1) NOT NULL DEFAULT 0,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    appointment_duration INT NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_user_day (user_id, day_of_week),
    CONSTRAINT fk_working_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ====================================================
-- Tabla: appointments
-- Registra las citas agendadas por los clientes en el calendario de un admin.
-- Incluye la fecha, hora y datos del cliente.
-- Se relaciona con la tabla users mediante user_id (el admin dueño del calendario).
-- Se crea un índice para optimizar consultas por fecha y hora.
-- ====================================================
CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    customer_name VARCHAR(255) NOT NULL,
    customer_phone VARCHAR(50) NOT NULL,
    customer_email VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_appointment_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_date_time (user_id, appointment_date, appointment_time)
) ENGINE=InnoDB;
