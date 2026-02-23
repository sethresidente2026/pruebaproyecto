-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         10.11.14-MariaDB-log - mariadb.org binary distribution
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Volcando datos para la tabla mydb.actividades: ~5 rows (aproximadamente)
INSERT INTO `actividades` (`id`, `nombre`, `categoria_id`, `created_at`, `updated_at`) VALUES
	(2, 'Futbol', NULL, '2026-02-23 23:37:17', '2026-02-23 23:37:17'),
	(3, 'Futbol Americano', NULL, '2026-02-23 23:37:26', '2026-02-23 23:37:26'),
	(4, 'Basketball', NULL, '2026-02-23 23:37:38', '2026-02-23 23:37:38'),
	(5, 'Danza', NULL, '2026-02-24 00:26:05', '2026-02-24 00:26:05'),
	(6, 'Artes', NULL, '2026-02-24 00:26:21', '2026-02-24 00:26:21');

-- Volcando datos para la tabla mydb.asistencias: ~2 rows (aproximadamente)
INSERT INTO `asistencias` (`id`, `grupo_id`, `docente_id`, `docente_sustituto_id`, `fecha`, `estado`, `observaciones`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, NULL, '2026-02-23', 'Asistió', NULL, '2026-02-23 23:47:34', '2026-02-23 23:47:34'),
	(2, 2, 2, NULL, '2026-02-23', 'Asistió', NULL, '2026-02-23 23:47:41', '2026-02-23 23:47:41');

-- Volcando datos para la tabla mydb.cache: ~0 rows (aproximadamente)

-- Volcando datos para la tabla mydb.cache_locks: ~0 rows (aproximadamente)

-- Volcando datos para la tabla mydb.categorias: ~0 rows (aproximadamente)

-- Volcando datos para la tabla mydb.ciclos_escolares: ~4 rows (aproximadamente)
INSERT INTO `ciclos_escolares` (`id`, `nombre`, `activo`, `created_at`, `updated_at`) VALUES
	(1, '2025-2026', 1, '2026-02-23 17:40:17', NULL),
	(2, '2025-2026', 1, '2026-02-23 17:40:22', NULL),
	(3, '2025-2026', 1, '2026-02-23 23:43:42', '2026-02-23 23:43:42'),
	(4, '2026-2027', 1, '2026-02-23 23:43:50', '2026-02-23 23:43:50');

-- Volcando datos para la tabla mydb.docentes: ~3 rows (aproximadamente)
INSERT INTO `docentes` (`id`, `nombre`, `apellidos`, `email`, `estatus`, `created_at`, `updated_at`) VALUES
	(1, 'Roberto', 'Gomez Bolaños', 'chespirito@gmail.com', 'Activo', '2026-02-23 23:38:28', '2026-02-23 23:38:28'),
	(2, 'Brad', 'Pitt', 'bradpit@gmail.com', 'Activo', '2026-02-23 23:38:48', '2026-02-23 23:38:48');

-- Volcando datos para la tabla mydb.espacios: ~3 rows (aproximadamente)
INSERT INTO `espacios` (`id`, `nombre`, `created_at`, `updated_at`, `capacidad`) VALUES
	(1, 'Aula 10', '2026-02-23 23:37:49', '2026-02-23 23:37:49', 30),
	(2, 'Aula 20', '2026-02-23 23:38:00', '2026-02-23 23:38:00', 20),
	(3, 'Aula 15', '2026-02-24 00:28:36', '2026-02-24 00:28:36', 12);

-- Volcando datos para la tabla mydb.failed_jobs: ~0 rows (aproximadamente)

-- Volcando datos para la tabla mydb.grupos: ~3 rows (aproximadamente)
INSERT INTO `grupos` (`id`, `nombre`, `nivel`, `cupo_maximo`, `actividad_id`, `docente_id`, `ciclo_id`, `nivel_id`, `created_at`, `updated_at`) VALUES
	(1, 'Hombres Futbol', 'Mixto', 30, 2, 1, 1, 3, '2026-02-23 23:45:35', '2026-02-23 23:45:35'),
	(2, 'Femenil Futbol', 'Primaria', 34, 2, 2, 1, 3, '2026-02-23 23:46:07', '2026-02-23 23:46:07'),
	(3, 'Varonil Futbol Americano', 'Bachillerato', 30, 3, 1, 1, 5, '2026-02-24 00:04:07', '2026-02-24 00:04:07');

-- Volcando datos para la tabla mydb.horarios: ~2 rows (aproximadamente)
INSERT INTO `horarios` (`id`, `grupo_id`, `espacio_id`, `dia_semana`, `hora_inicio`, `hora_fin`, `created_at`, `updated_at`) VALUES
	(1, 1, 1, 'Lunes', '07:00:00', '08:00:00', '2026-02-23 23:46:33', '2026-02-23 23:46:33'),
	(2, 2, 2, 'Lunes', '07:00:00', '08:00:00', '2026-02-23 23:47:15', '2026-02-23 23:47:15');

-- Volcando datos para la tabla mydb.jobs: ~0 rows (aproximadamente)

-- Volcando datos para la tabla mydb.job_batches: ~0 rows (aproximadamente)

-- Volcando datos para la tabla mydb.migrations: ~14 rows (aproximadamente)
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '0001_01_01_000000_create_users_table', 1),
	(2, '0001_01_01_000001_create_cache_table', 1),
	(3, '0001_01_01_000002_create_jobs_table', 1),
	(4, '2026_02_18_170753_create_nivels_table', 1),
	(5, '2026_02_18_170802_create_ciclo_escolars_table', 1),
	(6, '2026_02_18_171146_create_categorias_table', 1),
	(7, '2026_02_18_171156_create_espacios_table', 1),
	(8, '2026_02_18_171205_create_docentes_table', 1),
	(9, '2026_02_18_171603_create_actividads_table', 1),
	(10, '2026_02_18_171614_create_grupos_table', 1),
	(11, '2026_02_18_172038_create_horarios_table', 1),
	(12, '2026_02_18_185850_create_personal_access_tokens_table', 1),
	(13, '2026_02_23_153845_add_nivel_a_grupos_table', 1),
	(14, '2026_02_23_155208_create_asistencias_table', 1);

-- Volcando datos para la tabla mydb.niveles: ~6 rows (aproximadamente)
INSERT INTO `niveles` (`id`, `nombre`, `created_at`, `updated_at`) VALUES
	(1, 'PREESCOLAR', '2026-02-23 23:45:06', '2026-02-23 23:45:06'),
	(2, 'PRIMARIA', '2026-02-23 23:45:06', '2026-02-23 23:45:06'),
	(3, 'SECUNDARIA', '2026-02-23 23:45:06', '2026-02-23 23:45:06'),
	(4, 'BACHILLERATO', '2026-02-23 23:45:06', '2026-02-23 23:45:06'),
	(5, 'LICENCIATURA', '2026-02-23 23:45:06', '2026-02-23 23:45:06'),
	(6, 'MAESTRÍA', '2026-02-23 23:45:06', '2026-02-23 23:45:06');

-- Volcando datos para la tabla mydb.password_reset_tokens: ~0 rows (aproximadamente)

-- Volcando datos para la tabla mydb.personal_access_tokens: ~0 rows (aproximadamente)

-- Volcando datos para la tabla mydb.sessions: ~1 rows (aproximadamente)
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
	('CdXAgrbXLzP1L4uYfZFFt14L4zyDnCGjLxNoDG5U', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiTGlsb21BUkoyRXhqMThPMjJRWUNzb0NlZ1R5amtpT0tvY3pOeTB0MyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771867133);

-- Volcando datos para la tabla mydb.users: ~2 rows (aproximadamente)
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
	(1, 'Test User', 'test@example.com', '2026-02-23 23:17:26', '$2y$12$xViqN97/GUySIlNRF1/0Xu81vTd6qV7aWGZrOGf0w929eTvrn8m5a', 'yaLpfVdOE5', '2026-02-23 23:17:26', '2026-02-23 23:17:26'),
	(2, 'YO', 'prueba@gmail.com', NULL, '$2y$12$j3xieaShCPgWU2LtDQMTU.aTMSV/n2LrXnfvRJD6.wjKswJ8SVpf6', NULL, '2026-02-23 23:18:30', '2026-02-23 23:18:30');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
