CREATE DATABASE IF NOT EXISTS animal_shop CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE animal_shop;


-- Встановлюємо правильне кодування для імпорту
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;


-- Таблиця користувачів
CREATE TABLE users (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(50) NOT NULL,
  email VARCHAR(50) NOT NULL,
  role VARCHAR(20) NOT NULL,
  password VARCHAR(255) NOT NULL DEFAULT '',
  PRIMARY KEY (id),
  UNIQUE KEY id (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Таблиця тварин
CREATE TABLE animals (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(30) NOT NULL,
  type VARCHAR(20) NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  description TEXT NULL,
  photo VARCHAR(255) NULL,
  PRIMARY KEY (id),
  UNIQUE KEY id (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Таблиця замовлень
CREATE TABLE orders (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT(20) UNSIGNED NOT NULL,
  animal_id BIGINT(20) UNSIGNED NOT NULL,
  order_date DATE NOT NULL,
  status VARCHAR(30) NOT NULL,
  transfer_date DATE NOT NULL,
  payment_date DATE NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY id (id),
  KEY user_id (user_id, animal_id),
  KEY animal_id (animal_id),
  CONSTRAINT orders_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT orders_ibfk_2 FOREIGN KEY (animal_id) REFERENCES animals (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Наповнення таблиці users
INSERT INTO users (id, name, email, role) VALUES
(1, 'Савченко Антон Миколайович', 'anton.mykolaiovych@gmail.com', 'Кур\'єр'),
(2, 'Песоцький Дмитро Олександрович', 'dmytro.oleksandrovych@gmail.com', 'Кур\'єр'),
(3, 'Пришва Олександра Михайлівна', 'oleksandra.mykhailivna@gmail.com', 'Заводник'),
(4, 'Скоробагатько Леся Олексіївна', 'lesia.skorobahatko@gmail.com', 'Заводник'),
(5, 'Чорна Софія Ігорівна', 'julisew@gmail.com', 'Клієнт'),
(6, 'Бринь Єлизавета Євгеніївна', 'bryn.liza@gmail.com', 'Адміністратор сайту'),
(7, 'Коваль Катерина Семенівна', 'katia.kov@gmail.com', 'Клієнт'),
(8, 'Мартиненко Вадим Сергійович', 'vadym123@gmail.com', 'Клієнт'),
(9, 'Пономаренко Максим Максимович', 'maksym.maksymovych@gmail.com', 'Клієнт'),
(10, 'Боднар Альона Олександрівна', 'thu.mbelina@gmail.com', 'Клієнт'),
(11, 'Сльозка Тетяна Віталіївна', 'slz.tetiana@gmail.com', 'Клієнт'),
(12, 'Отрощенко Анна Андріївна', 'otroshchenko.anna@lll.kpi.ua', 'Клієнт'),
(13, 'Кучеренко Максим Сергійович', 'savedheart@gmail.com', 'Клієнт'),
(14, 'Ювженко Дмитро Олександрович', 'karakatihf@gmail.com', 'Клієнт'),
(15, 'Фесюн Наталія Степанівна', 'natfes@ukr.net', 'Заводник');

-- Наповнення таблиці animals
INSERT INTO animals (id, name, type, price) VALUES
(1, 'Вест-хайленд-вайт-тер\'єр', 'Собака', 20000.00),
(2, 'Цвергшнауцер', 'Собака', 18000.00),
(3, 'Бігль', 'Собака', 6500.00),
(4, 'Мопс', 'Собака', 7000.00),
(5, 'Англійський кокер-спанієль', 'Собака', 3000.00),
(6, 'Кане-корсо', 'Собака', 5000.00),
(7, 'Сіба-іну', 'Собака', 36000.00),
(8, 'Японський шпіц', 'Собака', 40000.00),
(9, 'Сфінкс', 'Кіт', 1000.00),
(10, 'Као-мані', 'Кіт', 8000.00),
(11, 'Мейн-кун', 'Кіт', 3500.00),
(12, 'Єгипетська мау', 'Кіт', 15000.00),
(13, 'Тонкінська', 'Кіт', 19000.00),
(14, 'Японський бобтейл', 'Кіт', 8600.00),
(15, 'Бенгалька кішка', 'Кіт', 7780.00),
(16, 'Бурмила', 'Кіт', 17530.00);

-- Наповнення таблиці orders
INSERT INTO orders (id, user_id, animal_id, order_date, status, transfer_date, payment_date, amount) VALUES
(1, 5, 1, '2016-03-17', 'Замовлення завершено', '2016-03-26', '2016-03-25', 22300.00),
(2, 9, 4, '2020-09-29', 'Замовлення завершено', '2020-10-02', '2020-10-03', 6890.00),
(3, 7, 10, '2025-09-02', 'Товар відправлено', '2025-09-28', '2025-09-17', 8000.00),
(4, 11, 7, '2023-12-21', 'Кошти повернуті', '2024-01-12', '2024-01-03', 36000.00),
(5, 10, 6, '2024-04-24', 'Замовлення завершено', '2024-04-30', '2024-05-04', 5000.00),
(6, 8, 12, '2025-10-08', 'Замовлення очікує обробки', '2025-10-12', '2025-10-10', 15000.00),
(7, 12, 16, '2024-12-12', 'Замовлення завершено', '2024-12-19', '2024-12-13', 18000.00),
(8, 13, 10, '2025-09-29', 'Товар відправлено', '2025-10-03', '2025-10-01', 8200.00);


UPDATE users
SET password = '$2y$10$CyyYn/2clcwCTSE3pL.on.LY2OtjNHQUDeVg45QAZodNGQKpNcCJm'
WHERE role = 'Адміністратор сайту';

UPDATE users 
SET password = '$2y$10$CyyYn/2clcwCTSE3pL.on.LY2OtjNHQUDeVg45QAZodNGQKpNcCJm' 
WHERE role IN ('Заводник', 'Кур\'єр');

ALTER TABLE users 
ADD COLUMN IF NOT EXISTS password VARCHAR(255) NOT NULL DEFAULT '' AFTER role;