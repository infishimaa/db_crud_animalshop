-- MySQL dump 10.13  Distrib 8.0.44, for Linux (x86_64)
--
-- Host: localhost    Database: animal_shop
-- ------------------------------------------------------
-- Server version	8.0.44

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Position to start replication or point-in-time recovery from
--

-- CHANGE MASTER TO MASTER_LOG_FILE='binlog.000002', MASTER_LOG_POS=2816;

--
-- Current Database: `animal_shop`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `animal_shop` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `animal_shop`;

--
-- Table structure for table `animals`
--

DROP TABLE IF EXISTS `animals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `animals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `type` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `animals`
--

LOCK TABLES `animals` WRITE;
/*!40000 ALTER TABLE `animals` DISABLE KEYS */;
INSERT INTO `animals` VALUES (1,'Вест-хайленд-вайт-тер\'єр','Собака',20000.00),(2,'Цвергшнауцер','Собака',18000.00),(3,'Бігль','Собака',6500.00),(4,'Мопс','Собака',7000.00),(5,'Англійський кокер-спанієль','Собака',3000.00),(6,'Кане-корсо','Собака',5000.00),(7,'Сіба-іну','Собака',36000.00),(8,'Японський шпіц','Собака',40000.00),(9,'Сфінкс','Кіт',1000.00),(10,'Као-мані','Кіт',8000.00),(11,'Мейн-кун','Кіт',3500.00),(12,'Єгипетська мау','Кіт',15000.00),(13,'Тонкінська','Кіт',19000.00),(14,'Японський бобтейл','Кіт',8600.00),(15,'Бенгалька кішка','Кіт',7780.00),(16,'Бурмила','Кіт',17530.00);
/*!40000 ALTER TABLE `animals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `animal_id` bigint unsigned NOT NULL,
  `order_date` date NOT NULL,
  `status` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `transfer_date` date NOT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `user_id` (`user_id`,`animal_id`),
  KEY `animal_id` (`animal_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`animal_id`) REFERENCES `animals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,5,1,'2016-03-17','Замовлення завершено','2016-03-26','2016-03-25',22300.00),(2,9,4,'2020-09-29','Замовлення завершено','2020-10-02','2020-10-03',6890.00),(3,7,10,'2025-09-02','Товар відправлено','2025-09-28','2025-09-17',8000.00),(4,11,7,'2023-12-21','Кошти повернуті','2024-01-12','2024-01-03',36000.00),(5,10,6,'2024-04-24','Замовлення завершено','2024-04-30','2024-05-04',5000.00),(6,8,12,'2025-10-08','Замовлення очікує обробки','2025-10-12','2025-10-10',15000.00),(7,12,16,'2024-12-12','Замовлення завершено','2024-12-19','2024-12-13',18000.00),(8,13,10,'2025-09-29','Товар відправлено','2025-10-03','2025-10-01',8200.00);
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `role` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Савченко Антон Миколайович','anton.mykolaiovych@gmail.com','Кур\'єр'),(2,'Песоцький Дмитро Олександрович','dmytro.oleksandrovych@gmail.com','Кур\'єр'),(3,'Пришва Олександра Михайлівна','oleksandra.mykhailivna@gmail.com','Заводник'),(4,'Скоробагатько Леся Олексіївна','lesia.skorobahatko@gmail.com','Заводник'),(5,'Чорна Софія Ігорівна','julisew@gmail.com','Клієнт'),(6,'Бринь Єлизавета Євгеніївна','bryn.liza@gmail.com','Адміністратор сайту'),(7,'Коваль Катерина Семенівна','katia.kov@gmail.com','Клієнт'),(8,'Мартиненко Вадим Сергійович','vadym123@gmail.com','Клієнт'),(9,'Пономаренко Максим Максимович','maksym.maksymovych@gmail.com','Клієнт'),(10,'Боднар Альона Олександрівна','thu.mbelina@gmail.com','Клієнт'),(11,'Сльозка Тетяна Віталіївна','slz.tetiana@gmail.com','Клієнт'),(12,'Отрощенко Анна Андріївна','otroshchenko.anna@lll.kpi.ua','Клієнт'),(13,'Кучеренко Максим Сергійович','savedheart@gmail.com','Клієнт'),(14,'Ювженко Дмитро Олександрович','karakatihf@gmail.com','Клієнт'),(15,'Фесюн Наталія Степанівна','natfes@ukr.net','Заводник');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-12-06 20:39:58
