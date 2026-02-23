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

-- Volcando datos para la tabla mydb.actividades: ~1 rows (aproximadamente)
INSERT INTO `actividades` (`id`, `nombre`, `categoria_id`, `created_at`, `updated_at`) VALUES
	(1, 'Fútbol', 1, '2026-02-18 19:14:09', NULL);

-- Volcando datos para la tabla mydb.cache: ~0 rows (aproximadamente)

-- Volcando datos para la tabla mydb.cache_locks: ~0 rows (aproximadamente)

-- Volcando datos para la tabla mydb.categorias: ~0 rows (aproximadamente)
INSERT INTO `categorias` (`id`, `nombre`, `created_at`, `updated_at`) VALUES
	(1, 'Deportiva', '2026-02-18 19:14:09', NULL);

-- Volcando datos para la tabla mydb.ciclos_escolares: ~0 rows (aproximadamente)
INSERT INTO `ciclos_escolares` (`id`, `nombre`, `activo`, `created_at`, `updated_at`) VALUES
	(1, '2025-2026', 1, '2026-02-18 19:14:09', NULL);

-- Volcando datos para la tabla mydb.docentes: ~5 rows (aproximadamente)
INSERT INTO `docentes` (`id`, `nombre`, `apellidos`, `email`, `estatus`, `created_at`, `updated_at`) VALUES
	(1, 'Roberto', 'Gómez Bolaños', 'chespirito@escuela.edu.mx', 'Activo', '2026-02-19 01:15:45', '2026-02-19 01:15:45'),
	(3, 'Maestro2', 'maestro', 'maestro@gmail.com', 'Activo', '2026-02-19 21:47:27', '2026-02-19 21:47:27'),
	(4, 'Docente1', '1', 'docente@gmail.com', 'Activo', '2026-02-19 21:49:42', '2026-02-19 21:49:42'),
	(5, 'd1', 'd', 'd@gmail.com', 'Activo', '2026-02-19 21:57:29', '2026-02-19 21:57:29'),
	(6, 'a', 'B', 'TEST@gmail.com', 'Activo', '2026-02-20 00:04:21', '2026-02-20 00:04:21');

-- Volcando datos para la tabla mydb.espacios: ~2 rows (aproximadamente)
INSERT INTO `espacios` (`id`, `nombre`, `created_at`, `updated_at`, `capacidad`) VALUES
	(1, 'Aula 11', '2026-02-19 01:14:23', '2026-02-20 00:37:16', 37),
	(3, 'Aula101', '2026-02-20 01:38:01', '2026-02-20 01:38:01', 30);

-- Volcando datos para la tabla mydb.failed_jobs: ~0 rows (aproximadamente)

-- Volcando datos para la tabla mydb.grupos: ~1 rows (aproximadamente)
INSERT INTO `grupos` (`id`, `nombre`, `cupo_maximo`, `actividad_id`, `docente_id`, `ciclo_id`, `nivel_id`, `created_at`, `updated_at`) VALUES
	(3, 'futbol hombres', 12, 1, 1, 1, 1, '2026-02-19 22:06:17', '2026-02-19 22:06:17'),
	(4, 'Futbol Femenino', 20, 1, 1, 1, 1, '2026-02-20 01:39:50', '2026-02-20 01:39:50');

-- Volcando datos para la tabla mydb.horarios: ~1 rows (aproximadamente)
INSERT INTO `horarios` (`id`, `grupo_id`, `espacio_id`, `dia_semana`, `hora_inicio`, `hora_fin`, `created_at`, `updated_at`) VALUES
	(4, 3, 1, 'Lunes', '18:00:00', '19:00:00', '2026-02-20 00:10:18', '2026-02-20 00:10:18'),
	(5, 3, 3, 'Lunes', '08:00:00', '10:00:00', '2026-02-20 01:39:17', '2026-02-20 01:39:17');

-- Volcando datos para la tabla mydb.jobs: ~0 rows (aproximadamente)

-- Volcando datos para la tabla mydb.job_batches: ~0 rows (aproximadamente)

-- Volcando datos para la tabla mydb.migrations: ~12 rows (aproximadamente)
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
	(12, '2026_02_18_185850_create_personal_access_tokens_table', 1);

-- Volcando datos para la tabla mydb.niveles: ~0 rows (aproximadamente)
INSERT INTO `niveles` (`id`, `nombre`, `created_at`, `updated_at`) VALUES
	(1, 'Bachillerato', '2026-02-18 19:14:09', NULL);

-- Volcando datos para la tabla mydb.password_reset_tokens: ~0 rows (aproximadamente)

-- Volcando datos para la tabla mydb.personal_access_tokens: ~0 rows (aproximadamente)

-- Volcando datos para la tabla mydb.sessions: ~2 rows (aproximadamente)
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
	('Q5NMBl6DnyHgTLtPloId0Zy9bwBtb7OCy5AEqETv', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiVjlSeWV5SE5VWXdSSEtzN2ZrdFdBRWR5b1hGN21zcWtDUURkTmM4ZyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771529456),
	('YW5ZrIPmVXduNx6NOdl5xQRt3Jc2M4vd2LsUqttT', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiN0hVREZQZmdYZmRKN2g3enQ3dnlpZHNrMzAyS2pDc2NNQkM3SUhqWCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMzoiaHR0cDovL3Npc3RlbWFwcmFjdGljYS50ZXN0L2xvZ2luIjtzOjU6InJvdXRlIjtOO319', 1771529166);

-- Volcando datos para la tabla mydb.users: ~0 rows (aproximadamente)
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
	(1, 'Admin UGM', 'admin@ugm.edu.mx', NULL, '$2y$12$Uxpaen4yfmgeJghLiamUg.N5pVFmMUEa5cuGEYKvK8rQwB7Vcnkdm', NULL, '2026-02-19 22:28:03', '2026-02-19 22:28:03');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
