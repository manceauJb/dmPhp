-- MySQL dump 10.13  Distrib 5.7.28, for Linux (x86_64)
--
-- Host: mysql.info.unicaen.fr    Database: 21713189_dev
-- ------------------------------------------------------
-- Server version	5.5.5-10.1.41-MariaDB-0+deb9u1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `account`
--

DROP TABLE IF EXISTS `account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `login` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `account`
--

LOCK TABLES `account` WRITE;
/*!40000 ALTER TABLE `account` DISABLE KEYS */;
INSERT INTO `account` VALUES (1,'admin','admin','$2y$10$KH6dqIF5jCBZamyZha.bR..7ZPbFomPPBPpXVjmRPEYEjoyAtc/cW',1),(2,'user1','user1','$2y$10$WzPChJZ/9KPhcMqq.hoBPeE2iC6y9Km7vtO.CWdJ4K35jSae6PaLa',0),(3,'user2','user2','$2y$10$iwGyQCjAkEvmL7RIaWfut.ozd8Ehr7NGJleFv37HB6ALNaIcc2wJ2',0);
/*!40000 ALTER TABLE `account` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `motos`
--

DROP TABLE IF EXISTS `motos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `motos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `modele` varchar(255) DEFAULT NULL,
  `marque` varchar(255) DEFAULT NULL,
  `annee` int(4) DEFAULT NULL,
  `cyl` int(11) DEFAULT NULL,
  `hp` int(11) DEFAULT NULL,
  `owner` int(11) DEFAULT NULL,
  `img` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `motos`
--

LOCK TABLES `motos` WRITE;
/*!40000 ALTER TABLE `motos` DISABLE KEYS */;
INSERT INTO `motos` VALUES (1,'YZF R1','Yamaha',2003,998,152,1,1),(2,'YZF R1','Yamaha',2005,998,172,1,1),(3,'YZF R1','Yamaha',2011,998,182,1,1),(4,'YZF R1','Yamaha',2018,998,200,1,1),(5,'1000 GSX R','Suzuki',2017,999,202,1,1),(6,'1000 GSX R','Suzuki',2009,999,185,1,1),(7,'1000 GSX R','Suzuki',2007,998,172,1,0),(8,'750 GSX R','Suzuki',2006,749,150,1,1),(9,'VeloSoleX 3800','Solex',1966,49,1,1,1),(10,'Daytona','Triumph',2013,675,128,1,0),(11,'Daytona','Triumph',2015,675,128,1,1);
/*!40000 ALTER TABLE `motos` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-12-01 19:44:51
