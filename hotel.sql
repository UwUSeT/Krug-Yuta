-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Ноя 16 2025 г., 19:23
-- Версия сервера: 8.0.30
-- Версия PHP: 8.1.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `hotel`
--

-- --------------------------------------------------------

--
-- Структура таблицы `amenity`
--

CREATE TABLE `amenity` (
  `amenity_id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Название удобства',
  `amenity_icon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Иконка удобства',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `amenity`
--

INSERT INTO `amenity` (`amenity_id`, `name`, `amenity_icon`, `created_at`, `updated_at`) VALUES
(1, 'Smart TV', '/icons/smartTV.svg', '2025-11-12 00:42:35', '2025-11-12 14:11:17'),
(2, 'Односп. кровать (160*200)', '/icons/bed.svg', '2025-11-12 00:42:35', '2025-11-12 14:15:30'),
(3, 'Фен', '/icons/hairdryer.svg', '2025-11-12 00:42:35', '2025-11-12 14:11:17'),
(4, 'Приточно-вытяжная система', '/icons/ventilation.svg', '2025-11-12 00:42:35', '2025-11-12 14:11:17'),
(5, 'Wi-Fi', '/icons/wi_fi.svg', '2025-11-12 00:42:35', '2025-11-12 14:11:17'),
(6, 'Гардероб', '/icons/wardrobe.svg', '2025-11-12 00:42:35', '2025-11-12 14:11:17'),
(7, 'Вентилятор', '/icons/ventilator.svg', '2025-11-12 00:42:35', '2025-11-12 14:11:17'),
(8, 'Письменный стол', '/icons/workspace.svg', '2025-11-12 00:42:35', '2025-11-12 14:11:17'),
(9, 'Чайник и чай', '/icons/tea_pot.svg', '2025-11-12 00:42:35', '2025-11-12 14:11:17'),
(10, 'Зубной набор', '/icons/tooth.svg', '2025-11-12 00:42:35', '2025-11-12 14:11:17'),
(11, 'Тапочки', '/icons/slippers.svg', '2025-11-12 00:42:35', '2025-11-12 14:11:17'),
(12, 'Полотенца', '/icons/towel.svg', '2025-11-12 00:42:35', '2025-11-12 14:11:17');

-- --------------------------------------------------------

--
-- Структура таблицы `bookings`
--

CREATE TABLE `bookings` (
  `id` int NOT NULL,
  `booking_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id_user` int NOT NULL,
  `room_id` int NOT NULL,
  `check_in` date NOT NULL,
  `check_out` date NOT NULL,
  `guests` int UNSIGNED NOT NULL,
  `nights` int UNSIGNED NOT NULL,
  `total` int UNSIGNED NOT NULL,
  `status` enum('ожидает','подтверждено','отменено') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'ожидает',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `bookings`
--

INSERT INTO `bookings` (`id`, `booking_id`, `id_user`, `room_id`, `check_in`, `check_out`, `guests`, `nights`, `total`, `status`, `created_at`) VALUES
(1, 'BECA6A9', 2, 1, '2025-11-14', '2025-11-15', 1, 1, 4500, 'ожидает', '2025-11-14 13:13:50'),
(2, 'B9BC0B6', 2, 1, '2025-11-15', '2025-11-16', 1, 1, 4500, 'отменено', '2025-11-14 18:06:33');

-- --------------------------------------------------------

--
-- Структура таблицы `reviews`
--

CREATE TABLE `reviews` (
  `id_review` int NOT NULL,
  `id_user` int NOT NULL,
  `rating` int NOT NULL,
  `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `reviews`
--

INSERT INTO `reviews` (`id_review`, `id_user`, `rating`, `comment`, `created_at`) VALUES
(1, 4, 4, 'Останавливались в этом отеле на неделю. Впечатления только положительные! Просторный номер с удобной кроватью и хорошей звукоизоляцией. Бассейн и зона отдыха\r\nпревосходные.\r\n\r\nОсобенно хочу отметить работу ресторана - блюда были изумительные, а обслуживание на высшем уровне. Вечерняя анимация порадовала разнообразием программ.', '2025-11-15 13:27:25'),
(2, 5, 5, 'Идеальное место для семейного отдыха! Для детей есть все необходимое: игровая комната, бассейн с подогревом, детское меню в ресторане. Номер был чистым и уютным.\r\n\r\nПерсонал всегда готов помочь и ответить на любые вопросы. Расположение отеля очень удобное - в тихом месте, но при этом недалеко от центра города. Рекомендую всем!', '2025-11-15 13:29:22'),
(3, 6, 4, 'Отличное соотношение цена/качество! Отель превзошел все наши ожидания. Современные номера с качественным ремонтом, чистота на высшем уровне, вкусные завтраки.\r\n\r\nОсобенно понравился СПА-центр - профессиональные массажисты и расслабляющая атмосфера. Персонал очень внимательный и вежливый. Обязательно вернемся в этот отель!', '2025-11-15 13:31:24'),
(4, 3, 5, 'Прекрасный отель с отличным сервисом! Персонал очень внимательный и доброжелательный. Номер был чистым, уютным и со всем необходимым. Особенно порадовал\r\nвид из окна на море.\r\n\r\nЗавтраки были разнообразные и вкусные. Расположение отеля очень удобное - близко к пляжу и основным достопримечательностям. Обязательно вернемся снова!', '2025-11-15 13:40:46');

-- --------------------------------------------------------

--
-- Структура таблицы `role`
--

CREATE TABLE `role` (
  `id_role` int NOT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `role`
--

INSERT INTO `role` (`id_role`, `name`) VALUES
(1, 'user'),
(2, 'admin');

-- --------------------------------------------------------

--
-- Структура таблицы `rooms`
--

CREATE TABLE `rooms` (
  `id_room` int NOT NULL,
  `room_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `room_number` int NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `img1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `img2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `img3` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `capacity` int NOT NULL,
  `price` decimal(10,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `rooms`
--

INSERT INTO `rooms` (`id_room`, `room_name`, `room_number`, `description`, `img1`, `img2`, `img3`, `capacity`, `price`) VALUES
(1, 'Стандартный одноместный', 101, 'Идеальный выбор для деловых поездок и индивидуальных путешественников. Комфорт и функциональность в современном стиле.', 'img/101_1_17ffe2ec64.jpg', 'img/101_2_a062850e32.avif', 'img/101_3_996311e2be.avif', 1, '4500'),
(2, 'Полулюкс номер', 102, 'Просторный номер для тех, кто ценит комфорт и дополнительные удобства. Идеально подходит для семейного отдыха.', 'img/102_1_103fdbfcd9.jpg', 'img/102_2_ec366e725d.avif', 'img/102_3_95db13c818.avif', 2, '7500'),
(3, 'Люкс номер', 103, 'Просторный двухместный номер класса люкс', 'img/103_1_0df0a0f044.jpg', 'img/103_2_8d09fbe47b.avif', 'img/103_3_2da2c7c73c.avif', 2, '9500'),
(4, 'Стандартный одноместный', 201, 'Идеальный выбор для деловых поездок и индивидуальных путешественников. Комфорт и функциональность в современном стиле.', 'img/201_1_9d4c61329b.jpg', 'img/201_2_9cc49998c5.avif', 'img/201_3_cc5b189791.avif', 1, '4500'),
(5, 'Полулюкс номер', 202, 'Просторный номер для тех, кто ценит комфорт и дополнительные удобства. Идеально подходит для семейного отдыха.', 'img/202_1_6e2fa3f6cb.jpg', 'img/202_2_d4b3513a47.avif', 'img/202_3_cc0b705b82.avif', 2, '7500'),
(6, 'Люкс номер', 203, 'Просторный трехместный номер класса люкс', 'img/203_1_90acdfb7b7.jpg', 'img/203_2_fc3532f86c.avif', 'img/203_3_7fbd65a2ad.avif', 3, '12500');

-- --------------------------------------------------------

--
-- Структура таблицы `room_and_amenity`
--

CREATE TABLE `room_and_amenity` (
  `room_id` int NOT NULL,
  `amenity_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `room_and_amenity`
--

INSERT INTO `room_and_amenity` (`room_id`, `amenity_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(1, 2),
(2, 2),
(3, 2),
(4, 2),
(5, 2),
(6, 2),
(1, 3),
(2, 3),
(3, 3),
(4, 3),
(5, 3),
(6, 3),
(1, 4),
(2, 4),
(3, 4),
(4, 4),
(5, 4),
(6, 4),
(1, 5),
(2, 5),
(3, 5),
(4, 5),
(5, 5),
(6, 5),
(1, 6),
(2, 6),
(3, 6),
(4, 6),
(5, 6),
(6, 6),
(1, 7),
(2, 7),
(3, 7),
(4, 7),
(5, 7),
(6, 7),
(1, 8),
(2, 8),
(3, 8),
(4, 8),
(5, 8),
(6, 8),
(1, 9),
(2, 9),
(3, 9),
(4, 9),
(5, 9),
(6, 9),
(1, 10),
(2, 10),
(3, 10),
(4, 10),
(5, 10),
(6, 10),
(1, 11),
(2, 11),
(3, 11),
(4, 11),
(5, 11),
(6, 11),
(1, 12),
(2, 12),
(3, 12),
(4, 12),
(5, 12),
(6, 12);

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id_user` int NOT NULL,
  `firstname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `middlename` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lastname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birth` date DEFAULT NULL,
  `gender` enum('male','female') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(18) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `login` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_role` int NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id_user`, `firstname`, `middlename`, `lastname`, `country`, `birth`, `gender`, `email`, `phone`, `login`, `password`, `id_role`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'admin', 'admin', 'admin', NULL, NULL, NULL, 'admin@gmail.com', '8999999999999999', 'adminka', '$2y$10$w2.zE.3Nxt55C.uYci17GepA2sLXsqyuWF1K3nICbSQ6sJ5uzjPZW', 2, '2025-11-04 14:26:28', '2025-11-04 14:23:10', NULL),
(2, 'Иванфвфвфвф', 'Наташкович', 'Ивановфвфвфвф', 'Россия', '2025-11-06', 'male', 'example@gmail.com', '+7 (924) 700-80-00', 'example', '$2y$10$bIuBBmOEdRCsK1dewlaXhecOVzLTq0C7finfkgGS7I/2/cyEtZiBe', 1, '2025-11-05 01:32:08', '2025-11-05 01:32:08', NULL),
(3, 'Олег', 'Иванович', 'Иванов', 'Россия', '2005-03-15', 'male', 'olivki@gmail.com', '+7 (131) 412-13-12', 'olivki', '$2y$10$MKgIVOajhVsdg5uVnouXQOTABIL1DrF/9M3P8KuwoKYHtoW7.SA1m', 1, '2025-11-15 13:17:36', '2025-11-15 13:17:36', NULL),
(4, 'Мария', 'Александровна', 'Семенова', 'Россия', '2005-02-15', 'female', 'marya@gmail.com', '+7 (131) 412-13-32', 'marya', '$2y$10$A69Gfjv6tEx0kpLV8RKhf.dI1RYQHEiTpCRhVztYRNS4R6UmiAQcu', 1, '2025-11-15 13:19:26', '2025-11-15 13:19:26', NULL),
(5, 'Андрей', 'Олегович', 'Петров', 'Казахстан', '2005-01-15', 'male', 'andrey@gmail.com', '+7 (131) 412-13-35', 'andrey', '$2y$10$87/eFaBwMdvfYX1IVmc1uOnsZraEliQoe5FA/uKQK5IlTntb/7luS', 1, '2025-11-15 13:22:07', '2025-11-15 13:22:07', NULL),
(6, 'Елена', 'Николаевна', 'Ковалева', 'Беларусь', '2005-05-15', 'female', 'elena@gmail.com', '+7 (131) 412-13-37', 'elena', '$2y$10$udStfY93CVu0WHMCc7e6YO4mTTT1NSMu.3wOKXb6CWxMFV80HDZ56', 1, '2025-11-15 13:23:35', '2025-11-15 13:23:35', NULL);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `amenity`
--
ALTER TABLE `amenity`
  ADD PRIMARY KEY (`amenity_id`);

--
-- Индексы таблицы `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `room_id` (`room_id`);

--
-- Индексы таблицы `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id_review`),
  ADD KEY `id_user` (`id_user`);

--
-- Индексы таблицы `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id_role`);

--
-- Индексы таблицы `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id_room`);

--
-- Индексы таблицы `room_and_amenity`
--
ALTER TABLE `room_and_amenity`
  ADD PRIMARY KEY (`room_id`,`amenity_id`),
  ADD KEY `amenity_id` (`amenity_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD KEY `id_role` (`id_role`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `amenity`
--
ALTER TABLE `amenity`
  MODIFY `amenity_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT для таблицы `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id_review` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `role`
--
ALTER TABLE `role`
  MODIFY `id_role` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id_room` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id_room`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Ограничения внешнего ключа таблицы `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `room_and_amenity`
--
ALTER TABLE `room_and_amenity`
  ADD CONSTRAINT `room_and_amenity_ibfk_1` FOREIGN KEY (`amenity_id`) REFERENCES `amenity` (`amenity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `room_and_amenity_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id_room`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `role` (`id_role`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
