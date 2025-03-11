-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 11-03-2025 a las 01:15:55
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `appointments_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_phone` varchar(50) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `appointments`
--

INSERT INTO `appointments` (`id`, `user_id`, `appointment_date`, `appointment_time`, `customer_name`, `customer_phone`, `customer_email`, `created_at`) VALUES
(1, 1, '2025-03-15', '09:00:00', 'hola', '123', 'fajob99221@makroyal.com', '2025-03-10 23:56:15'),
(2, 1, '2025-03-11', '11:00:00', 'hola 2', '456', 'fajob99221@makroyal.com', '2025-03-10 23:59:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `business_info`
--

CREATE TABLE `business_info` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `business_name` varchar(255) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `twitter` varchar(255) DEFAULT NULL,
  `scheduling_min_days` int(11) DEFAULT 0,
  `scheduling_max_days` int(11) DEFAULT 30,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `business_info`
--

INSERT INTO `business_info` (`id`, `user_id`, `business_name`, `photo`, `facebook`, `instagram`, `twitter`, `scheduling_min_days`, `scheduling_max_days`, `updated_at`) VALUES
(1, 1, 'Koala', '', '', '', '', 1, 5, '2025-03-11 00:15:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `country_code` varchar(10) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','customer') DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `country_code`, `password`, `role`, `created_at`) VALUES
(1, 'Roberth', 'fajob99221@makroyal.com', '3228761513', '+1', '$2y$10$ZhBCJ1lqnQm.eqW0qGvqre5nJ3jBckLVwpprkXRvNpcHY/ja98XpC', 'admin', '2025-03-10 23:50:09');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `working_hours`
--

CREATE TABLE `working_hours` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `day_of_week` tinyint(4) NOT NULL,
  `is_working` tinyint(1) NOT NULL DEFAULT 0,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `appointment_duration` int(11) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `working_hours`
--

INSERT INTO `working_hours` (`id`, `user_id`, `day_of_week`, `is_working`, `start_time`, `end_time`, `appointment_duration`, `updated_at`) VALUES
(1, 1, 1, 1, '09:00:00', '17:00:00', 60, '2025-03-10 23:50:44'),
(2, 1, 2, 1, '09:00:00', '17:00:00', 60, '2025-03-10 23:50:44'),
(3, 1, 3, 1, '09:00:00', '17:00:00', 60, '2025-03-10 23:50:44'),
(4, 1, 4, 1, '09:00:00', '17:00:00', 60, '2025-03-10 23:50:44'),
(5, 1, 5, 1, '09:00:00', '17:00:00', 60, '2025-03-10 23:50:44'),
(6, 1, 6, 1, '09:00:00', '12:00:00', 60, '2025-03-10 23:50:44'),
(7, 1, 7, 0, '09:00:00', '17:00:00', 60, '2025-03-10 23:50:44');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_date_time` (`user_id`,`appointment_date`,`appointment_time`);

--
-- Indices de la tabla `business_info`
--
ALTER TABLE `business_info`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_business_user` (`user_id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `working_hours`
--
ALTER TABLE `working_hours`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_user_day` (`user_id`,`day_of_week`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `business_info`
--
ALTER TABLE `business_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `working_hours`
--
ALTER TABLE `working_hours`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `fk_appointment_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `business_info`
--
ALTER TABLE `business_info`
  ADD CONSTRAINT `fk_business_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `working_hours`
--
ALTER TABLE `working_hours`
  ADD CONSTRAINT `fk_working_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
