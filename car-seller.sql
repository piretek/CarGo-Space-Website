-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Czas generowania: 05 Kwi 2020, 17:41
-- Wersja serwera: 10.4.10-MariaDB
-- Wersja PHP: 7.3.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `car-seller`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `brands`
--

DROP TABLE IF EXISTS `brands`;
CREATE TABLE IF NOT EXISTS `brands` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Zrzut danych tabeli `brands`
--

INSERT INTO `brands` (`id`, `name`) VALUES
(1, 'Audi'),
(2, 'BMW');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `cars`
--

DROP TABLE IF EXISTS `cars`;
CREATE TABLE IF NOT EXISTS `cars` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model` int(11) NOT NULL,
  `year` year(4) NOT NULL,
  `engine` varchar(20) NOT NULL,
  `clutch` varchar(20) NOT NULL,
  `registration` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cars_models` (`model`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Zrzut danych tabeli `cars`
--

INSERT INTO `cars` (`id`, `model`, `year`, `engine`, `clutch`, `registration`) VALUES
(1, 2, 2000, '1.9 tdi', 'manual', 'LU 1612C'),
(2, 2, 2001, '2.0 tdi', 'manual', 'LUB 1730K');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `clients`
--

DROP TABLE IF EXISTS `clients`;
CREATE TABLE IF NOT EXISTS `clients` (
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `models`
--

DROP TABLE IF EXISTS `models`;
CREATE TABLE IF NOT EXISTS `models` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `brand` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `model` varchar(30) NOT NULL,
  `year_from` year(4) NOT NULL,
  `year_to` year(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `models_brands` (`brand`),
  KEY `models_types` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Zrzut danych tabeli `models`
--

INSERT INTO `models` (`id`, `brand`, `type`, `model`, `year_from`, `year_to`) VALUES
(2, 1, 1, 'A4 I', 1994, 2001);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `rents`
--

DROP TABLE IF EXISTS `rents`;
CREATE TABLE IF NOT EXISTS `rents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client` int(11) NOT NULL,
  `car` int(11) NOT NULL,
  `from` int(11) NOT NULL,
  `to` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `rents_cars` (`car`),
  KEY `rents_clients` (`client`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `types`
--

DROP TABLE IF EXISTS `types`;
CREATE TABLE IF NOT EXISTS `types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Zrzut danych tabeli `types`
--

INSERT INTO `types` (`id`, `name`) VALUES
(1, 'Combi');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) NOT NULL,
  `password` varchar(80) NOT NULL,
  `firstname` varchar(20) NOT NULL,
  `lastname` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4;

--
-- Ograniczenia dla zrzutów tabel
--

--
-- Ograniczenia dla tabeli `cars`
--
ALTER TABLE `cars`
  ADD CONSTRAINT `cars_models` FOREIGN KEY (`model`) REFERENCES `models` (`id`);

--
-- Ograniczenia dla tabeli `models`
--
ALTER TABLE `models`
  ADD CONSTRAINT `models_brands` FOREIGN KEY (`brand`) REFERENCES `brands` (`id`),
  ADD CONSTRAINT `models_types` FOREIGN KEY (`type`) REFERENCES `types` (`id`);

--
-- Ograniczenia dla tabeli `rents`
--
ALTER TABLE `rents`
  ADD CONSTRAINT `rents_cars` FOREIGN KEY (`car`) REFERENCES `cars` (`id`),
  ADD CONSTRAINT `rents_clients` FOREIGN KEY (`client`) REFERENCES `clients` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
