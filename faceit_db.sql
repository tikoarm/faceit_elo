-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Хост: faceit_db
-- Время создания: Июл 27 2025 г., 17:31
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
(1, 'root', '$2y$10$r1etxN7q.t/7zKmDAP3l7ukbKuJTt7K3oCKKAgftODDeF7h350H2C', '2025-07-12 20:15:48');

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
(10, '148.251.162.18', 5055, '9e5eeeb557a12510616e4168d39c208c', 0, '2025-06-17 00:21:48', 'Germany, Falkenstein'),
(11, '93.193.114.112', 5055, 'dfgdfsgdsgds', 0, '2025-07-05 16:09:44', 'localhost pc'),
(13, '87.182.31.10', 5055, 'c8e7edb16900f948279b7a2a5b4f93da', 0, '2025-07-13 11:58:08', 'localhost'),
(14, '167.86.98.193', 5055, '5c13f7d4a1293be3a9e612f4d7b1c25f', 2, '2025-07-18 18:01:54', 'Localhost VPS');

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
(2, 1, 14, '2025-05-22 19:23:03', '2025-05-22 19:23:03', '2025-05-23 19:23:03', '53a8d759-076b-4b4a-8101-7b12fa40032d', 'bauld', 251464707, '123123');

--
-- Триггеры `users`
--
DELIMITER $$
CREATE TRIGGER `trg_users_reset_subscription` BEFORE UPDATE ON `users` FOR EACH ROW BEGIN
  -- Если статус меняется на 0
  IF NEW.status = 0 AND OLD.status <> 0 THEN
    SET NEW.sub_start_day = NULL,
        NEW.sub_end_day   = NULL,
        NEW.subserver_id  = NULL;
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_user_load` AFTER UPDATE ON `users` FOR EACH ROW BEGIN
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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT для таблицы `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `subservers`
--
ALTER TABLE `subservers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `matches`
--
ALTER TABLE `matches`
  ADD CONSTRAINT `matches_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`subserver_id`) REFERENCES `subservers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
