-- Database: seafood_store (utf8mb4). Import vao phpMyAdmin sau khi tao database seafood_store.
-- MariaDB dump 10.19  Distrib 10.4.18-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: seafood_store
-- ------------------------------------------------------
-- Server version	10.4.18-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `banners`
--

DROP TABLE IF EXISTS `banners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `banners` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_url` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `banners`
--

LOCK TABLES `banners` WRITE;
/*!40000 ALTER TABLE `banners` DISABLE KEYS */;
INSERT INTO `banners` VALUES (1,'Đại Tiệc Cua Hoàng Đế','uploads/crab_premium.png','/danh-muc/cua-hoang-de','Giảm đến 15% cua hoàng đế Alaska nhập khẩu cao cấp.',1,1,'2026-09-26 03:54:37','2026-09-26 03:54:37');
INSERT INTO `banners` VALUES (2,'Tôm Hùm Tươi Sống','uploads/shrimp_premium.png','/danh-muc/tom-hum','Tôm hùm bông sống size lớn - chuẩn nhà hàng 5 sao.',2,1,'2026-09-28 03:54:37','2026-09-28 03:54:37');
INSERT INTO `banners` VALUES (3,'Cá Hồi Na Uy Nhập Khẩu','uploads/fish_premium.png','/danh-muc/ca-hoi','Phi lê cá hồi chuẩn sashimi, tươi mỗi ngày.',3,1,'2026-10-01 03:54:37','2026-10-01 03:54:37');
INSERT INTO `banners` VALUES (4,'Hải Sản Tươi Sống Mỗi Ngày','uploads/shellfish_premium.png','/san-pham','Đánh bắt và giao nhanh, cam kết tươi ngon 100%.',4,1,'2026-10-06 03:54:37','2026-10-06 03:54:37');
INSERT INTO `banners` VALUES (5,'Combo Lẩu Hải Sản Cuối Tuần','uploads/squid_premium.png','/khuyen-mai','Set lẩu hải sản đầy đủ - ưu đãi cho đơn từ 500K.',5,0,'2026-10-11 03:54:37','2026-10-11 03:54:37');
/*!40000 ALTER TABLE `banners` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cart_items`
--

DROP TABLE IF EXISTS `cart_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cart_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cart_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(18,2) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cart_items_cart_id_product_id_unique` (`cart_id`,`product_id`),
  KEY `cart_items_product_id_foreign` (`product_id`),
  CONSTRAINT `cart_items_cart_id_foreign` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cart_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart_items`
--

LOCK TABLES `cart_items` WRITE;
/*!40000 ALTER TABLE `cart_items` DISABLE KEYS */;
INSERT INTO `cart_items` VALUES (2,1,11,1,650000.00);
/*!40000 ALTER TABLE `cart_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carts`
--

DROP TABLE IF EXISTS `carts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `carts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `carts_user_id_unique` (`user_id`),
  CONSTRAINT `carts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carts`
--

LOCK TABLES `carts` WRITE;
/*!40000 ALTER TABLE `carts` DISABLE KEYS */;
INSERT INTO `carts` VALUES (1,1,'2026-10-16 03:54:36','2026-10-16 10:24:14');
INSERT INTO `carts` VALUES (2,2,'2026-10-16 03:54:37','2026-10-16 10:28:28');
INSERT INTO `carts` VALUES (3,3,'2026-10-16 03:54:37','2026-10-16 03:54:37');
INSERT INTO `carts` VALUES (4,4,'2026-10-16 03:54:37','2026-10-16 03:54:37');
INSERT INTO `carts` VALUES (5,5,'2026-10-16 03:54:37','2026-10-16 03:54:37');
INSERT INTO `carts` VALUES (6,6,'2026-10-16 03:54:37','2026-10-16 03:54:37');
INSERT INTO `carts` VALUES (7,7,'2026-10-16 03:54:37','2026-10-16 03:54:37');
INSERT INTO `carts` VALUES (8,8,'2026-10-16 03:54:37','2026-10-16 03:54:37');
INSERT INTO `carts` VALUES (9,9,'2026-10-16 03:54:37','2026-10-16 03:54:37');
INSERT INTO `carts` VALUES (10,10,'2026-10-16 03:54:37','2026-10-16 03:54:37');
INSERT INTO `carts` VALUES (11,11,'2026-10-16 03:54:37','2026-10-16 03:54:37');
INSERT INTO `carts` VALUES (12,12,'2026-10-16 03:54:37','2026-10-16 03:54:37');
INSERT INTO `carts` VALUES (13,13,'2026-10-16 03:54:37','2026-10-16 03:54:37');
INSERT INTO `carts` VALUES (14,14,'2026-10-16 03:54:37','2026-10-16 03:54:37');
INSERT INTO `carts` VALUES (15,15,'2026-10-16 03:54:37','2026-10-16 03:54:37');
INSERT INTO `carts` VALUES (16,16,'2026-10-16 03:54:37','2026-10-16 03:54:37');
INSERT INTO `carts` VALUES (17,17,'2026-10-16 10:20:22','2026-10-16 10:36:31');
/*!40000 ALTER TABLE `carts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_url` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`),
  KEY `categories_parent_id_index` (`parent_id`),
  CONSTRAINT `categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Tôm','tom','Các loại tôm tươi sống và đông lạnh cao cấp','images/local/cat_1.jpg',NULL,1,1);
INSERT INTO `categories` VALUES (2,'Tôm sú','tom-tom-su','Tôm sú tươi sống size lớn, thịt chắc ngọt','images/local/cat_2.jpg',1,1,1);
INSERT INTO `categories` VALUES (3,'Tôm thẻ','tom-tom-the','Tôm thẻ chân trắng tươi ngon, giá tốt','images/local/cat_3.jpg',1,2,1);
INSERT INTO `categories` VALUES (4,'Tôm hùm','tom-tom-hum','Tôm hùm bông, tôm hùm baby cao cấp','images/local/cat_4.jpg',1,3,1);
INSERT INTO `categories` VALUES (5,'Tôm càng xanh','tom-tom-cang-xanh','Tôm càng xanh sông nước miền Tây','images/local/cat_5.jpg',1,4,1);
INSERT INTO `categories` VALUES (6,'Tôm tích','tom-tom-tich','Tôm tích (bề bề) thịt dai ngọt','images/local/cat_6.jpg',1,5,1);
INSERT INTO `categories` VALUES (7,'Cua - Ghẹ','cua-ghe','Cua biển, cua hoàng đế, ghẹ tươi sống','images/local/cat_7.jpg',NULL,2,1);
INSERT INTO `categories` VALUES (8,'Cua hoàng đế','cua-ghe-cua-hoang-de','King Crab nhập khẩu Alaska, Na Uy','images/local/cat_8.jpg',7,1,1);
INSERT INTO `categories` VALUES (9,'Cua gạch','cua-ghe-cua-g-ch','Cua gạch son Cà Mau béo ngậy','images/local/cat_9.jpg',7,2,1);
INSERT INTO `categories` VALUES (10,'Cua thịt','cua-ghe-cua-thit','Cua thịt chắc, nhiều thịt','images/local/cat_10.jpg',7,3,1);
INSERT INTO `categories` VALUES (11,'Ghẹ xanh','cua-ghe-ghe-xanh','Ghẹ xanh tươi sống, thịt ngọt','images/local/cat_11.jpg',7,4,1);
INSERT INTO `categories` VALUES (12,'Cá','ca','Cá biển và cá nhập khẩu tươi ngon mỗi ngày','images/local/cat_12.jpg',NULL,3,1);
INSERT INTO `categories` VALUES (13,'Cá hồi','ca-ca-h-i','Cá hồi Na Uy nhập khẩu chuẩn sashimi','images/local/cat_13.jpg',12,1,1);
INSERT INTO `categories` VALUES (14,'Cá thu','ca-ca-thu','Cá thu tươi đánh bắt trong ngày','images/local/cat_14.jpg',12,2,1);
INSERT INTO `categories` VALUES (15,'Cá ngừ đại dương','ca-ca-ngu-d-i-duong','Cá ngừ đại dương phi lê tươi','images/local/cat_15.jpg',12,3,1);
INSERT INTO `categories` VALUES (16,'Cá bớp','ca-ca-bop','Cá bớp (cá giò) thịt dai chắc','images/local/cat_16.jpg',12,4,1);
INSERT INTO `categories` VALUES (17,'Cá mú','ca-ca-mu','Cá mú nghệ, cá mú đỏ cao cấp','images/local/cat_17.jpg',12,5,1);
INSERT INTO `categories` VALUES (18,'Cá chẽm','ca-ca-chem','Cá chẽm (cá vược) thịt trắng ngọt','images/local/cat_18.jpg',12,6,1);
INSERT INTO `categories` VALUES (19,'Mực - Bạch tuộc','muc-b-ch-tuoc','Mực ống, mực lá, bạch tuộc tươi','images/local/cat_19.jpg',NULL,4,1);
INSERT INTO `categories` VALUES (20,'Mực ống','muc-b-ch-tuoc-muc-ng','Mực ống tươi size đều, làm sạch','images/local/cat_20.jpg',19,1,1);
INSERT INTO `categories` VALUES (21,'Mực lá','muc-b-ch-tuoc-muc-la','Mực lá dày mình, ngọt giòn','images/local/cat_21.jpg',19,2,1);
INSERT INTO `categories` VALUES (22,'Mực một nắng','muc-b-ch-tuoc-muc-mot-nang','Mực một nắng phơi tự nhiên','images/local/cat_22.jpg',19,3,1);
INSERT INTO `categories` VALUES (23,'Bạch tuộc','muc-b-ch-tuoc-b-ch-tuoc','Bạch tuộc baby và bạch tuộc lớn','images/local/cat_23.jpg',19,4,1);
INSERT INTO `categories` VALUES (24,'Ốc - Sò - Nghêu','c-so-ngheu','Ốc hương, sò điệp, hàu, nghêu tươi sống','images/local/cat_24.jpg',NULL,5,1);
INSERT INTO `categories` VALUES (25,'Ốc hương','c-so-ngheu-c-huong','Ốc hương biển tự nhiên béo giòn','images/local/cat_25.jpg',24,1,1);
INSERT INTO `categories` VALUES (26,'Sò huyết','c-so-ngheu-so-huyet','Sò huyết tươi sống size lớn','images/local/cat_26.jpg',24,2,1);
INSERT INTO `categories` VALUES (27,'Sò điệp','c-so-ngheu-so-diep','Sò điệp Nhật, cồi sò điệp tươi','images/local/cat_27.jpg',24,3,1);
INSERT INTO `categories` VALUES (28,'Hàu sữa','c-so-ngheu-hau-sua','Hàu sữa Pháp, hàu Quảng Ninh','images/local/cat_28.jpg',24,4,1);
INSERT INTO `categories` VALUES (29,'Nghêu','c-so-ngheu-ngheu','Nghêu trắng Bến Tre sạch cát','images/local/cat_29.jpg',24,5,1);
INSERT INTO `categories` VALUES (30,'Hải sản khô & chế biến','hai-san-kho-che-bien','Hải sản khô, chả cá, đồ chế biến sẵn','images/local/cat_30.jpg',NULL,6,1);
INSERT INTO `categories` VALUES (31,'Khô mực','hai-san-kho-che-bien-kho-muc','Khô mực một nắng, khô mực tẩm','images/local/cat_31.jpg',30,1,1);
INSERT INTO `categories` VALUES (32,'Cá khô','hai-san-kho-che-bien-ca-kho','Cá khô các loại đậm đà','images/local/cat_32.jpg',30,2,1);
INSERT INTO `categories` VALUES (33,'Chả cá - Chả mực','hai-san-kho-che-bien-cha-ca-cha-muc','Chả cá thác lác, chả mực Hạ Long','images/local/cat_33.jpg',30,3,1);
INSERT INTO `categories` VALUES (34,'Hải sản viên','hai-san-kho-che-bien-hai-san-vien','Cá viên, tôm viên, bò biển viên','images/local/cat_34.jpg',30,4,1);
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contacts`
--

DROP TABLE IF EXISTS `contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contacts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contacts`
--

LOCK TABLES `contacts` WRITE;
/*!40000 ALTER TABLE `contacts` DISABLE KEYS */;
INSERT INTO `contacts` VALUES (1,'Nguyễn Văn An','annv@gmail.com','0901234567','Tôi muốn mua sỉ tôm sú cho nhà hàng tiệc cưới, xin báo giá số lượng lớn.',0,'2026-10-15 03:54:37','2026-10-15 03:54:37');
INSERT INTO `contacts` VALUES (2,'Trần Thị Bích','bichtt@gmail.com','0918888777','Cua hoàng đế giao đến Quận 7 còn sống không shop? Có chế biến sẵn không ạ?',0,'2026-10-14 03:54:37','2026-10-14 03:54:37');
INSERT INTO `contacts` VALUES (3,'Lê Minh Cường','cuonglm@gmail.com','0905556677','Shop có giao hàng tỉnh Bình Dương không? Phí ship bao nhiêu vậy?',0,'2026-10-14 03:54:37','2026-10-14 03:54:37');
INSERT INTO `contacts` VALUES (4,'Phạm Thu Dung','dungpt@gmail.com','0933221144','Mình đặt cá hồi hôm qua mà chưa thấy gọi xác nhận, nhờ shop kiểm tra giúp.',1,'2026-10-12 03:54:37','2026-10-12 03:54:37');
INSERT INTO `contacts` VALUES (5,'Hoàng Đức Em','emhd@gmail.com','0977889900','Cho hỏi mực một nắng bảo quản được bao lâu trong ngăn đông ạ?',1,'2026-10-11 03:54:37','2026-10-11 03:54:37');
INSERT INTO `contacts` VALUES (6,'Vũ Thị Hoa','hoavt@gmail.com','0944112233','Nhà hàng mình cần đặt hàu sữa định kỳ mỗi tuần, mong được hợp tác lâu dài.',1,'2026-10-09 03:54:37','2026-10-09 03:54:37');
INSERT INTO `contacts` VALUES (7,'Đặng Quốc Huy','huydq@gmail.com','0966554433','Shop có chương trình khách hàng thân thiết không? Mình mua khá thường xuyên.',1,'2026-10-07 03:54:37','2026-10-07 03:54:37');
INSERT INTO `contacts` VALUES (8,'Bùi Thị Lan','lanbt@gmail.com','0922334455','Ghẹ xanh hôm trước mình mua rất tươi và ngon, cảm ơn shop nhiều nhé!',1,'2026-10-04 03:54:37','2026-10-04 03:54:37');
/*!40000 ALTER TABLE `contacts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_100000_create_password_resets_table',1);
INSERT INTO `migrations` VALUES (2,'2019_08_19_000000_create_failed_jobs_table',1);
INSERT INTO `migrations` VALUES (3,'2019_12_14_000001_create_personal_access_tokens_table',1);
INSERT INTO `migrations` VALUES (4,'2024_01_01_000001_create_users_table',1);
INSERT INTO `migrations` VALUES (5,'2024_01_01_000002_create_categories_table',1);
INSERT INTO `migrations` VALUES (6,'2024_01_01_000003_create_products_table',1);
INSERT INTO `migrations` VALUES (7,'2024_01_01_000004_create_carts_table',1);
INSERT INTO `migrations` VALUES (8,'2024_01_01_000005_create_orders_table',1);
INSERT INTO `migrations` VALUES (9,'2024_01_01_000006_create_banners_contacts_vouchers_reviews_tables',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `product_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_image` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(18,2) NOT NULL,
  `unit` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'kg',
  PRIMARY KEY (`id`),
  KEY `order_items_order_id_foreign` (`order_id`),
  KEY `order_items_product_id_foreign` (`product_id`),
  CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (1,1,15,'Phi lê cá hồi Na Uy','https://image.pollinations.ai/prompt/premium%20food%20photography%2C%20fresh%20raw%20norwegian%20salmon%20fillet%20cut%2C%20dark%20slate%20background%2C%20highly%20detailed?width=500&height=500&nologo=true&seed=114',3,459000.00,'kg');
INSERT INTO `order_items` VALUES (2,2,34,'Khô mực một nắng loại 1','https://image.pollinations.ai/prompt/premium%20food%20photography%2C%20dried%20squid%20ready%20to%20eat%2C%20dark%20slate%20background%2C%20highly%20detailed?width=500&height=500&nologo=true&seed=133',3,799000.00,'kg');
INSERT INTO `order_items` VALUES (3,2,26,'Bạch tuộc baby','https://image.pollinations.ai/prompt/premium%20food%20photography%2C%20raw%20fresh%20baby%20octopus%20on%20ice%2C%20dark%20slate%20background%2C%20highly%20detailed?width=500&height=500&nologo=true&seed=125',1,240000.00,'kg');
INSERT INTO `order_items` VALUES (4,3,5,'Tôm hùm bông sống','uploads/3512dad685c444ac8d9759b17d854722.jpg',2,1490000.00,'kg');
INSERT INTO `order_items` VALUES (5,3,21,'Cá mú nghệ tươi sống','https://image.pollinations.ai/prompt/premium%20food%20photography%2C%20live%20giant%20grouper%20fish%20on%20ice%2C%20dark%20slate%20background%2C%20highly%20detailed?width=500&height=500&nologo=true&seed=120',1,390000.00,'kg');
INSERT INTO `order_items` VALUES (6,4,9,'Cua hoàng đế Alaska sống','https://image.pollinations.ai/prompt/premium%20food%20photography%2C%20whole%20live%20alaskan%20king%20crab%20on%20ice%2C%20dark%20slate%20background%2C%20highly%20detailed?width=500&height=500&nologo=true&seed=108',1,1950000.00,'kg');
INSERT INTO `order_items` VALUES (7,5,35,'Khô mực xé tẩm gia vị','https://image.pollinations.ai/prompt/premium%20food%20photography%2C%20shredded%20seasoned%20dried%20squid%20snack%2C%20dark%20slate%20background%2C%20highly%20detailed?width=500&height=500&nologo=true&seed=134',3,320000.00,'hộp');
INSERT INTO `order_items` VALUES (8,5,4,'Tôm thẻ size nhỏ (70 con/kg)','https://image.pollinations.ai/prompt/premium%20food%20photography%2C%20small%20raw%20whiteleg%20shrimp%20on%20ice%2C%20dark%20slate%20background%2C%20highly%20detailed?width=500&height=500&nologo=true&seed=103',3,135000.00,'kg');
INSERT INTO `order_items` VALUES (9,5,10,'Chân cua hoàng đế đông lạnh','https://image.pollinations.ai/prompt/premium%20food%20photography%2C%20frozen%20king%20crab%20legs%20clusters%2C%20dark%20slate%20background%2C%20highly%20detailed?width=500&height=500&nologo=true&seed=109',1,1590000.00,'kg');
INSERT INTO `order_items` VALUES (10,6,37,'Khô cá chỉ vàng','https://image.pollinations.ai/prompt/premium%20food%20photography%2C%20small%20dried%20yellowstripe%20scad%20fish%2C%20dark%20slate%20background%2C%20highly%20detailed?width=500&height=500&nologo=true&seed=136',2,260000.00,'kg');
INSERT INTO `order_items` VALUES (11,6,41,'Tôm viên nhân phô mai','https://image.pollinations.ai/prompt/premium%20food%20photography%2C%20shrimp%20balls%20with%20melting%20cheese%20filling%2C%20dark%20slate%20background%2C%20highly%20detailed?width=500&height=500&nologo=true&seed=140',1,110000.00,'gói');
INSERT INTO `order_items` VALUES (12,6,27,'Bạch tuộc lớn','https://image.pollinations.ai/prompt/premium%20food%20photography%2C%20large%20raw%20octopus%20tentacles%2C%20dark%20slate%20background%2C%20highly%20detailed?width=500&height=500&nologo=true&seed=126',1,269000.00,'kg');
INSERT INTO `order_items` VALUES (13,7,9,'Cua hoàng đế Alaska sống','https://image.pollinations.ai/prompt/premium%20food%20photography%2C%20whole%20live%20alaskan%20king%20crab%20on%20ice%2C%20dark%20slate%20background%2C%20highly%20detailed?width=500&height=500&nologo=true&seed=108',2,1950000.00,'kg');
INSERT INTO `order_items` VALUES (14,7,11,'Cua gạch son Cà Mau','https://image.pollinations.ai/prompt/premium%20food%20photography%2C%20raw%20mud%20crab%20with%20orange%20roe%2C%20dark%20slate%20background%2C%20highly%20detailed?width=500&height=500&nologo=true&seed=110',2,650000.00,'kg');
INSERT INTO `order_items` VALUES (15,8,30,'Cồi sò điệp Nhật','https://image.pollinations.ai/prompt/premium%20food%20photography%2C%20large%20raw%20japanese%20scallop%20meat%20adductor%20muscle%2C%20dark%20slate%20background%2C%20highly%20detailed?width=500&height=500&nologo=true&seed=129',3,489000.00,'kg');
INSERT INTO `order_items` VALUES (16,8,24,'Mực lá tươi size lớn','https://image.pollinations.ai/prompt/premium%20food%20photography%2C%20large%20fresh%20bigfin%20reef%20squid%2C%20dark%20slate%20background%2C%20highly%20detailed?width=500&height=500&nologo=true&seed=123',3,350000.00,'kg');
INSERT INTO `order_items` VALUES (17,8,31,'Hàu sữa Pháp','https://image.pollinations.ai/prompt/premium%20food%20photography%2C%20fresh%20raw%20pacific%20oysters%20on%20the%20half%20shell%2C%20dark%20slate%20background%2C%20highly%20detailed?width=500&height=500&nologo=true&seed=130',3,95000.00,'chục');
INSERT INTO `order_items` VALUES (18,9,25,'Mực một nắng Phan Thiết','https://image.pollinations.ai/prompt/premium%20food%20photography%2C%20semi%20dried%20squid%20ready%20to%20grill%2C%20dark%20slate%20background%2C%20highly%20detailed?width=500&height=500&nologo=true&seed=124',3,399000.00,'kg');
INSERT INTO `order_items` VALUES (19,10,39,'Chả cá thác lác','https://image.pollinations.ai/prompt/premium%20food%20photography%2C%20raw%20scraped%20bronze%20featherback%20fish%20paste%2C%20dark%20slate%20background%2C%20highly%20detailed?width=500&height=500&nologo=true&seed=138',3,220000.00,'kg');
INSERT INTO `order_items` VALUES (20,10,12,'Cua thịt Cà Mau (4 con/kg)','https://image.pollinations.ai/prompt/premium%20food%20photography%2C%20large%20meaty%20raw%20mud%20crab%2C%20dark%20slate%20background%2C%20highly%20detailed?width=500&height=500&nologo=true&seed=111',1,520000.00,'kg');
INSERT INTO `order_items` VALUES (21,11,8,'Tôm tích (bề bề) sống','https://image.pollinations.ai/prompt/premium%20food%20photography%2C%20raw%20mantis%20shrimp%20on%20ice%2C%20dark%20slate%20background%2C%20highly%20detailed?width=500&height=500&nologo=true&seed=107',2,480000.00,'kg');
INSERT INTO `order_items` VALUES (22,11,19,'Phi lê cá ngừ đại dương','https://image.pollinations.ai/prompt/premium%20food%20photography%2C%20raw%20yellowfin%20tuna%20saku%20block%20fillet%20red%20meat%2C%20dark%20slate%20background%2C%20highly%20detailed?width=500&height=500&nologo=true&seed=118',3,299000.00,'kg');
INSERT INTO `order_items` VALUES (23,12,30,'Cồi sò điệp Nhật','https://image.pollinations.ai/prompt/premium%20food%20photography%2C%20large%20raw%20japanese%20scallop%20meat%20adductor%20muscle%2C%20dark%20slate%20background%2C%20highly%20detailed?width=500&height=500&nologo=true&seed=129',2,489000.00,'kg');
INSERT INTO `order_items` VALUES (24,12,7,'Tôm càng xanh sông (8 con/kg)','https://image.pollinations.ai/prompt/premium%20food%20photography%2C%20raw%20giant%20freshwater%20prawn%20with%20blue%20claws%20on%20ice%2C%20dark%20slate%20background%2C%20highly%20detailed?width=500&height=500&nologo=true&seed=106',1,399000.00,'kg');
INSERT INTO `order_items` VALUES (25,12,28,'Ốc hương biển (80 con/kg)','https://image.pollinations.ai/prompt/premium%20food%20photography%2C%20raw%20babylonia%20areolata%20sea%20snails%20on%20ice%2C%20dark%20slate%20background%2C%20highly%20detailed?width=500&height=500&nologo=true&seed=127',1,359000.00,'kg');
INSERT INTO `order_items` VALUES (26,13,11,'Cua gạch son Cà Mau','https://image.pollinations.ai/prompt/premium%20food%20photography%2C%20raw%20mud%20crab%20with%20orange%20roe%2C%20dark%20slate%20background%2C%20highly%20detailed?width=500&height=500&nologo=true&seed=110',2,650000.00,'kg');
INSERT INTO `order_items` VALUES (27,14,10,'Chân cua hoàng đế đông lạnh','https://image.pollinations.ai/prompt/premium%20food%20photography%2C%20frozen%20king%20crab%20legs%20clusters%2C%20dark%20slate%20background%2C%20highly%20detailed?width=500&height=500&nologo=true&seed=109',2,1590000.00,'kg');
INSERT INTO `order_items` VALUES (28,15,38,'Chả mực Hạ Long','https://image.pollinations.ai/prompt/premium%20food%20photography%2C%20fried%20golden%20squid%20cake%20patties%2C%20dark%20slate%20background%2C%20highly%20detailed?width=500&height=500&nologo=true&seed=137',1,420000.00,'kg');
INSERT INTO `order_items` VALUES (29,16,11,'Cua gạch son Cà Mau','https://image.pollinations.ai/prompt/premium%20food%20photography%2C%20raw%20mud%20crab%20with%20orange%20roe%2C%20dark%20slate%20background%2C%20highly%20detailed?width=500&height=500&nologo=true&seed=110',1,650000.00,'kg');
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `order_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_method` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cod',
  `payment_status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unpaid',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `sub_total` decimal(18,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `voucher_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_amount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `vnpay_txn_ref` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vnpay_transaction_no` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vnpay_bank_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_order_code_unique` (`order_code`),
  KEY `orders_user_id_foreign` (`user_id`),
  CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,3,'DH260402001','Phạm Quỳnh Chi','0944555666','456 Nguyễn Trãi, Quận 5, TP. HCM','Giao giờ hành chính','cod','paid','delivered',1377000.00,0.00,NULL,1377000.00,NULL,NULL,NULL,NULL,'2026-07-30 14:54:37','2026-10-16 08:18:32');
INSERT INTO `orders` VALUES (2,15,'DH260327002','Cao Thị Ngọc','0938012233','43 Nguyễn Thị Minh Khai, Quận 1, TP. HCM',NULL,'bank','unpaid','pending',2637000.00,0.00,NULL,2637000.00,NULL,NULL,NULL,NULL,'2026-07-24 05:54:37','2026-07-24 05:54:37');
INSERT INTO `orders` VALUES (3,15,'DH260521003','Cao Thị Ngọc','0938012233','43 Nguyễn Thị Minh Khai, Quận 1, TP. HCM',NULL,'bank','paid','delivered',3370000.00,0.00,NULL,3370000.00,NULL,NULL,NULL,NULL,'2026-09-17 09:54:37','2026-09-17 09:54:37');
INSERT INTO `orders` VALUES (4,14,'DH260124004','Trương Đình Bảo','0917011122','21 Cộng Hòa, Tân Bình, TP. HCM',NULL,'bank','unpaid','confirmed',1950000.00,0.00,NULL,1950000.00,NULL,NULL,NULL,NULL,'2026-05-23 12:54:37','2026-05-23 12:54:37');
INSERT INTO `orders` VALUES (5,2,'DH260302005','Lê Hoàng Nam','0933111222','789 Lê Lợi, Gò Vấp, TP. HCM','Giao giờ hành chính','cod','paid','delivered',2955000.00,0.00,NULL,2955000.00,NULL,NULL,NULL,NULL,'2026-06-29 16:54:37','2026-06-29 16:54:37');
INSERT INTO `orders` VALUES (6,9,'DH260306006','Đặng Thu Trang','0911006677','11 Lý Thường Kiệt, Quận 10, TP. HCM',NULL,'cod','paid','delivered',899000.00,0.00,NULL,899000.00,NULL,NULL,NULL,NULL,'2026-07-03 21:54:37','2026-07-03 21:54:37');
INSERT INTO `orders` VALUES (7,5,'DH260204007','Nguyễn Thị Hằng','0917002233','34 Cách Mạng Tháng 8, Quận 3, TP. HCM',NULL,'cod','unpaid','shipping',5200000.00,0.00,NULL,5200000.00,NULL,NULL,NULL,NULL,'2026-06-03 16:54:37','2026-06-03 16:54:37');
INSERT INTO `orders` VALUES (8,12,'DH260327008','Phan Văn Phú','0944009900','67 Điện Biên Phủ, Bình Thạnh, TP. HCM',NULL,'bank','unpaid','cancelled',2802000.00,0.00,NULL,2802000.00,NULL,NULL,NULL,NULL,'2026-07-24 01:54:37','2026-07-24 01:54:37');
INSERT INTO `orders` VALUES (9,13,'DH260410009','Lý Tuyết Như','0905010011','89 Lê Văn Sỹ, Tân Bình, TP. HCM','Giao giờ hành chính','bank','paid','delivered',1197000.00,0.00,NULL,1197000.00,NULL,NULL,NULL,NULL,'2026-08-07 01:54:37','2026-08-07 01:54:37');
INSERT INTO `orders` VALUES (10,2,'DH260203010','Lê Hoàng Nam','0933111222','789 Lê Lợi, Gò Vấp, TP. HCM',NULL,'cod','paid','delivered',1180000.00,0.00,NULL,1180000.00,NULL,NULL,NULL,NULL,'2026-06-02 04:54:37','2026-06-02 04:54:37');
INSERT INTO `orders` VALUES (11,3,'DH260322011','Phạm Quỳnh Chi','0944555666','456 Nguyễn Trãi, Quận 5, TP. HCM',NULL,'bank','unpaid','shipping',1857000.00,0.00,NULL,1857000.00,NULL,NULL,NULL,NULL,'2026-07-19 14:54:37','2026-07-19 14:54:37');
INSERT INTO `orders` VALUES (12,8,'DH260415012','Bùi Thanh Tùng','0922005566','90 Võ Văn Tần, Quận 3, TP. HCM',NULL,'cod','unpaid','pending',1736000.00,0.00,NULL,1736000.00,NULL,NULL,NULL,NULL,'2026-08-12 23:54:37','2026-08-12 23:54:37');
INSERT INTO `orders` VALUES (13,8,'DH260313013','Bùi Thanh Tùng','0922005566','90 Võ Văn Tần, Quận 3, TP. HCM','Giao giờ hành chính','bank','paid','delivered',1300000.00,0.00,NULL,1300000.00,NULL,NULL,NULL,NULL,'2026-07-10 01:54:37','2026-07-10 01:54:37');
INSERT INTO `orders` VALUES (14,10,'DH260404014','Hồ Quang Huy','0977007788','23 Nguyễn Văn Cừ, Quận 5, TP. HCM',NULL,'cod','unpaid','cancelled',3180000.00,0.00,NULL,3180000.00,NULL,NULL,NULL,NULL,'2026-08-01 04:54:37','2026-08-01 04:54:37');
INSERT INTO `orders` VALUES (15,2,'DH260619015','Test','0900000000','HN',NULL,'cod','unpaid','pending',420000.00,0.00,NULL,420000.00,NULL,NULL,NULL,NULL,'2026-10-16 10:28:28','2026-10-16 10:28:28');
INSERT INTO `orders` VALUES (16,17,'DH260619016','Nguyễn Trung Hiếu','0868701413','Kim Chung, Hoài Đức, Hà Nội','Hàng dễ hỏng trong quá trình vận chuyển, cẩn thận giúp tôi','cod','paid','delivered',650000.00,0.00,NULL,650000.00,NULL,NULL,NULL,NULL,'2026-10-16 10:36:30','2026-10-18 05:31:17');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_images`
--

DROP TABLE IF EXISTS `product_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_images` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL,
  `image_url` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `product_images_product_id_foreign` (`product_id`),
  CONSTRAINT `product_images_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=165 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_images`
--

LOCK TABLES `product_images` WRITE;
/*!40000 ALTER TABLE `product_images` DISABLE KEYS */;
INSERT INTO `product_images` VALUES (1,1,'images/local/prod_1.jpg',1,1);
INSERT INTO `product_images` VALUES (2,1,'uploads/shrimp_premium.png',0,2);
INSERT INTO `product_images` VALUES (3,1,'uploads/shrimp_premium.png',0,3);
INSERT INTO `product_images` VALUES (4,1,'uploads/shrimp_premium.png',0,4);
INSERT INTO `product_images` VALUES (5,2,'images/local/prod_2.jpg',1,1);
INSERT INTO `product_images` VALUES (6,2,'uploads/shrimp_premium.png',0,2);
INSERT INTO `product_images` VALUES (7,2,'uploads/shrimp_premium.png',0,3);
INSERT INTO `product_images` VALUES (8,2,'uploads/shrimp_premium.png',0,4);
INSERT INTO `product_images` VALUES (9,3,'images/local/prod_3.jpg',1,1);
INSERT INTO `product_images` VALUES (10,3,'uploads/shrimp_premium.png',0,2);
INSERT INTO `product_images` VALUES (11,3,'uploads/shrimp_premium.png',0,3);
INSERT INTO `product_images` VALUES (12,3,'uploads/shrimp_premium.png',0,4);
INSERT INTO `product_images` VALUES (13,4,'images/local/prod_4.jpg',1,1);
INSERT INTO `product_images` VALUES (14,4,'uploads/shrimp_premium.png',0,2);
INSERT INTO `product_images` VALUES (15,4,'uploads/shrimp_premium.png',0,3);
INSERT INTO `product_images` VALUES (16,4,'uploads/shrimp_premium.png',0,4);
INSERT INTO `product_images` VALUES (17,5,'images/local/prod_5.jpg',1,1);
INSERT INTO `product_images` VALUES (18,5,'uploads/shrimp_premium.png',0,2);
INSERT INTO `product_images` VALUES (19,5,'uploads/shrimp_premium.png',0,3);
INSERT INTO `product_images` VALUES (20,5,'uploads/shrimp_premium.png',0,4);
INSERT INTO `product_images` VALUES (21,6,'images/local/prod_6.jpg',1,1);
INSERT INTO `product_images` VALUES (22,6,'uploads/shrimp_premium.png',0,2);
INSERT INTO `product_images` VALUES (23,6,'uploads/shrimp_premium.png',0,3);
INSERT INTO `product_images` VALUES (24,6,'uploads/shrimp_premium.png',0,4);
INSERT INTO `product_images` VALUES (25,7,'images/local/prod_7.jpg',1,1);
INSERT INTO `product_images` VALUES (26,7,'uploads/shrimp_premium.png',0,2);
INSERT INTO `product_images` VALUES (27,7,'uploads/shrimp_premium.png',0,3);
INSERT INTO `product_images` VALUES (28,7,'uploads/shrimp_premium.png',0,4);
INSERT INTO `product_images` VALUES (29,8,'images/local/prod_8.jpg',1,1);
INSERT INTO `product_images` VALUES (30,8,'uploads/shrimp_premium.png',0,2);
INSERT INTO `product_images` VALUES (31,8,'uploads/shrimp_premium.png',0,3);
INSERT INTO `product_images` VALUES (32,8,'uploads/shrimp_premium.png',0,4);
INSERT INTO `product_images` VALUES (33,9,'images/local/prod_9.jpg',1,1);
INSERT INTO `product_images` VALUES (34,9,'uploads/crab_premium.png',0,2);
INSERT INTO `product_images` VALUES (35,9,'uploads/crab_premium.png',0,3);
INSERT INTO `product_images` VALUES (36,9,'uploads/crab_premium.png',0,4);
INSERT INTO `product_images` VALUES (37,10,'images/local/prod_10.jpg',1,1);
INSERT INTO `product_images` VALUES (38,10,'uploads/crab_premium.png',0,2);
INSERT INTO `product_images` VALUES (39,10,'uploads/crab_premium.png',0,3);
INSERT INTO `product_images` VALUES (40,10,'uploads/crab_premium.png',0,4);
INSERT INTO `product_images` VALUES (41,11,'images/local/prod_11.jpg',1,1);
INSERT INTO `product_images` VALUES (42,11,'uploads/crab_premium.png',0,2);
INSERT INTO `product_images` VALUES (43,11,'uploads/crab_premium.png',0,3);
INSERT INTO `product_images` VALUES (44,11,'uploads/crab_premium.png',0,4);
INSERT INTO `product_images` VALUES (45,12,'images/local/prod_12.jpg',1,1);
INSERT INTO `product_images` VALUES (46,12,'uploads/crab_premium.png',0,2);
INSERT INTO `product_images` VALUES (47,12,'uploads/crab_premium.png',0,3);
INSERT INTO `product_images` VALUES (48,12,'uploads/crab_premium.png',0,4);
INSERT INTO `product_images` VALUES (49,13,'images/local/prod_13.jpg',1,1);
INSERT INTO `product_images` VALUES (50,13,'uploads/crab_premium.png',0,2);
INSERT INTO `product_images` VALUES (51,13,'uploads/crab_premium.png',0,3);
INSERT INTO `product_images` VALUES (52,13,'uploads/crab_premium.png',0,4);
INSERT INTO `product_images` VALUES (53,14,'images/local/prod_14.jpg',1,1);
INSERT INTO `product_images` VALUES (54,14,'uploads/crab_premium.png',0,2);
INSERT INTO `product_images` VALUES (55,14,'uploads/crab_premium.png',0,3);
INSERT INTO `product_images` VALUES (56,14,'uploads/crab_premium.png',0,4);
INSERT INTO `product_images` VALUES (57,15,'images/local/prod_15.jpg',1,1);
INSERT INTO `product_images` VALUES (58,15,'uploads/fish_premium.png',0,2);
INSERT INTO `product_images` VALUES (59,15,'uploads/fish_premium.png',0,3);
INSERT INTO `product_images` VALUES (60,15,'uploads/fish_premium.png',0,4);
INSERT INTO `product_images` VALUES (61,16,'images/local/prod_16.jpg',1,1);
INSERT INTO `product_images` VALUES (62,16,'uploads/fish_premium.png',0,2);
INSERT INTO `product_images` VALUES (63,16,'uploads/fish_premium.png',0,3);
INSERT INTO `product_images` VALUES (64,16,'uploads/fish_premium.png',0,4);
INSERT INTO `product_images` VALUES (65,17,'images/local/prod_17.jpg',1,1);
INSERT INTO `product_images` VALUES (66,17,'uploads/fish_premium.png',0,2);
INSERT INTO `product_images` VALUES (67,17,'uploads/fish_premium.png',0,3);
INSERT INTO `product_images` VALUES (68,17,'uploads/fish_premium.png',0,4);
INSERT INTO `product_images` VALUES (69,18,'images/local/prod_18.jpg',1,1);
INSERT INTO `product_images` VALUES (70,18,'uploads/fish_premium.png',0,2);
INSERT INTO `product_images` VALUES (71,18,'uploads/fish_premium.png',0,3);
INSERT INTO `product_images` VALUES (72,18,'uploads/fish_premium.png',0,4);
INSERT INTO `product_images` VALUES (73,19,'images/local/prod_19.jpg',1,1);
INSERT INTO `product_images` VALUES (74,19,'uploads/fish_premium.png',0,2);
INSERT INTO `product_images` VALUES (75,19,'uploads/fish_premium.png',0,3);
INSERT INTO `product_images` VALUES (76,19,'uploads/fish_premium.png',0,4);
INSERT INTO `product_images` VALUES (77,20,'images/local/prod_20.jpg',1,1);
INSERT INTO `product_images` VALUES (78,20,'uploads/fish_premium.png',0,2);
INSERT INTO `product_images` VALUES (79,20,'uploads/fish_premium.png',0,3);
INSERT INTO `product_images` VALUES (80,20,'uploads/fish_premium.png',0,4);
INSERT INTO `product_images` VALUES (81,21,'images/local/prod_21.jpg',1,1);
INSERT INTO `product_images` VALUES (82,21,'uploads/fish_premium.png',0,2);
INSERT INTO `product_images` VALUES (83,21,'uploads/fish_premium.png',0,3);
INSERT INTO `product_images` VALUES (84,21,'uploads/fish_premium.png',0,4);
INSERT INTO `product_images` VALUES (85,22,'images/local/prod_22.jpg',1,1);
INSERT INTO `product_images` VALUES (86,22,'uploads/fish_premium.png',0,2);
INSERT INTO `product_images` VALUES (87,22,'uploads/fish_premium.png',0,3);
INSERT INTO `product_images` VALUES (88,22,'uploads/fish_premium.png',0,4);
INSERT INTO `product_images` VALUES (89,23,'images/local/prod_23.jpg',1,1);
INSERT INTO `product_images` VALUES (90,23,'uploads/squid_premium.png',0,2);
INSERT INTO `product_images` VALUES (91,23,'uploads/squid_premium.png',0,3);
INSERT INTO `product_images` VALUES (92,23,'uploads/squid_premium.png',0,4);
INSERT INTO `product_images` VALUES (93,24,'images/local/prod_24.jpg',1,1);
INSERT INTO `product_images` VALUES (94,24,'uploads/squid_premium.png',0,2);
INSERT INTO `product_images` VALUES (95,24,'uploads/squid_premium.png',0,3);
INSERT INTO `product_images` VALUES (96,24,'uploads/squid_premium.png',0,4);
INSERT INTO `product_images` VALUES (97,25,'images/local/prod_25.jpg',1,1);
INSERT INTO `product_images` VALUES (98,25,'uploads/squid_premium.png',0,2);
INSERT INTO `product_images` VALUES (99,25,'uploads/squid_premium.png',0,3);
INSERT INTO `product_images` VALUES (100,25,'uploads/squid_premium.png',0,4);
INSERT INTO `product_images` VALUES (101,26,'images/local/prod_26.jpg',1,1);
INSERT INTO `product_images` VALUES (102,26,'uploads/squid_premium.png',0,2);
INSERT INTO `product_images` VALUES (103,26,'uploads/squid_premium.png',0,3);
INSERT INTO `product_images` VALUES (104,26,'uploads/squid_premium.png',0,4);
INSERT INTO `product_images` VALUES (105,27,'images/local/gallery_105.jpg',1,1);
INSERT INTO `product_images` VALUES (106,27,'images/local/prod_27.jpg',0,2);
INSERT INTO `product_images` VALUES (107,27,'images/local/prod_27.jpg',0,3);
INSERT INTO `product_images` VALUES (108,27,'images/local/prod_27.jpg',0,4);
INSERT INTO `product_images` VALUES (109,28,'images/local/gallery_109.jpg',1,1);
INSERT INTO `product_images` VALUES (110,28,'images/local/prod_28.jpg',0,2);
INSERT INTO `product_images` VALUES (111,28,'images/local/prod_28.jpg',0,3);
INSERT INTO `product_images` VALUES (112,28,'images/local/gallery_112.jpg',0,4);
INSERT INTO `product_images` VALUES (113,29,'images/local/gallery_113.jpg',1,1);
INSERT INTO `product_images` VALUES (114,29,'images/local/prod_29.jpg',0,2);
INSERT INTO `product_images` VALUES (115,29,'images/local/prod_29.jpg',0,3);
INSERT INTO `product_images` VALUES (116,29,'images/local/gallery_116.jpg',0,4);
INSERT INTO `product_images` VALUES (117,30,'images/local/gallery_117.jpg',1,1);
INSERT INTO `product_images` VALUES (118,30,'images/local/gallery_118.jpg',0,2);
INSERT INTO `product_images` VALUES (119,30,'images/local/gallery_119.jpg',0,3);
INSERT INTO `product_images` VALUES (120,30,'images/local/prod_30.jpg',0,4);
INSERT INTO `product_images` VALUES (121,31,'images/local/gallery_121.jpg',1,1);
INSERT INTO `product_images` VALUES (122,31,'images/local/gallery_122.jpg',0,2);
INSERT INTO `product_images` VALUES (123,31,'images/local/prod_31.jpg',0,3);
INSERT INTO `product_images` VALUES (124,31,'images/local/prod_31.jpg',0,4);
INSERT INTO `product_images` VALUES (125,32,'images/local/gallery_125.jpg',1,1);
INSERT INTO `product_images` VALUES (126,32,'images/local/gallery_126.jpg',0,2);
INSERT INTO `product_images` VALUES (127,32,'images/local/prod_32.jpg',0,3);
INSERT INTO `product_images` VALUES (128,32,'images/local/prod_32.jpg',0,4);
INSERT INTO `product_images` VALUES (129,33,'images/local/gallery_129.jpg',1,1);
INSERT INTO `product_images` VALUES (130,33,'images/local/gallery_130.jpg',0,2);
INSERT INTO `product_images` VALUES (131,33,'images/local/gallery_131.jpg',0,3);
INSERT INTO `product_images` VALUES (132,33,'images/local/gallery_132.jpg',0,4);
INSERT INTO `product_images` VALUES (133,34,'images/local/gallery_133.jpg',1,1);
INSERT INTO `product_images` VALUES (134,34,'images/local/prod_34.jpg',0,2);
INSERT INTO `product_images` VALUES (135,34,'images/local/prod_34.jpg',0,3);
INSERT INTO `product_images` VALUES (136,34,'images/local/prod_34.jpg',0,4);
INSERT INTO `product_images` VALUES (137,35,'images/local/gallery_137.jpg',1,1);
INSERT INTO `product_images` VALUES (138,35,'images/local/prod_35.jpg',0,2);
INSERT INTO `product_images` VALUES (139,35,'images/local/prod_35.jpg',0,3);
INSERT INTO `product_images` VALUES (140,35,'images/local/prod_35.jpg',0,4);
INSERT INTO `product_images` VALUES (141,36,'images/local/gallery_141.jpg',1,1);
INSERT INTO `product_images` VALUES (142,36,'images/local/gallery_142.jpg',0,2);
INSERT INTO `product_images` VALUES (143,36,'images/local/gallery_143.jpg',0,3);
INSERT INTO `product_images` VALUES (144,36,'images/local/gallery_144.jpg',0,4);
INSERT INTO `product_images` VALUES (145,37,'images/local/gallery_145.jpg',1,1);
INSERT INTO `product_images` VALUES (146,37,'images/local/prod_37.jpg',0,2);
INSERT INTO `product_images` VALUES (147,37,'images/local/prod_37.jpg',0,3);
INSERT INTO `product_images` VALUES (148,37,'images/local/gallery_148.jpg',0,4);
INSERT INTO `product_images` VALUES (149,38,'images/local/gallery_149.jpg',1,1);
INSERT INTO `product_images` VALUES (150,38,'images/local/gallery_150.jpg',0,2);
INSERT INTO `product_images` VALUES (151,38,'images/local/prod_38.jpg',0,3);
INSERT INTO `product_images` VALUES (152,38,'images/local/prod_38.jpg',0,4);
INSERT INTO `product_images` VALUES (153,39,'images/local/gallery_153.jpg',1,1);
INSERT INTO `product_images` VALUES (154,39,'images/local/gallery_154.jpg',0,2);
INSERT INTO `product_images` VALUES (155,39,'images/local/gallery_155.jpg',0,3);
INSERT INTO `product_images` VALUES (156,39,'images/local/gallery_156.jpg',0,4);
INSERT INTO `product_images` VALUES (157,40,'images/local/gallery_157.jpg',1,1);
INSERT INTO `product_images` VALUES (158,40,'images/local/gallery_158.jpg',0,2);
INSERT INTO `product_images` VALUES (159,40,'images/local/prod_40.jpg',0,3);
INSERT INTO `product_images` VALUES (160,40,'images/local/gallery_160.jpg',0,4);
INSERT INTO `product_images` VALUES (161,41,'images/local/gallery_161.jpg',1,1);
INSERT INTO `product_images` VALUES (162,41,'images/local/gallery_162.jpg',0,2);
INSERT INTO `product_images` VALUES (163,41,'images/local/gallery_163.jpg',0,3);
INSERT INTO `product_images` VALUES (164,41,'images/local/gallery_164.jpg',0,4);
/*!40000 ALTER TABLE `product_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint(20) unsigned NOT NULL,
  `product_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(18,2) NOT NULL,
  `sale_price` decimal(18,2) DEFAULT NULL,
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `unit` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'kg',
  `image_url` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_product_code_unique` (`product_code`),
  UNIQUE KEY `products_slug_unique` (`slug`),
  KEY `products_category_id_index` (`category_id`),
  KEY `products_is_active_index` (`is_active`),
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,2,'SP001','Tôm sú tươi sống loại 1 (20 con/kg)','tom-su-tuoi-song-loai-1-20-con-kg','Tôm sú tươi sống size lớn 20 con/kg, thịt chắc ngọt, vỏ bóng đẹp. Đánh bắt tự nhiên vùng biển miền Trung.',320000.00,289000.00,60,'kg','images/local/prod_1.jpg',1,1,'2026-09-05 03:54:37','2026-10-18 13:58:06');
INSERT INTO `products` VALUES (2,2,'SP002','Tôm sú thịt bóc nõn','tom-su-thit-boc-non','Tôm sú đã bóc vỏ rút chỉ, tiện lợi cho chế biến lẩu, xào, chiên.',380000.00,NULL,40,'kg','images/local/prod_2.jpg',1,0,'2026-09-06 03:54:37','2026-10-18 13:58:06');
INSERT INTO `products` VALUES (3,3,'SP003','Tôm thẻ chân trắng (40 con/kg)','tom-the-chan-trang-40-con-kg','Tôm thẻ chân trắng tươi sống, size 40 con/kg, giá hợp lý cho bữa ăn gia đình.',175000.00,159000.00,120,'kg','images/local/prod_3.jpg',1,1,'2026-09-07 03:54:37','2026-10-18 13:58:06');
INSERT INTO `products` VALUES (4,3,'SP004','Tôm thẻ size nhỏ (70 con/kg)','tom-the-size-nho-70-con-kg','Tôm thẻ size nhỏ phù hợp nấu canh, rang.',135000.00,NULL,90,'kg','images/local/prod_4.jpg',1,0,'2026-09-08 03:54:37','2026-10-18 13:58:06');
INSERT INTO `products` VALUES (5,4,'SP005','Tôm hùm bông sống','tom-hum-bong-song','Tôm hùm bông sống size 0.5-0.7kg/con, càng to khỏe, thịt ngọt đậm đà, đạt chuẩn nhà hàng.',1650000.00,1490000.00,18,'kg','images/local/prod_5.jpg',1,1,'2026-09-09 03:54:37','2026-10-18 13:58:06');
INSERT INTO `products` VALUES (6,4,'SP006','Tôm hùm baby','tom-hum-baby','Tôm hùm baby size 150-200g/con, thích hợp nướng phô mai.',850000.00,NULL,25,'kg','images/local/prod_6.jpg',1,0,'2026-09-10 03:54:37','2026-10-18 13:58:06');
INSERT INTO `products` VALUES (7,5,'SP007','Tôm càng xanh sông (8 con/kg)','tom-cang-xanh-song-8-con-kg','Tôm càng xanh sông nước miền Tây, càng xanh to, gạch béo.',420000.00,399000.00,35,'kg','images/local/prod_7.jpg',1,1,'2026-09-11 03:54:37','2026-10-18 13:58:06');
INSERT INTO `products` VALUES (8,6,'SP008','Tôm tích (bề bề) sống','tom-tich-be-be-song','Tôm tích sống thịt dai ngọt, nhiều gạch, hấp bia cực ngon.',480000.00,NULL,22,'kg','images/local/prod_8.jpg',1,0,'2026-09-12 03:54:37','2026-10-18 13:58:06');
INSERT INTO `products` VALUES (9,8,'SP009','Cua hoàng đế Alaska sống','cua-hoang-de-alaska-song','King Crab nhập khẩu trực tiếp từ Alaska, thịt cua săn chắc ngọt đậm, giàu dinh dưỡng.',2200000.00,1950000.00,12,'kg','images/local/prod_9.jpg',1,1,'2026-09-13 03:54:37','2026-10-18 13:58:06');
INSERT INTO `products` VALUES (10,8,'SP010','Chân cua hoàng đế đông lạnh','chan-cua-hoang-de-dong-lanh','Chân cua hoàng đế đông lạnh nhập khẩu, đã hấp chín, chỉ cần rã đông và thưởng thức.',1750000.00,1590000.00,20,'kg','images/local/prod_10.jpg',1,0,'2026-09-14 03:54:37','2026-10-18 13:58:06');
INSERT INTO `products` VALUES (11,9,'SP011','Cua gạch son Cà Mau','cua-gach-son-ca-mau','Cua gạch Cà Mau lớp gạch đỏ au béo ngậy, thịt chắc. Thích hợp cua rang me, cua hấp.',680000.00,650000.00,44,'kg','images/local/prod_11.jpg',1,1,'2026-09-15 03:54:37','2026-10-18 13:58:06');
INSERT INTO `products` VALUES (12,10,'SP012','Cua thịt Cà Mau (4 con/kg)','cua-thit-ca-mau-4-con-kg','Cua thịt Cà Mau chắc thịt, ngọt tự nhiên, size 4 con/kg.',520000.00,NULL,50,'kg','images/local/prod_12.jpg',1,0,'2026-09-16 03:54:37','2026-10-18 13:58:06');
INSERT INTO `products` VALUES (13,11,'SP013','Ghẹ xanh tươi sống (5 con/kg)','ghe-xanh-tuoi-song-5-con-kg','Ghẹ xanh tươi sống thịt ngọt, gạch nhiều, hấp sả hoặc rang muối đều tuyệt.',360000.00,329000.00,55,'kg','images/local/prod_13.jpg',1,1,'2026-09-17 03:54:37','2026-10-18 13:58:06');
INSERT INTO `products` VALUES (14,11,'SP014','Ghẹ xanh đông lạnh','ghe-xanh-dong-lanh','Ghẹ xanh đông lạnh cấp đông nhanh giữ trọn độ tươi.',280000.00,NULL,70,'kg','images/local/prod_14.jpg',1,0,'2026-09-18 03:54:37','2026-10-18 13:58:06');
INSERT INTO `products` VALUES (15,13,'SP015','Phi lê cá hồi Na Uy','phi-le-ca-hoi-na-uy','Cá hồi Na Uy nhập khẩu phi lê sạch xương, màu cam tươi vân mỡ đẹp, đạt chuẩn ăn sashimi.',490000.00,459000.00,65,'kg','images/local/prod_15.jpg',1,1,'2026-09-19 03:54:37','2026-10-18 13:58:06');
INSERT INTO `products` VALUES (16,13,'SP016','Cá hồi nguyên con Na Uy','ca-hoi-nguyen-con-na-uy','Cá hồi Na Uy nguyên con tươi, thích hợp chế biến đa dạng món.',420000.00,NULL,30,'kg','images/local/prod_16.jpg',1,0,'2026-09-20 03:54:37','2026-10-18 13:58:06');
INSERT INTO `products` VALUES (17,13,'SP017','Đầu cá hồi Na Uy','dau-ca-hoi-na-uy','Đầu cá hồi tươi nấu canh chua, kho, nướng.',145000.00,129000.00,80,'kg','images/local/prod_17.jpg',1,0,'2026-09-21 03:54:37','2026-10-18 13:58:06');
INSERT INTO `products` VALUES (18,14,'SP018','Cá thu tươi cắt khúc','ca-thu-tuoi-cat-khuc','Cá thu tươi đánh bắt trong ngày, cắt khúc dày, kho hoặc chiên đều ngon.',260000.00,NULL,75,'kg','images/local/prod_18.jpg',1,1,'2026-09-22 03:54:37','2026-10-18 13:58:06');
INSERT INTO `products` VALUES (19,15,'SP019','Phi lê cá ngừ đại dương','phi-le-ca-ngu-dai-duong','Cá ngừ đại dương phi lê tươi, thịt đỏ đẹp, làm steak hoặc sashimi.',320000.00,299000.00,40,'kg','images/local/prod_19.jpg',1,0,'2026-09-23 03:54:37','2026-10-18 13:58:06');
INSERT INTO `products` VALUES (20,16,'SP020','Cá bớp cắt khúc','ca-bop-cat-khuc','Cá bớp (cá giò) thịt dai chắc, ít xương, nấu lẩu cực ngon.',230000.00,NULL,60,'kg','images/local/prod_20.jpg',1,0,'2026-09-24 03:54:37','2026-10-18 13:58:06');
INSERT INTO `products` VALUES (21,17,'SP021','Cá mú nghệ tươi sống','ca-mu-nghe-tuoi-song','Cá mú nghệ tươi sống thịt trắng dai ngọt, hấp Hong Kong tuyệt hảo.',420000.00,390000.00,28,'kg','images/local/prod_21.jpg',1,1,'2026-09-25 03:54:37','2026-10-18 13:58:06');
INSERT INTO `products` VALUES (22,18,'SP022','Cá chẽm tươi nguyên con','ca-chem-tuoi-nguyen-con','Cá chẽm (cá vược) thịt trắng ngọt, ít xương, hấp hoặc chiên giòn.',175000.00,159000.00,70,'kg','images/local/prod_22.jpg',1,0,'2026-09-26 03:54:37','2026-10-18 13:58:06');
INSERT INTO `products` VALUES (23,20,'SP023','Mực ống tươi loại 1','muc-ong-tuoi-loai-1','Mực ống tươi size đều đã làm sạch, thân dày, ngọt giòn.',280000.00,259000.00,80,'kg','images/local/prod_23.jpg',1,1,'2026-09-27 03:54:37','2026-10-18 13:58:06');
INSERT INTO `products` VALUES (24,21,'SP024','Mực lá tươi size lớn','muc-la-tuoi-size-lon','Mực lá dày mình, ngọt giòn, thích hợp nướng và hấp gừng.',350000.00,NULL,45,'kg','images/local/prod_24.jpg',1,0,'2026-09-28 03:54:37','2026-10-18 13:58:06');
INSERT INTO `products` VALUES (25,22,'SP025','Mực một nắng Phan Thiết','muc-mot-nang-phan-thiet','Mực một nắng phơi tự nhiên Phan Thiết, nướng thơm lừng chấm tương ớt.',420000.00,399000.00,50,'kg','images/local/prod_25.jpg',1,1,'2026-09-29 03:54:37','2026-10-18 13:58:06');
INSERT INTO `products` VALUES (26,23,'SP026','Bạch tuộc baby','bach-tuoc-baby','Bạch tuộc baby tươi làm sạch, nướng sa tế hoặc xào dứa.',240000.00,NULL,65,'kg','images/local/prod_26.jpg',1,0,'2026-09-30 03:54:37','2026-10-18 13:58:06');
INSERT INTO `products` VALUES (27,23,'SP027','Bạch tuộc lớn','bach-tuoc-lon','Bạch tuộc lớn thịt dai giòn, thích hợp lẩu và nướng.',290000.00,269000.00,40,'kg','images/local/prod_27.jpg',1,0,'2026-10-01 03:54:37','2026-10-18 13:58:06');
INSERT INTO `products` VALUES (28,25,'SP028','Ốc hương biển (80 con/kg)','oc-huong-bien-80-con-kg','Ốc hương biển tự nhiên size 70-80 con/kg, thịt giòn béo, thơm tự nhiên khi chín.',380000.00,359000.00,55,'kg','images/local/prod_28.jpg',1,1,'2026-10-02 03:54:37','2026-10-18 13:58:06');
INSERT INTO `products` VALUES (29,26,'SP029','Sò huyết size lớn','so-huyet-size-lon','Sò huyết tươi sống size lớn, nhiều huyết, nướng mỡ hành hấp dẫn.',180000.00,NULL,60,'kg','images/local/prod_29.jpg',1,0,'2026-10-03 03:54:37','2026-10-18 13:58:06');
INSERT INTO `products` VALUES (30,27,'SP030','Cồi sò điệp Nhật','coi-so-diep-nhat','Cồi sò điệp Nhật tươi to tròn, ngọt thanh, áp chảo bơ tỏi tuyệt vời.',520000.00,489000.00,30,'kg','images/local/prod_30.jpg',1,1,'2026-10-04 03:54:37','2026-10-18 13:58:06');
INSERT INTO `products` VALUES (31,28,'SP031','Hàu sữa Pháp','hau-sua-phap','Hàu sữa Pháp size lớn, béo ngậy, ăn sống mù tạt hoặc nướng phô mai.',95000.00,NULL,100,'chục','images/local/prod_31.jpg',1,1,'2026-10-05 03:54:37','2026-10-18 13:58:06');
INSERT INTO `products` VALUES (32,28,'SP032','Hàu sữa Quảng Ninh','hau-sua-quang-ninh','Hàu sữa Quảng Ninh tươi rói, giá tốt cho tiệc nướng.',60000.00,55000.00,120,'chục','images/local/prod_32.jpg',1,0,'2026-10-06 03:54:37','2026-10-18 13:58:06');
INSERT INTO `products` VALUES (33,29,'SP033','Nghêu trắng Bến Tre','ngheu-trang-ben-tre','Nghêu trắng Bến Tre sạch cát, ngọt nước, hấp sả ngon ngọt.',65000.00,NULL,150,'kg','images/local/prod_33.jpg',1,0,'2026-10-07 03:54:37','2026-10-18 13:58:06');
INSERT INTO `products` VALUES (34,31,'SP034','Khô mực một nắng loại 1','kho-muc-mot-nang-loai-1','Khô mực một nắng size lớn, dẻo ngọt, nướng lên thơm phức.',850000.00,799000.00,35,'kg','images/local/prod_34.jpg',1,1,'2026-10-08 03:54:37','2026-10-18 13:58:06');
INSERT INTO `products` VALUES (35,31,'SP035','Khô mực xé tẩm gia vị','kho-muc-xe-tam-gia-vi','Khô mực xé sẵn tẩm gia vị, ăn liền tiện lợi, hộp 200g.',320000.00,NULL,60,'hộp','images/local/prod_35.jpg',1,0,'2026-10-09 03:54:37','2026-10-18 13:58:06');
INSERT INTO `products` VALUES (36,32,'SP036','Khô cá dứa một nắng','kho-ca-dua-mot-nang','Khô cá dứa một nắng béo thịt, chiên ăn với cơm cháy.',380000.00,359000.00,40,'kg','images/local/prod_36.jpg',1,0,'2026-10-10 03:54:37','2026-10-18 13:58:06');
INSERT INTO `products` VALUES (37,32,'SP037','Khô cá chỉ vàng','kho-ca-chi-vang','Khô cá chỉ vàng đậm đà, mồi nhậu khoái khẩu.',260000.00,NULL,55,'kg','images/local/prod_37.jpg',1,0,'2026-10-11 03:54:37','2026-10-18 13:58:06');
INSERT INTO `products` VALUES (38,33,'SP038','Chả mực Hạ Long','cha-muc-ha-long','Chả mực Hạ Long giã tay, giòn dai ngọt, chiên vàng ăn kèm xôi trắng.',450000.00,420000.00,44,'kg','images/local/prod_38.jpg',1,1,'2026-10-12 03:54:37','2026-10-18 13:58:06');
INSERT INTO `products` VALUES (39,33,'SP039','Chả cá thác lác','cha-ca-thac-lac','Chả cá thác lác nạo tay dai ngon, nấu canh hoặc chiên.',220000.00,NULL,70,'kg','images/local/prod_39.jpg',1,0,'2026-10-13 03:54:37','2026-10-18 13:58:06');
INSERT INTO `products` VALUES (40,34,'SP040','Combo cá viên thập cẩm','combo-ca-vien-thap-cam','Gói cá viên, tôm viên, bò biển viên thập cẩm 500g cho nồi lẩu.',95000.00,85000.00,200,'gói','images/local/prod_40.jpg',1,0,'2026-10-14 03:54:37','2026-10-18 13:58:06');
INSERT INTO `products` VALUES (41,34,'SP041','Tôm viên nhân phô mai','tom-vien-nhan-pho-mai','Tôm viên nhân phô mai tan chảy, gói 300g, chiên giòn.',110000.00,NULL,150,'gói','images/local/prod_41.jpg',1,0,'2026-10-15 03:54:37','2026-10-18 13:58:06');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reviews` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `user_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rating` tinyint(3) unsigned NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reviews_user_id_foreign` (`user_id`),
  KEY `reviews_product_id_index` (`product_id`),
  CONSTRAINT `reviews_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reviews`
--

LOCK TABLES `reviews` WRITE;
/*!40000 ALTER TABLE `reviews` DISABLE KEYS */;
INSERT INTO `reviews` VALUES (1,1,14,'Trương Đình Bảo',3,'Hải sản rất tươi, đóng gói cẩn thận, sẽ ủng hộ tiếp!',1,'2026-10-14 03:54:37','2026-10-14 03:54:37');
INSERT INTO `reviews` VALUES (2,1,5,'Nguyễn Thị Hằng',5,'Sản phẩm ổn, giao đúng giờ, đóng gói chắc chắn.',1,'2026-09-15 03:54:37','2026-09-15 03:54:37');
INSERT INTO `reviews` VALUES (3,1,12,'Phan Văn Phú',3,'Tươi ngon, cả nhà đều thích. Cảm ơn shop!',1,'2026-09-18 03:54:37','2026-09-18 03:54:37');
INSERT INTO `reviews` VALUES (4,3,2,'Lê Hoàng Nam',4,'Tươi ngon, cả nhà đều thích. Cảm ơn shop!',0,'2026-10-05 03:54:37','2026-10-05 03:54:37');
INSERT INTO `reviews` VALUES (5,3,16,'Đinh Gia Bảo',4,'Tôm còn nhảy luôn, quá tươi! 5 sao.',1,'2026-10-11 03:54:37','2026-10-11 03:54:37');
INSERT INTO `reviews` VALUES (6,5,9,'Đặng Thu Trang',3,'Đặt lần thứ 3 rồi, lần nào cũng hài lòng.',0,'2026-09-30 03:54:37','2026-09-30 03:54:37');
INSERT INTO `reviews` VALUES (7,5,4,'Trần Văn Dũng',5,'Đặt lần thứ 3 rồi, lần nào cũng hài lòng.',1,'2026-09-23 03:54:37','2026-09-23 03:54:37');
INSERT INTO `reviews` VALUES (8,5,2,'Lê Hoàng Nam',5,'Hải sản rất tươi, đóng gói cẩn thận, sẽ ủng hộ tiếp!',0,'2026-09-19 03:54:37','2026-09-19 03:54:37');
INSERT INTO `reviews` VALUES (9,5,15,'Cao Thị Ngọc',5,'Sản phẩm ổn, giao đúng giờ, đóng gói chắc chắn.',1,'2026-09-26 03:54:37','2026-09-26 03:54:37');
INSERT INTO `reviews` VALUES (10,7,10,'Hồ Quang Huy',5,'Sẽ giới thiệu bạn bè mua ủng hộ shop.',1,'2026-10-05 03:54:37','2026-10-05 03:54:37');
INSERT INTO `reviews` VALUES (11,7,13,'Lý Tuyết Như',4,'Giá hơi cao nhưng chất lượng xứng đáng.',1,'2026-09-29 03:54:37','2026-09-29 03:54:37');
INSERT INTO `reviews` VALUES (12,7,11,'Ngô Thị Mai',4,'Hàng tươi sống, nhân viên tư vấn nhiệt tình.',1,'2026-09-27 03:54:37','2026-09-27 03:54:37');
INSERT INTO `reviews` VALUES (13,9,3,'Phạm Quỳnh Chi',4,'Hàng tươi sống, nhân viên tư vấn nhiệt tình.',1,'2026-10-13 03:54:37','2026-10-13 03:54:37');
INSERT INTO `reviews` VALUES (14,9,5,'Nguyễn Thị Hằng',4,'Tôm còn nhảy luôn, quá tươi! 5 sao.',0,'2026-09-18 03:54:37','2026-09-18 03:54:37');
INSERT INTO `reviews` VALUES (15,9,13,'Lý Tuyết Như',3,'Giá hơi cao nhưng chất lượng xứng đáng.',1,'2026-09-25 03:54:37','2026-09-25 03:54:37');
INSERT INTO `reviews` VALUES (16,9,3,'Phạm Quỳnh Chi',4,'Tươi ngon, cả nhà đều thích. Cảm ơn shop!',0,'2026-09-18 03:54:37','2026-09-18 03:54:37');
INSERT INTO `reviews` VALUES (17,11,15,'Cao Thị Ngọc',4,'Giao hàng nhanh, hàng tươi đúng như mô tả.',1,'2026-09-17 03:54:37','2026-09-17 03:54:37');
INSERT INTO `reviews` VALUES (18,11,11,'Ngô Thị Mai',4,'Giá hơi cao nhưng chất lượng xứng đáng.',1,'2026-09-16 03:54:37','2026-09-16 03:54:37');
INSERT INTO `reviews` VALUES (19,11,4,'Trần Văn Dũng',5,'Tôm còn nhảy luôn, quá tươi! 5 sao.',1,'2026-09-21 03:54:37','2026-09-21 03:54:37');
INSERT INTO `reviews` VALUES (20,13,7,'Đỗ Thị Lan Anh',5,'Tươi ngon, cả nhà đều thích. Cảm ơn shop!',1,'2026-10-07 03:54:37','2026-10-07 03:54:37');
INSERT INTO `reviews` VALUES (21,13,11,'Ngô Thị Mai',4,'Sẽ giới thiệu bạn bè mua ủng hộ shop.',1,'2026-09-20 03:54:37','2026-09-20 03:54:37');
INSERT INTO `reviews` VALUES (22,13,16,'Đinh Gia Bảo',4,'Tươi ngon, cả nhà đều thích. Cảm ơn shop!',1,'2026-09-08 03:54:37','2026-09-08 03:54:37');
INSERT INTO `reviews` VALUES (23,13,2,'Lê Hoàng Nam',4,'Giá hơi cao nhưng chất lượng xứng đáng.',1,'2026-09-30 03:54:37','2026-09-30 03:54:37');
INSERT INTO `reviews` VALUES (24,15,13,'Lý Tuyết Như',5,'Chất lượng tuyệt vời, thịt chắc và ngọt.',1,'2026-09-20 03:54:37','2026-09-20 03:54:37');
INSERT INTO `reviews` VALUES (25,15,6,'Võ Minh Khôi',3,'Giá hơi cao nhưng chất lượng xứng đáng.',1,'2026-09-10 03:54:37','2026-09-10 03:54:37');
INSERT INTO `reviews` VALUES (26,15,4,'Trần Văn Dũng',4,'Sẽ giới thiệu bạn bè mua ủng hộ shop.',1,'2026-09-25 03:54:37','2026-09-25 03:54:37');
INSERT INTO `reviews` VALUES (27,18,16,'Đinh Gia Bảo',5,'Đặt lần thứ 3 rồi, lần nào cũng hài lòng.',0,'2026-09-14 03:54:37','2026-09-14 03:54:37');
INSERT INTO `reviews` VALUES (28,18,8,'Bùi Thanh Tùng',3,'Hải sản rất tươi, đóng gói cẩn thận, sẽ ủng hộ tiếp!',1,'2026-10-15 03:54:37','2026-10-15 03:54:37');
INSERT INTO `reviews` VALUES (29,21,10,'Hồ Quang Huy',4,'Đặt lần thứ 3 rồi, lần nào cũng hài lòng.',1,'2026-09-16 03:54:37','2026-09-16 03:54:37');
INSERT INTO `reviews` VALUES (30,21,14,'Trương Đình Bảo',4,'Tôm còn nhảy luôn, quá tươi! 5 sao.',1,'2026-09-17 03:54:37','2026-09-17 03:54:37');
INSERT INTO `reviews` VALUES (31,23,10,'Hồ Quang Huy',4,'Giao hàng nhanh, hàng tươi đúng như mô tả.',1,'2026-10-01 03:54:37','2026-10-01 03:54:37');
INSERT INTO `reviews` VALUES (32,23,14,'Trương Đình Bảo',4,'Tôm còn nhảy luôn, quá tươi! 5 sao.',1,'2026-09-09 03:54:37','2026-09-09 03:54:37');
INSERT INTO `reviews` VALUES (33,23,11,'Ngô Thị Mai',5,'Sản phẩm ổn, giao đúng giờ, đóng gói chắc chắn.',1,'2026-09-17 03:54:37','2026-09-17 03:54:37');
INSERT INTO `reviews` VALUES (34,25,6,'Võ Minh Khôi',4,'Chất lượng tuyệt vời, thịt chắc và ngọt.',1,'2026-10-05 03:54:37','2026-10-05 03:54:37');
INSERT INTO `reviews` VALUES (35,25,14,'Trương Đình Bảo',4,'Hải sản rất tươi, đóng gói cẩn thận, sẽ ủng hộ tiếp!',0,'2026-09-11 03:54:37','2026-09-11 03:54:37');
INSERT INTO `reviews` VALUES (36,25,13,'Lý Tuyết Như',3,'Chất lượng tuyệt vời, thịt chắc và ngọt.',1,'2026-09-13 03:54:37','2026-09-13 03:54:37');
INSERT INTO `reviews` VALUES (37,28,9,'Đặng Thu Trang',4,'Giao hàng nhanh, hàng tươi đúng như mô tả.',1,'2026-10-12 03:54:37','2026-10-12 03:54:37');
INSERT INTO `reviews` VALUES (38,28,8,'Bùi Thanh Tùng',5,'Chất lượng tuyệt vời, thịt chắc và ngọt.',1,'2026-09-24 03:54:37','2026-09-24 03:54:37');
INSERT INTO `reviews` VALUES (39,28,13,'Lý Tuyết Như',4,'Tươi ngon, cả nhà đều thích. Cảm ơn shop!',0,'2026-10-06 03:54:37','2026-10-06 03:54:37');
INSERT INTO `reviews` VALUES (40,28,2,'Lê Hoàng Nam',5,'Tôm còn nhảy luôn, quá tươi! 5 sao.',1,'2026-10-10 03:54:37','2026-10-10 03:54:37');
INSERT INTO `reviews` VALUES (41,30,8,'Bùi Thanh Tùng',3,'Sẽ giới thiệu bạn bè mua ủng hộ shop.',1,'2026-10-06 03:54:37','2026-10-06 03:54:37');
INSERT INTO `reviews` VALUES (42,30,12,'Phan Văn Phú',3,'Chất lượng tuyệt vời, thịt chắc và ngọt.',1,'2026-10-11 03:54:37','2026-10-11 03:54:37');
INSERT INTO `reviews` VALUES (43,31,4,'Trần Văn Dũng',5,'Hàng tươi sống, nhân viên tư vấn nhiệt tình.',1,'2026-09-20 03:54:37','2026-09-20 03:54:37');
INSERT INTO `reviews` VALUES (44,31,11,'Ngô Thị Mai',4,'Sản phẩm ổn, giao đúng giờ, đóng gói chắc chắn.',1,'2026-09-25 03:54:37','2026-09-25 03:54:37');
INSERT INTO `reviews` VALUES (45,31,10,'Hồ Quang Huy',3,'Hải sản rất tươi, đóng gói cẩn thận, sẽ ủng hộ tiếp!',0,'2026-09-19 03:54:37','2026-09-19 03:54:37');
INSERT INTO `reviews` VALUES (46,34,2,'Lê Hoàng Nam',5,'Giao hàng nhanh, hàng tươi đúng như mô tả.',1,'2026-09-18 03:54:37','2026-09-18 03:54:37');
INSERT INTO `reviews` VALUES (47,34,7,'Đỗ Thị Lan Anh',4,'Đặt lần thứ 3 rồi, lần nào cũng hài lòng.',1,'2026-09-12 03:54:37','2026-09-12 03:54:37');
INSERT INTO `reviews` VALUES (48,34,6,'Võ Minh Khôi',5,'Hải sản rất tươi, đóng gói cẩn thận, sẽ ủng hộ tiếp!',0,'2026-09-11 03:54:37','2026-09-11 03:54:37');
INSERT INTO `reviews` VALUES (49,34,7,'Đỗ Thị Lan Anh',5,'Hàng tươi sống, nhân viên tư vấn nhiệt tình.',1,'2026-10-04 03:54:37','2026-10-04 03:54:37');
INSERT INTO `reviews` VALUES (50,38,2,'Lê Hoàng Nam',5,'Chất lượng tuyệt vời, thịt chắc và ngọt.',1,'2026-09-13 03:54:37','2026-09-13 03:54:37');
INSERT INTO `reviews` VALUES (51,38,9,'Đặng Thu Trang',4,'Hải sản rất tươi, đóng gói cẩn thận, sẽ ủng hộ tiếp!',1,'2026-09-28 03:54:37','2026-09-28 03:54:37');
/*!40000 ALTER TABLE `reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'customer',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin@seafood.com','$2y$10$V4sFnSmxwmOA8eq7UF6uTeJagtfjIdtA6.OW9hn7rW1hcgOMxZfbG','Quản Trị Viên','0987654321','123 Đường Hải Sản, Quận 1, TP. HCM','admin',1,NULL,'2026-04-17 03:54:36','2026-10-18 01:13:28');
INSERT INTO `users` VALUES (2,'namlh@gmail.com','$2a$11$9s8uIQs9WwJH27wA9jRCUe/MhmWea7KBcOD051xr5kECIrkunu.Fu','Lê Hoàng Nam','0933111222','789 Lê Lợi, Gò Vấp, TP. HCM','customer',1,NULL,'2026-07-13 03:54:37','2026-10-16 03:54:37');
INSERT INTO `users` VALUES (3,'chipq@gmail.com','$2a$11$9s8uIQs9WwJH27wA9jRCUe/MhmWea7KBcOD051xr5kECIrkunu.Fu','Phạm Quỳnh Chi','0944555666','456 Nguyễn Trãi, Quận 5, TP. HCM','customer',1,NULL,'2026-07-20 03:54:37','2026-10-16 03:54:37');
INSERT INTO `users` VALUES (4,'dungtv@gmail.com','$2a$11$9s8uIQs9WwJH27wA9jRCUe/MhmWea7KBcOD051xr5kECIrkunu.Fu','Trần Văn Dũng','0905001122','12 Trần Hưng Đạo, Quận 1, TP. HCM','customer',1,NULL,'2026-08-01 03:54:37','2026-10-16 03:54:37');
INSERT INTO `users` VALUES (5,'hangnt@gmail.com','$2a$11$9s8uIQs9WwJH27wA9jRCUe/MhmWea7KBcOD051xr5kECIrkunu.Fu','Nguyễn Thị Hằng','0917002233','34 Cách Mạng Tháng 8, Quận 3, TP. HCM','customer',1,NULL,'2026-08-07 03:54:37','2026-10-16 03:54:37');
INSERT INTO `users` VALUES (6,'khoivm@gmail.com','$2a$11$9s8uIQs9WwJH27wA9jRCUe/MhmWea7KBcOD051xr5kECIrkunu.Fu','Võ Minh Khôi','0938003344','56 Phan Xích Long, Phú Nhuận, TP. HCM','customer',1,NULL,'2026-08-13 03:54:37','2026-10-16 03:54:37');
INSERT INTO `users` VALUES (7,'anhdtl@gmail.com','$2a$11$9s8uIQs9WwJH27wA9jRCUe/MhmWea7KBcOD051xr5kECIrkunu.Fu','Đỗ Thị Lan Anh','0969004455','78 Hoàng Văn Thụ, Tân Bình, TP. HCM','customer',1,NULL,'2026-08-19 03:54:37','2026-10-16 03:54:37');
INSERT INTO `users` VALUES (8,'tungbt@gmail.com','$2a$11$9s8uIQs9WwJH27wA9jRCUe/MhmWea7KBcOD051xr5kECIrkunu.Fu','Bùi Thanh Tùng','0922005566','90 Võ Văn Tần, Quận 3, TP. HCM','customer',1,NULL,'2026-08-27 03:54:37','2026-10-16 03:54:37');
INSERT INTO `users` VALUES (9,'trangdt@gmail.com','$2a$11$9s8uIQs9WwJH27wA9jRCUe/MhmWea7KBcOD051xr5kECIrkunu.Fu','Đặng Thu Trang','0911006677','11 Lý Thường Kiệt, Quận 10, TP. HCM','customer',1,NULL,'2026-09-02 03:54:37','2026-10-16 03:54:37');
INSERT INTO `users` VALUES (10,'huyhq@gmail.com','$2a$11$9s8uIQs9WwJH27wA9jRCUe/MhmWea7KBcOD051xr5kECIrkunu.Fu','Hồ Quang Huy','0977007788','23 Nguyễn Văn Cừ, Quận 5, TP. HCM','customer',1,NULL,'2026-09-08 03:54:37','2026-10-16 03:54:37');
INSERT INTO `users` VALUES (11,'maint@gmail.com','$2a$11$9s8uIQs9WwJH27wA9jRCUe/MhmWea7KBcOD051xr5kECIrkunu.Fu','Ngô Thị Mai','0933008899','45 Sư Vạn Hạnh, Quận 10, TP. HCM','customer',1,NULL,'2026-09-16 03:54:37','2026-10-16 03:54:37');
INSERT INTO `users` VALUES (12,'phupv@gmail.com','$2a$11$9s8uIQs9WwJH27wA9jRCUe/MhmWea7KBcOD051xr5kECIrkunu.Fu','Phan Văn Phú','0944009900','67 Điện Biên Phủ, Bình Thạnh, TP. HCM','customer',1,NULL,'2026-09-22 03:54:37','2026-10-16 03:54:37');
INSERT INTO `users` VALUES (13,'nhult@gmail.com','$2a$11$9s8uIQs9WwJH27wA9jRCUe/MhmWea7KBcOD051xr5kECIrkunu.Fu','Lý Tuyết Như','0905010011','89 Lê Văn Sỹ, Tân Bình, TP. HCM','customer',1,NULL,'2026-09-28 03:54:37','2026-10-16 03:54:37');
INSERT INTO `users` VALUES (14,'baotd@gmail.com','$2a$11$9s8uIQs9WwJH27wA9jRCUe/MhmWea7KBcOD051xr5kECIrkunu.Fu','Trương Đình Bảo','0917011122','21 Cộng Hòa, Tân Bình, TP. HCM','customer',0,NULL,'2026-10-04 03:54:37','2026-10-16 03:54:37');
INSERT INTO `users` VALUES (15,'ngocct@gmail.com','$2a$11$9s8uIQs9WwJH27wA9jRCUe/MhmWea7KBcOD051xr5kECIrkunu.Fu','Cao Thị Ngọc','0938012233','43 Nguyễn Thị Minh Khai, Quận 1, TP. HCM','customer',1,NULL,'2026-10-08 03:54:37','2026-10-16 03:54:37');
INSERT INTO `users` VALUES (16,'baodg@gmail.com','$2a$11$9s8uIQs9WwJH27wA9jRCUe/MhmWea7KBcOD051xr5kECIrkunu.Fu','Đinh Gia Bảo','0969013344','65 Trường Chinh, Tân Phú, TP. HCM','customer',1,NULL,'2026-10-12 03:54:37','2026-10-16 03:54:37');
INSERT INTO `users` VALUES (17,'hieukien0303@gmail.com','$2a$11$ds9ccOjmviL7Fx2B4Z.m3.v.y7uwePgxFac3Bz4MLY8Hanj0vZozq','Nguyễn Trung Hiếu','0868701413',NULL,'customer',1,NULL,'2026-10-16 10:20:22','2026-10-16 10:20:22');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vouchers`
--

DROP TABLE IF EXISTS `vouchers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vouchers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount_amount` decimal(18,2) NOT NULL,
  `discount_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fixed',
  `min_order_amount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `expiry_date` datetime NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vouchers_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vouchers`
--

LOCK TABLES `vouchers` WRITE;
/*!40000 ALTER TABLE `vouchers` DISABLE KEYS */;
INSERT INTO `vouchers` VALUES (1,'WELCOME10',10.00,'percentage',300000.00,'2026-12-16 10:54:37',1,'2026-09-16 03:54:37','2026-09-16 03:54:37');
INSERT INTO `vouchers` VALUES (2,'FREESHIP50',50000.00,'fixed',500000.00,'2026-11-15 10:54:37',1,'2026-09-21 03:54:37','2026-09-21 03:54:37');
INSERT INTO `vouchers` VALUES (3,'SALE15',15.00,'percentage',1000000.00,'2026-11-15 10:54:37',1,'2026-09-26 03:54:37','2026-09-26 03:54:37');
INSERT INTO `vouchers` VALUES (4,'GIAM100K',100000.00,'fixed',800000.00,'2026-11-05 10:54:37',1,'2026-10-01 03:54:37','2026-10-01 03:54:37');
INSERT INTO `vouchers` VALUES (5,'CUOITUAN',8.00,'percentage',200000.00,'2026-11-30 10:54:37',1,'2026-10-06 03:54:37','2026-10-06 03:54:37');
INSERT INTO `vouchers` VALUES (6,'VIP200K',200000.00,'fixed',2000000.00,'2027-01-16 10:54:37',1,'2026-10-08 03:54:37','2026-10-08 03:54:37');
INSERT INTO `vouchers` VALUES (7,'TET2025',20.00,'percentage',1500000.00,'2026-10-13 10:54:37',0,'2026-08-17 03:54:37','2026-08-17 03:54:37');
INSERT INTO `vouchers` VALUES (8,'NEWYEAR',30000.00,'fixed',250000.00,'2026-11-15 10:54:37',1,'2026-10-11 03:54:37','2026-10-11 03:54:37');
/*!40000 ALTER TABLE `vouchers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'seafood_store'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-18  8:13:29
