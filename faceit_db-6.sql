-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Хост: faceit_db
-- Время создания: Июн 15 2025 г., 22:55
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
  `gameid` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  `api_key` varchar(64) NOT NULL,
  `current_user_load` int NOT NULL DEFAULT '0',
  `creation_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `location` varchar(64) NOT NULL DEFAULT 'Unknown'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `subservers`
--

INSERT INTO `subservers` (`id`, `ip`, `api_key`, `current_user_load`, `creation_date`, `location`) VALUES
(7, '87.182.31.10', 'c8e7edb16900f948279b7a2a5b4f93da', 0, '2025-05-22 18:51:43', 'localhost');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `status` int NOT NULL DEFAULT '0',
  `subserver_id` int DEFAULT NULL,
  `reg_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sub_start_day` timestamp NOT NULL,
  `sub_end_day` timestamp NOT NULL,
  `faceit_id` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `faceit_username` varchar(32) NOT NULL,
  `telegram_id` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `status`, `subserver_id`, `reg_date`, `sub_start_day`, `sub_end_day`, `faceit_id`, `faceit_username`, `telegram_id`) VALUES
(2, 1, 7, '2025-05-22 19:23:03', '2025-05-22 19:23:03', '2025-05-22 19:23:03', '53a8d759-076b-4b4a-8101-7b12fa40032d', 'bauld', 251464707),
(3, 1, 7, '2025-05-22 19:36:29', '2025-05-22 19:36:29', '2025-05-22 19:36:29', '2', 'bonna', 2),
(4, 1, 7, '2025-06-15 19:59:57', '2025-06-15 19:59:57', '2025-06-15 19:59:57', '', 'unknown', 251464707);

--
-- Индексы сохранённых таблиц
--

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
  ADD KEY `subserver_id` (`subserver_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `matches`
--
ALTER TABLE `matches`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT для таблицы `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `subservers`
--
ALTER TABLE `subservers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
