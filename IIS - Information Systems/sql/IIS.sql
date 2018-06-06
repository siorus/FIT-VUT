-- phpMyAdmin SQL Dump
-- version 4.6.6deb4
-- https://www.phpmyadmin.net/
--
-- Hostiteľ: localhost:3306
-- Čas generovania: Pi 01.Dec 2017, 12:06
-- Verzia serveru: 5.7.20
-- Verzia PHP: 7.0.19-1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáza: `IIS`
--

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `ingredience`
--

CREATE TABLE `ingredience` (
  `id_ingredience` int(11) NOT NULL,
  `nazev_ingredience` varchar(30) COLLATE utf8_czech_ci NOT NULL,
  `hide_ingred` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Sťahujem dáta pre tabuľku `ingredience`
--

INSERT INTO `ingredience` (`id_ingredience`, `nazev_ingredience`, `hide_ingred`) VALUES
(1, 'Brambory', 0),
(2, 'Rýže', 0),
(3, 'Penne', 0),
(4, 'Voda', 0),
(5, 'Drcená rajčata', 0),
(6, 'Šunka', 0),
(7, 'Mozzarella', 0),
(8, 'Hermelín', 0),
(9, 'Parmezán', 0),
(10, 'Žampiony', 0),
(11, 'Kuřecí­ prsa', 0),
(12, 'Vepřové koleno', 0),
(13, 'Hovězí svíčková', 0),
(14, 'Smetana', 0),
(15, 'Gorgonzola', 0),
(16, 'Droždí', 0),
(17, 'Mléko', 0),
(18, 'Olivový olej', 0),
(19, 'Slunečnicový olej', 0),
(20, 'Sůl', 0),
(21, 'Olivy', 0),
(22, 'Kukuřice', 0),
(23, 'Vejce', 0),
(24, 'Cibule', 0),
(25, 'Pancetta', 0),
(26, 'Broskev', 0),
(27, 'Eidam', 0),
(28, 'Filet z candáta', 0),
(29, 'Filet z lososa', 0),
(30, 'Máslo', 0),
(31, 'Rozmarýn', 0),
(32, 'Soda', 0),
(33, 'Kofola', 0),
(34, 'Fanta', 0),
(35, 'Merlot', 0),
(36, 'Lambrusco', 0),
(37, 'Cinzano', 0),
(38, 'Svijany 13', 0),
(39, 'Pilsner Urquell', 0),
(40, 'Citron', 0),
(41, 'Tonic', 0),
(42, 'Jablečný džus', 0),
(43, 'Pomerančový džus', 0),
(44, 'Jahodovy dzus', 0),
(45, 'Mouka', 0),
(46, 'Bazalka', 0),
(47, 'Salám', 0),
(49, 'Brambůrky', 0),
(50, 'Arašídy', 0),
(51, 'Hranolky', 0),
(52, 'Špagety', 0),
(53, 'Tvarůžky', 0);

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `objednana_polozka`
--

CREATE TABLE `objednana_polozka` (
  `id_objednavka` int(11) NOT NULL,
  `status` varchar(15) COLLATE utf8_czech_ci NOT NULL DEFAULT 'objednano',
  `datum` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `nazev` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `cena` decimal(8,2) DEFAULT NULL,
  `id_menu` int(11) NOT NULL,
  `id_zamestnanec` int(11) NOT NULL,
  `id_stul` int(11) NOT NULL,
  `id_rezervace` int(11) DEFAULT NULL,
  `id_uctenka` int(11) DEFAULT NULL,
  `hide_objednana` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Sťahujem dáta pre tabuľku `objednana_polozka`
--

INSERT INTO `objednana_polozka` (`id_objednavka`, `status`, `datum`, `nazev`, `cena`, `id_menu`, `id_zamestnanec`, `id_stul`, `id_rezervace`, `id_uctenka`, `hide_objednana`) VALUES
(1, 'objednano', '2017-12-01 01:57:57', 'Pizza Margherita', '129.00', 1, 1, 1, NULL, NULL, 0),
(2, 'zuctovano', '2017-12-01 01:58:18', 'Merlot', '45.00', 11, 1, 22, NULL, 1, 0),
(3, 'zuctovano', '2017-12-01 01:58:23', 'Merlot', '45.00', 11, 1, 22, NULL, 1, 0),
(4, 'zuctovano', '2017-12-01 01:58:27', 'Grilovaná hovězi svíčková s americkými bramborami', '250.00', 7, 1, 22, NULL, 1, 0),
(5, 'zuctovano', '2017-12-01 01:58:32', 'Smažené kuřecí prsa s bramborami', '179.00', 5, 1, 22, NULL, 1, 0),
(6, 'zuctovano', '2017-12-01 01:58:37', 'Voda s citronem', '35.00', 17, 1, 22, NULL, 1, 0),
(7, 'objednano', '2017-12-01 02:07:18', 'Španělský ptáček', '169.00', 21, 1, 2, NULL, NULL, 0),
(8, 'objednano', '2017-12-01 02:07:25', 'Rýže', '29.00', 22, 1, 2, NULL, NULL, 0),
(9, 'objednano', '2017-12-01 02:07:40', 'Pečené vepřové koleno', '219.00', 20, 1, 2, NULL, NULL, 0),
(10, 'objednano', '2017-12-01 02:07:44', 'Hranolky', '35.00', 23, 1, 2, NULL, NULL, 0),
(11, 'objednano', '2017-12-01 02:07:54', 'Kofola', '30.00', 9, 1, 2, NULL, NULL, 0),
(12, 'objednano', '2017-12-01 02:07:59', 'Fanta', '28.00', 10, 1, 2, NULL, NULL, 0),
(13, 'objednano', '2017-12-01 02:08:28', 'Lambrusco', '40.00', 12, 1, 4, NULL, NULL, 0),
(14, 'objednano', '2017-12-01 02:08:35', 'Merlot', '45.00', 11, 1, 4, NULL, NULL, 0),
(16, 'objednano', '2017-12-01 02:48:24', 'Brambůrky', '29.00', 24, 1, 4, NULL, NULL, 0),
(17, 'objednano', '2017-12-01 02:48:31', 'Pizza Margherita', '129.00', 1, 1, 5, NULL, NULL, 0),
(18, 'objednano', '2017-12-01 02:48:34', 'Pizza Salami', '135.00', 3, 1, 5, NULL, NULL, 0),
(19, 'objednano', '2017-12-01 02:48:37', 'Voda s citronem', '35.00', 17, 1, 5, NULL, NULL, 0),
(20, 'objednano', '2017-12-01 02:48:48', 'Svijany 13', '27.00', 14, 1, 6, NULL, NULL, 0),
(21, 'objednano', '2017-12-01 02:48:53', 'Pilsner Urquell', '35.00', 16, 1, 6, NULL, NULL, 0),
(22, 'objednano', '2017-12-01 02:49:04', 'Pizza Margherita', '129.00', 1, 1, 7, NULL, NULL, 0),
(23, 'objednano', '2017-12-01 02:49:11', 'Penne s rajčaty', '115.00', 6, 1, 7, NULL, NULL, 0),
(24, 'objednano', '2017-12-01 02:49:18', 'Losos na rozmarýnu', '249.00', 19, 1, 7, NULL, NULL, 0),
(25, 'objednano', '2017-12-01 02:49:23', 'Fanta', '28.00', 10, 1, 7, NULL, NULL, 0),
(26, 'objednano', '2017-12-01 02:49:26', 'Kofola', '30.00', 9, 1, 7, NULL, NULL, 0),
(27, 'objednano', '2017-12-01 02:49:28', 'Kofola', '30.00', 9, 1, 7, NULL, NULL, 0),
(28, 'objednano', '2017-12-01 02:49:44', 'Hranolky', '35.00', 23, 1, 7, NULL, NULL, 0),
(29, 'objednano', '2017-12-01 02:50:25', 'Pizza Šunková', '130.00', 4, 1, 8, NULL, NULL, 0),
(30, 'objednano', '2017-12-01 02:50:33', 'Pečené vepřové koleno', '219.00', 20, 1, 9, NULL, NULL, 0),
(31, 'objednano', '2017-12-01 02:50:39', 'Grilovaná hovězi svíčková s americkými bramborami', '250.00', 7, 1, 9, NULL, NULL, 0),
(32, 'objednano', '2017-12-01 02:50:48', 'Svijany 13', '35.00', 15, 1, 9, NULL, NULL, 0),
(33, 'objednano', '2017-12-01 02:50:52', 'Svijany 13', '35.00', 15, 1, 9, NULL, NULL, 0),
(34, 'objednano', '2017-12-01 02:51:08', 'Kofola', '30.00', 9, 1, 10, NULL, NULL, 0),
(35, 'objednano', '2017-12-01 02:51:11', 'Kofola', '30.00', 9, 1, 10, NULL, NULL, 0),
(36, 'objednano', '2017-12-01 02:51:23', 'Hranolky', '35.00', 23, 1, 11, NULL, NULL, 0),
(37, 'objednano', '2017-12-01 02:51:36', 'Španělský ptáček', '169.00', 21, 1, 12, NULL, NULL, 0),
(38, 'objednano', '2017-12-01 02:51:40', 'Rýže', '29.00', 22, 1, 12, NULL, NULL, 0),
(39, 'objednano', '2017-12-01 02:51:43', 'Kofola', '30.00', 9, 1, 12, NULL, NULL, 0),
(40, 'objednano', '2017-12-01 02:52:00', 'Špagety Carbonara', '149.00', 26, 1, 14, NULL, NULL, 0),
(41, 'objednano', '2017-12-01 02:52:13', 'Smažené kuřecí prsa s bramborami', '179.00', 5, 1, 14, NULL, NULL, 0),
(42, 'objednano', '2017-12-01 02:52:23', 'Pilsner Urquell', '35.00', 16, 1, 15, NULL, NULL, 0),
(43, 'objednano', '2017-12-01 02:52:27', 'Pilsner Urquell', '35.00', 16, 1, 15, NULL, NULL, 0),
(44, 'objednano', '2017-12-01 02:52:31', 'Arašídy', '20.00', 25, 1, 15, NULL, NULL, 0),
(45, 'objednano', '2017-12-01 02:52:41', 'Penne s rajčaty', '115.00', 6, 1, 16, NULL, NULL, 0),
(46, 'objednano', '2017-12-01 02:52:42', 'Pizza Margherita', '129.00', 1, 1, 16, NULL, NULL, 0),
(47, 'objednano', '2017-12-01 02:52:49', 'Lambrusco', '40.00', 12, 1, 17, NULL, NULL, 0),
(48, 'objednano', '2017-12-01 02:52:55', 'Grilovaná hovězi svíčková s americkými bramborami', '250.00', 7, 1, 17, NULL, NULL, 0),
(49, 'objednano', '2017-12-01 02:53:04', 'Brambůrky', '29.00', 24, 1, 18, NULL, NULL, 0),
(50, 'objednano', '2017-12-01 02:53:08', 'Brambůrky', '29.00', 24, 1, 18, NULL, NULL, 0),
(51, 'objednano', '2017-12-01 02:53:11', 'Pizza Šunková', '130.00', 4, 1, 18, NULL, NULL, 0),
(52, 'zuctovano', '2017-12-01 02:53:23', 'Pizza Funghi', '135.00', 2, 1, 20, NULL, 3, 0),
(53, 'zuctovano', '2017-12-01 02:53:25', 'Fanta', '28.00', 10, 1, 20, NULL, 3, 0),
(54, 'objednano', '2017-12-01 02:53:42', 'Candát na másle', '229.00', 18, 1, 21, NULL, NULL, 0),
(55, 'objednano', '2017-12-01 02:53:45', 'Fanta', '28.00', 10, 1, 21, NULL, NULL, 0),
(56, 'objednano', '2017-12-01 02:53:55', 'Špagety Carbonara', '149.00', 26, 1, 22, NULL, NULL, 0),
(57, 'objednano', '2017-12-01 02:53:59', 'Špagety Carbonara', '149.00', 26, 1, 22, NULL, NULL, 0),
(58, 'objednano', '2017-12-01 02:54:03', 'Merlot', '45.00', 11, 1, 22, NULL, NULL, 0),
(59, 'objednano', '2017-12-01 02:54:05', 'Lambrusco', '40.00', 12, 1, 22, NULL, NULL, 0),
(60, 'objednano', '2017-12-01 02:54:18', 'Cinzano', '35.00', 13, 1, 1, NULL, NULL, 0),
(61, 'objednano', '2017-12-01 02:54:21', 'Cinzano', '35.00', 13, 1, 1, NULL, NULL, 0),
(63, 'objednano', '2017-12-01 03:01:30', 'Grilovaná hovězi svíčková s americkými bramborami', '250.00', 7, 4, 20, NULL, NULL, 0),
(64, 'objednano', '2017-12-01 03:01:33', 'Cinzano', '35.00', 13, 4, 20, NULL, NULL, 0),
(65, 'zuctovano', '2017-12-01 03:01:59', 'Lambrusco', '40.00', 12, 4, 13, NULL, 4, 0),
(66, 'zuctovano', '2017-12-01 03:02:01', 'Cinzano', '35.00', 13, 4, 13, NULL, 4, 0),
(67, 'zuctovano', '2017-12-01 03:02:03', 'Pilsner Urquell', '35.00', 16, 4, 13, NULL, 4, 0),
(68, 'zuctovano', '2017-12-01 03:02:18', 'Brambůrky', '29.00', 24, 4, 13, NULL, 5, 0),
(69, 'zuctovano', '2017-12-01 03:02:21', 'Candát na másle', '229.00', 18, 4, 13, NULL, 5, 0),
(70, 'zuctovano', '2017-12-01 03:02:24', 'Losos na rozmarýnu', '249.00', 19, 4, 13, NULL, 6, 0),
(71, 'zuctovano', '2017-12-01 03:02:45', 'Losos na rozmarýnu', '249.00', 19, 4, 1, NULL, 7, 0),
(72, 'zuctovano', '2017-12-01 03:02:49', 'Candát na másle', '229.00', 18, 4, 1, NULL, 7, 0),
(73, 'zuctovano', '2017-12-01 03:02:51', 'Pizza Funghi', '135.00', 2, 4, 1, NULL, 8, 0),
(74, 'zuctovano', '2017-12-01 03:02:53', 'Pizza Šunková', '130.00', 4, 4, 1, NULL, 8, 0),
(75, 'zuctovano', '2017-12-01 03:03:14', 'Voda s citronem', '35.00', 17, 4, 12, NULL, 9, 0),
(76, 'zuctovano', '2017-12-01 03:03:19', 'Rýže', '29.00', 22, 4, 12, NULL, 9, 0),
(77, 'zuctovano', '2017-12-01 03:03:23', 'Smažené kuřecí prsa s bramborami', '179.00', 5, 4, 12, NULL, 9, 0),
(78, 'zuctovano', '2017-12-01 03:03:30', 'Španělský ptáček', '169.00', 21, 4, 12, NULL, 9, 0),
(79, 'zuctovano', '2017-12-01 03:03:47', 'Pizza Margherita', '129.00', 1, 4, 13, NULL, 10, 0),
(80, 'zuctovano', '2017-12-01 03:03:49', 'Cinzano', '35.00', 13, 4, 13, NULL, 10, 0),
(81, 'zuctovano', '2017-12-01 03:03:51', 'Pizza Salami', '135.00', 3, 4, 13, NULL, 10, 0),
(82, 'zuctovano', '2017-12-01 03:03:53', 'Lambrusco', '40.00', 12, 4, 13, NULL, 10, 0),
(83, 'zuctovano', '2017-12-01 03:04:07', 'Pizza Salami', '135.00', 3, 4, 13, NULL, 11, 0),
(84, 'zuctovano', '2017-12-01 03:04:09', 'Pilsner Urquell', '35.00', 16, 4, 13, NULL, 11, 0),
(85, 'zuctovano', '2017-12-01 03:04:11', 'Merlot', '45.00', 11, 4, 13, NULL, 11, 0),
(86, 'zuctovano', '2017-12-01 03:04:15', 'Pizza Margherita', '129.00', 1, 4, 13, NULL, 11, 0),
(87, 'zuctovano', '2017-12-01 03:04:32', 'Penne s rajčaty', '115.00', 6, 4, 13, NULL, 12, 0),
(88, 'zuctovano', '2017-12-01 03:04:34', 'Kofola', '20.00', 8, 4, 13, NULL, 12, 0),
(89, 'zuctovano', '2017-12-01 03:04:37', 'Špagety Carbonara', '149.00', 26, 4, 13, NULL, 12, 0),
(90, 'zuctovano', '2017-12-01 03:04:40', 'Kofola', '20.00', 8, 4, 13, NULL, 12, 0),
(91, 'zuctovano', '2017-12-01 03:05:01', 'Pizza Margherita', '129.00', 1, 4, 3, NULL, 13, 0),
(92, 'zuctovano', '2017-12-01 03:05:05', 'Candát na másle', '229.00', 18, 4, 3, NULL, 13, 0),
(93, 'zuctovano', '2017-12-01 03:05:08', 'Pilsner Urquell', '35.00', 16, 4, 3, NULL, 13, 0),
(94, 'zuctovano', '2017-12-01 03:05:10', 'Svijany 13', '27.00', 14, 4, 3, NULL, 13, 0),
(95, 'zuctovano', '2017-12-01 03:05:23', 'Arašídy', '20.00', 25, 4, 3, NULL, 14, 0),
(96, 'zuctovano', '2017-12-01 03:05:27', 'Lambrusco', '40.00', 12, 4, 3, NULL, 14, 0),
(97, 'zuctovano', '2017-12-01 03:05:31', 'Cinzano', '35.00', 13, 4, 3, NULL, 14, 0),
(98, 'zuctovano', '2017-12-01 03:05:45', 'Losos na rozmarýnu', '249.00', 19, 4, 3, NULL, 15, 0),
(99, 'zuctovano', '2017-12-01 03:05:48', 'Pizza Šunková', '130.00', 4, 4, 3, NULL, 15, 0),
(100, 'zuctovano', '2017-12-01 03:05:50', 'Pizza Salami', '135.00', 3, 4, 3, NULL, 15, 0),
(101, 'zuctovano', '2017-12-01 03:05:53', 'Candát na másle', '229.00', 18, 4, 3, NULL, 15, 0),
(102, 'zuctovano', '2017-12-01 03:05:57', 'Pečené vepřové koleno', '219.00', 20, 4, 3, NULL, 15, 0),
(103, 'zuctovano', '2017-12-01 03:06:17', 'Pizza Margherita', '129.00', 1, 4, 19, NULL, 16, 0),
(104, 'zuctovano', '2017-12-01 03:06:19', 'Candát na másle', '229.00', 18, 4, 19, NULL, 16, 0),
(105, 'zuctovano', '2017-12-01 03:06:21', 'Losos na rozmarýnu', '249.00', 19, 4, 19, NULL, 16, 0),
(106, 'zuctovano', '2017-12-01 03:06:32', 'Voda s citronem', '35.00', 17, 4, 19, NULL, 17, 0),
(107, 'zuctovano', '2017-12-01 03:06:37', 'Hranolky', '35.00', 23, 4, 19, NULL, 17, 0),
(108, 'zuctovano', '2017-12-01 03:06:40', 'Hranolky', '35.00', 23, 4, 19, NULL, 17, 0),
(109, 'zuctovano', '2017-12-01 03:06:52', 'Smažené kuřecí prsa s bramborami', '179.00', 5, 4, 19, NULL, 18, 0),
(110, 'zuctovano', '2017-12-01 03:06:55', 'Candát na másle', '229.00', 18, 4, 19, NULL, 18, 0),
(111, 'zuctovano', '2017-12-01 03:07:01', 'Voda s citronem', '35.00', 17, 4, 19, NULL, 18, 0),
(112, 'zuctovano', '2017-12-01 03:07:23', 'Grilovaná hovězi svíčková s americkými bramborami', '250.00', 7, 4, 19, NULL, 19, 0),
(113, 'zuctovano', '2017-12-01 03:07:35', 'Fanta', '28.00', 10, 4, 19, NULL, 19, 0);

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `obsahuje`
--

CREATE TABLE `obsahuje` (
  `id_menu` int(11) NOT NULL,
  `id_ingredience` int(11) NOT NULL,
  `mnozstvi` int(11) NOT NULL,
  `jednotka` varchar(3) COLLATE utf8_czech_ci NOT NULL,
  `hide_obsahuje` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Sťahujem dáta pre tabuľku `obsahuje`
--

INSERT INTO `obsahuje` (`id_menu`, `id_ingredience`, `mnozstvi`, `jednotka`, `hide_obsahuje`) VALUES
(1, 4, 100, 'ml', 0),
(1, 5, 100, 'g', 0),
(1, 7, 100, 'g', 0),
(1, 18, 10, 'ml', 0),
(1, 45, 250, 'g', 0),
(1, 46, 10, 'g', 0),
(2, 4, 100, 'ml', 0),
(2, 5, 100, 'g', 0),
(2, 7, 100, 'g', 0),
(2, 10, 100, 'g', 0),
(2, 18, 10, 'ml', 0),
(2, 45, 250, 'g', 0),
(3, 4, 100, 'ml', 0),
(3, 5, 100, 'g', 0),
(3, 7, 100, 'g', 0),
(3, 18, 10, 'ml', 0),
(3, 45, 250, 'g', 0),
(3, 47, 100, 'g', 0),
(4, 4, 100, 'ml', 0),
(4, 5, 100, 'g', 0),
(4, 6, 100, 'g', 0),
(4, 7, 100, 'g', 0),
(4, 45, 250, 'g', 0),
(5, 1, 200, 'g', 0),
(5, 11, 200, 'g', 0),
(5, 30, 100, 'g', 0),
(6, 3, 200, 'g', 0),
(6, 5, 100, 'g', 0),
(6, 9, 30, 'g', 0),
(6, 18, 10, 'ml', 0),
(6, 46, 20, 'g', 0),
(7, 1, 200, 'g', 0),
(7, 4, 50, 'ml', 0),
(7, 13, 150, 'g', 0),
(7, 30, 50, 'g', 0),
(8, 33, 300, 'ml', 0),
(9, 33, 500, 'ml', 0),
(10, 34, 330, 'ml', 0),
(11, 35, 200, 'ml', 0),
(12, 36, 200, 'ml', 0),
(13, 37, 150, 'ml', 0),
(14, 38, 300, 'ml', 0),
(15, 38, 500, 'ml', 0),
(16, 39, 500, 'ml', 0),
(17, 4, 500, 'ml', 0),
(17, 40, 50, 'g', 0),
(18, 18, 20, 'ml', 0),
(18, 28, 200, 'g', 0),
(18, 30, 50, 'g', 0),
(18, 31, 5, 'g', 0),
(19, 18, 20, 'ml', 0),
(19, 29, 200, 'g', 0),
(19, 30, 30, 'g', 0),
(19, 31, 10, 'g', 0),
(20, 12, 500, 'g', 0),
(20, 24, 50, 'g', 0),
(21, 8, 30, 'g', 0),
(21, 13, 100, 'g', 0),
(21, 19, 10, 'ml', 0),
(21, 25, 30, 'g', 0),
(22, 2, 200, 'g', 0),
(23, 51, 200, 'g', 0),
(24, 49, 80, 'g', 0),
(25, 50, 80, 'g', 0),
(26, 9, 40, 'g', 0),
(26, 23, 50, 'g', 0),
(26, 25, 80, 'g', 0),
(26, 52, 200, 'g', 0);

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `polozka_menu`
--

CREATE TABLE `polozka_menu` (
  `id_menu` int(11) NOT NULL,
  `nazev` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  `popis` varchar(100) COLLATE utf8_czech_ci DEFAULT NULL,
  `cena` decimal(8,2) NOT NULL,
  `typ` varchar(1) COLLATE utf8_czech_ci NOT NULL,
  `hide_polozka` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Sťahujem dáta pre tabuľku `polozka_menu`
--

INSERT INTO `polozka_menu` (`id_menu`, `nazev`, `popis`, `cena`, `typ`, `hide_polozka`) VALUES
(1, 'Pizza Margherita', 'Rajčatová omáčka, čerstvá bazalka, mozzarella, olivový olej', '129.00', 'J', 0),
(2, 'Pizza Funghi', 'Rajčatová omáčka, oregáno, mozzarella, žampiony', '135.00', 'J', 0),
(3, 'Pizza Salami', 'Rajčatová omáčka, mozzarella, salám', '135.00', 'J', 0),
(4, 'Pizza Šunková', 'Rajčatová omáčka, mozzarella, šunka', '130.00', 'J', 0),
(5, 'Smažené kuřecí prsa s bramborami', 'Šťavnaté kuřecí prsa v bylinkové omáčce s opečenými bramborami', '179.00', 'J', 0),
(6, 'Penne s rajčaty', 'Domácí těstoviny s rajčatovou omáčkou', '115.00', 'J', 0),
(7, 'Grilovaná hovězi svíčková s americkými bramborami', 'jidlo pro labuzniky', '250.00', 'J', 0),
(8, 'Kofola', '', '20.00', 'P', 0),
(9, 'Kofola', '', '30.00', 'P', 0),
(10, 'Fanta', '', '28.00', 'P', 0),
(11, 'Merlot', '', '45.00', 'P', 0),
(12, 'Lambrusco', '', '40.00', 'P', 0),
(13, 'Cinzano', '', '35.00', 'P', 0),
(14, 'Svijany 13', '', '27.00', 'P', 0),
(15, 'Svijany 13', '', '35.00', 'P', 0),
(16, 'Pilsner Urquell', '', '35.00', 'P', 0),
(17, 'Voda s citronem', '', '35.00', 'P', 0),
(18, 'Candát na másle', 'Lehce opečený candát z místního chovu', '229.00', 'J', 0),
(19, 'Losos na rozmarýnu', 'Steak z lososa na grilu', '249.00', 'J', 0),
(20, 'Pečené vepřové koleno', '', '219.00', 'J', 0),
(21, 'Španělský ptáček', '', '169.00', 'J', 0),
(22, 'Rýže', '', '29.00', 'J', 0),
(23, 'Hranolky', '', '35.00', 'J', 0),
(24, 'Brambůrky', '', '29.00', 'J', 0),
(25, 'Arašídy', '', '20.00', 'J', 0),
(26, 'Špagety Carbonara', '', '149.00', 'J', 0);

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `rezervace`
--

CREATE TABLE `rezervace` (
  `id_rezervace` int(11) NOT NULL,
  `pocet_osob` int(11) DEFAULT '1',
  `jmeno` varchar(20) COLLATE utf8_czech_ci NOT NULL,
  `telefon` varchar(10) COLLATE utf8_czech_ci NOT NULL,
  `poznamka` varchar(100) COLLATE utf8_czech_ci DEFAULT NULL,
  `hide_rez` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Sťahujem dáta pre tabuľku `rezervace`
--

INSERT INTO `rezervace` (`id_rezervace`, `pocet_osob`, `jmeno`, `telefon`, `poznamka`, `hide_rez`) VALUES
(1, 20, 'Emil Zátopek', '604203955', 'Rezervace celé přední zahrádky', 0),
(2, 2, 'Jan Pospíšil', '582394282', '', 0),
(3, 6, 'Michal Tesař', '649540328', 'Potřeba spojit stoly', 0),
(4, 1, 'Filip Pros', '605495333', 'S sebou Margherita', 0),
(5, 1, 'Michal Kubiš', '606493024', '', 0),
(6, 66, 'Alois Jirásek', '666888444', 'Soukromá akce v restauraci', 0);

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `rezervace_stul`
--

CREATE TABLE `rezervace_stul` (
  `id_rezervace` int(11) NOT NULL,
  `id_stul` int(11) NOT NULL,
  `datum_rezervace` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `datum_do` datetime DEFAULT NULL,
  `hide_rezstul` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Sťahujem dáta pre tabuľku `rezervace_stul`
--

INSERT INTO `rezervace_stul` (`id_rezervace`, `id_stul`, `datum_rezervace`, `datum_do`, `hide_rezstul`) VALUES
(1, 2, '2017-12-01 12:00:00', '2017-12-01 15:00:00', 0),
(1, 3, '2017-12-01 12:00:00', '2017-12-01 15:00:00', 0),
(1, 4, '2017-12-01 12:00:00', '2017-12-01 15:00:00', 0),
(1, 5, '2017-12-01 12:00:00', '2017-12-01 15:00:00', 0),
(1, 6, '2017-12-01 12:00:00', '2017-12-01 15:00:00', 0),
(2, 21, '2017-12-01 19:00:00', '2017-12-01 21:00:00', 0),
(3, 14, '2017-12-02 14:00:00', '2017-12-02 16:30:00', 0),
(3, 15, '2017-12-02 14:00:00', '2017-12-02 16:30:00', 0),
(4, 1, '2017-12-03 12:00:00', '2017-12-03 13:00:00', 0),
(5, 21, '2017-12-13 10:50:00', '2017-12-13 12:00:00', 0),
(6, 2, '2017-12-22 18:00:00', '2017-12-22 21:00:00', 0),
(6, 3, '2017-12-22 18:00:00', '2017-12-22 21:00:00', 0),
(6, 4, '2017-12-22 18:00:00', '2017-12-22 21:00:00', 0),
(6, 5, '2017-12-22 18:00:00', '2017-12-22 21:00:00', 0),
(6, 6, '2017-12-22 18:00:00', '2017-12-22 21:00:00', 0),
(6, 7, '2017-12-22 18:00:00', '2017-12-22 21:00:00', 0),
(6, 8, '2017-12-22 18:00:00', '2017-12-22 21:00:00', 0),
(6, 9, '2017-12-22 18:00:00', '2017-12-22 21:00:00', 0),
(6, 10, '2017-12-22 18:00:00', '2017-12-22 21:00:00', 0),
(6, 11, '2017-12-22 18:00:00', '2017-12-22 21:00:00', 0),
(6, 12, '2017-12-22 18:00:00', '2017-12-22 21:00:00', 0),
(6, 13, '2017-12-22 18:00:00', '2017-12-22 21:00:00', 0),
(6, 14, '2017-12-22 18:00:00', '2017-12-22 21:00:00', 0),
(6, 15, '2017-12-22 18:00:00', '2017-12-22 21:00:00', 0),
(6, 16, '2017-12-22 18:00:00', '2017-12-22 21:00:00', 0),
(6, 17, '2017-12-22 18:00:00', '2017-12-22 21:00:00', 0),
(6, 18, '2017-12-22 18:00:00', '2017-12-22 21:00:00', 0),
(6, 19, '2017-12-22 18:00:00', '2017-12-22 21:00:00', 0),
(6, 20, '2017-12-22 18:00:00', '2017-12-22 21:00:00', 0),
(6, 21, '2017-12-22 18:00:00', '2017-12-22 21:00:00', 0),
(6, 22, '2017-12-22 18:00:00', '2017-12-22 21:00:00', 0);

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `stul`
--

CREATE TABLE `stul` (
  `id_stul` int(11) NOT NULL,
  `mistnost` varchar(20) COLLATE utf8_czech_ci NOT NULL,
  `kapacita` int(11) NOT NULL,
  `cislo` int(3) NOT NULL,
  `hide_stul` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Sťahujem dáta pre tabuľku `stul`
--

INSERT INTO `stul` (`id_stul`, `mistnost`, `kapacita`, `cislo`, `hide_stul`) VALUES
(1, 'bar', 100, 1, 0),
(2, 'predni zahradka', 4, 1, 0),
(3, 'predni zahradka', 4, 2, 0),
(4, 'predni zahradka', 2, 3, 0),
(5, 'predni zahradka', 8, 4, 0),
(6, 'predni zahradka', 4, 5, 0),
(7, 'zadni zahradka', 4, 1, 0),
(8, 'zadni zahradka', 4, 2, 0),
(9, 'zadni zahradka', 2, 3, 0),
(10, 'zadni zahradka', 8, 4, 0),
(11, 'zadni zahradka', 4, 5, 0),
(12, 'zadni zahradka', 8, 6, 0),
(13, 'zadni zahradka', 4, 7, 0),
(14, 'sala', 4, 1, 0),
(15, 'sala', 4, 2, 0),
(16, 'sala', 2, 3, 0),
(17, 'sala', 2, 4, 0),
(18, 'sala', 6, 5, 0),
(19, 'sala', 6, 6, 0),
(20, 'sala', 8, 7, 0),
(21, 'salonek', 2, 1, 0),
(22, 'salonek', 2, 2, 0);

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `uctenka`
--

CREATE TABLE `uctenka` (
  `id_uctenka` int(11) NOT NULL,
  `datum` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `suma` decimal(8,2) DEFAULT NULL,
  `id_zamestnanec` int(11) NOT NULL,
  `status` varchar(10) COLLATE utf8_czech_ci NOT NULL DEFAULT 'vystaveno',
  `hide_uctenka` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Sťahujem dáta pre tabuľku `uctenka`
--

INSERT INTO `uctenka` (`id_uctenka`, `datum`, `suma`, `id_zamestnanec`, `status`, `hide_uctenka`) VALUES
(1, '2017-12-01 15:58:52', '554.00', 1, 'zaplaceno', 0),
(2, '2017-12-02 12:30:35', '378.00', 4, 'zaplaceno', 0),
(3, '2017-12-02 16:01:24', '163.00', 4, 'zaplaceno', 0),
(4, '2017-12-02 19:11:29', '110.00', 4, 'zaplaceno', 0),
(5, '2017-12-03 12:22:11', '258.00', 4, 'zaplaceno', 0),
(6, '2017-12-03 18:35:22', '249.00', 4, 'zaplaceno', 0),
(7, '2017-12-03 19:00:23', '478.00', 4, 'zaplaceno', 0),
(8, '2017-12-04 14:25:18', '265.00', 4, 'zaplaceno', 0),
(9, '2017-12-04 14:31:12', '412.00', 4, 'zaplaceno', 0),
(10, '2017-12-05 12:26:31', '339.00', 4, 'zaplaceno', 0),
(11, '2017-12-06 12:22:22', '344.00', 4, 'zaplaceno', 0),
(12, '2017-12-06 12:34:47', '304.00', 4, 'zaplaceno', 0),
(13, '2017-12-07 14:34:26', '420.00', 4, 'zaplaceno', 0),
(14, '2017-12-07 16:32:23', '95.00', 4, 'zaplaceno', 0),
(15, '2017-12-08 12:27:20', '962.00', 4, 'zaplaceno', 0),
(16, '2017-12-09 11:26:14', '607.00', 4, 'zaplaceno', 0),
(17, '2017-12-10 16:20:14', '105.00', 4, 'zaplaceno', 0),
(18, '2017-12-11 13:24:24', '443.00', 4, 'zaplaceno', 0),
(19, '2017-12-12 15:26:09', '278.00', 4, 'zaplaceno', 0);

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `zamestnanec`
--

CREATE TABLE `zamestnanec` (
  `id_zamestnanec` int(11) NOT NULL,
  `jmeno` varchar(20) COLLATE utf8_czech_ci NOT NULL,
  `prijmeni` varchar(30) COLLATE utf8_czech_ci NOT NULL,
  `login` varchar(30) COLLATE utf8_czech_ci NOT NULL,
  `heslo` varchar(30) COLLATE utf8_czech_ci NOT NULL,
  `cislo_op` varchar(9) COLLATE utf8_czech_ci NOT NULL,
  `telefon` varchar(10) COLLATE utf8_czech_ci DEFAULT NULL,
  `email` varchar(30) COLLATE utf8_czech_ci DEFAULT NULL,
  `ulice` varchar(30) COLLATE utf8_czech_ci NOT NULL,
  `cislo_popisne` int(11) NOT NULL,
  `mesto` varchar(25) COLLATE utf8_czech_ci NOT NULL,
  `psc` varchar(11) COLLATE utf8_czech_ci NOT NULL,
  `rola` enum('cisnik','kuchar','provozni','majitel') COLLATE utf8_czech_ci NOT NULL,
  `hide_zam` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

--
-- Sťahujem dáta pre tabuľku `zamestnanec`
--

INSERT INTO `zamestnanec` (`id_zamestnanec`, `jmeno`, `prijmeni`, `login`, `heslo`, `cislo_op`, `telefon`, `email`, `ulice`, `cislo_popisne`, `mesto`, `psc`, `rola`, `hide_zam`) VALUES
(1, 'Jan', 'Kubica', 'xkubic01', 'user1', '647850288', '720618435', 'jan.kubica@email.cz', 'Nebelova', 75, 'Prostejov', '79604', 'majitel', 0),
(2, 'Juraj', 'Korček', 'xkorce01', 'user2', '102573994', '605332817', 'juraj.korcek@email.cz', 'Mánesova', 17, 'Brno', '61200', 'provozni', 0),
(3, 'Lukáš', 'Novák', 'xnovak01', 'user3', '102594338', '723042024', 'l.novak@gmail.com', 'Horvatská', 5, 'Blansko', '678 01', 'kuchar', 0),
(4, 'Petr', 'Svoboda', 'xsvobo01', 'user4', '304666954', '777234089', 'petr.svoboda@centrum.cz', 'Havířská', 8, 'Rosice', '66501', 'cisnik', 0);

--
-- Kľúče pre exportované tabuľky
--

--
-- Indexy pre tabuľku `ingredience`
--
ALTER TABLE `ingredience`
  ADD PRIMARY KEY (`id_ingredience`),
  ADD UNIQUE KEY `nazev_ingredience` (`nazev_ingredience`);

--
-- Indexy pre tabuľku `objednana_polozka`
--
ALTER TABLE `objednana_polozka`
  ADD PRIMARY KEY (`id_objednavka`),
  ADD KEY `fk_id_menu_pol` (`id_menu`),
  ADD KEY `fk_id_zamestnanec_pol` (`id_zamestnanec`),
  ADD KEY `fk_id_stul_pol` (`id_stul`),
  ADD KEY `fk_id_rezervace_pol` (`id_rezervace`),
  ADD KEY `fk_id_uctenka_pol` (`id_uctenka`);

--
-- Indexy pre tabuľku `obsahuje`
--
ALTER TABLE `obsahuje`
  ADD PRIMARY KEY (`id_menu`,`id_ingredience`),
  ADD KEY `fk_id_ingredience_obs` (`id_ingredience`);

--
-- Indexy pre tabuľku `polozka_menu`
--
ALTER TABLE `polozka_menu`
  ADD PRIMARY KEY (`id_menu`);

--
-- Indexy pre tabuľku `rezervace`
--
ALTER TABLE `rezervace`
  ADD PRIMARY KEY (`id_rezervace`);

--
-- Indexy pre tabuľku `rezervace_stul`
--
ALTER TABLE `rezervace_stul`
  ADD PRIMARY KEY (`id_rezervace`,`id_stul`),
  ADD KEY `fk_id_stul_rezst` (`id_stul`);

--
-- Indexy pre tabuľku `stul`
--
ALTER TABLE `stul`
  ADD PRIMARY KEY (`id_stul`),
  ADD UNIQUE KEY `uni_keys` (`mistnost`,`cislo`);

--
-- Indexy pre tabuľku `uctenka`
--
ALTER TABLE `uctenka`
  ADD PRIMARY KEY (`id_uctenka`),
  ADD KEY `fk_id_zamestnanec_uc` (`id_zamestnanec`);

--
-- Indexy pre tabuľku `zamestnanec`
--
ALTER TABLE `zamestnanec`
  ADD PRIMARY KEY (`id_zamestnanec`),
  ADD UNIQUE KEY `login` (`login`),
  ADD UNIQUE KEY `cislo_op` (`cislo_op`);

--
-- AUTO_INCREMENT pre exportované tabuľky
--

--
-- AUTO_INCREMENT pre tabuľku `ingredience`
--
ALTER TABLE `ingredience`
  MODIFY `id_ingredience` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;
--
-- AUTO_INCREMENT pre tabuľku `objednana_polozka`
--
ALTER TABLE `objednana_polozka`
  MODIFY `id_objednavka` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;
--
-- AUTO_INCREMENT pre tabuľku `polozka_menu`
--
ALTER TABLE `polozka_menu`
  MODIFY `id_menu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;
--
-- AUTO_INCREMENT pre tabuľku `rezervace`
--
ALTER TABLE `rezervace`
  MODIFY `id_rezervace` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT pre tabuľku `stul`
--
ALTER TABLE `stul`
  MODIFY `id_stul` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
--
-- AUTO_INCREMENT pre tabuľku `uctenka`
--
ALTER TABLE `uctenka`
  MODIFY `id_uctenka` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT pre tabuľku `zamestnanec`
--
ALTER TABLE `zamestnanec`
  MODIFY `id_zamestnanec` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- Obmedzenie pre exportované tabuľky
--

--
-- Obmedzenie pre tabuľku `objednana_polozka`
--
ALTER TABLE `objednana_polozka`
  ADD CONSTRAINT `fk_id_menu_pol` FOREIGN KEY (`id_menu`) REFERENCES `polozka_menu` (`id_menu`),
  ADD CONSTRAINT `fk_id_rezervace_pol` FOREIGN KEY (`id_rezervace`) REFERENCES `rezervace` (`id_rezervace`),
  ADD CONSTRAINT `fk_id_stul_pol` FOREIGN KEY (`id_stul`) REFERENCES `stul` (`id_stul`),
  ADD CONSTRAINT `fk_id_uctenka_pol` FOREIGN KEY (`id_uctenka`) REFERENCES `uctenka` (`id_uctenka`),
  ADD CONSTRAINT `fk_id_zamestnanec_pol` FOREIGN KEY (`id_zamestnanec`) REFERENCES `zamestnanec` (`id_zamestnanec`);

--
-- Obmedzenie pre tabuľku `obsahuje`
--
ALTER TABLE `obsahuje`
  ADD CONSTRAINT `fk_id_ingredience_obs` FOREIGN KEY (`id_ingredience`) REFERENCES `ingredience` (`id_ingredience`),
  ADD CONSTRAINT `fk_id_menu_obs` FOREIGN KEY (`id_menu`) REFERENCES `polozka_menu` (`id_menu`);

--
-- Obmedzenie pre tabuľku `rezervace_stul`
--
ALTER TABLE `rezervace_stul`
  ADD CONSTRAINT `fk_id_rezervace_rezst` FOREIGN KEY (`id_rezervace`) REFERENCES `rezervace` (`id_rezervace`),
  ADD CONSTRAINT `fk_id_stul_rezst` FOREIGN KEY (`id_stul`) REFERENCES `stul` (`id_stul`);

--
-- Obmedzenie pre tabuľku `uctenka`
--
ALTER TABLE `uctenka`
  ADD CONSTRAINT `fk_id_zamestnanec_uc` FOREIGN KEY (`id_zamestnanec`) REFERENCES `zamestnanec` (`id_zamestnanec`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
