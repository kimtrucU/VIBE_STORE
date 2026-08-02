-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: localhost    Database: vibe_fashion
-- ------------------------------------------------------
-- Server version	8.4.3

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
-- Table structure for table `activity_logs`
--

DROP TABLE IF EXISTS `activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `action` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model_id` bigint unsigned DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `activity_logs_user_id_foreign` (`user_id`),
  CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_logs`
--

LOCK TABLES `activity_logs` WRITE;
/*!40000 ALTER TABLE `activity_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `brands`
--

DROP TABLE IF EXISTS `brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `brands` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `brands_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `brands`
--

LOCK TABLES `brands` WRITE;
/*!40000 ALTER TABLE `brands` DISABLE KEYS */;
/*!40000 ALTER TABLE `brands` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cart_items`
--

DROP TABLE IF EXISTS `cart_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cart_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cart_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `size` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int unsigned NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cart_items_cart_id_foreign` (`cart_id`),
  KEY `cart_items_product_id_foreign` (`product_id`),
  CONSTRAINT `cart_items_cart_id_foreign` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cart_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart_items`
--

LOCK TABLES `cart_items` WRITE;
/*!40000 ALTER TABLE `cart_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `cart_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carts`
--

DROP TABLE IF EXISTS `carts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `carts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `session_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `carts_user_id_foreign` (`user_id`),
  CONSTRAINT `carts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carts`
--

LOCK TABLES `carts` WRITE;
/*!40000 ALTER TABLE `carts` DISABLE KEYS */;
INSERT INTO `carts` VALUES (1,NULL,'dh4Owktqlj3ESNUKX9JsVFQFfCV4WP89dNYWetEk','2026-07-18 00:51:45','2026-07-18 00:51:45'),(2,1,NULL,'2026-07-18 01:09:39','2026-07-18 01:09:39'),(3,NULL,'KLFlLRDvX9DFoKfw8K41Fx8s6Dkx0Cdj4lwln83S','2026-07-18 01:40:02','2026-07-18 01:40:02'),(4,NULL,'yWZZ4RKyFJEf0QvqwnpkJ8uo9NizMvH0FGHyWnBB','2026-07-18 20:00:10','2026-07-18 20:00:10'),(5,NULL,'6qo7ErkzSwMf79bgIvLWdQbqeZ4sMs4MWruhxIas','2026-07-20 22:45:10','2026-07-20 22:45:10');
/*!40000 ALTER TABLE `carts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Sweaters','sweaters','Sweaters collection',NULL,1,'2026-07-18 00:43:13','2026-07-18 00:43:13'),(2,'Bomber','bomber','Bomber collection',NULL,1,'2026-07-18 00:43:13','2026-07-18 00:43:13'),(3,'Accessories','accessories','Accessories collection',NULL,1,'2026-07-18 00:43:13','2026-07-18 00:43:13'),(4,'Flannel','flannel','Flannel collection',NULL,1,'2026-07-18 00:43:13','2026-07-18 00:43:13'),(5,'Handmade','handmade','Handmade collection',NULL,1,'2026-07-18 00:43:13','2026-07-18 00:43:13'),(6,'Slippers','slippers','Slippers collection',NULL,1,'2026-07-18 00:43:13','2026-07-18 00:43:13'),(7,'Zip Hoodies','zip-hoodies','Zip Hoodies collection',NULL,1,'2026-07-18 00:43:13','2026-07-18 00:43:13'),(8,'T-Shirts','t-shirts','T-Shirts collection',NULL,1,'2026-07-18 00:43:13','2026-07-18 00:43:13'),(9,'Loungewear','loungewear','Loungewear collection',NULL,1,'2026-07-18 00:43:13','2026-07-18 00:43:13'),(10,'Shorts','shorts','Shorts collection',NULL,1,'2026-07-18 00:43:13','2026-07-18 00:43:13');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coupons`
--

DROP TABLE IF EXISTS `coupons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `coupons` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('percent','fixed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percent',
  `value` decimal(12,2) NOT NULL,
  `min_order` decimal(12,2) NOT NULL DEFAULT '0.00',
  `max_discount` decimal(12,2) DEFAULT NULL,
  `usage_limit` int unsigned DEFAULT NULL,
  `used_count` int unsigned NOT NULL DEFAULT '0',
  `starts_at` date DEFAULT NULL,
  `expires_at` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `coupons_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coupons`
--

LOCK TABLES `coupons` WRITE;
/*!40000 ALTER TABLE `coupons` DISABLE KEYS */;
/*!40000 ALTER TABLE `coupons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'1999_01_01_000000_create_roles_table',1),(2,'2026_01_01_000000_create_users_table',1),(3,'2026_01_01_000001_create_password_reset_tokens_table',1),(4,'2026_01_01_000002_create_categories_table',1),(5,'2026_01_01_000003_create_products_table',1),(6,'2026_01_01_000004_create_carts_table',1),(7,'2026_01_01_000005_create_wishlists_table',1),(8,'2026_01_01_000006_create_orders_table',1),(9,'2026_07_19_020121_create_cache_table',2),(10,'2026_07_21_000001_extend_orders_add_new_tables',3);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned DEFAULT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_image` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `quantity` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_order_id_foreign` (`order_id`),
  KEY `order_items_product_id_foreign` (`product_id`),
  CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `order_number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','confirmed','processing','shipped','delivered','completed','cancelled','returned') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `payment_method` enum('COD','bank_transfer','momo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'COD',
  `subtotal` decimal(12,2) NOT NULL,
  `shipping_fee` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total` decimal(12,2) NOT NULL,
  `shipping_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `coupon_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `confirmed_at` timestamp NULL DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `shipped_at` timestamp NULL DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `returned_at` timestamp NULL DEFAULT NULL,
  `cancel_reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_order_number_unique` (`order_number`),
  KEY `orders_user_id_foreign` (`user_id`),
  CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint unsigned NOT NULL,
  `brand_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `original_price` decimal(12,2) DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `images` json DEFAULT NULL,
  `sizes` json DEFAULT NULL,
  `details` json DEFAULT NULL,
  `rating` decimal(3,2) NOT NULL DEFAULT '0.00',
  `reviews_count` int unsigned NOT NULL DEFAULT '0',
  `is_new_arrival` tinyint(1) NOT NULL DEFAULT '0',
  `is_best_seller` tinyint(1) NOT NULL DEFAULT '0',
  `stock` int unsigned NOT NULL DEFAULT '100',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_slug_unique` (`slug`),
  KEY `products_category_id_foreign` (`category_id`),
  KEY `products_brand_id_foreign` (`brand_id`),
  CONSTRAINT `products_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL,
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,3,NULL,'Accessories - B1','accessories-b1-1623',450000.00,500000.00,'A high-quality accessories product.','[\"images/products/accessories/b1.webp\", \"images/products/accessories/b1.1.webp\", \"images/products/accessories/b1.2.webp\"]','[\"S\", \"M\", \"L\", \"XL\"]',NULL,4.80,30,0,0,100,1,'2026-07-18 01:02:14','2026-07-18 01:02:14'),(2,3,NULL,'Accessories - B2','accessories-b2-5408',450000.00,500000.00,'A high-quality accessories product.','[\"images/products/accessories/b2.webp\", \"images/products/accessories/b2.1.webp\", \"images/products/accessories/b2.3.webp\"]','[\"S\", \"M\", \"L\", \"XL\"]',NULL,4.80,20,0,0,100,1,'2026-07-18 01:02:14','2026-07-18 01:02:14'),(3,3,NULL,'Accessories - B3','accessories-b3-8500',450000.00,500000.00,'A high-quality accessories product.','[\"images/products/accessories/b3.webp\", \"images/products/accessories/b3.1.webp\", \"images/products/accessories/b3.2.webp\"]','[\"S\", \"M\", \"L\", \"XL\"]',NULL,4.80,42,0,0,100,1,'2026-07-18 01:02:14','2026-07-18 01:02:14'),(4,3,NULL,'Accessories - B4','accessories-b4-9593',450000.00,500000.00,'A high-quality accessories product.','[\"images/products/accessories/b4.webp\", \"images/products/accessories/b4.1.webp\", \"images/products/accessories/b4.2.webp\", \"images/products/accessories/b4.3.webp\"]','[\"S\", \"M\", \"L\", \"XL\"]',NULL,4.80,36,0,0,100,1,'2026-07-18 01:02:14','2026-07-18 01:02:14'),(5,3,NULL,'Accessories - B5','accessories-b5-1496',450000.00,500000.00,'A high-quality accessories product.','[\"images/products/accessories/b5.webp\", \"images/products/accessories/b5.1.webp\", \"images/products/accessories/b5.2.webp\", \"images/products/accessories/b5.3.webp\"]','[\"S\", \"M\", \"L\", \"XL\"]',NULL,4.80,47,0,0,100,1,'2026-07-18 01:02:14','2026-07-18 01:02:14'),(6,3,NULL,'Accessories - B6','accessories-b6-5395',450000.00,500000.00,'A high-quality accessories product.','[\"images/products/accessories/b6.webp\", \"images/products/accessories/b6.1.webp\", \"images/products/accessories/b6.2.webp\", \"images/products/accessories/b6.3.webp\", \"images/products/accessories/b6.4.webp\", \"images/products/accessories/b6.6.webp\"]','[\"S\", \"M\", \"L\", \"XL\"]',NULL,4.80,33,0,0,100,1,'2026-07-18 01:02:14','2026-07-18 01:02:14'),(7,2,NULL,'Bomber - C1','bomber-c1-2308',450000.00,500000.00,'A high-quality bomber product.','[\"images/products/bomber/c1.webp\", \"images/products/bomber/c1.1.webp\"]','[\"S\", \"M\", \"L\", \"XL\"]',NULL,4.80,23,0,0,100,1,'2026-07-18 01:02:14','2026-07-18 01:02:14'),(8,2,NULL,'Bomber - C2','bomber-c2-7194',450000.00,500000.00,'A high-quality bomber product.','[\"images/products/bomber/c2.webp\", \"images/products/bomber/c2.1.webp\", \"images/products/bomber/c2.2.webp\"]','[\"S\", \"M\", \"L\", \"XL\"]',NULL,4.80,38,0,0,100,1,'2026-07-18 01:02:14','2026-07-18 01:02:14'),(9,2,NULL,'Bomber - C3','bomber-c3-3869',450000.00,500000.00,'A high-quality bomber product.','[\"images/products/bomber/c3.webp\", \"images/products/bomber/c3.1.webp\", \"images/products/bomber/c3.2.webp\"]','[\"S\", \"M\", \"L\", \"XL\"]',NULL,4.80,19,0,0,100,1,'2026-07-18 01:02:14','2026-07-18 01:02:14'),(10,2,NULL,'Bomber - C4','bomber-c4-1056',450000.00,500000.00,'A high-quality bomber product.','[\"images/products/bomber/c4.webp\", \"images/products/bomber/c4.2.jpg\", \"images/products/bomber/c4.1.webp\", \"images/products/bomber/c4.3.webp\"]','[\"S\", \"M\", \"L\", \"XL\"]',NULL,4.80,39,0,0,100,1,'2026-07-18 01:02:14','2026-07-18 01:02:14'),(11,2,NULL,'Bomber - C5','bomber-c5-8937',450000.00,500000.00,'A high-quality bomber product.','[\"images/products/bomber/c5.webp\", \"images/products/bomber/c5.1.webp\", \"images/products/bomber/c5.2.webp\", \"images/products/bomber/c5.3.webp\"]','[\"S\", \"M\", \"L\", \"XL\"]',NULL,4.80,50,0,0,100,1,'2026-07-18 01:02:14','2026-07-18 01:02:14'),(12,4,NULL,'Flannel - E1','flannel-e1-1934',450000.00,500000.00,'A high-quality flannel product.','[\"images/products/flannel/e1.webp\", \"images/products/flannel/e1.3.jpg\", \"images/products/flannel/e1.1.webp\", \"images/products/flannel/e1.2.webp\"]','[\"S\", \"M\", \"L\", \"XL\"]',NULL,4.80,40,0,0,100,1,'2026-07-18 01:02:14','2026-07-18 01:02:14'),(13,4,NULL,'Flannel - E2','flannel-e2-2193',450000.00,500000.00,'A high-quality flannel product.','[\"images/products/flannel/e2.webp\", \"images/products/flannel/e2.1.webp\", \"images/products/flannel/e2.3.webp\", \"images/products/flannel/e2.4.webp\"]','[\"S\", \"M\", \"L\", \"XL\"]',NULL,4.80,35,0,0,100,1,'2026-07-18 01:02:14','2026-07-18 01:02:14'),(14,4,NULL,'Flannel - E3','flannel-e3-7941',450000.00,500000.00,'A high-quality flannel product.','[\"images/products/flannel/e3.webp\", \"images/products/flannel/e3.1.webp\", \"images/products/flannel/e3.2.webp\", \"images/products/flannel/e3.3.webp\"]','[\"S\", \"M\", \"L\", \"XL\"]',NULL,4.80,40,0,0,100,1,'2026-07-18 01:02:14','2026-07-18 01:02:14'),(15,5,NULL,'Handmade - G1','handmade-g1-8490',450000.00,500000.00,'A high-quality handmade product.','[\"images/products/handmade/g1.webp\", \"images/products/handmade/g1.1.webp\"]','[\"S\", \"M\", \"L\", \"XL\"]',NULL,4.80,11,0,0,100,1,'2026-07-18 01:02:14','2026-07-18 01:02:14'),(16,5,NULL,'Handmade - G2','handmade-g2-8401',450000.00,500000.00,'A high-quality handmade product.','[\"images/products/handmade/g2.webp\", \"images/products/handmade/g2.1.webp\"]','[\"S\", \"M\", \"L\", \"XL\"]',NULL,4.80,41,0,0,100,1,'2026-07-18 01:02:14','2026-07-18 01:02:14'),(17,5,NULL,'Handmade - G3','handmade-g3-2405',450000.00,500000.00,'A high-quality handmade product.','[\"images/products/handmade/g3.webp\", \"images/products/handmade/g3.1.webp\"]','[\"S\", \"M\", \"L\", \"XL\"]',NULL,4.80,28,0,0,100,1,'2026-07-18 01:02:14','2026-07-18 01:02:14'),(18,10,NULL,'Shorts - A1','shorts-a1-6706',450000.00,500000.00,'A high-quality shorts product.','[\"images/products/knit-shorts/a1.webp\", \"images/products/knit-shorts/a1.1.webp\", \"images/products/knit-shorts/a1.2.webp\"]','[\"S\", \"M\", \"L\", \"XL\"]',NULL,4.80,44,0,0,100,1,'2026-07-18 01:02:14','2026-07-18 01:02:14'),(19,10,NULL,'Shorts - A2','shorts-a2-2619',450000.00,500000.00,'A high-quality shorts product.','[\"images/products/knit-shorts/a2.webp\", \"images/products/knit-shorts/a2.1.jpg\", \"images/products/knit-shorts/a2.2.webp\"]','[\"S\", \"M\", \"L\", \"XL\"]',NULL,4.80,49,0,0,100,1,'2026-07-18 01:02:14','2026-07-18 01:02:14'),(20,10,NULL,'Shorts - A3','shorts-a3-7862',450000.00,500000.00,'A high-quality shorts product.','[\"images/products/knit-shorts/a3.webp\", \"images/products/knit-shorts/a3.2.webp\", \"images/products/knit-shorts/a3.3.webp\"]','[\"S\", \"M\", \"L\", \"XL\"]',NULL,4.80,12,0,0,100,1,'2026-07-18 01:02:14','2026-07-18 01:02:14'),(21,10,NULL,'Shorts - A4','shorts-a4-9983',450000.00,500000.00,'A high-quality shorts product.','[\"images/products/knit-shorts/a4.webp\", \"images/products/knit-shorts/a4.1.webp\"]','[\"S\", \"M\", \"L\", \"XL\"]',NULL,4.80,25,0,0,100,1,'2026-07-18 01:02:14','2026-07-18 01:02:14'),(22,1,NULL,'Sweaters - 1','sweaters-1-4879',450000.00,500000.00,'A high-quality sweaters product.','[\"images/products/long-sleeve/1.webp\", \"images/products/long-sleeve/1.1.webp\"]','[\"S\", \"M\", \"L\", \"XL\"]',NULL,4.80,24,0,0,100,1,'2026-07-18 01:02:14','2026-07-18 01:02:14'),(23,1,NULL,'Sweaters - 2','sweaters-2-5739',450000.00,500000.00,'A high-quality sweaters product.','[\"images/products/long-sleeve/2.webp\", \"images/products/long-sleeve/2.1.webp\"]','[\"S\", \"M\", \"L\", \"XL\"]',NULL,4.80,19,0,0,100,1,'2026-07-18 01:02:14','2026-07-18 01:02:14'),(24,1,NULL,'Sweaters - 3','sweaters-3-8783',450000.00,500000.00,'A high-quality sweaters product.','[\"images/products/long-sleeve/3.webp\", \"images/products/long-sleeve/3.1.jpg\"]','[\"S\", \"M\", \"L\", \"XL\"]',NULL,4.80,15,0,0,100,1,'2026-07-18 01:02:14','2026-07-18 01:02:14'),(25,1,NULL,'Sweaters - 4','sweaters-4-4538',450000.00,500000.00,'A high-quality sweaters product.','[\"images/products/long-sleeve/4.webp\", \"images/products/long-sleeve/4.1.jpg\"]','[\"S\", \"M\", \"L\", \"XL\"]',NULL,4.80,36,0,0,100,1,'2026-07-18 01:02:14','2026-07-18 01:02:14'),(26,1,NULL,'Sweaters - 5','sweaters-5-7071',450000.00,500000.00,'A high-quality sweaters product.','[\"images/products/long-sleeve/5.webp\", \"images/products/long-sleeve/5.1.jpg\"]','[\"S\", \"M\", \"L\", \"XL\"]',NULL,4.80,13,0,0,100,1,'2026-07-18 01:02:14','2026-07-18 01:02:14'),(27,9,NULL,'Loungewear - 10','loungewear-10-8987',450000.00,500000.00,'A high-quality loungewear product.','[\"images/products/loungewear/10.webp\", \"images/products/loungewear/10.1.webp\"]','[\"S\", \"M\", \"L\", \"XL\"]',NULL,4.80,45,0,0,100,1,'2026-07-18 01:02:14','2026-07-18 01:02:14'),(28,9,NULL,'Loungewear - 11','loungewear-11-9677',450000.00,500000.00,'A high-quality loungewear product.','[\"images/products/loungewear/11.webp\", \"images/products/loungewear/11.1.webp\", \"images/products/loungewear/11.2.webp\"]','[\"S\", \"M\", \"L\", \"XL\"]',NULL,4.80,38,0,0,100,1,'2026-07-18 01:02:14','2026-07-18 01:02:14'),(29,9,NULL,'Loungewear - 12','loungewear-12-6659',450000.00,500000.00,'A high-quality loungewear product.','[\"images/products/loungewear/12.webp\", \"images/products/loungewear/12.1.webp\", \"images/products/loungewear/12.2.webp\"]','[\"S\", \"M\", \"L\", \"XL\"]',NULL,4.80,39,0,0,100,1,'2026-07-18 01:02:14','2026-07-18 01:02:14'),(30,9,NULL,'Loungewear - 13','loungewear-13-3941',450000.00,500000.00,'A high-quality loungewear product.','[\"images/products/loungewear/13.webp\", \"images/products/loungewear/13.1.jpg\", \"images/products/loungewear/13.2.webp\"]','[\"S\", \"M\", \"L\", \"XL\"]',NULL,4.80,21,0,0,100,1,'2026-07-18 01:02:14','2026-07-18 01:02:14'),(31,9,NULL,'Loungewear - 6','loungewear-6-7349',450000.00,500000.00,'A high-quality loungewear product.','[\"images/products/loungewear/6.webp\", \"images/products/loungewear/6.1.jpg\"]','[\"S\", \"M\", \"L\", \"XL\"]',NULL,4.80,35,0,0,100,1,'2026-07-18 01:02:14','2026-07-18 01:02:14'),(32,9,NULL,'Loungewear - 7','loungewear-7-9716',450000.00,500000.00,'A high-quality loungewear product.','[\"images/products/loungewear/7.webp\", \"images/products/loungewear/7.1.webp\"]','[\"S\", \"M\", \"L\", \"XL\"]',NULL,4.80,32,0,0,100,1,'2026-07-18 01:02:14','2026-07-18 01:02:14'),(33,9,NULL,'Loungewear - 8','loungewear-8-4546',450000.00,500000.00,'A high-quality loungewear product.','[\"images/products/loungewear/8.webp\", \"images/products/loungewear/8.1.webp\"]','[\"S\", \"M\", \"L\", \"XL\"]',NULL,4.80,17,0,0,100,1,'2026-07-18 01:02:14','2026-07-18 01:02:14'),(34,9,NULL,'Loungewear - 9','loungewear-9-7050',450000.00,500000.00,'A high-quality loungewear product.','[\"images/products/loungewear/9.webp\", \"images/products/loungewear/9.1.webp\"]','[\"S\", \"M\", \"L\", \"XL\"]',NULL,4.80,49,0,0,100,1,'2026-07-18 01:02:14','2026-07-18 01:02:14'),(35,10,NULL,'Shorts - 14','shorts-14-4939',450000.00,500000.00,'A high-quality shorts product.','[\"images/products/shorts/14.webp\", \"images/products/shorts/14.1.jpg\"]','[\"S\", \"M\", \"L\", \"XL\"]',NULL,4.80,30,0,0,100,1,'2026-07-18 01:02:14','2026-07-18 01:02:14'),(36,10,NULL,'Shorts - 15','shorts-15-7851',450000.00,500000.00,'A high-quality shorts product.','[\"images/products/shorts/15.webp\"]','[\"S\", \"M\", \"L\", \"XL\"]',NULL,4.80,25,0,0,100,1,'2026-07-18 01:02:14','2026-07-18 01:02:14'),(37,10,NULL,'Shorts - 16','shorts-16-8287',450000.00,500000.00,'A high-quality shorts product.','[\"images/products/shorts/16.webp\", \"images/products/shorts/16.1.webp\", \"images/products/shorts/16.2.webp\"]','[\"S\", \"M\", \"L\", \"XL\"]',NULL,4.80,27,0,0,100,1,'2026-07-18 01:02:14','2026-07-18 01:02:14'),(38,10,NULL,'Shorts - 17','shorts-17-9146',450000.00,500000.00,'A high-quality shorts product.','[\"images/products/shorts/17.webp\", \"images/products/shorts/17.1.webp\", \"images/products/shorts/17.2.webp\"]','[\"S\", \"M\", \"L\", \"XL\"]',NULL,4.80,41,0,0,100,1,'2026-07-18 01:02:14','2026-07-18 01:02:14'),(39,10,NULL,'Shorts - 18','shorts-18-7195',450000.00,500000.00,'A high-quality shorts product.','[\"images/products/shorts/18.webp\", \"images/products/shorts/18.1.webp\", \"images/products/shorts/18.2.webp\"]','[\"S\", \"M\", \"L\", \"XL\"]',NULL,4.80,14,0,0,100,1,'2026-07-18 01:02:14','2026-07-18 01:02:14'),(40,10,NULL,'Shorts - 19','shorts-19-6934',450000.00,500000.00,'A high-quality shorts product.','[\"images/products/shorts/19.webp\", \"images/products/shorts/19.1.webp\", \"images/products/shorts/19.2.webp\"]','[\"S\", \"M\", \"L\", \"XL\"]',NULL,4.80,18,0,0,100,1,'2026-07-18 01:02:14','2026-07-18 01:02:14'),(41,10,NULL,'Shorts - 20','shorts-20-2438',450000.00,500000.00,'A high-quality shorts product.','[\"images/products/shorts/20.webp\", \"images/products/shorts/20.1.webp\", \"images/products/shorts/20.2.webp\"]','[\"S\", \"M\", \"L\", \"XL\"]',NULL,4.80,21,0,0,100,1,'2026-07-18 01:02:14','2026-07-18 01:02:14'),(42,6,NULL,'Slippers - D1','slippers-d1-6074',450000.00,500000.00,'A high-quality slippers product.','[\"images/products/slippers/d1.webp\", \"images/products/slippers/d1.1.webp\", \"images/products/slippers/d1.2.webp\", \"images/products/slippers/d1.3.webp\"]','[\"S\", \"M\", \"L\", \"XL\"]',NULL,4.80,24,0,0,100,1,'2026-07-18 01:02:14','2026-07-18 01:02:14'),(43,6,NULL,'Slippers - D2','slippers-d2-5113',450000.00,500000.00,'A high-quality slippers product.','[\"images/products/slippers/d2.webp\", \"images/products/slippers/d2.1.webp\", \"images/products/slippers/d2.2.webp\", \"images/products/slippers/d2.3.webp\"]','[\"S\", \"M\", \"L\", \"XL\"]',NULL,4.80,39,0,0,100,1,'2026-07-18 01:02:14','2026-07-18 01:02:14'),(44,6,NULL,'Slippers - D3','slippers-d3-3785',450000.00,500000.00,'A high-quality slippers product.','[\"images/products/slippers/d3.webp\", \"images/products/slippers/d3.1.webp\", \"images/products/slippers/d3.2.webp\", \"images/products/slippers/d3.3.webp\", \"images/products/slippers/d3.4.webp\"]','[\"S\", \"M\", \"L\", \"XL\"]',NULL,4.80,21,0,0,100,1,'2026-07-18 01:02:14','2026-07-18 01:02:14'),(45,7,NULL,'Zip Hoodies - 21','zip-hoodies-21-7294',450000.00,500000.00,'A high-quality zip hoodies product.','[\"images/products/zip-hoodie/21.webp\", \"images/products/zip-hoodie/21.1.webp\"]','[\"S\", \"M\", \"L\", \"XL\"]',NULL,4.80,28,0,0,100,1,'2026-07-18 01:02:14','2026-07-18 01:02:14'),(46,7,NULL,'Zip Hoodies - 22','zip-hoodies-22-6004',450000.00,500000.00,'A high-quality zip hoodies product.','[\"images/products/zip-hoodie/22.webp\", \"images/products/zip-hoodie/22.1.webp\"]','[\"S\", \"M\", \"L\", \"XL\"]',NULL,4.80,30,0,0,100,1,'2026-07-18 01:02:14','2026-07-18 01:02:14'),(47,7,NULL,'Zip Hoodies - 23','zip-hoodies-23-5107',450000.00,500000.00,'A high-quality zip hoodies product.','[\"images/products/zip-hoodie/23.webp\", \"images/products/zip-hoodie/23.1.webp\"]','[\"S\", \"M\", \"L\", \"XL\"]',NULL,4.80,36,0,0,100,1,'2026-07-18 01:02:14','2026-07-18 01:02:14'),(48,7,NULL,'Zip Hoodies - 24','zip-hoodies-24-5174',450000.00,500000.00,'A high-quality zip hoodies product.','[\"images/products/zip-hoodie/24.webp\", \"images/products/zip-hoodie/24.1.webp\"]','[\"S\", \"M\", \"L\", \"XL\"]',NULL,4.80,35,0,0,100,1,'2026-07-18 01:02:14','2026-07-18 01:02:14'),(49,7,NULL,'Zip Hoodies - 25','zip-hoodies-25-9544',450000.00,500000.00,'A high-quality zip hoodies product.','[\"images/products/zip-hoodie/25.webp\", \"images/products/zip-hoodie/25.1.jpg\"]','[\"S\", \"M\", \"L\", \"XL\"]',NULL,4.80,27,0,0,100,1,'2026-07-18 01:02:14','2026-07-18 01:02:14'),(50,7,NULL,'Zip Hoodies - 26','zip-hoodies-26-2978',450000.00,500000.00,'A high-quality zip hoodies product.','[\"images/products/zip-hoodie/26.webp\", \"images/products/zip-hoodie/26.1.jpg\"]','[\"S\", \"M\", \"L\", \"XL\"]',NULL,4.80,34,0,0,100,1,'2026-07-18 01:02:14','2026-07-18 01:02:14'),(51,8,NULL,'T-Shirts - 1','t-shirts-1-2567',450000.00,500000.00,'A high-quality sweaters product.','[\"images/products/long-sleeve/1.webp\", \"images/products/long-sleeve/1.1.webp\"]','[\"S\", \"M\", \"L\", \"XL\"]',NULL,4.80,24,0,0,100,1,'2026-07-18 01:02:14','2026-07-18 01:02:14'),(52,8,NULL,'T-Shirts - 2','t-shirts-2-9335',450000.00,500000.00,'A high-quality sweaters product.','[\"images/products/long-sleeve/2.webp\", \"images/products/long-sleeve/2.1.webp\"]','[\"S\", \"M\", \"L\", \"XL\"]',NULL,4.80,19,0,0,100,1,'2026-07-18 01:02:14','2026-07-18 01:02:14');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reviews` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `rating` tinyint unsigned NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `is_approved` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reviews_product_id_foreign` (`product_id`),
  KEY `reviews_user_id_foreign` (`user_id`),
  CONSTRAINT `reviews_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reviews`
--

LOCK TABLES `reviews` WRITE;
/*!40000 ALTER TABLE `reviews` DISABLE KEYS */;
/*!40000 ALTER TABLE `reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'admin',NULL,NULL,NULL),(2,'customer',NULL,NULL,NULL);
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `group` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'site_name','Vibe Fashion','general','2026-07-20 23:43:08','2026-07-20 23:43:08'),(2,'site_email','contact@vibe.com','general','2026-07-20 23:43:08','2026-07-20 23:43:08'),(3,'site_phone','+84 901 234 567','general','2026-07-20 23:43:08','2026-07-20 23:43:08'),(4,'shipping_fee','30000','shipping','2026-07-20 23:43:08','2026-07-20 23:43:08'),(5,'free_shipping_threshold','500000','shipping','2026-07-20 23:43:08','2026-07-20 23:43:08'),(6,'currency','VND','general','2026-07-20 23:43:08','2026-07-20 23:43:08');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_id_foreign` (`role_id`),
  CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Administrator','admin@vibe.com',NULL,'$2y$12$xfTVk4GaRrzc0kkimQX/8uSu4xq7FgL6u5UdUYlH8dhRx40cJFZeC',1,NULL,1,NULL,NULL,NULL,NULL,'2026-07-18 00:42:12','2026-07-18 01:08:48'),(2,'Customer','customer@vibe.com',NULL,'$2y$12$xfTVk4GaRrzc0kkimQX/8uSu4xq7FgL6u5UdUYlH8dhRx40cJFZeC',2,NULL,1,NULL,NULL,NULL,NULL,'2026-07-18 00:42:12','2026-07-18 01:08:48');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wishlists`
--

DROP TABLE IF EXISTS `wishlists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wishlists` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `wishlists_user_id_product_id_unique` (`user_id`,`product_id`),
  KEY `wishlists_product_id_foreign` (`product_id`),
  CONSTRAINT `wishlists_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `wishlists_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wishlists`
--

LOCK TABLES `wishlists` WRITE;
/*!40000 ALTER TABLE `wishlists` DISABLE KEYS */;
/*!40000 ALTER TABLE `wishlists` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-07-21 13:44:05
