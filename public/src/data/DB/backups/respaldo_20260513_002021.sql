-- MySQL dump 10.13  Distrib 8.0.44, for Win64 (x86_64)
--
-- Host: localhost    Database: abdarcproyecto1
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
-- Table structure for table `abogados`
--

DROP TABLE IF EXISTS `abogados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `abogados` (
  `id_abogado` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `cedula_profesional` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `rol` enum('abogado','admin') DEFAULT 'abogado',
  `token_recuperacion` varchar(100) DEFAULT NULL,
  `expiracion_token` datetime DEFAULT NULL,
  PRIMARY KEY (`id_abogado`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `abogados`
--

LOCK TABLES `abogados` WRITE;
/*!40000 ALTER TABLE `abogados` DISABLE KEYS */;
INSERT INTO `abogados` VALUES (1,'Edwin David Morales Guevara','6141112233','CED12345','carlos@bufete.com','hash3','abogado',NULL,NULL),(2,'Carlos Alberto Cerda Altamirano','6142223344','CED67890','ana@bufete.com','hash4','admin','1',NULL);
/*!40000 ALTER TABLE `abogados` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `abogados_especialidades`
--

DROP TABLE IF EXISTS `abogados_especialidades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `abogados_especialidades` (
  `id_abogado` int NOT NULL,
  `id_especialidad` int NOT NULL,
  PRIMARY KEY (`id_abogado`,`id_especialidad`),
  KEY `id_especialidad` (`id_especialidad`),
  CONSTRAINT `fk_ae_abogado` FOREIGN KEY (`id_abogado`) REFERENCES `abogados` (`id_abogado`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_ae_especialidad` FOREIGN KEY (`id_especialidad`) REFERENCES `especialidades` (`id_especialidad`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `abogados_especialidades`
--

LOCK TABLES `abogados_especialidades` WRITE;
/*!40000 ALTER TABLE `abogados_especialidades` DISABLE KEYS */;
INSERT INTO `abogados_especialidades` VALUES (1,1),(1,2),(1,3),(2,3),(2,4);
/*!40000 ALTER TABLE `abogados_especialidades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `actividades`
--

DROP TABLE IF EXISTS `actividades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `actividades` (
  `id_actividad` int NOT NULL AUTO_INCREMENT,
  `id_expediente` int DEFAULT NULL,
  `id_abogado` int DEFAULT NULL,
  `id_tipo` int NOT NULL,
  `descripcion` varchar(250) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `hora` time DEFAULT NULL,
  `id_estado` int NOT NULL,
  `creada_por` enum('abogado','cliente') DEFAULT NULL,
  PRIMARY KEY (`id_actividad`),
  KEY `id_expediente` (`id_expediente`),
  KEY `id_abogado` (`id_abogado`),
  KEY `id_tipo` (`id_tipo`),
  KEY `id_estado` (`id_estado`),
  CONSTRAINT `fk_actividad_abogado` FOREIGN KEY (`id_abogado`) REFERENCES `abogados` (`id_abogado`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_actividad_estado` FOREIGN KEY (`id_estado`) REFERENCES `estados_actividad` (`id_estado`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_actividad_expediente` FOREIGN KEY (`id_expediente`) REFERENCES `expedientes` (`id_expediente`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_actividad_tipo` FOREIGN KEY (`id_tipo`) REFERENCES `tipos_actividad` (`id_tipo`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `actividades`
--

LOCK TABLES `actividades` WRITE;
/*!40000 ALTER TABLE `actividades` DISABLE KEYS */;
INSERT INTO `actividades` VALUES (1,1,1,1,'Primera reunión con cliente','2026-04-15','10:00:00',1,'abogado'),(2,1,1,2,'Audiencia inicial','2026-04-20','09:00:00',1,'abogado'),(3,2,2,1,'Revisión de documentos','2026-04-18','12:00:00',1,'abogado');
/*!40000 ALTER TABLE `actividades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `administradores`
--

DROP TABLE IF EXISTS `administradores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `administradores` (
  `id_admin` int NOT NULL AUTO_INCREMENT,
  `user` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `ap1` varchar(255) NOT NULL,
  `telefono` varchar(255) NOT NULL,
  `correo` varchar(255) NOT NULL,
  PRIMARY KEY (`id_admin`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `administradores`
--

LOCK TABLES `administradores` WRITE;
/*!40000 ALTER TABLE `administradores` DISABLE KEYS */;
INSERT INTO `administradores` VALUES (1,'CarlosAdmin','pass123','Carlos','Villalobos','6141234567','carlos.vg@gmail.com'),(2,'EdwinAdmin','pass456','Edwin','Morales','6149876543','edwin.mg@gmail.com'),(3,'FerAdmin','pass789','Fernado','Delgado','6145556677','fer.dj@gmail.com'),(4,'KenAdmin','pass555','Ken','Meza','6145556677','ken.my@gmail.com');
/*!40000 ALTER TABLE `administradores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `agencias`
--

DROP TABLE IF EXISTS `agencias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `agencias` (
  `id_agencia` int NOT NULL AUTO_INCREMENT,
  `descripcion` text NOT NULL,
  `nombre_agencia` varchar(255) NOT NULL,
  `fecha_registro` date NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `imagen_url` varchar(255) NOT NULL,
  `user` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `telefono` varchar(10) NOT NULL,
  `correo` varchar(255) NOT NULL,
  PRIMARY KEY (`id_agencia`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `agencias`
--

LOCK TABLES `agencias` WRITE;
/*!40000 ALTER TABLE `agencias` DISABLE KEYS */;
INSERT INTO `agencias` VALUES (1,'Especializada en viajes grupales y experiencias personalizadas','Mega Viajes','2025-01-15','Av. Universidad 1234, Chihuahua, Chih.','eimg1.png','megaviajes','megaviajes123','6141234567','contacto@megaviajes.com'),(2,'Agencia enfocada en turismo y paquetes culturales','Platinum Travels','2025-02-10','Av. Fco. Villa 4907, Plaza Arboledas Local 217, Chihuahua, Chih.','eimg2.png','platinum','platinum456','6142345678','info@platinumtravels.com'),(3,'Ofrece paquetes de viaje y asesoría personalizada para turistas','Al Viajar','2025-03-05','Av. Francisco Villa 4904-5, Arboledas, Chihuahua, Chih.','eimg3.png','alviajar','alviajar789','6143456789','contacto@alviajar.com');
/*!40000 ALTER TABLE `agencias` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `tr_insertaragencia` AFTER INSERT ON `agencias` FOR EACH ROW begin	
		insert into bit_agencias(
			usuario,
            bit_descripcion,
            fecha,
            id,
            desc_nvo,
            nom_nvo,
            direc_nvo,
            usuario_nvo,
            clave_nvo,
            tel_nvo,
            correo_nvo)
		values(
			user(),
            'Agencia nueva',
            now(),
            new.id_agencia,
            new.descripcion,
            new.nombre_agencia,
            new.direccion,
            new.user,
            new.password,
            new.telefono,
            new.correo
            );
    END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `tr_actualizaragencia` BEFORE UPDATE ON `agencias` FOR EACH ROW begin	
		insert into bit_agencias(
			usuario,
            bit_descripcion,
            fecha,
            id,
            desc_ant,
            desc_nvo,
            nom_ant,
            nom_nvo,
            direc_ant,
            direc_nvo,
            usuario_ant,
            usuario_nvo,
            clave_ant,
            clave_nvo,
            tel_ant,
            tel_nvo,
            correo_ant,
            correo_nvo)
		values(
			user(),
            'Agencia modificada',
            now(),
            new.id_agencia,
            old.descripcion,
            new.descripcion,
            old.nombre_agencia,
            new.nombre_agencia,
            old.direccion,
            new.direccion,
            old.user,
            new.user,
            old.password,
            new.password,
            old.telefono,
            new.telefono,
            old.correo,
            new.correo
            );
    END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `tr_eliminaragencia` BEFORE DELETE ON `agencias` FOR EACH ROW BEGIN
    INSERT INTO bit_agencias(
        usuario,
        bit_descripcion,
        fecha,
        id,
        desc_ant,
        nom_ant,
        direc_ant,
        usuario_ant,
        clave_ant,
        tel_ant,
        correo_ant
    )
    VALUES(
        USER(),
        'Agencia eliminada',
        NOW(),
        OLD.id_agencia,
        OLD.descripcion,
        OLD.nombre_agencia,
        OLD.direccion,
        OLD.user,
        OLD.password,
        OLD.telefono,
        OLD.correo
    );
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `archivos`
--

DROP TABLE IF EXISTS `archivos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `archivos` (
  `id_archivo` int NOT NULL AUTO_INCREMENT,
  `id_expediente` int NOT NULL,
  `id_usuario_subio` int DEFAULT NULL COMMENT 'ID del abogado o cliente que subio el archivo',
  `nombre_original` varchar(255) NOT NULL,
  `nombre_fisico` varchar(150) NOT NULL,
  `categoria` varchar(50) DEFAULT NULL,
  `extension` varchar(10) DEFAULT NULL,
  `peso_bytes` int DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `fecha_subida` datetime DEFAULT CURRENT_TIMESTAMP,
  `estado` enum('activo','eliminado') DEFAULT 'activo',
  PRIMARY KEY (`id_archivo`),
  KEY `id_expediente` (`id_expediente`),
  CONSTRAINT `archivos_ibfk_1` FOREIGN KEY (`id_expediente`) REFERENCES `expedientes` (`id_expediente`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `archivos`
--

LOCK TABLES `archivos` WRITE;
/*!40000 ALTER TABLE `archivos` DISABLE KEYS */;
INSERT INTO `archivos` VALUES (1,1,1,'denuncia.pdf','file_001.pdf','legal','pdf',204800,'Documento de denuncia','2026-04-13 10:07:05','activo'),(2,1,1,'evidencia.jpg','file_002.jpg','evidencia','jpg',102400,'Foto evidencia','2026-04-13 10:07:05','activo'),(3,2,2,'contrato.pdf','file_003.pdf','laboral','pdf',307200,'Contrato laboral','2026-04-13 10:07:05','activo');
/*!40000 ALTER TABLE `archivos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audiencias`
--

DROP TABLE IF EXISTS `audiencias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audiencias` (
  `id_actividad` int NOT NULL,
  `juzgado` varchar(100) DEFAULT NULL,
  `sala` varchar(50) DEFAULT NULL,
  `juez` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_actividad`),
  CONSTRAINT `fk_audiencia_actividad` FOREIGN KEY (`id_actividad`) REFERENCES `actividades` (`id_actividad`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audiencias`
--

LOCK TABLES `audiencias` WRITE;
/*!40000 ALTER TABLE `audiencias` DISABLE KEYS */;
INSERT INTO `audiencias` VALUES (2,'Juzgado Penal #3','Sala 2','Lic. Gómez');
/*!40000 ALTER TABLE `audiencias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bit_agencias`
--

DROP TABLE IF EXISTS `bit_agencias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bit_agencias` (
  `usuario` varchar(255) DEFAULT NULL,
  `bit_descripcion` varchar(255) DEFAULT NULL,
  `fecha` timestamp NULL DEFAULT NULL,
  `id` int DEFAULT NULL,
  `desc_ant` varchar(255) DEFAULT NULL,
  `desc_nvo` varchar(255) DEFAULT NULL,
  `nom_ant` varchar(255) DEFAULT NULL,
  `nom_nvo` varchar(255) DEFAULT NULL,
  `direc_ant` varchar(255) DEFAULT NULL,
  `direc_nvo` varchar(255) DEFAULT NULL,
  `usuario_ant` varchar(255) DEFAULT NULL,
  `usuario_nvo` varchar(255) DEFAULT NULL,
  `clave_ant` varchar(255) DEFAULT NULL,
  `clave_nvo` varchar(255) DEFAULT NULL,
  `tel_ant` varchar(255) DEFAULT NULL,
  `tel_nvo` varchar(255) DEFAULT NULL,
  `correo_ant` varchar(255) DEFAULT NULL,
  `correo_nvo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bit_agencias`
--

LOCK TABLES `bit_agencias` WRITE;
/*!40000 ALTER TABLE `bit_agencias` DISABLE KEYS */;
/*!40000 ALTER TABLE `bit_agencias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bitclientes`
--

DROP TABLE IF EXISTS `bitclientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bitclientes` (
  `id_bitclientes` int NOT NULL AUTO_INCREMENT,
  `usuario` varchar(255) DEFAULT NULL,
  `fecha_hora` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `accion` varchar(255) DEFAULT NULL,
  `id_afectado` int DEFAULT NULL,
  `old_user` varchar(255) DEFAULT NULL,
  `new_user` varchar(255) DEFAULT NULL,
  `old_password` varchar(255) DEFAULT NULL,
  `new_password` varchar(255) DEFAULT NULL,
  `old_nombre` varchar(255) DEFAULT NULL,
  `new_nombre` varchar(255) DEFAULT NULL,
  `old_ap1` varchar(255) DEFAULT NULL,
  `new_ap1` varchar(255) DEFAULT NULL,
  `old_correo` varchar(255) DEFAULT NULL,
  `new_correo` varchar(255) DEFAULT NULL,
  `old_telefono` varchar(10) DEFAULT NULL,
  `new_telefono` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id_bitclientes`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bitclientes`
--

LOCK TABLES `bitclientes` WRITE;
/*!40000 ALTER TABLE `bitclientes` DISABLE KEYS */;
/*!40000 ALTER TABLE `bitclientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `biteventos`
--

DROP TABLE IF EXISTS `biteventos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `biteventos` (
  `id_bitacora` int NOT NULL AUTO_INCREMENT,
  `usuario` varchar(100) DEFAULT NULL,
  `fecha_hora` datetime DEFAULT NULL,
  `accion` varchar(50) DEFAULT NULL,
  `id_afectado` int DEFAULT NULL,
  `old_nombre` varchar(255) DEFAULT NULL,
  `new_nombre` varchar(255) DEFAULT NULL,
  `old_descripcion` text,
  `new_descripcion` text,
  `old_fecha` date DEFAULT NULL,
  `new_fecha` date DEFAULT NULL,
  `old_hora` time DEFAULT NULL,
  `new_hora` time DEFAULT NULL,
  `old_precio` decimal(10,2) DEFAULT NULL,
  `new_precio` decimal(10,2) DEFAULT NULL,
  `old_id_lugar` int DEFAULT NULL,
  `new_id_lugar` int DEFAULT NULL,
  PRIMARY KEY (`id_bitacora`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `biteventos`
--

LOCK TABLES `biteventos` WRITE;
/*!40000 ALTER TABLE `biteventos` DISABLE KEYS */;
/*!40000 ALTER TABLE `biteventos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bitlugares`
--

DROP TABLE IF EXISTS `bitlugares`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bitlugares` (
  `id_bitlugares` int NOT NULL AUTO_INCREMENT,
  `usuario` varchar(255) DEFAULT NULL,
  `fecha_hora` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `accion` varchar(255) DEFAULT NULL,
  `id_afectado` int DEFAULT NULL,
  `old_nombre_lugar` varchar(255) DEFAULT NULL,
  `new_nombre_lugar` varchar(255) DEFAULT NULL,
  `old_descripcion` text,
  `new_descripcion` text,
  `old_direccion` varchar(255) DEFAULT NULL,
  `new_direccion` varchar(255) DEFAULT NULL,
  `old_ciudad` varchar(255) DEFAULT NULL,
  `new_ciudad` varchar(255) DEFAULT NULL,
  `old_zona` varchar(255) DEFAULT NULL,
  `new_zona` varchar(255) DEFAULT NULL,
  `old_imagen_url` varchar(255) DEFAULT NULL,
  `new_imagen_url` varchar(255) DEFAULT NULL,
  `old_id_admin` int DEFAULT NULL,
  `new_id_admin` int DEFAULT NULL,
  PRIMARY KEY (`id_bitlugares`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bitlugares`
--

LOCK TABLES `bitlugares` WRITE;
/*!40000 ALTER TABLE `bitlugares` DISABLE KEYS */;
INSERT INTO `bitlugares` VALUES (1,'root@localhost','2026-03-17 01:55:18','INSERT / Creacion de lugar',0,NULL,'NoOficial',NULL,'NoOficial',NULL,'NoOficial',NULL,'NoOficial',NULL,'NoOficial',NULL,'NoOficial',NULL,1),(2,'root@localhost','2026-03-17 01:55:42','Update / Modificacion de lugar',51,'NoOficial','Oficial','NoOficial','NoOficial','NoOficial','NoOficial','NoOficial','NoOficial','NoOficial','NoOficial','NoOficial','NoOficial',1,1),(3,'root@localhost','2026-03-17 01:56:25','Delete / Eliminacion de lugar',51,'Oficial',NULL,'NoOficial',NULL,'NoOficial',NULL,'NoOficial',NULL,'NoOficial',NULL,'NoOficial',NULL,1,NULL);
/*!40000 ALTER TABLE `bitlugares` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bitpaquetes`
--

DROP TABLE IF EXISTS `bitpaquetes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bitpaquetes` (
  `id_bitpaquete` int NOT NULL AUTO_INCREMENT,
  `usuario` varchar(100) DEFAULT NULL,
  `fecha_hora` datetime DEFAULT NULL,
  `accion` varchar(100) DEFAULT NULL,
  `id_afectado` int DEFAULT NULL,
  `old_nombre_paquete` varchar(255) DEFAULT NULL,
  `new_nombre_paquete` varchar(255) DEFAULT NULL,
  `old_descripcion_paquete` text,
  `new_descripcion_paquete` text,
  `old_precio` decimal(10,2) DEFAULT NULL,
  `new_precio` decimal(10,2) DEFAULT NULL,
  `old_id_agencia` int DEFAULT NULL,
  `new_id_agencia` int DEFAULT NULL,
  `old_id_lugar` int DEFAULT NULL,
  `new_id_lugar` int DEFAULT NULL,
  `old_imagen_url` varchar(255) DEFAULT NULL,
  `new_imagen_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_bitpaquete`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bitpaquetes`
--

LOCK TABLES `bitpaquetes` WRITE;
/*!40000 ALTER TABLE `bitpaquetes` DISABLE KEYS */;
/*!40000 ALTER TABLE `bitpaquetes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bitreservaciones`
--

DROP TABLE IF EXISTS `bitreservaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bitreservaciones` (
  `id_bitreservaciones` int NOT NULL AUTO_INCREMENT,
  `usuario` varchar(45) DEFAULT NULL,
  `fecha_hora` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `accion` varchar(45) DEFAULT NULL,
  `id_afectado` varchar(45) DEFAULT NULL,
  `old_id_evento` int DEFAULT NULL,
  `new_id_evento` int DEFAULT NULL,
  `old_id_cliente` int DEFAULT NULL,
  `new_id_cliente` int DEFAULT NULL,
  `old_estado` varchar(255) DEFAULT NULL,
  `new_estado` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_bitreservaciones`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bitreservaciones`
--

LOCK TABLES `bitreservaciones` WRITE;
/*!40000 ALTER TABLE `bitreservaciones` DISABLE KEYS */;
INSERT INTO `bitreservaciones` VALUES (1,'root@localhost','2026-04-13 18:22:49','INSERT / Creacion de reservacion','131',NULL,18,NULL,9,NULL,'pendiente'),(2,'root@localhost','2026-04-13 18:22:50','UPDATE / Modificacion de reservacion','131',18,18,9,9,'pendiente','cancelado'),(3,'root@localhost','2026-04-13 18:22:50','DELETE / Eliminacion de reservacion','131',18,NULL,9,NULL,'cancelado',NULL),(4,'root@localhost','2026-04-13 18:23:08','INSERT / Creacion de reservacion','131',NULL,18,NULL,9,NULL,'pendiente'),(5,'root@localhost','2026-04-13 18:23:08','UPDATE / Modificacion de reservacion','131',18,18,9,9,'pendiente','cancelado'),(6,'root@localhost','2026-04-13 18:23:08','DELETE / Eliminacion de reservacion','131',18,NULL,9,NULL,'cancelado',NULL),(7,'root@localhost','2026-04-13 18:23:18','INSERT / Creacion de reservacion','131',NULL,18,NULL,9,NULL,'pendiente'),(8,'root@localhost','2026-04-13 18:23:18','UPDATE / Modificacion de reservacion','131',18,18,9,9,'pendiente','cancelado'),(9,'root@localhost','2026-04-13 18:23:18','DELETE / Eliminacion de reservacion','131',18,NULL,9,NULL,'cancelado',NULL);
/*!40000 ALTER TABLE `bitreservaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bitviajes`
--

DROP TABLE IF EXISTS `bitviajes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bitviajes` (
  `id_bitacora` int NOT NULL AUTO_INCREMENT,
  `usuario` varchar(100) DEFAULT NULL,
  `fecha_hora` datetime DEFAULT NULL,
  `accion` varchar(100) DEFAULT NULL,
  `id_afectado` int DEFAULT NULL,
  `old_id_cliente` int DEFAULT NULL,
  `new_id_cliente` int DEFAULT NULL,
  `old_id_paquete` int DEFAULT NULL,
  `new_id_paquete` int DEFAULT NULL,
  `old_estado` varchar(255) DEFAULT NULL,
  `new_estado` varchar(255) DEFAULT NULL,
  `old_fecha_viaje` date DEFAULT NULL,
  `new_fecha_viaje` date DEFAULT NULL,
  `old_hora_viaje` time DEFAULT NULL,
  `new_hora_viaje` time DEFAULT NULL,
  PRIMARY KEY (`id_bitacora`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bitviajes`
--

LOCK TABLES `bitviajes` WRITE;
/*!40000 ALTER TABLE `bitviajes` DISABLE KEYS */;
/*!40000 ALTER TABLE `bitviajes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `citas`
--

DROP TABLE IF EXISTS `citas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `citas` (
  `id_actividad` int NOT NULL,
  `ubicacion` varchar(150) DEFAULT NULL,
  `notas_extra` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id_actividad`),
  CONSTRAINT `fk_cita_actividad` FOREIGN KEY (`id_actividad`) REFERENCES `actividades` (`id_actividad`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `citas`
--

LOCK TABLES `citas` WRITE;
/*!40000 ALTER TABLE `citas` DISABLE KEYS */;
INSERT INTO `citas` VALUES (1,'Oficina central','Cliente llegó puntual'),(3,'Oficina laboral','Traer documentos adicionales');
/*!40000 ALTER TABLE `citas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clientes`
--

DROP TABLE IF EXISTS `clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clientes` (
  `id_cliente` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `token_recuperacion` varchar(100) DEFAULT NULL,
  `expiracion_token` datetime DEFAULT NULL,
  PRIMARY KEY (`id_cliente`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes`
--

LOCK TABLES `clientes` WRITE;
/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
INSERT INTO `clientes` VALUES (1,'Luis Raul Torres Valles','6141234567','luis@gmail.com','$2y$10$DsbnwzybSEgSwbR0DngRMeWVZles0jnl/ZTNWL21LeM.J7oGPxn5C',NULL,NULL),(2,'Alberto Jeremoth Mayen Palma','6147654321','alberto@gmail.com','123',NULL,NULL),(3,'EDWIN DAVID MORALES GUEVARA','6145974407','edwin116david@gmail.com','$2y$10$zuOo8cYhm5f1hqjjbB5h0uSYbdQEzmxAKPU8Fg0XQCSq42TMcthkO',NULL,NULL);
/*!40000 ALTER TABLE `clientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `colaboradores_expediente`
--

DROP TABLE IF EXISTS `colaboradores_expediente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `colaboradores_expediente` (
  `id_expediente` int NOT NULL,
  `id_abogado` int NOT NULL,
  PRIMARY KEY (`id_expediente`,`id_abogado`),
  KEY `fk_colab_abog` (`id_abogado`),
  CONSTRAINT `fk_colab_abog` FOREIGN KEY (`id_abogado`) REFERENCES `abogados` (`id_abogado`) ON DELETE CASCADE,
  CONSTRAINT `fk_colab_exp` FOREIGN KEY (`id_expediente`) REFERENCES `expedientes` (`id_expediente`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `colaboradores_expediente`
--

LOCK TABLES `colaboradores_expediente` WRITE;
/*!40000 ALTER TABLE `colaboradores_expediente` DISABLE KEYS */;
INSERT INTO `colaboradores_expediente` VALUES (2,1),(1,2);
/*!40000 ALTER TABLE `colaboradores_expediente` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `especialidades`
--

DROP TABLE IF EXISTS `especialidades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `especialidades` (
  `id_especialidad` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_especialidad`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `especialidades`
--

LOCK TABLES `especialidades` WRITE;
/*!40000 ALTER TABLE `especialidades` DISABLE KEYS */;
INSERT INTO `especialidades` VALUES (1,'Derecho Penal'),(2,'Derecho Civil'),(3,'Derecho Familiar'),(4,'Derecho Laboral');
/*!40000 ALTER TABLE `especialidades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estados_actividad`
--

DROP TABLE IF EXISTS `estados_actividad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `estados_actividad` (
  `id_estado` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(20) NOT NULL,
  PRIMARY KEY (`id_estado`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estados_actividad`
--

LOCK TABLES `estados_actividad` WRITE;
/*!40000 ALTER TABLE `estados_actividad` DISABLE KEYS */;
INSERT INTO `estados_actividad` VALUES (3,'Cancelada'),(2,'Completada'),(1,'Pendiente'),(4,'Reprogramada');
/*!40000 ALTER TABLE `estados_actividad` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `eventos`
--

DROP TABLE IF EXISTS `eventos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `eventos` (
  `id_evento` int NOT NULL AUTO_INCREMENT,
  `nombre_evento` varchar(255) NOT NULL,
  `descripcion` text NOT NULL,
  `fecha_evento` date NOT NULL,
  `hora_evento` time NOT NULL,
  `fecha_registro` date NOT NULL,
  `precio_boleto` decimal(10,2) NOT NULL,
  `imagen_url` varchar(255) NOT NULL,
  `id_lugar` int NOT NULL,
  `id_tipo_actividad` int NOT NULL,
  `id_organizadora` int NOT NULL,
  PRIMARY KEY (`id_evento`),
  KEY `id_lugar` (`id_lugar`),
  KEY `id_tipo_actividad` (`id_tipo_actividad`),
  KEY `id_organizadora` (`id_organizadora`),
  CONSTRAINT `eventos_ibfk_1` FOREIGN KEY (`id_lugar`) REFERENCES `lugares` (`id_lugar`),
  CONSTRAINT `eventos_ibfk_2` FOREIGN KEY (`id_tipo_actividad`) REFERENCES `tipoactividad` (`id_tipo_actividad`),
  CONSTRAINT `eventos_ibfk_3` FOREIGN KEY (`id_organizadora`) REFERENCES `organizadoras` (`id_organizadora`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `eventos`
--

LOCK TABLES `eventos` WRITE;
/*!40000 ALTER TABLE `eventos` DISABLE KEYS */;
INSERT INTO `eventos` VALUES (1,'Exposición Arte Contemporáneo','Exposición temporal de arte contemporáneo en Chihuahua.','2025-11-15','18:00:00','2025-11-01',150.00,'eimg1.png',1,1,1),(2,'Taller Historia Local','Taller interactivo sobre la historia de Chihuahua.','2025-11-20','10:00:00','2025-11-02',120.00,'eimg2.png',1,1,2),(3,'Noche de Jazz','Concierto de jazz en el museo.','2025-11-22','20:00:00','2025-11-03',180.00,'eimg3.png',1,3,3),(4,'Caminata Guiada','Senderismo con guía experto.','2025-11-18','08:00:00','2025-11-03',100.00,'eimg4.png',2,2,1),(5,'Picnic Familiar','Actividades recreativas y picnic.','2025-11-19','11:00:00','2025-11-04',80.00,'eimg5.png',2,3,2),(6,'Observación de Aves','Tour guiado de observación de aves locales.','2025-11-20','09:00:00','2025-11-05',120.00,'eimg6.png',2,5,3),(7,'Senderismo Cascada','Recorrido guiado hasta la cascada.','2025-11-22','08:30:00','2025-11-06',150.00,'eimg7.png',3,2,1),(8,'Tour Fotográfico','Recorrido fotográfico guiado.','2025-11-23','09:00:00','2025-11-07',200.00,'eimg8.png',3,5,2),(9,'Campamento Nocturno','Acampada con fogata y actividades nocturnas.','2025-11-24','19:00:00','2025-11-08',180.00,'eimg9.png',3,3,3),(10,'Tour Panorámico Creel','Recorrido en teleférico y paseo por el valle.','2025-11-25','14:00:00','2025-11-09',200.00,'eimg10.png',4,5,1),(11,'Taller de Fotografía','Aprende fotografía de paisajes con guía experto.','2025-11-26','10:00:00','2025-11-10',150.00,'eimg11.png',4,5,2),(12,'Exploración Barrancas','Senderismo y vistas panorámicas con guía.','2025-11-28','08:30:00','2025-11-11',250.00,'eimg12.png',5,2,1),(13,'Tour Fotográfico','Recorrido fotográfico guiado.','2025-11-29','09:00:00','2025-11-12',200.00,'eimg13.png',5,5,2),(14,'Aventura Extrema','Actividades de aventura y tirolesa.','2025-11-30','10:00:00','2025-11-13',300.00,'eimg14.png',5,2,3),(15,'Exploración Grutas','Recorrido guiado por las cuevas y formaciones geológicas.','2025-12-01','09:30:00','2025-11-14',130.00,'eimg15.png',6,5,1),(16,'Tour Aventura','Recorrido guiado con actividades de aventura en cuevas.','2025-12-02','10:00:00','2025-11-15',160.00,'eimg16.png',6,2,2),(17,'Ecotour Majalca','Recorrido natural con guía y actividades recreativas.','2025-12-03','08:00:00','2025-11-16',170.00,'eimg17.png',7,5,1),(18,'Picnic y Juegos','Actividades recreativas para familias.','2025-12-04','11:00:00','2025-11-17',90.00,'eimg18.png',7,3,2),(19,'Taller Científico Infantil','Actividades interactivas de ciencia para niños.','2025-12-05','10:00:00','2025-11-18',90.00,'eimg19.png',8,3,1),(20,'Experimento Químico','Demostraciones de química divertida.','2025-12-06','12:00:00','2025-11-19',100.00,'eimg20.png',8,3,2),(21,'Festival Gastronómico','Evento de comida típica y música en vivo.','2025-12-07','12:00:00','2025-11-20',180.00,'eimg21.png',9,4,2),(22,'Concierto al Aire Libre','Evento musical para toda la familia.','2025-12-08','19:00:00','2025-11-21',200.00,'eimg22.png',9,3,3),(23,'Carrera Familiar','Evento deportivo con actividades recreativas.','2025-12-09','09:00:00','2025-11-22',50.00,'eimg23.png',10,3,3),(24,'Yoga al Amanecer','Clase de yoga al aire libre.','2025-12-10','07:00:00','2025-11-23',60.00,'eimg24.png',10,3,1),(25,'Picnic Musical','Música en vivo con picnic para familias.','2025-12-11','12:00:00','2025-11-24',90.00,'eimg25.png',10,3,2),(26,'Senderismo Cerro Coronel','Recorrido guiado de senderismo con vistas panorámicas.','2025-12-12','08:30:00','2025-11-25',120.00,'eimg26.png',11,2,1),(27,'Tour Fotográfico','Captura la mejor vista del Cerro Coronel.','2025-12-13','09:00:00','2025-11-26',150.00,'eimg27.png',11,5,2),(28,'Tour Arqueológico Paquimé','Recorrido guiado por el sitio arqueológico.','2025-12-14','09:00:00','2025-11-27',200.00,'eimg28.png',12,1,2),(29,'Taller de Historia','Taller educativo sobre la cultura Paquimé.','2025-12-15','11:00:00','2025-11-28',180.00,'eimg29.png',12,1,3),(30,'Festival Acuático La Boquilla','Competencia de deportes acuáticos y festival de música.','2026-03-05','10:00:00','2025-11-29',150.00,'eimg30.png',31,3,2),(31,'Ruta del Desierto en 4x4','Tour guiado en vehículo 4x4 por la Sierra de Órganos.','2026-02-10','08:00:00','2025-12-01',350.00,'eimg31.png',34,2,1),(32,'Noche de Leyendas en la Misión','Recorrido nocturno con narración de leyendas locales.','2026-01-25','20:00:00','2025-12-05',180.00,'eimg32.png',32,1,3),(33,'Concurso de Pesca en el Río Conchos','Competencia de pesca deportiva con premios.','2026-04-12','07:00:00','2025-12-10',100.00,'eimg33.png',40,3,2),(34,'Caminata a Cueva de Ávalos','Senderismo con guía geológico.','2026-01-05','09:00:00','2025-12-15',120.00,'eimg34.png',35,5,1),(35,'Taller de Identificación de Pastos','Taller educativo sobre la flora local.','2026-03-20','10:30:00','2025-12-20',80.00,'eimg35.png',36,5,3),(36,'Festival Cultural Rarámuri','Muestra de danza, música y gastronomía Tarahumara.','2026-05-01','12:00:00','2025-12-25',220.00,'eimg36.png',45,1,2),(37,'Tour Fotográfico Cañón de Namúrachi','Excursión para capturar los paisajes del cañón.','2026-02-28','08:30:00','2026-01-01',150.00,'eimg37.png',41,5,1),(38,'Día de Campo Menonita','Experiencia gastronómica y cultural en Cuauhtémoc.','2026-04-05','11:00:00','2026-01-05',250.00,'eimg38.png',44,4,3),(39,'Taller de Arqueología Cueva de la Olla','Charla y visita guiada sobre las culturas prehispánicas.','2026-03-15','09:30:00','2026-01-10',180.00,'eimg39.png',42,1,1),(40,'Recorrido Histórico Paseo Bolivar','Tour a pie con explicación de la arquitectura histórica.','2026-01-30','17:00:00','2026-01-15',50.00,'eimg40.png',48,1,2),(41,'Concierto Filarmónica Paso del Norte','Concierto de música clásica.','2026-02-20','19:30:00','2026-01-20',300.00,'eimg41.png',50,3,3),(42,'Torneo de Pesca en El Tintero','Evento deportivo en la presa.','2026-04-25','06:30:00','2026-01-25',150.00,'eimg42.png',49,2,1),(43,'Feria del Libro en Delicias','Evento cultural con presentación de autores.','2026-03-01','10:00:00','2026-01-30',0.00,'eimg43.png',33,1,2),(44,'Taller de Cerveza Artesanal','Degustación y proceso de elaboración.','2026-01-28','18:00:00','2026-02-05',200.00,'eimg44.png',43,4,3),(45,'Retiro de Yoga en Lago Arareco','Fin de semana de yoga y meditación.','2026-05-15','16:00:00','2026-02-10',400.00,'eimg45.png',37,3,1),(46,'Trekking a Cerocahui','Ruta de senderismo de varios días por la Sierra.','2026-06-01','07:00:00','2026-02-15',500.00,'eimg46.png',38,2,2),(47,'Exposición Histórica de Parral','Muestra sobre la historia de Hidalgo del Parral.','2026-03-10','10:00:00','2026-02-20',50.00,'eimg47.png',46,1,3),(48,'Concierto Órgano en Templo San José','Recital de música clásica en el templo.','2026-04-18','19:00:00','2026-02-25',100.00,'eimg48.png',47,3,1),(49,'Maratón del Chepe','Carrera de larga distancia en la ruta del tren.','2026-05-20','06:00:00','2026-03-01',350.00,'eimg49.png',39,2,2),(50,'Noche Astronómica en Cumbres de Majalca','Observación de estrellas con telescopios y guía.','2026-04-01','21:00:00','2026-03-05',150.00,'eimg50.png',7,5,3);
/*!40000 ALTER TABLE `eventos` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `trgEventosInsert` AFTER INSERT ON `eventos` FOR EACH ROW BEGIN
    INSERT INTO biteventos (
        usuario, fecha_hora, accion, id_afectado,
        old_nombre, new_nombre,
        old_descripcion, new_descripcion,
        old_fecha, new_fecha,
        old_hora, new_hora,
        old_precio, new_precio,
        old_id_lugar, new_id_lugar
    ) VALUES (
        USER(), NOW(), 'INSERT / Creación de evento', NEW.id_evento,
        NULL, NEW.nombre_evento,
        NULL, NEW.descripcion,
        NULL, NEW.fecha_evento,
        NULL, NEW.hora_evento,
        NULL, NEW.precio_boleto,
        NULL, NEW.id_lugar
    );
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `trgEventosUpdate` AFTER UPDATE ON `eventos` FOR EACH ROW BEGIN
    INSERT INTO biteventos (
        usuario, fecha_hora, accion, id_afectado,
        old_nombre, new_nombre,
        old_descripcion, new_descripcion,
        old_fecha, new_fecha,
        old_hora, new_hora,
        old_precio, new_precio,
        old_id_lugar, new_id_lugar
    ) VALUES (
        USER(), NOW(), 'UPDATE / Actualización de evento', NEW.id_evento,
        OLD.nombre_evento, NEW.nombre_evento,
        OLD.descripcion, NEW.descripcion,
        OLD.fecha_evento, NEW.fecha_evento,
        OLD.hora_evento, NEW.hora_evento,
        OLD.precio_boleto, NEW.precio_boleto,
        OLD.id_lugar, NEW.id_lugar
    );
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `trgEventosDelete` AFTER DELETE ON `eventos` FOR EACH ROW BEGIN
    INSERT INTO biteventos (
        usuario, fecha_hora, accion, id_afectado,
        old_nombre, new_nombre,
        old_descripcion, new_descripcion,
        old_fecha, new_fecha,
        old_hora, new_hora,
        old_precio, new_precio,
        old_id_lugar, new_id_lugar
    ) VALUES (
        USER(), NOW(), 'DELETE / Eliminación de evento', OLD.id_evento,
        OLD.nombre_evento, NULL,
        OLD.descripcion, NULL,
        OLD.fecha_evento, NULL,
        OLD.hora_evento, NULL,
        OLD.precio_boleto, NULL,
        OLD.id_lugar, NULL
    );
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `expedientes`
--

DROP TABLE IF EXISTS `expedientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expedientes` (
  `id_expediente` int NOT NULL AUTO_INCREMENT,
  `id_cliente` int DEFAULT NULL,
  `id_especialidad` int DEFAULT NULL,
  `id_abogado` int DEFAULT NULL,
  `titulo` varchar(150) DEFAULT NULL,
  `descripcion` varchar(250) DEFAULT NULL,
  `estado` enum('activo','disponible','cerrado') DEFAULT 'disponible',
  `fecha_inicio` date DEFAULT NULL,
  `fecha_cierre` date DEFAULT NULL,
  PRIMARY KEY (`id_expediente`),
  KEY `id_cliente` (`id_cliente`),
  KEY `id_abogado` (`id_abogado`),
  KEY `fk_expediente_especialidad` (`id_especialidad`),
  CONSTRAINT `expedientes_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `expedientes_ibfk_2` FOREIGN KEY (`id_abogado`) REFERENCES `abogados` (`id_abogado`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_expediente_especialidad` FOREIGN KEY (`id_especialidad`) REFERENCES `especialidades` (`id_especialidad`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expedientes`
--

LOCK TABLES `expedientes` WRITE;
/*!40000 ALTER TABLE `expedientes` DISABLE KEYS */;
INSERT INTO `expedientes` VALUES (1,1,NULL,1,'Caso Robo','Defensa en caso de robo','activo','2026-01-10',NULL),(2,2,NULL,2,'Despido Injustificado','Demanda laboral','activo','2026-02-15',NULL),(3,3,1,NULL,'Solicitud de Nuevo Caso','LA VERDAD ES QUE ME QUIERO DIVORCIAR','disponible','2026-05-01',NULL);
/*!40000 ALTER TABLE `expedientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lugares`
--

DROP TABLE IF EXISTS `lugares`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lugares` (
  `id_lugar` int NOT NULL AUTO_INCREMENT,
  `nombre_lugar` varchar(255) NOT NULL,
  `descripcion` text NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `ciudad` varchar(255) NOT NULL,
  `zona` varchar(255) NOT NULL,
  `imagen_url` varchar(255) NOT NULL,
  `id_admin` int NOT NULL,
  PRIMARY KEY (`id_lugar`),
  KEY `id_admin` (`id_admin`),
  CONSTRAINT `lugares_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `administradores` (`id_admin`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lugares`
--

LOCK TABLES `lugares` WRITE;
/*!40000 ALTER TABLE `lugares` DISABLE KEYS */;
INSERT INTO `lugares` VALUES (1,'Museo Casa Chihuahua','Museo de historia y arte en el centro histórico de Chihuahua.','Av. Venustiano Carranza 1202, Centro','Chihuahua','Centro Historico','limg1.jpg',1),(2,'Parque Barrancas del Cobre','Parque urbano con áreas verdes, juegos y vistas panorámicas de la ciudad.','Av. Tecnológico s/n, Fraccionamiento Campestre','Chihuahua','Sierra Madre Occidental','limg2.jpg',2),(3,'Cascada Basaseachic','Segunda cascada más alta de México, ubicada en la Sierra Tarahumara.','Parque Nacional Basaseachic, Chihuahua','Chihuahua','Sierra Madre Occidental','limg3.jpg',3),(4,'Teleférico de Creel','Paseo panorámico en teleférico con vistas espectaculares del valle.','Av. Juárez 105, Creel','Creel','Sierra Madre Occidental','limg4.jpg',4),(5,'Barrancas del Cobre','Sistema de cañones más grande que el Gran Cañón, ideal para senderismo y aventura.','Carretera Chihuahua - Creel km 120','Chihuahua','Sierra Madre Occidental','limg5.jpg',1),(6,'Grutas Nombre de Dios','Complejo de cuevas y formaciones geológicas con tours guiados.','Nombre de Dios, Chihuahua','Chihuahua','Capital Norte','limg6.jpg',2),(7,'Parque Nacional Cumbres de Majalca','Área natural protegida con montañas y bosques para ecoturismo.','Cumbres de Majalca, Chihuahua','Chihuahua','Sierra Madre Occidental','limg7.jpg',3),(8,'Museo Semilla','Museo interactivo de ciencia y tecnología para toda la familia.','Av. Tecnológico 1234, Chihuahua','Chihuahua','Centro Historico','limg8.jpg',4),(9,'Plaza Mayor Chihuahua','Centro comercial y cultural con tiendas, restaurantes y eventos.','Av. 16 de Septiembre 1910, Centro','Chihuahua','Centro Historico','limg9.jpg',1),(10,'Parque Metropolitano Presa el Rejon','Parque urbano con lago artificial, áreas deportivas y recreativas.','Av. Tecnológico s/n, Chihuahua','Chihuahua','Capital Sur','limg10.jpg',2),(11,'Teleférico Parque Aventura','Paseo panorámico para observar la ciudad y disfrutar de aventura ligera.','Parque Aventura, Chihuahua','Chihuahua','Sierra Madre Occidental','limg11.jpg',3),(12,'Museo de la Revolución','Área dedicada a la historia de la Revolución Mexicana en Chihuahua.','Av. Independencia 101, Centro','Chihuahua','Centro Historico','limg12.jpg',4),(13,'Mirador Cerro Grande','Mirador con vista panorámica de la ciudad ideal para fotografía y caminatas.','Cerro Grande, Chihuahua','Chihuahua','Capital Sur','limg13.jpg',1),(14,'Parque El Palomar','Parque recreativo con áreas verdes y juegos infantiles.','Av. Tecnológico 2300, Chihuahua','Chihuahua','Centro Historico','limg14.jpg',2),(15,'Museo de la Revolución en la Frontera','Museo histórico sobre la Revolución Mexicana y la vida en la frontera.','Av. 16 de Septiembre 2001, Ciudad Juárez','Ciudad Juárez','Juarez Norte','limg15.jpg',3),(16,'Catedral de Chihuahua','Principal catedral del estado, ejemplo de arquitectura barroca y neoclásica.','Av. Venustiano Carranza 1200, Centro','Chihuahua','Centro Historico','limg16.jpg',1),(17,'Zoológico de Aldama','Parque zoológico con especies locales y de otros continentes, ideal para familias.','Carretera a Aldama km 2, Aldama','Chihuahua','Carretera Aldama','limg17.jpg',2),(18,'Campo de Girasoles','Campo abierto con girasoles para turismo fotográfico y recreativo.','Carretera a Aldama s/n, Chihuahua','Chihuahua','Carretera Aldama','limg18.jpg',3),(19,'Plaza Mariachi','Espacio cultural con música en vivo y gastronomía tradicional.','Av. de las Americas 120, Chihuahua','Chihuahua','Centro Historico','limg19.jpg',4),(20,'Cerro Coronel','Mirador natural con vistas panorámicas de la ciudad y senderismo ligero.','Cerro Coronel, Chihuahua','Chihuahua','Capital Noreste','limg20.jpg',1),(21,'Zona Arqueológica de Paquimé','Sitio arqueológico prehispánico declarado Patrimonio de la Humanidad por la UNESCO.','Casas Grandes, Chihuahua','Casas Grandes','Casas Grandes','limg21.jpg',1),(22,'Desierto de Samalayuca','Extensas dunas de arena ideales para tours de aventura y fotografía.','Carretera Panamericana km 75','Samalayuca','Juarez Sur','limg22.jpg',2),(23,'Cascadas de Cusárare','Cascadas naturales con zonas para picnic y senderismo.','Cusárare, Chihuahua','Cusárare','Sierra Madre Occidental','limg23.jpg',3),(24,'Barrancas de Sinforosa','Zona de cañones para senderismo, camping y turismo de aventura.','Carretera Creel-San Juanito','Sierra Tarahumara','Sierra Madre Occidental','limg24.jpg',4),(25,'Estación Divisadero Chepe','Estación del tren Chepe en la Barranca del Cobre con miradores panorámicos.','Divisadero, Chihuahua','Divisadero','Sierra Madre Occidental','limg25.jpg',1),(26,'Valle de los Monjes','Formaciones rocosas únicas y senderos para excursiones y fotografía.','Carretera Creel-Chihuahua km 45','Sierra Tarahumara','Sierra Madre Occidental','limg26.jpg',2),(27,'Cuarenta Casas','Sitio arqueológico prehispánico construido en cuevas y acantilados de la Sierra Tarahumara.','Carretera Creel-Chihuahua km 30','Creel','Sierra Madre Occidental','limg27.jpg',3),(28,'Angel de la Independencia','Experiencia cultural visitando antiguas casas y tradiciones del norte de México.','Matachí, Chihuahua','Matachí','Centro Historico','limg28.jpg',4),(29,'Río Papigochi','Río con zonas para campamento, pesca deportiva y observación de flora y fauna.','Carretera a Papigochi km 10','Papigochi','Sierra Madre Occidental','limg29.jpg',1),(30,'Cañón del Pegüis','Cañón natural con rutas de senderismo, miradores y actividades de aventura.','Carretera Creel-Cañón del Pegüis','Creel','Ojinaga','limg30.jpg',2),(31,'Presa La Boquilla','La presa más grande del estado, ideal para pesca y deportes acuáticos.','Carretera a La Boquilla, Camargo','Camargo','Sur Oriente','limg31.jpg',3),(32,'Misión de San Francisco de Conchos','Antigua misión jesuita con valor histórico y arquitectónico.','Centro Histórico, San Francisco de Conchos','San Francisco de Conchos','Sur Oriente','limg32.jpg',4),(33,'Museo de Paleontología de Delicias','Exhibición de fósiles y restos prehistóricos de la región.','Av. Las Moras 230, Delicias','Delicias','Centro Sur','limg33.jpg',1),(34,'Sierra de Organos','Formaciones rocosas únicas que simulan tubos de órgano. Ideal para escalada.','Parque Nacional Sierra de Órganos','Zacatecas (Cerca de Chihuahua)','Límite Sur','limg34.jpg',2),(35,'Cueva de Ávalos','Sistema de cuevas y formaciones geológicas con tours.','Col. Ávalos, Chihuahua','Chihuahua','Capital Sur','limg35.jpg',3),(36,'Estación Experimental de Pastos','Área para estudio y demostración de la flora local.','Carretera a Cuauhtémoc km 15, Chihuahua','Chihuahua','Capital Oeste','limg36.jpg',4),(37,'Lago Arareco','Hermoso lago cerca de Creel, ideal para paseos en bote y cabañas.','Arareco, Creel','Creel','Sierra Madre Occidental','limg37.jpg',1),(38,'Cerocahui','Pueblo mágico en la Sierra con una misión y vistas a la Barranca del Cobre.','Cerocahui, Urique','Urique','Sierra Madre Occidental','limg38.jpg',2),(39,'El Chepe Regional','Viaje en el tren Chepe en la ruta local.','Estación de Tren, Chihuahua','Chihuahua','Capital Centro','limg39.jpg',3),(40,'Río Conchos','Principal río de Chihuahua, con zonas para campamento y pesca.','Diversas ubicaciones','Camargo','Sur Oriente','limg40.jpg',4),(41,'Cañón de Namúrachi','Cañón con paredes de roca que forman un estrecho paso.','Carretera a Namúrachi','Namúrachi','Capital Oeste','limg41.jpg',1),(42,'Zona Arqueológica Cueva de la Olla','Viviendas en cuevas con forma de olla en la Sierra Tarahumara.','Cueva de la Olla, Casas Grandes','Casas Grandes','Casas Grandes','limg42.jpg',2),(43,'Hacienda Encinillas','Ex-hacienda histórica con producción de sotol y eventos.','Carretera Panamericana km 31, Ahumada','Ahumada','Norte','limg43.jpg',3),(44,'Museo Menonita de Cuauhtémoc','Museo que muestra la cultura e historia de la comunidad Menonita.','Campo 105, Cuauhtémoc','Cuauhtémoc','Oeste','limg44.jpg',4),(45,'El Granero - Misión Tarahumara','Centro de ayuda y difusión de la cultura Rarámuri.','Guachochi, Sierra Tarahumara','Guachochi','Sierra Madre Occidental','limg45.jpg',1),(46,'Museo Fuego Nuevo','Museo de historia y arte local en la zona de Hidalgo del Parral.','Centro Histórico, Parral','Hidalgo del Parral','Sur','limg46.jpg',2),(47,'Templo de San José de Parral','Antiguo templo con arquitectura colonial y gran valor histórico.','Centro Histórico, Parral','Hidalgo del Parral','Sur','limg47.jpg',3),(48,'Paseo Bolivar','Calle histórica y comercial en Chihuahua con edificios de época.','Paseo Bolivar, Centro','Chihuahua','Centro Historico','limg48.jpg',4),(49,'Presa El Tintero','Zona de recreación y pesca en el norte de Chihuahua.','Carretera a El Tintero, Nuevo Casas Grandes','Nuevo Casas Grandes','Noroeste','limg49.jpg',1),(50,'Centro Cultural Paso del Norte','Principal centro de eventos y artes en Ciudad Juárez.','Av. Plutarco Elías Calles 345, Ciudad Juárez','Ciudad Juárez','Juarez Norte','limg50.jpg',2);
/*!40000 ALTER TABLE `lugares` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `trgLugaresInsert` BEFORE INSERT ON `lugares` FOR EACH ROW begin
INSERT INTO bitlugares (
        usuario,
        fecha_hora,
        accion,
        id_afectado,
		old_nombre_lugar,
        new_nombre_lugar,
        old_descripcion,
        new_descripcion,
        old_direccion,
        new_direccion,
        old_ciudad,
        new_ciudad,
        old_zona,
        new_zona,
        old_imagen_url,
        new_imagen_url,
        old_id_admin,
        new_id_admin
    )
values(user(),now(),'INSERT / Creacion de lugar',new.id_lugar,NULL,new.nombre_lugar,NULL,new.descripcion,NULL,new.direccion,NULL,new.ciudad,NULL,new.zona,NULL,new.imagen_url,NULL,new.id_admin);
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `trgLugaresUpdate` BEFORE UPDATE ON `lugares` FOR EACH ROW begin
INSERT INTO bitlugares (
        usuario,
        fecha_hora,
        accion,
        id_afectado,
		old_nombre_lugar,
        new_nombre_lugar,
        old_descripcion,
        new_descripcion,
        old_direccion,
        new_direccion,
        old_ciudad,
        new_ciudad,
        old_zona,
        new_zona,
        old_imagen_url,
        new_imagen_url,
        old_id_admin,
        new_id_admin
    )
values(user(),now(),'Update / Modificacion de lugar',new.id_lugar,old.nombre_lugar,new.nombre_lugar,old.descripcion,new.descripcion,old.direccion,new.direccion,old.ciudad,new.ciudad,old.zona,new.zona,old.imagen_url,new.imagen_url,old.id_admin,new.id_admin);
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `trgLugaresDelete` BEFORE DELETE ON `lugares` FOR EACH ROW begin
INSERT INTO bitlugares (
        usuario,
        fecha_hora,
        accion,
        id_afectado,
		old_nombre_lugar,
        new_nombre_lugar,
        old_descripcion,
        new_descripcion,
        old_direccion,
        new_direccion,
        old_ciudad,
        new_ciudad,
        old_zona,
        new_zona,
        old_imagen_url,
        new_imagen_url,
        old_id_admin,
        new_id_admin
    )
values(user(),now(),'Delete / Eliminacion de lugar',old.id_lugar,old.nombre_lugar,NULL,old.descripcion,NULL,old.direccion,NULL,old.ciudad,NULL,old.zona,NULL,old.imagen_url,NULL,old.id_admin,NULL);
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `organizadoras`
--

DROP TABLE IF EXISTS `organizadoras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `organizadoras` (
  `id_organizadora` int NOT NULL AUTO_INCREMENT,
  `user` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `descripcion_agencia` text NOT NULL,
  `nombre_agencia` varchar(255) NOT NULL,
  `fecha_registro` date NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `imagen_url` varchar(255) NOT NULL,
  `telefono` varchar(10) NOT NULL,
  `correo` varchar(255) NOT NULL,
  PRIMARY KEY (`id_organizadora`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `organizadoras`
--

LOCK TABLES `organizadoras` WRITE;
/*!40000 ALTER TABLE `organizadoras` DISABLE KEYS */;
INSERT INTO `organizadoras` VALUES (1,'govChihEvents','gov2025','Dependencia pública del estado encargada de la organización de eventos oficiales y culturales en Chihuahua.','Gobierno del Estado de Chihuahua – Secretaría de Turismo','2025-04-01','Av. Universidad 3000, Chihuahua, Chih.','org1.png','6141110000','eventos@chihuahua.gob.mx'),(2,'increscendo','inci2025','Empresa de organización y logística de eventos masivos y corporativos, con más de 18 años de experiencia en Chihuahua.','Increscendo Eventos','2024-08-15','Fresno 503, Col. Granjas, Chihuahua, Chih.','org2.png','6141420809','info@increscendoeventos.com'),(3,'xpoOrganizacion','xpo2025','Empresa especializada en montaje, logística y renta de stands para exposiciones, convenciones y eventos en Chihuahua.','XPO Organización','2024-09-20','J. J. Escudero 3305, Col. Santo Niño, Chihuahua, Chih.','org3.png','6144146483','contacto@xpo-organizacion.com');
/*!40000 ALTER TABLE `organizadoras` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `trgOrganizadorasInsert` BEFORE INSERT ON `organizadoras` FOR EACH ROW begin
INSERT INTO bitorganizadoras (
        usuario,
        fecha_hora,
        accion,
        id_afectado,
        old_user,
        new_user,
        old_password,
        new_password,
        old_descripcion_agencia,
        new_descripcion_agencia,
        old_nombre_agencia,
        new_nombre_agencia,
        old_fecha_registro,
        new_fecha_registro,
        old_direccion,
        new_direccion,
        old_imagen_url,
        new_imagen_url,
        old_telefono,
        new_telefono,
        old_correo,
        new_correo
    )
values(
        user(),
        now(),
        'INSERT / Creacion de organizadora',
        new.id_organizadora,
        NULL, new.user,
        NULL, new.password,
        NULL, new.descripcion_agencia,
        NULL, new.nombre_agencia,
        NULL, new.fecha_registro,
        NULL, new.direccion,
        NULL, new.imagen_url,
        NULL, new.telefono,
        NULL, new.correo
);
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `trgOrganizadorasUpdate` BEFORE UPDATE ON `organizadoras` FOR EACH ROW begin
INSERT INTO bitorganizadoras (
        usuario,
        fecha_hora,
        accion,
        id_afectado,
        old_user,
        new_user,
        old_password,
        new_password,
        old_descripcion_agencia,
        new_descripcion_agencia,
        old_nombre_agencia,
        new_nombre_agencia,
        old_fecha_registro,
        new_fecha_registro,
        old_direccion,
        new_direccion,
        old_imagen_url,
        new_imagen_url,
        old_telefono,
        new_telefono,
        old_correo,
        new_correo
    )
values(
        user(),
        now(),
        'UPDATE / Modificacion de organizadora',
        new.id_organizadora,
        old.user, new.user,
        old.password, new.password,
        old.descripcion_agencia, new.descripcion_agencia,
        old.nombre_agencia, new.nombre_agencia,
        old.fecha_registro, new.fecha_registro,
        old.direccion, new.direccion,
        old.imagen_url, new.imagen_url,
        old.telefono, new.telefono,
        old.correo, new.correo
);
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `trgOrganizadorasDelete` BEFORE DELETE ON `organizadoras` FOR EACH ROW begin
INSERT INTO bitorganizadoras (
        usuario,
        fecha_hora,
        accion,
        id_afectado,
        old_user,
        new_user,
        old_password,
        new_password,
        old_descripcion_agencia,
        new_descripcion_agencia,
        old_nombre_agencia,
        new_nombre_agencia,
        old_fecha_registro,
        new_fecha_registro,
        old_direccion,
        new_direccion,
        old_imagen_url,
        new_imagen_url,
        old_telefono,
        new_telefono,
        old_correo,
        new_correo
    )
values(
        user(),
        now(),
        'DELETE / Eliminacion de organizadora',
        old.id_organizadora,
        old.user, NULL,
        old.password, NULL,
        old.descripcion_agencia, NULL,
        old.nombre_agencia, NULL,
        old.fecha_registro, NULL,
        old.direccion, NULL,
        old.imagen_url, NULL,
        old.telefono, NULL,
        old.correo, NULL
);
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `paquetes`
--

DROP TABLE IF EXISTS `paquetes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `paquetes` (
  `id_paquete` int NOT NULL AUTO_INCREMENT,
  `id_agencia` int NOT NULL,
  `id_lugar` int NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `nombre_paquete` varchar(255) NOT NULL,
  `descripcion_paquete` text NOT NULL,
  `imagen_url` varchar(255) NOT NULL,
  PRIMARY KEY (`id_paquete`),
  KEY `id_agencia` (`id_agencia`),
  KEY `id_lugar` (`id_lugar`),
  CONSTRAINT `paquetes_ibfk_1` FOREIGN KEY (`id_agencia`) REFERENCES `agencias` (`id_agencia`),
  CONSTRAINT `paquetes_ibfk_2` FOREIGN KEY (`id_lugar`) REFERENCES `lugares` (`id_lugar`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paquetes`
--

LOCK TABLES `paquetes` WRITE;
/*!40000 ALTER TABLE `paquetes` DISABLE KEYS */;
INSERT INTO `paquetes` VALUES (1,1,1,2000.00,'Paquimé Económico','Recorrido básico con transporte y guía local.','pimg1.png'),(2,1,1,2800.00,'Paquimé Estándar','Incluye transporte, entradas y guía certificado.','pimg2.png'),(3,2,1,2500.00,'Paquimé Cultural','Tour con guía experto y experiencias culturales.','pimg3.png'),(4,1,27,2200.00,'Cuarenta Casas Económico','Recorrido básico con guía local.','pimg4.png'),(5,1,27,3000.00,'Cuarenta Casas Premium','Tour VIP con transporte y picnic.','pimg5.png'),(6,2,22,1600.00,'Samalayuca Aventura','Recorrido con actividades extremas y guía experto.','pimg6.png'),(7,2,22,2300.00,'Samalayuca Cultural','Incluye transporte, guía y explicación de historia natural.','pimg7.png'),(8,1,22,3000.00,'Samalayuca Deluxe','Tour completo con transporte VIP y guía.','pimg8.png'),(9,1,2,1200.00,'Catedral Tour Básico','Recorrido guiado dentro de la catedral.','pimg9.png'),(10,1,3,1800.00,'Zoológico Familiar','Incluye transporte y actividades educativas.','pimg10.png'),(11,3,3,2000.00,'Zoológico Deluxe','Experiencia VIP con guía y picnic.','pimg11.png'),(12,2,4,1200.00,'Plaza Mariachi VIP','Incluye guía y entrada a evento especial.','pimg12.png'),(13,2,5,1500.00,'Cerro Coronel Aventura','Senderismo y vista panorámica.','pimg13.png'),(14,3,5,1700.00,'Cerro Coronel Deluxe','Incluye transporte, guía y picnic.','pimg14.png'),(15,1,5,1300.00,'Cerro Coronel Económico','Recorrido básico con guía local.','pimg15.png'),(16,1,6,3500.00,'Chepe Económico','Viaje por tren panorámico, incluye guía.','pimg16.png'),(17,3,6,4500.00,'Chepe Premium','Experiencia completa con transporte VIP y hospedaje.','pimg17.png'),(18,1,7,4500.00,'Paquimé y Samalayuca Económico','Recorrido combinado con guía local.','pimg18.png'),(19,2,7,5200.00,'Paquimé y Samalayuca Premium','Tour con transporte VIP y guía experto.','pimg19.png'),(20,3,7,5500.00,'Paquimé y Samalayuca Deluxe','Experiencia completa con guía y comida incluida.','pimg20.png'),(21,1,8,5000.00,'Desierto y Cuarenta Casas Deluxe','Tour completo con transporte y guía experto.','pimg21.png'),(22,1,8,5500.00,'Desierto y Cuarenta Casas Premium','Tour con guía y transporte VIP.','pimg22.png'),(23,3,8,5500.00,'Desierto y Cuarenta Casas VIP','Experiencia premium con transporte privado y comidas incluidas.','pimg23.png'),(24,2,8,5200.00,'Desierto y Cuarenta Casas Estándar','Incluye transporte y guía certificado.','pimg24.png'),(25,1,31,1500.00,'Aventura en La Boquilla','Tour de pesca y paseo en lancha.','pimg25.png'),(26,2,31,2200.00,'Boquilla Premium','Incluye hospedaje rústico y comida.','pimg26.png'),(27,3,32,1000.00,'Misión Histórica','Recorrido guiado por la Misión de San Francisco.','pimg27.png'),(28,1,33,800.00,'Paleontología Express','Entrada y guía rápido por el museo.','pimg28.png'),(29,2,34,3500.00,'Órganos Escalada','Tour de aventura con equipo y guía de escalada.','pimg29.png'),(30,3,34,2800.00,'Órganos Natural','Senderismo y observación de flora y fauna.','pimg30.png'),(31,1,35,750.00,'Cueva de Ávalos Básico','Recorrido corto y guía local.','pimg31.png'),(32,2,35,1100.00,'Cueva de Ávalos Familiar','Incluye guía experto y actividades para niños.','pimg32.png'),(33,3,36,600.00,'Pastos Educativo','Tour guiado sobre flora y agronomía.','pimg33.png'),(34,1,37,1800.00,'Arareco en Cabaña','Hospedaje de una noche y paseo en bote.','pimg34.png'),(35,2,38,4000.00,'Cerocahui Misión','Incluye tren Chepe (ruta corta), hospedaje y tour por la misión.','pimg35.png'),(36,3,39,1500.00,'Chepe Regional Día','Boleto de ida y vuelta en ruta corta.','pimg36.png'),(37,1,40,900.00,'Pesca en Río Conchos','Día de pesca con equipo incluido.','pimg37.png'),(38,2,41,1300.00,'Namúrachi Senderismo','Ruta de senderismo con guía de aventura.','pimg38.png'),(39,3,41,1800.00,'Namúrachi Fotográfico','Tour para fotógrafos con guías de localización.','pimg39.png'),(40,1,42,1600.00,'Cueva de la Olla','Tour arqueológico con transporte.','pimg40.png'),(41,2,43,1200.00,'Encinillas Sotol Tour','Degustación de sotol y recorrido por la hacienda.','pimg41.png'),(42,3,44,850.00,'Menonita Cultural','Entrada al museo y visita a una granja.','pimg42.png'),(43,1,45,2000.00,'Tarahumara Experiencia','Visita al centro cultural y comida Rarámuri.','pimg43.png'),(44,2,46,650.00,'Fuego Nuevo Breve','Entrada al museo y recorrido rápido.','pimg44.png'),(45,3,47,400.00,'Templo Parral','Visita guiada por el Templo de San José.','pimg45.png'),(46,1,48,700.00,'Paseo Bolivar Histórico','Recorrido guiado por la arquitectura y sitios históricos.','pimg46.png'),(47,2,49,1500.00,'Tintero Pesca','Día de pesca en la presa con renta de equipo.','pimg47.png'),(48,3,50,950.00,'Paso del Norte Teatral','Boleto para un evento en el Centro Cultural.','pimg48.png'),(49,1,40,1200.00,'Río Conchos Aventura','Kayaking y campamento en el río.','pimg49.png'),(50,2,42,2000.00,'Cueva de la Olla Premium','Tour completo con transporte VIP y comida.','pimg50.png');
/*!40000 ALTER TABLE `paquetes` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `trgPaquetesInsert` AFTER INSERT ON `paquetes` FOR EACH ROW BEGIN
    INSERT INTO bitpaquetes (
        usuario, fecha_hora, accion, id_afectado,
        old_nombre_paquete, new_nombre_paquete,
        old_descripcion_paquete, new_descripcion_paquete,
        old_precio, new_precio,
        old_id_agencia, new_id_agencia,
        old_id_lugar, new_id_lugar,
        old_imagen_url, new_imagen_url
    ) VALUES (
        USER(), NOW(), 'INSERT / Creación de paquete', NEW.id_paquete,
        NULL, NEW.nombre_paquete,
        NULL, NEW.descripcion_paquete,
        NULL, NEW.precio,
        NULL, NEW.id_agencia,
        NULL, NEW.id_lugar,
        NULL, NEW.imagen_url
    );
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `trgPaquetesUpdate` AFTER UPDATE ON `paquetes` FOR EACH ROW BEGIN
    INSERT INTO bitpaquetes (
        usuario, fecha_hora, accion, id_afectado,
        old_nombre_paquete, new_nombre_paquete,
        old_descripcion_paquete, new_descripcion_paquete,
        old_precio, new_precio,
        old_id_agencia, new_id_agencia,
        old_id_lugar, new_id_lugar,
        old_imagen_url, new_imagen_url
    ) VALUES (
        USER(), NOW(), 'UPDATE / Actualización de paquete', NEW.id_paquete,
        OLD.nombre_paquete, NEW.nombre_paquete,
        OLD.descripcion_paquete, NEW.descripcion_paquete,
        OLD.precio, NEW.precio,
        OLD.id_agencia, NEW.id_agencia,
        OLD.id_lugar, NEW.id_lugar,
        OLD.imagen_url, NEW.imagen_url
    );
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `trgPaquetesDelete` AFTER DELETE ON `paquetes` FOR EACH ROW BEGIN
    INSERT INTO bitpaquetes (
        usuario, fecha_hora, accion, id_afectado,
        old_nombre_paquete, new_nombre_paquete,
        old_descripcion_paquete, new_descripcion_paquete,
        old_precio, new_precio,
        old_id_agencia, new_id_agencia,
        old_id_lugar, new_id_lugar,
        old_imagen_url, new_imagen_url
    ) VALUES (
        USER(), NOW(), 'DELETE / Eliminación de paquete', OLD.id_paquete,
        OLD.nombre_paquete, NULL,
        OLD.descripcion_paquete, NULL,
        OLD.precio, NULL,
        OLD.id_agencia, NULL,
        OLD.id_lugar, NULL,
        OLD.imagen_url, NULL
    );
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `reservaciones`
--

DROP TABLE IF EXISTS `reservaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reservaciones` (
  `id_reservacion` int NOT NULL AUTO_INCREMENT,
  `id_evento` int NOT NULL,
  `id_cliente` int NOT NULL,
  `estado` varchar(255) NOT NULL,
  PRIMARY KEY (`id_reservacion`),
  KEY `id_evento` (`id_evento`),
  KEY `id_cliente` (`id_cliente`),
  CONSTRAINT `reservaciones_ibfk_1` FOREIGN KEY (`id_evento`) REFERENCES `eventos` (`id_evento`),
  CONSTRAINT `reservaciones_ibfk_2` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`)
) ENGINE=InnoDB AUTO_INCREMENT=132 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservaciones`
--

LOCK TABLES `reservaciones` WRITE;
/*!40000 ALTER TABLE `reservaciones` DISABLE KEYS */;
INSERT INTO `reservaciones` VALUES (1,1,1,'completado'),(2,3,1,'completado'),(3,5,1,'completado'),(4,12,1,'completado'),(5,15,1,'pendiente'),(6,22,1,'pendiente'),(7,26,1,'pendiente'),(8,28,1,'cancelado'),(9,2,1,'completado'),(10,7,1,'completado'),(11,21,1,'pendiente'),(12,24,1,'cancelado'),(13,2,2,'completado'),(14,4,2,'completado'),(15,10,2,'completado'),(16,16,2,'completado'),(17,19,2,'pendiente'),(18,29,2,'pendiente'),(19,2,2,'cancelado'),(20,1,2,'pendiente'),(21,20,2,'completado'),(22,17,2,'completado'),(23,7,3,'completado'),(24,9,3,'completado'),(25,14,3,'completado'),(26,23,3,'pendiente'),(27,27,3,'pendiente'),(28,13,4,'completado'),(29,25,8,'pendiente'),(30,11,6,'completado'),(31,30,1,'completado'),(32,30,2,'completado'),(33,31,3,'pendiente'),(34,31,4,'pendiente'),(35,32,5,'completado'),(36,32,6,'completado'),(37,33,7,'pendiente'),(38,33,8,'pendiente'),(39,34,9,'completado'),(40,34,10,'completado'),(41,35,1,'pendiente'),(42,35,2,'pendiente'),(43,36,3,'completado'),(44,36,4,'completado'),(45,37,5,'pendiente'),(46,37,6,'pendiente'),(47,38,7,'completado'),(48,38,8,'completado'),(49,39,9,'pendiente'),(50,39,10,'pendiente'),(51,40,1,'completado'),(52,40,2,'completado'),(53,41,3,'pendiente'),(54,41,4,'pendiente'),(55,42,5,'completado'),(56,42,6,'completado'),(57,43,7,'pendiente'),(58,43,8,'pendiente'),(59,44,9,'completado'),(60,44,10,'completado'),(61,45,1,'cancelado'),(62,45,2,'pendiente'),(63,46,3,'completado'),(64,46,4,'completado'),(65,47,5,'pendiente'),(66,47,6,'pendiente'),(67,48,7,'completado'),(68,48,8,'completado'),(69,49,9,'pendiente'),(70,49,10,'pendiente'),(71,50,1,'completado'),(72,50,2,'completado'),(73,1,3,'pendiente'),(74,3,4,'pendiente'),(75,5,5,'completado'),(76,7,6,'completado'),(77,9,7,'pendiente'),(78,11,8,'pendiente'),(79,13,9,'completado'),(80,15,10,'completado'),(81,17,1,'completado'),(82,19,2,'completado'),(83,21,3,'pendiente'),(84,23,4,'pendiente'),(85,25,5,'completado'),(86,27,6,'completado'),(87,29,7,'pendiente'),(88,30,8,'pendiente'),(89,31,9,'completado'),(90,32,10,'completado'),(91,33,1,'cancelado'),(92,34,2,'completado'),(93,35,3,'pendiente'),(94,36,4,'cancelado'),(95,37,5,'completado'),(96,38,6,'pendiente'),(97,39,7,'completado'),(98,40,8,'cancelado'),(99,41,9,'pendiente'),(100,42,10,'completado'),(101,43,1,'pendiente'),(102,44,2,'completado'),(103,45,3,'cancelado'),(104,46,4,'pendiente'),(105,47,5,'completado'),(106,48,6,'pendiente'),(107,49,7,'completado'),(108,50,8,'cancelado'),(109,2,9,'pendiente'),(110,4,10,'completado'),(111,6,1,'completado'),(112,8,2,'pendiente'),(113,10,3,'completado'),(114,12,4,'cancelado'),(115,14,5,'pendiente'),(116,16,6,'completado'),(117,18,7,'pendiente'),(118,20,8,'completado'),(119,22,9,'cancelado'),(120,24,10,'pendiente'),(121,26,1,'completado'),(122,28,2,'pendiente'),(123,4,3,'completado'),(124,6,4,'cancelado'),(125,8,5,'pendiente'),(126,10,6,'completado'),(127,12,7,'pendiente'),(128,14,8,'completado'),(129,16,9,'cancelado'),(130,18,10,'pendiente');
/*!40000 ALTER TABLE `reservaciones` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `trgReservacionesInsert` BEFORE INSERT ON `reservaciones` FOR EACH ROW begin
INSERT INTO bitreservaciones (
        usuario,
        fecha_hora,
        accion,
        id_afectado,
        old_id_evento,
        new_id_evento,
        old_id_cliente,
        new_id_cliente,
        old_estado,
        new_estado
    )
values(
        user(),
        now(),
        'INSERT / Creacion de reservacion',
        new.id_reservacion,
        NULL, new.id_evento,
        NULL, new.id_cliente,
        NULL, new.estado
);
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `trgReservacionesUpdate` BEFORE UPDATE ON `reservaciones` FOR EACH ROW begin
INSERT INTO bitreservaciones (
        usuario,
        fecha_hora,
        accion,
        id_afectado,
        old_id_evento,
        new_id_evento,
        old_id_cliente,
        new_id_cliente,
        old_estado,
        new_estado
    )
values(
        user(),
        now(),
        'UPDATE / Modificacion de reservacion',
        new.id_reservacion,
        old.id_evento, new.id_evento,
        old.id_cliente, new.id_cliente,
        old.estado, new.estado
);
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `trgReservacionesDelete` BEFORE DELETE ON `reservaciones` FOR EACH ROW begin
INSERT INTO bitreservaciones (
        usuario,
        fecha_hora,
        accion,
        id_afectado,
        old_id_evento,
        new_id_evento,
        old_id_cliente,
        new_id_cliente,
        old_estado,
        new_estado
    )
values(
        user(),
        now(),
        'DELETE / Eliminacion de reservacion',
        old.id_reservacion,
        old.id_evento, NULL,
        old.id_cliente, NULL,
        old.estado, NULL
);
end */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `tipoactividad`
--

DROP TABLE IF EXISTS `tipoactividad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipoactividad` (
  `id_tipo_actividad` int NOT NULL AUTO_INCREMENT,
  `nombre_tipo_actividad` varchar(100) NOT NULL,
  `descripcion` text NOT NULL,
  PRIMARY KEY (`id_tipo_actividad`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipoactividad`
--

LOCK TABLES `tipoactividad` WRITE;
/*!40000 ALTER TABLE `tipoactividad` DISABLE KEYS */;
INSERT INTO `tipoactividad` VALUES (1,'Cultural','Visitas a museos, sitios históricos, recorridos culturales por ciudades y pueblos del estado de Chihuahua.'),(2,'Aventura','Actividades al aire libre que ofrecen adrenalina, como rappel, senderismo, ciclismo de montaña o kayak en la Sierra Tarahumara.'),(3,'Recreativo','Actividades aptas para toda la familia, parques, zoológicos, excursiones y entretenimiento para niños y adultos.'),(4,'Gastronomico','Tours y experiencias de comida típica chihuahuense, mercados locales y festivales gastronómicos.'),(5,'Naturaleza','Exploración de entornos naturales, observación de flora y fauna, camping y ecotours en la región de Chihuahua.');
/*!40000 ALTER TABLE `tipoactividad` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipos_actividad`
--

DROP TABLE IF EXISTS `tipos_actividad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipos_actividad` (
  `id_tipo` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  PRIMARY KEY (`id_tipo`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipos_actividad`
--

LOCK TABLES `tipos_actividad` WRITE;
/*!40000 ALTER TABLE `tipos_actividad` DISABLE KEYS */;
INSERT INTO `tipos_actividad` VALUES (2,'Audiencia'),(1,'Cita'),(4,'Llamada'),(3,'Recordatorio');
/*!40000 ALTER TABLE `tipos_actividad` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `viajes`
--

DROP TABLE IF EXISTS `viajes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `viajes` (
  `id_viaje` int NOT NULL AUTO_INCREMENT,
  `id_cliente` int NOT NULL,
  `id_paquete` int NOT NULL,
  `estado` varchar(255) NOT NULL,
  `fecha_viaje` date NOT NULL,
  `hora_viaje` time NOT NULL,
  PRIMARY KEY (`id_viaje`),
  KEY `id_cliente` (`id_cliente`),
  KEY `id_paquete` (`id_paquete`),
  CONSTRAINT `viajes_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`),
  CONSTRAINT `viajes_ibfk_2` FOREIGN KEY (`id_paquete`) REFERENCES `paquetes` (`id_paquete`)
) ENGINE=InnoDB AUTO_INCREMENT=119 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `viajes`
--

LOCK TABLES `viajes` WRITE;
/*!40000 ALTER TABLE `viajes` DISABLE KEYS */;
INSERT INTO `viajes` VALUES (1,1,1,'completado','2025-09-10','07:00:00'),(2,1,17,'completado','2025-10-05','06:30:00'),(3,1,8,'pendiente','2026-01-15','08:00:00'),(4,1,16,'cancelado','2025-05-20','06:00:00'),(5,1,18,'completado','2025-08-01','08:45:00'),(6,1,22,'pendiente','2026-03-20','09:00:00'),(7,1,6,'completado','2025-07-11','09:30:00'),(8,1,3,'cancelado','2025-06-05','10:00:00'),(9,2,2,'completado','2025-08-20','07:30:00'),(10,2,7,'completado','2025-10-25','09:00:00'),(11,2,19,'pendiente','2026-02-01','07:45:00'),(12,2,12,'cancelado','2025-06-15','12:00:00'),(13,2,24,'completado','2025-09-02','11:00:00'),(14,3,3,'completado','2025-07-25','08:15:00'),(15,3,23,'pendiente','2026-03-10','09:30:00'),(16,3,17,'completado','2025-09-29','06:00:00'),(17,5,1,'pendiente','2026-01-20','07:15:00'),(18,10,4,'completado','2025-11-01','11:00:00'),(19,1,25,'completado','2026-03-01','10:00:00'),(20,2,25,'completado','2026-03-01','10:00:00'),(21,3,26,'pendiente','2026-03-05','15:00:00'),(22,4,26,'pendiente','2026-03-05','15:00:00'),(23,5,27,'completado','2026-01-20','09:00:00'),(24,6,27,'completado','2026-01-20','09:00:00'),(25,7,28,'cancelado','2026-02-15','11:00:00'),(26,8,28,'cancelado','2026-02-15','11:00:00'),(27,9,29,'completado','2026-04-10','07:00:00'),(28,10,29,'completado','2026-04-10','07:00:00'),(29,1,30,'cancelado','2026-04-15','08:30:00'),(30,2,30,'pendiente','2026-04-15','08:30:00'),(31,3,31,'completado','2026-01-01','10:30:00'),(32,4,31,'completado','2026-01-01','10:30:00'),(33,5,32,'pendiente','2026-02-05','13:00:00'),(34,6,32,'pendiente','2026-02-05','13:00:00'),(35,7,33,'completado','2026-03-25','10:00:00'),(36,8,33,'completado','2026-03-25','10:00:00'),(37,9,34,'cancelado','2026-05-10','16:00:00'),(38,10,34,'cancelado','2026-05-10','16:00:00'),(39,1,35,'completado','2026-06-05','06:00:00'),(40,2,35,'completado','2026-06-05','06:00:00'),(41,3,36,'pendiente','2026-01-10','14:00:00'),(42,4,36,'pendiente','2026-01-10','14:00:00'),(43,5,37,'completado','2026-04-15','08:00:00'),(44,6,37,'completado','2026-04-15','08:00:00'),(45,7,38,'cancelado','2026-02-20','09:00:00'),(46,8,38,'cancelado','2026-02-20','09:00:00'),(47,9,39,'completado','2026-03-01','08:30:00'),(48,10,39,'completado','2026-03-01','08:30:00'),(49,1,40,'pendiente','2026-03-18','09:30:00'),(50,2,40,'pendiente','2026-03-18','09:30:00'),(51,3,41,'completado','2026-01-26','10:00:00'),(52,4,41,'completado','2026-01-26','10:00:00'),(53,5,42,'cancelado','2026-04-01','11:00:00'),(54,6,42,'cancelado','2026-04-01','11:00:00'),(55,7,43,'completado','2026-05-05','12:00:00'),(56,8,43,'completado','2026-05-05','12:00:00'),(57,9,44,'pendiente','2026-03-08','11:30:00'),(58,10,44,'pendiente','2026-03-08','11:30:00'),(59,1,45,'completado','2026-04-20','13:00:00'),(60,2,45,'completado','2026-04-20','13:00:00'),(61,3,46,'cancelado','2026-01-15','08:00:00'),(62,4,46,'cancelado','2026-01-15','08:00:00'),(63,5,47,'completado','2026-04-28','09:00:00'),(64,6,47,'completado','2026-04-28','09:00:00'),(65,7,48,'pendiente','2026-02-25','19:00:00'),(66,8,48,'pendiente','2026-02-25','19:00:00'),(67,9,49,'completado','2026-04-30','06:00:00'),(68,10,49,'completado','2026-04-30','06:00:00'),(69,1,50,'pendiente','2026-03-22','09:00:00'),(70,2,50,'pendiente','2026-03-22','09:00:00'),(71,1,1,'completado','2026-01-05','07:00:00'),(72,2,2,'completado','2026-01-15','07:30:00'),(73,3,3,'pendiente','2026-02-10','08:15:00'),(74,4,4,'cancelado','2026-03-05','11:00:00'),(75,5,5,'completado','2026-01-25','09:00:00'),(76,6,6,'pendiente','2026-02-28','09:30:00'),(77,7,7,'completado','2026-03-20','09:00:00'),(78,8,8,'cancelado','2026-04-10','10:00:00'),(79,9,9,'pendiente','2026-01-20','12:00:00'),(80,10,10,'completado','2026-02-01','11:00:00'),(81,1,11,'completado','2026-03-10','10:30:00'),(82,2,12,'pendiente','2026-04-05','12:00:00'),(83,3,13,'cancelado','2026-01-30','15:00:00'),(84,4,14,'completado','2026-02-20','17:00:00'),(85,5,15,'pendiente','2026-03-15','08:00:00'),(86,6,16,'completado','2026-04-01','06:00:00'),(87,7,17,'pendiente','2026-05-01','06:30:00'),(88,8,18,'cancelado','2026-03-28','08:45:00'),(89,9,19,'completado','2026-01-18','07:45:00'),(90,10,20,'pendiente','2026-02-12','09:30:00'),(91,1,21,'completado','2026-03-08','09:00:00'),(92,2,22,'pendiente','2026-04-18','09:00:00'),(93,3,23,'cancelado','2026-02-25','09:30:00'),(94,4,24,'completado','2026-03-29','11:00:00'),(95,5,25,'pendiente','2026-01-01','10:00:00'),(96,6,26,'completado','2026-01-11','15:00:00'),(97,7,27,'cancelado','2026-02-08','09:00:00'),(98,8,28,'pendiente','2026-03-05','11:00:00'),(99,9,29,'completado','2026-04-20','07:00:00'),(100,10,30,'pendiente','2026-04-25','08:30:00'),(101,1,31,'completado','2026-01-02','10:30:00'),(102,2,32,'cancelado','2026-02-06','13:00:00'),(103,3,33,'pendiente','2026-03-26','10:00:00'),(104,4,34,'completado','2026-05-11','16:00:00'),(105,5,35,'cancelado','2026-06-06','06:00:00'),(106,6,36,'pendiente','2026-01-11','14:00:00'),(107,7,37,'completado','2026-04-16','08:00:00'),(108,8,38,'cancelado','2026-02-21','09:00:00'),(109,9,39,'pendiente','2026-03-02','08:30:00'),(110,10,40,'completado','2026-03-19','09:30:00'),(111,1,41,'completado','2026-01-27','10:00:00'),(112,2,42,'pendiente','2026-04-02','11:00:00'),(113,3,43,'cancelado','2026-05-06','12:00:00'),(114,4,44,'completado','2026-03-09','11:30:00'),(115,5,45,'pendiente','2026-04-21','13:00:00'),(116,6,46,'cancelado','2026-01-16','08:00:00'),(117,7,47,'completado','2026-04-29','09:00:00'),(118,8,48,'pendiente','2026-02-26','19:00:00');
/*!40000 ALTER TABLE `viajes` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `trgViajesInsert` AFTER INSERT ON `viajes` FOR EACH ROW BEGIN
    INSERT INTO bitviajes (
        usuario, fecha_hora, accion, id_afectado,
        old_id_cliente, new_id_cliente,
        old_id_paquete, new_id_paquete,
        old_estado, new_estado,
        old_fecha_viaje, new_fecha_viaje,
        old_hora_viaje, new_hora_viaje
    ) VALUES (
        USER(), NOW(), 'INSERT / Creación de viaje', NEW.id_viaje,
        NULL, NEW.id_cliente,
        NULL, NEW.id_paquete,
        NULL, NEW.estado,
        NULL, NEW.fecha_viaje,
        NULL, NEW.hora_viaje
    );
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `trgViajesUpdate` AFTER UPDATE ON `viajes` FOR EACH ROW BEGIN
    INSERT INTO bitviajes (
        usuario, fecha_hora, accion, id_afectado,
        old_id_cliente, new_id_cliente,
        old_id_paquete, new_id_paquete,
        old_estado, new_estado,
        old_fecha_viaje, new_fecha_viaje,
        old_hora_viaje, new_hora_viaje
    ) VALUES (
        USER(), NOW(), 'UPDATE / Actualización de viaje', NEW.id_viaje,
        OLD.id_cliente, NEW.id_cliente,
        OLD.id_paquete, NEW.id_paquete,
        OLD.estado, NEW.estado,
        OLD.fecha_viaje, NEW.fecha_viaje,
        OLD.hora_viaje, NEW.hora_viaje
    );
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `trgViajesDelete` AFTER DELETE ON `viajes` FOR EACH ROW BEGIN
    INSERT INTO bitviajes (
        usuario, fecha_hora, accion, id_afectado,
        old_id_cliente, new_id_cliente,
        old_id_paquete, new_id_paquete,
        old_estado, new_estado,
        old_fecha_viaje, new_fecha_viaje,
        old_hora_viaje, new_hora_viaje
    ) VALUES (
        USER(), NOW(), 'DELETE / Eliminación de viaje', OLD.id_viaje,
        OLD.id_cliente, NULL,
        OLD.id_paquete, NULL,
        OLD.estado, NULL,
        OLD.fecha_viaje, NULL,
        OLD.hora_viaje, NULL
    );
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Dumping events for database 'abdarcproyecto1'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-13  0:20:22
