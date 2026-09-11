-- MySQL dump 10.13  Distrib 8.3.0, for Win64 (x86_64)
--
-- Host: localhost    Database: ecf2_mohammed
-- ------------------------------------------------------
-- Server version	8.3.0

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
-- Current Database: `ecf2_mohammed`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `ecf2_mohammed` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `ecf2_mohammed`;

--
-- Table structure for table `absence`
--

DROP TABLE IF EXISTS `absence`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `absence` (
  `absence_id` int unsigned NOT NULL AUTO_INCREMENT,
  `absence_date` date NOT NULL,
  `reason` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `proof_filename` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trainee_id` int unsigned NOT NULL,
  PRIMARY KEY (`absence_id`),
  UNIQUE KEY `uq_absence_trainee_date` (`trainee_id`,`absence_date`),
  CONSTRAINT `fk_absence_trainee` FOREIGN KEY (`trainee_id`) REFERENCES `trainee` (`trainee_id`) ON DELETE CASCADE,
  CONSTRAINT `chk_absence_reason` CHECK ((`reason` in (_utf8mb4'illness',_utf8mb4'unexcused',_utf8mb4'legal_leave',_utf8mb4'work_accident')))
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `absence`
--

LOCK TABLES `absence` WRITE;
/*!40000 ALTER TABLE `absence` DISABLE KEYS */;
/*!40000 ALTER TABLE `absence` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_user`
--

DROP TABLE IF EXISTS `admin_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admin_user` (
  `admin_user_id` int unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  PRIMARY KEY (`admin_user_id`),
  UNIQUE KEY `uq_admin_user_username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_user`
--

LOCK TABLES `admin_user` WRITE;
/*!40000 ALTER TABLE `admin_user` DISABLE KEYS */;
INSERT INTO `admin_user` VALUES (1,'ADMINAFPA','$2y$13$PVbgRY5RL09Dx3YAQrFUUuNd7eAViusVzKLdng3iia7xLfEqb7e/G','[\"ROLE_ADMIN\"]');
/*!40000 ALTER TABLE `admin_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trainee`
--

DROP TABLE IF EXISTS `trainee`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trainee` (
  `trainee_id` int unsigned NOT NULL AUTO_INCREMENT,
  `afpa_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `residence` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birth_date` date NOT NULL,
  `photo_filename` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`trainee_id`),
  UNIQUE KEY `uq_trainee_afpa_id` (`afpa_id`),
  UNIQUE KEY `uq_trainee_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trainee`
--

LOCK TABLES `trainee` WRITE;
/*!40000 ALTER TABLE `trainee` DISABLE KEYS */;
INSERT INTO `trainee` VALUES (1,'22116576','Adila','Kehlaoui','adi.kehlaoui@gmail.com','0645557195','Bordeaux','1990-12-18',NULL),(2,'26020093','Mohammed','Benerroua','benerrouamohammed@gmail.com','0767250170','Bordeaux','1990-04-01',NULL),(3,'26020095','Ghislène','Bellia','ghislenebellia@gmail.com','0662877894',NULL,'2005-08-25',NULL),(4,'26020096','Aurèle','Camps','campsaurele@gmail.com','0668368996',NULL,'1995-11-26',NULL),(5,'26020097','Nelly','Fabre','nelly.fabre@hotmail.fr','0627154096','Cussac Fort Médoc','1983-10-02',NULL),(6,'26020141','Sarah','Casabianca','sarah.casabianca@gmail.com','0683049749',NULL,'1996-06-10',NULL),(7,'26020143','Juan','Rojas Cuicas','rjuan3683@gmail.com','0635902566','Bordeaux','2000-08-04',NULL),(8,'26020156','Lucas','Merlet','merletlucas2@gmail.com','0788697051',NULL,'2002-09-12',NULL),(9,'26020263','Faten','Bannani','belmahriafatenn@gmail.com','0602568388',NULL,'1997-05-04',NULL),(10,'26020268','Nathanael','Kenzey','nathanael.kenzey@gmail.com','0745165819','Paris','1998-10-22',NULL),(11,'26020916','Anthony','Lutard','anthony.lutard33@gmail.com','0750862760',NULL,'2003-12-03',NULL),(12,'26028145','Mélanie','Saez','emel.saez@gmail.com','0760227763',NULL,'1986-02-16',NULL);
/*!40000 ALTER TABLE `trainee` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-11 15:42:31
