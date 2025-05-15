-- MySQL dump 10.13  Distrib 8.0.40, for Win64 (x86_64)
--
-- Host: localhost    Database: mygym
-- ------------------------------------------------------
-- Server version	8.0.40

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `members`
--

DROP TABLE IF EXISTS `members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `members` (
  `id` int NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) DEFAULT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `phone` varchar(11) DEFAULT NULL,
  `subscription_type` enum('حديد','اجهزه','private') DEFAULT NULL,
  `subscription_duration` enum('شهر','3 شهور','6 شهور','سنة') DEFAULT NULL,
  `age` int DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `coach_name` varchar(100) DEFAULT NULL,
  `notes` text,
  `previous_coach_name` varchar(100) DEFAULT NULL,
  `renewed_by` varchar(100) DEFAULT NULL,
  `renewed_at` datetime DEFAULT NULL,
  `notified_before_expiry` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `members`
--

LOCK TABLES `members` WRITE;
/*!40000 ALTER TABLE `members` DISABLE KEYS */;
INSERT INTO `members` VALUES (1,'محمد','أحمد','سعيد','01012345678','اجهزه','شهر',25,'2025-04-14','2025-05-14','ايهاب','1','احمد','ايهاب','2025-04-13 15:09:08',0),(2,'محمود','جمال','عبدالله','01098765432','اجهزه','3 شهور',32,'2025-03-09','2025-06-09','ايهاب','اشتراك قديم','احمد','ايهاب','2025-04-09 05:35:17',0),(3,'يوسف','خالد','عبدالرحمن','01122334455','حديد','شهر',22,'2025-04-10','2025-05-10','احمد','ههههه','ايهاب','احمد','2025-04-13 13:58:13',0),(4,'ياسين','محمد','طه','01234567890','اجهزه','شهر',28,'2025-03-15','2025-04-15','ايهاب','3','احمد','ايهاب','2025-04-13 15:08:50',1),(5,'عبدالرحمن','محمد','خاطر','01119709399','حديد','شهر',25,'2025-04-10','2025-05-10','محمد','بجربxv','ايهاب','محمد','2025-04-13 22:00:25',0),(6,'عمار ','محمد','عبدالرحمن','01119709399','حديد','3 شهور',20,'2025-03-10','2025-07-15','احمد','لا8','ايهاب','ايهاب','2025-04-15 21:49:32',0),(7,'احمد','عبد اللطيف','زيزو','01225607722','اجهزه','شهر',19,'2025-04-14','2025-05-14','ايهاب','شكرا','ايهاب','ايهاب','2025-04-12 09:24:56',0),(8,'اسلام','عصام','سعيد','01119709399','اجهزه','شهر',23,'2025-04-17','2025-05-17','ايهاب','لا ملاحظات','ايهاب','ايهاب','2025-04-17 13:22:57',0),(9,'احمد','عبد اللطيف','ذكريا','01119709399','اجهزه','شهر',19,'2025-04-14','2025-05-14','ايهاب','لا','محمد','ايهاب','2025-04-12 09:25:29',0),(10,'احمد','عبد اللطيف','عبدالعزيز','01212817383','حديد','شهر',20,'2025-01-14','2025-05-16','jsdh','بيدفع وي يمشي ','ايهاب','ايهاب','2025-04-16 11:45:35',0),(11,'عبدو','احمد','حسين','01119709399','حديد','شهر',12,'2025-03-13','2025-04-13','ايهاب','','ايهاب','ايهاب','2025-04-13 15:08:01',1),(12,'محمد','عبد اللطيف','محمود','01119709399','حديد','شهر',10,'2025-04-14','2025-05-14','عمار','',NULL,NULL,'2025-04-14 11:09:18',0),(13,'محمد','عصام','ذكريا','01119709399','حديد','شهر',10,'2025-04-14','2025-05-14','عمار','',NULL,NULL,'2025-04-14 11:10:17',0),(14,'محمد','محمد','محمد','01119709399','اجهزه','3 شهور',10,'2025-01-14','2025-04-14','ايهاب','','محمد','ايهاب','2025-04-14 11:28:10',0),(15,'عبد الرحمن','عصام','ذكريا','01119709399','اجهزه','شهر',10,'2025-03-14','2025-04-14','ايهاب','','عمار','ايهاب','2025-04-14 11:31:56',0),(16,'‪amar','Mohamed','aboamor‬‏','01119709399','private','6 شهور',14,'2024-11-14','2025-05-14','محمد','',NULL,NULL,'2025-04-14 11:34:25',0),(18,'عبده','محمد','عبد المقصود','01119709399','حديد','شهر',15,'2025-03-14','2025-04-14','محمد','',NULL,NULL,'2025-04-14 12:28:22',0),(19,'محمد','عبد اللطيف','عبدالله','01119709399','اجهزه','شهر',10,'2025-03-15','2025-04-15','ايهاب','','محمد','ايهاب','2025-04-16 13:03:50',1),(20,'عبد الرحمن','عبدالقادر','محمد','01119709399','private','شهر',20,'2025-03-16','2025-04-16','ايهاب','',NULL,NULL,NULL,0),(21,'test','test','test','01119709399','اجهزه','شهر',16,'2025-03-18','2025-04-18','عمار','',NULL,NULL,'2025-04-16 16:54:20',1),(22,'عبدالقادر','محمد','محمد','01119709399','private','سنة',22,'2025-04-16','2026-04-16','عمار','',NULL,NULL,'2025-04-16 18:10:10',0),(23,'احمد','سمير','احمد','01119709399','private','شهر',23,'2025-03-16','2025-04-16','عمار','',NULL,NULL,'2025-04-16 22:49:12',1),(24,'يوسف','يوسف','يوسف','01146018809','حديد','شهر',20,'2025-04-17','2025-05-17','عمار','',NULL,NULL,'2025-04-17 13:20:02',0),(25,'‪amar','t','aboamor‬‏','01119709399','اجهزه','6 شهور',10,'2025-04-18','2025-10-18','عمار','df',NULL,NULL,'2025-04-18 21:16:47',0);
/*!40000 ALTER TABLE `members` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `member_fullname` varchar(150) DEFAULT NULL,
  `session_type` enum('normal','equipment') DEFAULT NULL,
  `gender` enum('male','female') DEFAULT NULL,
  `phone` varchar(11) DEFAULT NULL,
  `age` int DEFAULT NULL,
  `coach_name` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES (35,'كارما ايهاب','normal','female','01119709309',10,'عزه','2025-04-24 07:03:30'),(36,'كارما ايهاب','normal','female','01119709309',10,'عزه','2025-04-24 07:04:59'),(37,'كارما ايهاب','normal','female','01119709309',10,'عزه','2025-04-24 07:16:24'),(38,'كارما ايهاب','normal','female','01119709399',21,'عزه','2025-04-24 07:17:43'),(39,'كارما ايهاب','normal','female','01119709399',10,'عزه','2025-04-24 07:24:45'),(40,'كارما ايهاب','normal','female','01119709399',10,'عزه','2025-04-24 07:26:48'),(41,'كارما ايهاب','normal','female','01119709399',10,'عزه','2025-04-24 07:28:45'),(42,'احمد عبداللطيف','normal','male','01119709399',10,'ايهاب','2025-04-24 07:43:43'),(43,'احمد عبداللطيف','equipment','male','01119709399',10,'عمار','2025-04-24 07:44:42'),(44,'احمد عبداللطيف','normal','male','01119709399',10,'عمار','2025-04-24 07:45:53'),(45,'احمد محمد','normal','male','01119709309',41,'عمار','2025-05-15 00:36:29');
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','myadmingym2015','admin'),(2,'coach','coach123','coach'),(4,'wcoach','123wcoach','wcoach');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `womembers`
--

DROP TABLE IF EXISTS `womembers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `womembers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) DEFAULT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `phone` varchar(11) DEFAULT NULL,
  `subscription_type` enum('حديد','اجهزه','private') DEFAULT NULL,
  `subscription_duration` enum('شهر','3 شهور','6 شهور','سنة') DEFAULT NULL,
  `age` int DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `coach_name` varchar(100) DEFAULT NULL,
  `notes` text,
  `previous_coach_name` varchar(100) DEFAULT NULL,
  `renewed_by` varchar(100) DEFAULT NULL,
  `renewed_at` datetime DEFAULT NULL,
  `notified_before_expiry` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `womembers`
--

LOCK TABLES `womembers` WRITE;
/*!40000 ALTER TABLE `womembers` DISABLE KEYS */;
INSERT INTO `womembers` VALUES (1,'مريم','محمد','سيد','01119709399','حديد','شهر',12,'2025-03-13','2025-04-13','عزه','','عزه','عزه','2025-04-14 13:04:50',0),(2,'كارما','ايهاب','خاطر','01119709399','حديد','شهر',4,'2025-03-13','2025-04-13','عزه','','عزه','عزه','2025-04-14 13:26:49',0),(4,'شيماء','احمد','حسين','01119709399','حديد','شهر',13,'2025-03-13','2025-04-13','عزه','','عزه','عزه','2025-04-13 17:51:02',1),(5,'شيماء','محمد','عمور','01119709399','حديد','شهر',10,'2025-04-13','2025-05-13','ايهاب','',NULL,NULL,'2025-04-13 22:06:58',0),(6,'اهله','ايهاب','خاطر','01220969078','حديد','شهر',12,'2025-04-13','2025-05-13','عزه','',NULL,NULL,'2025-04-13 22:45:03',0),(7,'اهله','محمد','حسين','01220969078','حديد','شهر',10,'2025-04-13','2025-05-13','يسنتب','',NULL,NULL,'2025-04-13 22:48:25',0),(8,'هناء','مجدي','محمد','01119709399','حديد','شهر',15,'2025-04-14','2025-05-14','عزه','',NULL,NULL,'2025-04-14 10:52:35',0),(9,'هدي ','مجدي','توفيق','01119709399','private','6 شهور',10,'2024-10-14','2025-04-14','عزه','','عزه','عزه','2025-04-14 13:03:27',0),(10,'My','gym','Gym','01119709399','private','3 شهور',14,'2025-01-14','2025-04-14','عزه','',NULL,NULL,'2025-04-14 13:07:23',0),(12,'مني','مجدي','عيده','01119709399','حديد','شهر',14,'2025-03-14','2025-04-14','عزه','',NULL,NULL,'2025-04-14 13:19:22',0),(14,'زينب','احمد','طه','01119709399','حديد','3 شهور',12,'2025-04-14','2025-07-14','عزة','',NULL,NULL,'2025-04-14 21:05:14',0),(15,'زينب','احمد','عبدالله','01119709399','اجهزه','شهر',14,'2025-04-15','2025-05-15','عمار','',NULL,NULL,NULL,0),(16,'test','test','test','01119709399','private','3 شهور',19,'2025-01-17','2025-04-17','عزه','',NULL,NULL,'2025-04-16 16:57:10',0),(17,'test','testv','test','01119709309','حديد','شهر',16,'2025-03-18','2025-04-18','aza','',NULL,NULL,'2025-04-18 21:20:50',0);
/*!40000 ALTER TABLE `womembers` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-05-15  2:22:38
