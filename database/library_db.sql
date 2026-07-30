-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Июл 30 2026 г., 10:16
-- Версия сервера: 10.4.32-MariaDB
-- Версия PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `library_db`
--

-- --------------------------------------------------------

--
-- Структура таблицы `authors`
--

CREATE TABLE `authors` (
  `id` int(11) NOT NULL,
  `full_name` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `authors`
--

INSERT INTO `authors` (`id`, `full_name`) VALUES
(1, 'Лев Толстой'),
(2, 'Александр Пушкин'),
(3, 'Джордж Оруэлл'),
(5, 'Джоан Кэтлин Роулинг'),
(6, 'Лии Арден'),
(7, 'Хэл Элрод'),
(8, 'Уильям Гибсон'),
(9, 'Уилл Смит и Марк Мэнсон'),
(10, 'Генри Марш');

-- --------------------------------------------------------

--
-- Структура таблицы `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author_id` int(11) NOT NULL,
  `genre_id` int(11) NOT NULL,
  `year_published` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `isbn` varchar(20) DEFAULT NULL,
  `publisher` varchar(150) DEFAULT NULL,
  `copies` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `books`
--

INSERT INTO `books` (`id`, `title`, `author_id`, `genre_id`, `year_published`, `description`, `image`, `isbn`, `publisher`, `copies`) VALUES
(1, 'Война и мир', 1, 1, 1869, 'Роман-эпопея Льва Толстого.', '1785154948_war-and-peace.jfif', '978-5-17-118366-7', 'АСТ', 4),
(2, 'Евгений Онегин', 2, 2, 1833, 'Роман в стихах.', '1785154937_onegin.jpg', '978-5-17-102614-8', 'Эксмо', 3),
(3, '1984', 3, 3, 1949, 'Антиутопический роман.', '1785154920_1984.webp', '978-5-699-12014-7', 'Эксмо', 5),
(5, 'Медный всадник', 2, 2, 1833, 'В центре сюжета — простой чиновник Евгений, чья жизнь и хрупкое счастье рушатся в один миг из-за разрушительного наводнения в Петербурге. В безумии и отчаянии он винит в своей трагедии основателя города и бросает вызов грозному монументу. С этого момента оживший бронзовый исполин начинает преследовать несчастного героя по безлюдным улицам, превращая реальность в мистический кошмар.', '1784411382_Без названия.jfif', '978-5-699-52877-6', '«Эксмо» (серия «Всемирная литература»)', 3),
(6, 'Гарри Поттер и философский камень', 5, 5, 1997, '«Гарри Поттер и философский камень» — это история об одиннадцатилетнем сироте, который узнает о своем магическом происхождении и отправляется учиться в школу чародейства Хогвартс. Там мальчик обретает верных друзей, осваивает основы магии и сталкивается с первыми серьезными опасностями. В финале книги Гарри и его товарищам удается разгадать тайну волшебного артефакта и сорвать заговор злого мага Волан-де-Морта.', '1785228209_i454685.jpg', '978-5-353-00308-3', 'Росмэн', 5),
(7, 'Гарри Поттер и Тайная комната', 5, 5, 1998, 'На втором курсе Хогвартса Гарри Поттер сталкивается с таинственной угрозой, которая превращает студентов в камень. Древняя Тайная комната снова открыта, и подозрение в нападениях падает на самого Гарри из-за его редкого дара говорить со змеями. Чтобы спасти школу и оправдать своё имя, юный волшебник должен разгадать тайну дневника Тома Реддла и сразиться с ужасающим чудовищем.', '1785228428_242-0.webp', '5-353-00309-1', 'Росмэн', 3),
(8, 'Гарри Поттер и узник Азкабана', 5, 5, 1999, 'В третьей части истории Гарри Поттер возвращается в Хогвартс и сталкивается с новой угрозой — из неприступной тюрьмы Азкабан сбегает опасный волшебник Сириус Блэк. Для защиты школы министерство привлекает жутких дементоров, которые заставляют Гарри пережить самые мрачные воспоминания из его прошлого. С помощью верных друзей и таинственного артефакта времени юный маг раскрывает шокирующую правду о предательстве и обретает семью.', '1785228571_images.jfif', '978-5-353-00432-5', 'Росмэн', 2),
(9, 'Гарри Поттер и Кубок огня', 5, 5, 2000, 'Четвертый год обучения Гарри Поттера в Хогвартсе омрачается его внезапным и опасным участием в легендарном Турнире Трех Волшебников. Проходя смертоносные испытания с драконами и русалками, юный маг сталкивается с завистью сверстников и первыми взрослыми чувствами на Святочном балу. В финале состязания старинный кубок переносит Гарри на жуткое кладбище, где происходит трагическая гибель его друга и долгожданное, мрачное возрождение Лорда Волан-де-Морта.', '1785228830_242-0 (1).webp', '978-5-353-00579-7', '«РОСМЭН»', 7),
(10, 'Гарри Поттер и Орден Феникса', 5, 5, 2003, 'Гарри Поттер возвращается в Хогвартс на пятый год обучения и сталкивается с тем, что Министерство магии яростно отрицает возрождение Волан-де-Морта. Чтобы противостоять тирании новой преподавательницы Долорес Амбридж, Гарри тайно собирает «Отряд Дамблдора» для обучения студентов защитной магии. Впереди героев ждет жестокое противостояние с Пожирателями смерти в Министерстве, которое обернется невосполнимой личной утратой.', '1785228973_b75b72f9-b07e-4735-b3a6-79dca21c8f88.jpg', '5-353-01435-9', '«Росмэн»', 1),
(11, 'Гарри Поттер и Принц-полукровка', 5, 5, 2005, 'Шестая часть саги погружает Хогвартс в атмосферу тревоги, пока Гарри Поттер вместе с Дамблдором раскрывает мрачные тайны прошлого Волан-де-Морта и узнает секрет его бессмертия. В это же время в руки героя попадает загадочный учебник по зельеварению, принадлежавший таинственному Принцу-полукровке и дающий опасные подсказки. История достигает своего пика в трагическом финале на Астрономической башне, который навсегда меняет расстановку сил в волшебном мире.', '1785229104_6130412513.jpg', '978-5-353-02187-2', '«РОСМЭН»', 2),
(12, 'Гарри Поттер и Дары Смерти', 5, 5, 2007, '«Гарри Поттер и Дары Смерти» — это финальный роман серии, в котором Гарри, Рон и Гермиона вместо выпускного года в Хогвартсе пускаются в бега ради уничтожения крестражей Волан-де-Морта. На фоне тотального захвата власти Пожирателями смерти герои узнают о трех легендарных Дарах Смерти, способных подчинить себе саму гибель. Книга завершается масштабной битвой за школу магии, самопожертвованием Гарри и финальной победой над Тёмным Лордом.', '1785229334_1.webp', '978-5-353-02907-6', '«РОСМЭН»', 2),
(13, 'Мара и Морок', 6, 6, 2020, 'Главная героиня Агата — одна из Мар, преданных служительниц богини смерти Мораны, которые были жестоко истреблены двадцать лет назад. Ее воскрешает таинственный и опасный слуга тьмы Морок, чтобы она помогла предотвратить политическую катастрофу и спасти живых от нечисти. Ради шанса отомстить своим палачам Агата соглашается на сделку, погружаясь в водоворот дворцовых интриг, древней магии и смертельных тайн.', '1785229793_cover1__w600.jpg', '978-5-04-107175-2', '«Эксмо»', 4),
(14, 'Магия утра', 7, 7, 2012, '«Магия утра» Хэла Элрода — это практическое руководство по личной эффективности, предлагающее начинать каждый день на час раньше ради саморазвития. В основе методики лежит комплекс из шести простых утренних ритуалов: от медитации и зарядки до чтения и ведения дневника. Эта система помогает настроить подсознание на успех, победить прокрастинацию и качественно изменить свою жизнь всего за первый утренний час.', '1785230188_70732375.jpg', '978-5-00100-066-2', '«МИФ»', 1),
(15, 'Нейромант', 8, 8, 1984, 'Отрезанный от сети хакер Кейс получает шанс исцелить свою нервную систему в обмен на участие в смертельно опасном кибер-ограблении. Вместе с модифицированной наемницей Молли он выполняет задания таинственного нанимателя, проникая в защищенные базы данных мегакорпораций. В ходе миссии герои раскрывают глобальный заговор двух сверхмощных искусственных интеллектов, стремящихся к слиянию и обретению абсолютной свободы.', '1785230389_b50640bb-48bf-470b-a950-883a204eda44.jpg', '978-0441569595', 'Ace Books', 1),
(16, 'Will. Чему может научить нас простой парень, ставший самым высокооплачиваемым актером Голливуда', 9, 9, 2021, 'Книга рассказывает о пути испуганного филадельфийского мальчика, который преодолел детские травмы и стал одной из величайших звезд Голливуда. Соавторство с Марком Мэнсоном помогло превратить историю успеха в глубокую исповедь о цене амбиций, эгоизме и поиске внутренней гармонии. В итоге мемуары показывают, что внешняя слава не способна заполнить внутреннюю пустоту без глубокой работы над собой.', '1785230652_66749313.webp', '978-1984877925', 'Penguin Press', 5),
(17, 'Не навреди. Истории о жизни, смерти и нейрохирургии', 10, 10, 2014, 'Эта книга — откровенные мемуары британского нейрохирурга Генри Марша, в которых он делится историями из своей многолетней медицинской практики. Автор честно рассказывает не только о спасенных жизнях, но и о трагических ошибках, с которыми сталкивается каждый врач при операциях на головном мозге. Повествование наглядно показывает, как тяжело дается баланс между профессиональной отстраненностью и глубоким состраданием к пациентам.', '1785230933_cover1__w820.jpg', '978-5-699-81358-2', '«БОМБОРА»', 2);

-- --------------------------------------------------------

--
-- Структура таблицы `borrowings`
--

CREATE TABLE `borrowings` (
  `id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `reader_id` int(11) NOT NULL,
  `issue_date` date NOT NULL,
  `due_date` date NOT NULL,
  `return_date` date DEFAULT NULL,
  `status` enum('Выдана','Возвращена') DEFAULT 'Выдана'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `borrowings`
--

INSERT INTO `borrowings` (`id`, `book_id`, `reader_id`, `issue_date`, `due_date`, `return_date`, `status`) VALUES
(1, 3, 2, '2026-07-22', '2026-08-05', '2026-07-22', 'Возвращена'),
(2, 1, 2, '2026-07-13', '2026-07-30', '2026-07-27', 'Возвращена'),
(3, 1, 4, '2026-07-22', '2026-08-05', '2026-07-23', 'Возвращена'),
(4, 3, 4, '2026-07-22', '2026-08-05', '2026-07-23', 'Возвращена'),
(5, 5, 4, '2026-07-27', '2026-08-10', '2026-07-27', 'Возвращена'),
(6, 1, 4, '2026-07-27', '2026-08-10', '2026-07-27', 'Возвращена'),
(7, 3, 4, '2026-07-27', '2026-08-10', NULL, 'Выдана');

-- --------------------------------------------------------

--
-- Структура таблицы `genres`
--

CREATE TABLE `genres` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `genres`
--

INSERT INTO `genres` (`id`, `name`) VALUES
(1, 'Роман'),
(2, 'Поэзия'),
(3, 'Антиутопия'),
(5, 'фэнтези'),
(6, 'фэнтези-роман'),
(7, 'Психология'),
(8, 'научно-фантастический роман'),
(9, 'Автобиография'),
(10, 'Медицинские мемуары');

-- --------------------------------------------------------

--
-- Структура таблицы `readers`
--

CREATE TABLE `readers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `full_name` varchar(150) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `registered_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `readers`
--

INSERT INTO `readers`
(`id`,`user_id`,`full_name`,`phone`,`email`,`address`)
VALUES
(1,2,'Иван Иванов','+7 (900) 000-00-00','ivan@example.com','г. Москва');

-- --------------------------------------------------------

--
-- Структура таблицы `requests`
--

CREATE TABLE `requests` (
  `id` int(11) NOT NULL,
  `reader_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `request_date` date NOT NULL,
  `visit_date` date DEFAULT NULL,
  `visit_time` time DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `status` enum('Ожидает','Одобрена','Отклонена') DEFAULT 'Ожидает'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `requests`
--

INSERT INTO `requests` (`id`, `reader_id`, `book_id`, `request_date`, `visit_date`, `visit_time`, `comment`, `status`) VALUES
(5, 3, 17, '2026-07-29', '2026-07-29', '15:47:00', '', 'Ожидает');

-- --------------------------------------------------------

--
-- Структура таблицы `return_requests`
--

CREATE TABLE `return_requests` (
  `id` int(11) NOT NULL,
  `borrowing_id` int(11) NOT NULL,
  `reader_id` int(11) NOT NULL,
  `request_date` date NOT NULL,
  `visit_date` date DEFAULT NULL,
  `visit_time` time DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `status` enum('Ожидает','Принята') DEFAULT 'Ожидает'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `return_requests`
--

INSERT INTO `return_requests` (`id`, `borrowing_id`, `reader_id`, `request_date`, `visit_date`, `visit_time`, `comment`, `status`) VALUES
(1, 4, 4, '2026-07-23', NULL, NULL, NULL, 'Принята'),
(2, 6, 4, '2026-07-27', '2026-07-27', '14:50:00', '', 'Принята'),
(3, 5, 4, '2026-07-27', '2026-07-27', '15:07:00', '', 'Принята');

-- --------------------------------------------------------

--
-- Структура таблицы `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `borrow_days` int(11) NOT NULL DEFAULT 14
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `settings`
--

INSERT INTO `settings` (`id`, `borrow_days`) VALUES
(1, 14);

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('superadmin','admin','reader') DEFAULT 'admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'admin', '$2y$10$XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX', 'superadmin'),
(2, 'reader', '$2y$10$YYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYY', 'reader');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `authors`
--
ALTER TABLE `authors`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD KEY `author_id` (`author_id`),
  ADD KEY `genre_id` (`genre_id`);

--
-- Индексы таблицы `borrowings`
--
ALTER TABLE `borrowings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `book_id` (`book_id`),
  ADD KEY `reader_id` (`reader_id`);

--
-- Индексы таблицы `genres`
--
ALTER TABLE `genres`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `readers`
--
ALTER TABLE `readers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_reader_user` (`user_id`);

--
-- Индексы таблицы `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reader_id` (`reader_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Индексы таблицы `return_requests`
--
ALTER TABLE `return_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `borrowing_id` (`borrowing_id`),
  ADD KEY `reader_id` (`reader_id`);

--
-- Индексы таблицы `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `authors`
--
ALTER TABLE `authors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT для таблицы `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT для таблицы `borrowings`
--
ALTER TABLE `borrowings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `genres`
--
ALTER TABLE `genres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT для таблицы `readers`
--
ALTER TABLE `readers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `requests`
--
ALTER TABLE `requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `return_requests`
--
ALTER TABLE `return_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `authors` (`id`),
  ADD CONSTRAINT `books_ibfk_2` FOREIGN KEY (`genre_id`) REFERENCES `genres` (`id`);

--
-- Ограничения внешнего ключа таблицы `borrowings`
--
ALTER TABLE `borrowings`
  ADD CONSTRAINT `borrowings_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `borrowings_ibfk_2` FOREIGN KEY (`reader_id`) REFERENCES `readers` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `readers`
--
ALTER TABLE `readers`
  ADD CONSTRAINT `fk_reader_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `requests`
--
ALTER TABLE `requests`
  ADD CONSTRAINT `requests_ibfk_1` FOREIGN KEY (`reader_id`) REFERENCES `readers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `requests_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `return_requests`
--
ALTER TABLE `return_requests`
  ADD CONSTRAINT `return_requests_ibfk_1` FOREIGN KEY (`borrowing_id`) REFERENCES `borrowings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `return_requests_ibfk_2` FOREIGN KEY (`reader_id`) REFERENCES `readers` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
