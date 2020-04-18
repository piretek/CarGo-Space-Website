<?php

if (!defined('SECURE_BOOT')) exit();

$schema = [];

$schema[] = "
CREATE TABLE `brands` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$schema[] = "
CREATE TABLE `cars` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model` int(11) NOT NULL,
  `year` year(4) NOT NULL,
  `engine` varchar(20) CHARACTER SET utf8 NOT NULL,
  `fuel` varchar(15) CHARACTER SET utf8 NOT NULL,
  `clutch` varchar(20) CHARACTER SET utf8 NOT NULL,
  `registration` varchar(10) CHARACTER SET utf8 NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(100) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cars_models` (`model`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$schema[] = "
CREATE TABLE `clients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) CHARACTER SET utf8 NOT NULL,
  `surname` varchar(25) CHARACTER SET utf8 NOT NULL,
  `city` varchar(25) CHARACTER SET utf8 NOT NULL,
  `street` varchar(40) CHARACTER SET utf8 NOT NULL,
  `number` varchar(10) CHARACTER SET utf8 NOT NULL,
  `phone` varchar(9) CHARACTER SET utf8 NOT NULL,
  `email` varchar(30) CHARACTER SET utf8 NOT NULL,
  `pesel` varchar(11) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pesel` (`pesel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$schema[] = "
CREATE TABLE `models` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `brand` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `model` varchar(30) CHARACTER SET utf8 NOT NULL,
  `year_from` year(4) NOT NULL,
  `year_to` year(4) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `models_brands` (`brand`),
  KEY `models_types` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$schema[] = "
CREATE TABLE `rents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client` int(11) NOT NULL,
  `car` int(11) NOT NULL,
  `begin` int(11) NOT NULL,
  `end` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `created_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `rents_cars` (`car`),
  KEY `rents_clients` (`client`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$schema[] = "
CREATE TABLE `types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$schema[] = "
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) CHARACTER SET utf8 NOT NULL,
  `password` varchar(80) CHARACTER SET utf8 NOT NULL,
  `firstname` varchar(20) CHARACTER SET utf8 NOT NULL,
  `lastname` varchar(30) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$schema[] = "
ALTER TABLE `cars`
  ADD CONSTRAINT `cars_models` FOREIGN KEY (`model`) REFERENCES `models` (`id`);";

$schema[] = "
ALTER TABLE `models`
  ADD CONSTRAINT `models_brands` FOREIGN KEY (`brand`) REFERENCES `brands` (`id`),
  ADD CONSTRAINT `models_types` FOREIGN KEY (`type`) REFERENCES `types` (`id`);";

$schema[] = "
ALTER TABLE `rents`
  ADD CONSTRAINT `rents_cars` FOREIGN KEY (`car`) REFERENCES `cars` (`id`),
  ADD CONSTRAINT `rents_clients` FOREIGN KEY (`client`) REFERENCES `clients` (`id`);";

return $schema;
