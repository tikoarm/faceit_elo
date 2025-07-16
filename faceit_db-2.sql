-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Хост: faceit_db
-- Время создания: Июл 16 2025 г., 09:40
-- Версия сервера: 8.0.42
-- Версия PHP: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `faceit_db`
--

-- --------------------------------------------------------

--
-- Структура таблицы `cp_users`
--

CREATE TABLE `cp_users` (
  `id` int NOT NULL,
  `username` varchar(32) NOT NULL,
  `password` varchar(256) NOT NULL,
  `reg_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `cp_users`
--

INSERT INTO `cp_users` (`id`, `username`, `password`, `reg_date`) VALUES
(1, 'bauld', '$2y$10$r1etxN7q.t/7zKmDAP3l7ukbKuJTt7K3oCKKAgftODDeF7h350H2C', '2025-07-12 20:15:48');

-- --------------------------------------------------------

--
-- Структура таблицы `matches`
--

CREATE TABLE `matches` (
  `id` int NOT NULL,
  `userid` int NOT NULL,
  `elo_before` int NOT NULL,
  `elo_after` int NOT NULL,
  `elo_difference` int NOT NULL,
  `win` tinyint(1) NOT NULL,
  `map` varchar(16) NOT NULL,
  `nickname` varchar(32) NOT NULL,
  `gameid` varchar(64) NOT NULL,
  `finish` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `matches`
--

INSERT INTO `matches` (`id`, `userid`, `elo_before`, `elo_after`, `elo_difference`, `win`, `map`, `nickname`, `gameid`, `finish`) VALUES
(8, 4, 4132, 4103, 29, 0, 'de_ancient', 'Bymas', '1-dc297f16-0c28-40d7-a81b-b10eff1a56a6', '2025-06-21 18:44:26'),
(9, 4, 3432, 3456, 24, 1, 'de_nuke', 'Qlocuu', '1-bafd3a87-7842-4b79-be02-87dccc486582', '2025-06-21 18:44:26'),
(10, 3, 2189, 2207, 18, 1, 'de_mirage', 'Bonnaa', '1-1f75614d-1601-4aa1-ba44-3faf77c48253', '2025-06-21 18:44:26'),
(11, 3, 2262, 2282, 20, 1, 'de_mirage', 'Bonnaa', '1-ab49615e-6fe0-47e5-9dfe-360441282a60', '2025-06-21 18:44:26'),
(12, 3, 2282, 2257, 25, 0, 'de_dust2', 'Bonnaa', '1-f3c5131e-5d6c-4d99-9466-f695da282762', '2025-06-21 18:44:26'),
(13, 4, 3575, 3545, 30, 0, 'de_mirage', 'Qlocuu', '1-b36d27b9-01c4-4d02-8e6c-fd0c53219bf4', '2025-06-21 18:44:26'),
(14, 3, 2257, 2232, 25, 0, 'de_dust2', 'Bonnaa', '1-a9416747-4c8b-4ea3-8341-819f90a468ad', '2025-06-21 18:44:26'),
(15, 4, 958, 982, 24, 1, 'de_anubis', 'Evqalipt', '1-50203dd1-d1f1-4616-a1fa-f28a5f413d2d', '2025-06-22 14:39:57'),
(16, 4, 961, 939, 22, 0, 'de_anubis', 'Evqalipt', '1-0771f182-7bbb-4422-834a-94834d165c0e', '2025-06-23 17:21:29'),
(17, 4, 939, 912, 27, 0, 'de_ancient', 'Evqalipt', '1-c6b134f7-9126-4b3d-93da-250f0c2fa4b3', '2025-06-23 19:01:55'),
(18, 3, 2232, 2254, 22, 1, 'de_dust2', 'Bonnaa', '1-d39b8bc8-b2c5-4058-9edb-7538b32bd9db', '2025-06-23 19:13:29'),
(19, 3, 2254, 2229, 25, 0, 'de_mirage', 'Bonnaa', '1-3cf11860-5cf2-41a6-bdcb-6cba3d6e062d', '2025-06-23 19:54:08'),
(20, 4, 912, 886, 26, 0, 'de_ancient', 'Evqalipt', '1-9f88c7d3-49b0-47fc-a5c0-3e93855c955c', '2025-06-24 17:36:58'),
(21, 4, 886, 858, 28, 0, 'de_mirage', 'Evqalipt', '1-b41a1fce-facc-4332-8e92-e4d422ab5c8b', '2025-06-24 18:16:15'),
(22, 4, 858, 889, 31, 1, 'de_nuke', 'Evqalipt', '1-2e62f04d-11e0-4727-8761-8d16b28d3f2b', '2025-06-25 18:13:03'),
(23, 4, 889, 869, 20, 0, 'de_dust2', 'Evqalipt', '1-4249a6d7-b1b4-49b2-9364-7508b9b7e91a', '2025-06-27 18:01:59'),
(24, 4, 869, 899, 30, 1, 'de_dust2', 'Evqalipt', '1-17b287cc-70df-43e0-b6a6-fba7bf1fb55b', '2025-06-27 18:59:45'),
(25, 3, 2229, 2211, 18, 0, 'de_mirage', 'Bonnaa', '1-16df7272-d57f-48a0-8ccd-7376b1a999d1', '2025-06-27 21:01:03'),
(26, 3, 2211, 2187, 24, 0, 'de_dust2', 'Bonnaa', '1-04570fb5-bb0d-47ee-838b-052be55533f3', '2025-06-28 14:58:33'),
(27, 4, 899, 923, 24, 1, 'de_dust2', 'Evqalipt', '1-efa19848-4aca-454c-9bc0-c86ef394ba65', '2025-06-28 16:25:39'),
(28, 3, 2187, 2171, 16, 0, 'de_mirage', 'Bonnaa', '1-15023a01-4007-44d4-9c04-670f38dadd34', '2025-06-28 17:44:51'),
(29, 3, 2171, 2187, 16, 1, 'de_mirage', 'Bonnaa', '1-31d6c7d9-85aa-4b09-90a3-9442cb02c0cb', '2025-06-28 18:32:29'),
(30, 4, 923, 949, 26, 1, 'de_inferno', 'Evqalipt', '1-1242865e-ca4c-4319-b945-bee963d8cf7d', '2025-06-28 19:03:38'),
(31, 3, 2187, 2171, 16, 0, 'de_ancient', 'Bonnaa', '1-77ffa2b4-b6f0-4f44-b93b-cecfe6e2e77c', '2025-06-28 19:12:44'),
(32, 3, 2171, 2146, 25, 0, 'de_anubis', 'Bonnaa', '1-bcc424f3-fda8-4732-85d9-e9a43d20e4df', '2025-06-29 15:20:02'),
(33, 4, 969, 942, 27, 0, 'de_ancient', 'Evqalipt', '1-1a8e95b8-bf77-41aa-9383-558c98d791f5', '2025-06-29 16:43:16'),
(34, 4, 942, 914, 28, 0, 'de_mirage', 'Evqalipt', '1-156f8754-dc4d-46f6-9adc-e589c6fbff01', '2025-06-29 21:04:30'),
(35, 4, 967, 991, 24, 1, 'de_mirage', 'Evqalipt', '1-8be3fb0e-b35f-41b3-aaf7-8ae2457d1c60', '2025-07-01 19:06:15'),
(36, 4, 991, 1012, 21, 1, 'de_mirage', 'Evqalipt', '1-94310f5d-8b80-472c-88fb-738e837716f5', '2025-07-01 20:07:19'),
(37, 4, 1012, 982, 30, 0, 'de_anubis', 'Evqalipt', '1-432a8b29-bb67-4e1b-87dd-f2ee503db748', '2025-07-01 21:08:56'),
(38, 4, 1036, 1010, 26, 0, 'de_mirage', 'Evqalipt', '1-48acb46c-7053-4861-a7eb-cd1154a9e6a4', '2025-07-03 18:00:09'),
(39, 4, 1010, 988, 22, 0, 'de_mirage', 'Evqalipt', '1-bdd737b9-94f5-4362-8e37-dcbefd75cfef', '2025-07-04 18:01:20'),
(40, 4, 988, 1018, 30, 1, 'de_mirage', 'Evqalipt', '1-c5209547-8c64-44ab-8450-2518a78cd63b', '2025-07-04 19:51:09'),
(41, 4, 1018, 994, 24, 0, 'de_mirage', 'Evqalipt', '1-95b9be83-b0bf-4b48-b25b-ef164000a9aa', '2025-07-04 20:31:59'),
(42, 4, 994, 1015, 21, 1, 'de_inferno', 'Evqalipt', '1-4eefe887-4922-4ed1-ab5a-4c5d61a45635', '2025-07-05 11:01:30'),
(43, 4, 2654, 2624, 30, 0, 'de_dust2', 'icytears', '1-ee2da94a-0f12-492e-b530-548b050bd67a', '2025-07-05 15:53:48'),
(44, 4, 1000, 1025, 25, 1, 'de_dust2', 'icytears', 'cs2', '2025-07-05 16:10:32'),
(45, 4, 1000, 1025, 25, 1, 'de_dust2', 'icytears', 'cs2', '2025-07-05 16:11:11'),
(46, 4, 1000, 1025, 25, 1, 'de_dust2', 'icytears', 'cs2', '2025-07-05 16:13:43'),
(47, 4, 1000, 1025, 25, 1, 'de_dust2', 'icytears', 'cs2', '2025-07-05 16:23:01'),
(48, 4, 1000, 1025, 25, 1, 'de_dust2', 'icytears', 'cs2', '2025-07-05 16:40:36'),
(49, 4, 1000, 1025, 25, 1, 'de_dust2', 'icytears', 'cs2', '2025-07-05 16:49:14'),
(50, 4, 1000, 1025, 25, 1, 'de_dust2', 'icytears', 'cs2', '2025-07-05 16:53:12'),
(51, 4, 1000, 1025, 25, 1, 'de_dust2', 'icytears', 'cs2', '2025-07-05 16:55:30'),
(52, 4, 1000, 1025, 25, 1, 'de_dust2', 'icytears', 'cs2', '2025-07-05 16:55:40'),
(53, 4, 1000, 1025, 25, 1, 'de_dust2', 'icytears', 'cs2', '2025-07-05 17:02:33'),
(54, 4, 1000, 1025, 25, 1, 'de_dust2', 'icytears', 'cs2', '2025-07-05 17:06:35'),
(55, 4, 1000, 1025, 25, 1, 'de_dust2', 'icytears', 'cs2', '2025-07-05 17:07:02'),
(56, 4, 1000, 1025, 25, 1, 'de_dust2', 'icytears', 'cs2', '2025-07-05 17:07:20'),
(57, 4, 1000, 1025, 25, 1, 'de_dust2', 'icytears', 'cs2', '2025-07-05 17:20:02'),
(58, 4, 2991, 2961, 30, 0, 'de_dust2', 'Recrent', '1-1f2adff6-5821-4c77-b457-88bf153e7971', '2025-07-05 17:34:46'),
(59, 4, 1000, 1025, 25, 1, 'de_dust2', 'icytears', 'cs2', '2025-07-05 17:42:15'),
(60, 4, 1000, 1025, 25, 1, 'de_dust2', 'icytears', 'cs2', '2025-07-05 17:42:25'),
(61, 4, 1000, 1025, 25, 1, 'de_dust2', 'icytears', 'cs2', '2025-07-05 17:43:10'),
(62, 4, 2845, 2875, 30, 1, 'de_inferno', 'Leha2077', '1-9ac8f390-28fa-4bd7-9294-a78dd8c4e109', '2025-07-05 17:47:16'),
(63, 4, 2187, 2217, 30, 1, 'de_ancient', 'Rayaner', '1-e5ae76ee-bf58-487e-be65-f75698a17a93', '2025-07-05 18:02:46'),
(64, 4, 2217, 2246, 29, 1, 'de_dust2', 'Rayaner', '1-c68d1736-98a9-4bd5-99a4-7b463a0ea899', '2025-07-05 18:38:54'),
(65, 4, 2246, 2274, 28, 1, 'de_inferno', 'Rayaner', '1-17844d44-f688-429b-bc3b-9d428c66c354', '2025-07-05 19:57:12');

-- --------------------------------------------------------

--
-- Структура таблицы `settings`
--

CREATE TABLE `settings` (
  `id` int NOT NULL,
  `max_users_per_vps` int NOT NULL,
  `month_sub_price` int NOT NULL,
  `trial_days` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `settings`
--

INSERT INTO `settings` (`id`, `max_users_per_vps`, `month_sub_price`, `trial_days`) VALUES
(1, 5, 10, 2);

-- --------------------------------------------------------

--
-- Структура таблицы `subservers`
--

CREATE TABLE `subservers` (
  `id` int NOT NULL,
  `ip` varchar(16) NOT NULL,
  `port` int NOT NULL DEFAULT '5055',
  `api_key` varchar(64) NOT NULL,
  `current_user_load` int NOT NULL DEFAULT '0',
  `creation_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `location` varchar(64) NOT NULL DEFAULT 'Unknown'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `subservers`
--

INSERT INTO `subservers` (`id`, `ip`, `port`, `api_key`, `current_user_load`, `creation_date`, `location`) VALUES
(10, '148.251.162.18', 5055, '9e5eeeb557a12510616e4168d39c208c', 3, '2025-06-17 00:21:48', 'Germany, Falkenstein'),
(11, '93.193.114.112', 5055, 'dfgdfsgdsgds', 0, '2025-07-05 16:09:44', 'localhost pc'),
(13, '87.182.31.10', 5055, 'c8e7edb16900f948279b7a2a5b4f93da', 0, '2025-07-13 11:58:08', 'localhost');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `status` int NOT NULL DEFAULT '0',
  `subserver_id` int DEFAULT NULL,
  `reg_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sub_start_day` timestamp NULL DEFAULT NULL,
  `sub_end_day` timestamp NULL DEFAULT NULL,
  `faceit_id` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `faceit_username` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'unknown',
  `telegram_id` bigint NOT NULL DEFAULT '0',
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `status`, `subserver_id`, `reg_date`, `sub_start_day`, `sub_end_day`, `faceit_id`, `faceit_username`, `telegram_id`, `password`) VALUES
(2, 1, 10, '2025-05-22 19:23:03', '2025-05-22 19:23:03', '2025-05-23 19:23:03', '53a8d759-076b-4b4a-8101-7b12fa40032d', 'bauld', 251464707, '123123'),
(3, 1, 10, '2025-05-22 19:36:29', '2025-05-22 19:36:29', '2025-05-22 19:36:29', '549c61c4-f97d-4e7d-9e5a-32403045a3b4', 'Bonnaa', 251464707, '123123'),
(4, 1, 10, '2025-06-15 19:59:57', '2025-06-15 19:59:57', '2025-06-15 19:59:57', 'b45c1bea-2ff1-4b28-a077-414d8f3bde28', 'random', 251464707, '123123'),
(14, 1, NULL, '2025-07-13 14:34:03', '2025-07-13 14:58:11', '2025-07-14 14:58:11', 'e15b7956-1216-4922-ac27-30150982719b', '-Kyson-', 0, '877b0af318d8ca7e5a56'),
(15, 1, NULL, '2025-07-13 14:36:40', '2024-07-14 14:55:12', '2025-09-18 14:55:12', '928857e9-48e7-41b1-b4e8-217fd1a6e51b', 'electronic', 0, 'd361c420dce16b56b91c');

--
-- Триггеры `users`
--
DELIMITER $$
CREATE TRIGGER `update_user_load` BEFORE UPDATE ON `users` FOR EACH ROW BEGIN
    -- Уменьшаем загрузку на старом сабсервере
    IF OLD.subserver_id IS NOT NULL THEN
        UPDATE subservers
        SET current_user_load = current_user_load - 1
        WHERE id = OLD.subserver_id;
    END IF;

    -- Увеличиваем загрузку на новом сабсервере
    IF NEW.subserver_id IS NOT NULL THEN
        UPDATE subservers
        SET current_user_load = current_user_load + 1
        WHERE id = NEW.subserver_id;
    END IF;
END
$$
DELIMITER ;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `cp_users`
--
ALTER TABLE `cp_users`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `matches`
--
ALTER TABLE `matches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userid` (`userid`);

--
-- Индексы таблицы `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `subservers`
--
ALTER TABLE `subservers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ip` (`ip`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `faceit_id` (`faceit_id`),
  ADD KEY `subserver_id` (`subserver_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `cp_users`
--
ALTER TABLE `cp_users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `matches`
--
ALTER TABLE `matches`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT для таблицы `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `subservers`
--
ALTER TABLE `subservers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `matches`
--
ALTER TABLE `matches`
  ADD CONSTRAINT `matches_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`subserver_id`) REFERENCES `subservers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
