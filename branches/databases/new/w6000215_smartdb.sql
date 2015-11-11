-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 08-11-2015 a las 05:52:57
-- Versión del servidor: 5.6.21
-- Versión de PHP: 5.5.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `w6000215_smart2`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `access`
--

CREATE TABLE IF NOT EXISTS `access` (
`access_id` int(11) NOT NULL,
  `board_id` int(6) NOT NULL,
  `building_id` int(6) NOT NULL,
  `access_n` int(2) NOT NULL,
  `access_d` varchar(45) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `access`
--

INSERT INTO `access` (`access_id`, `board_id`, `building_id`, `access_n`, `access_d`) VALUES
(1, 13, 1, 1, 'Guardia Luis Galeano'),
(2, 14, 1, 3, 'Puerta Principal Torre 2'),
(3, 15, 1, 10, 'Entrada de Servicio'),
(4, 1, 1, 10, 'Entrada de Servicio');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alerts`
--

CREATE TABLE IF NOT EXISTS `alerts` (
`alert_id` int(4) NOT NULL,
  `alert_uf` int(4) NOT NULL,
  `alert_type` varchar(45) NOT NULL,
  `alert_coords` varchar(450) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `alerts`
--

INSERT INTO `alerts` (`alert_id`, `alert_uf`, `alert_type`, `alert_coords`) VALUES
(1, 0, 'antipanico', 'Longitud: (sin_datos) Latitud: (sin_datos)'),
(2, 0, 'antipanico', 'Longitud: (sin_datos) Latitud: (sin_datos)'),
(3, 0, 'antipanico', 'Longitud: -58.29347579599505 Latitud: -34.71334661284051'),
(4, 0, 'antipanico', 'Longitud: (sin_datos) Latitud: (sin_datos)'),
(5, 0, 'antipanico', 'Longitud: (sin_datos) Latitud: (sin_datos)'),
(6, 0, 'antipanico', 'Longitud: -58.29305647680722 Latitud: -34.71318818196848'),
(7, 0, 'antipanico', 'Longitud: (sin_datos) Latitud: (sin_datos)'),
(8, 0, 'antipanico', 'Longitud: (sin_datos) Latitud: (sin_datos)');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `buildings`
--

CREATE TABLE IF NOT EXISTS `buildings` (
  `building_id` int(6) NOT NULL,
  `owner_id` int(4) NOT NULL,
  `owner_desc` varchar(45) NOT NULL,
  `bld_address` varchar(45) NOT NULL,
  `bld_city` varchar(45) NOT NULL,
  `bld_country` varchar(45) NOT NULL,
  `bld_hansa` int(6) DEFAULT NULL,
  `bld_img` varchar(400) DEFAULT NULL,
  `bld_map` varchar(400) DEFAULT NULL,
  `adm_name` varchar(45) DEFAULT NULL,
  `adm_mail` varchar(45) DEFAULT NULL,
  `adm_tel` varchar(20) DEFAULT NULL,
  `adm_hansa` int(6) DEFAULT NULL,
  `adm_addr` varchar(45) DEFAULT NULL,
  `mng_name` varchar(45) DEFAULT NULL,
  `mng_mail` varchar(45) DEFAULT NULL,
  `mng_tel` varchar(20) DEFAULT NULL,
  `mng_time` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `buildings`
--

INSERT INTO `buildings` (`building_id`, `owner_id`, `owner_desc`, `bld_address`, `bld_city`, `bld_country`, `bld_hansa`, `bld_img`, `bld_map`, `adm_name`, `adm_mail`, `adm_tel`, `adm_hansa`, `adm_addr`, `mng_name`, `mng_mail`, `mng_tel`, `mng_time`) VALUES
(1, 1, 'Smart Control', 'Av. Callao 1066', 'Ciudad de Buenos Aires', 'Argentina', 1506, '/buildings/bld0014.jpg', '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3405.7277385394113!2d-64.22296070000006!3d-31.394069899999995!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x943298c3df5cd909%3A0xbacff9ca4977f5c7!2sOpera+Luxury+Condominium!5e0!3m2!1ses-419!2sar!4v1443187616030" width="300" height="200" frameborder="0" style="border:0"></iframe>', 'Smart Control', 'pablo.pereda@gmail.com', '1131417303', 1, 'Av. Gaona 1830', 'Roberto', 'roberto@fakemail.com', '1155556666', '9 a 12');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cards`
--

CREATE TABLE IF NOT EXISTS `cards` (
  `card_id` varchar(10) NOT NULL,
  `card_tz` int(2) NOT NULL,
  `building_id` int(6) NOT NULL,
  `uf` varchar(4) NOT NULL,
  `username` varchar(45) NOT NULL,
  `card_mail` varchar(45) DEFAULT NULL,
  `card_tel` varchar(20) DEFAULT NULL,
  `card_status` varchar(10) DEFAULT NULL,
  `card_img` varchar(400) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `cards`
--

INSERT INTO `cards` (`card_id`, `card_tz`, `building_id`, `uf`, `username`, `card_mail`, `card_tel`, `card_status`, `card_img`) VALUES
('2D99831621', 1, 1, '1', 'Roberto', 'No Informado', 'No Informado', 'Activa', NULL),
('5DED92A183', 1, 1, '1', 'Pablo', 'No Informado', 'No Informado', 'Activa', NULL),
('AAASDDDSSS', 1, 20, '1', 'Pablo', 'No Informado', 'No Informado', 'Activa', NULL),
('AE45512AAA', 1, 20, '1', 'Pablo', 'No Informado', 'No Informado', 'Activa', NULL),
('asdasdasdd', 1, 1, '1', 'Pablo', 'No Informado', 'No Informado', 'Eliminada', NULL),
('CCCAAAAAAA', 1, 20, '1', 'Pablo', 'No Informado', 'No Informado', 'Activa', NULL),
('CCCCCCCCCC', 1, 20, '1', 'Pablo', 'No Informado', 'No Informado', 'Activa', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `events`
--

CREATE TABLE IF NOT EXISTS `events` (
`evt_id` int(11) NOT NULL,
  `evt_date` date NOT NULL,
  `evt_time` time NOT NULL,
  `building_id` int(6) NOT NULL,
  `card_id` varchar(10) NOT NULL,
  `evt_info` varchar(3) NOT NULL,
  `evt_img` varchar(400) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `events`
--

INSERT INTO `events` (`evt_id`, `evt_date`, `evt_time`, `building_id`, `card_id`, `evt_info`, `evt_img`) VALUES
(1, '2015-10-15', '15:20:56', 0, '1234567890', 'S1', 'http:///buildings/1510/06094459.jpg'),
(4, '2015-10-05', '16:52:38', 1, 'BUTTON', 'BT1', 'http:///buildings/1510/05165238.jpg'),
(5, '2015-10-05', '16:53:14', 1, 'BUTTON', 'BT1', 'http:///buildings/1510/05165314.jpg'),
(6, '2015-10-06', '09:43:42', 1, 'BUTTON', 'BT1', 'http:///buildings/1510/06094342.jpg'),
(7, '2015-10-06', '09:44:51', 1, '2D99831621', 'S19', 'http:///buildings/1510/06094451.jpg'),
(8, '2015-10-06', '09:44:59', 1, '5DED92A183', 'INV', 'http:///buildings/1510/06094459.jpg'),
(9, '2015-10-06', '09:45:04', 1, '1026CB45B8', 'INV', 'http:///buildings/1510/06094504.jpg'),
(10, '2015-10-06', '09:45:10', 1, '10BFD6453C', 'INV', 'http:///buildings/1510/06094510.jpg'),
(11, '2015-10-06', '09:47:05', 20, 'BUTTON', 'BT1', 'http:///buildings/1510/06094705.jpg'),
(12, '2015-10-06', '09:44:51', 1, '2D99831621', 'S19', 'http:///buildings/1510/06094451.jpg'),
(13, '2015-10-06', '09:44:51', 1, '2D99831621', 'S19', 'http:///buildings/1510/06094451.jpg'),
(14, '2015-10-06', '09:44:51', 1, '2D99831621', 'S19', 'http:///buildings/1510/06094451.jpg'),
(15, '2015-10-06', '09:44:51', 1, '2D99831621', 'S19', 'http:///buildings/1510/06094451.jpg'),
(16, '2015-10-06', '09:44:51', 14, '2D99831621', 'S18', 'http:///buildings/1510/06094451.jpg'),
(17, '2015-10-06', '09:44:51', 1, '2D99831621', 'S19', 'http:///buildings/1510/06094451.jpg'),
(18, '2015-10-06', '09:44:51', 1, '2D99831621', 'S19', 'http:///buildings/1510/06094451.jpg'),
(19, '2015-10-06', '09:44:51', 1, '2D99831621', 'S19', 'http:///buildings/1510/06094451.jpg'),
(20, '2015-10-06', '09:44:51', 15, '2D99831621', 'S19', 'http:///buildings/1510/06094451.jpg'),
(21, '2015-10-06', '09:44:51', 1, '2D99831621', 'S19', 'http:///buildings/1510/06094451.jpg'),
(22, '2015-10-06', '09:44:51', 1, '2D99831621', 'S19', 'http:///buildings/1510/06094451.jpg'),
(23, '2015-10-06', '09:44:51', 1, '2D99831621', 'S19', 'http:///buildings/1510/06094451.jpg'),
(24, '2015-10-06', '09:44:51', 13, '2D99831621', 'S19', 'http:///buildings/1510/06094451.jpg'),
(25, '2015-10-06', '09:44:51', 1, '2D99831621', 'S19', 'http:///buildings/1510/06094451.jpg'),
(26, '2015-10-06', '09:44:51', 1, '2D99831621', 'S19', 'http:///buildings/1510/06094451.jpg'),
(27, '2015-10-06', '09:44:51', 1, '2D99831621', 'S19', 'http:///buildings/1510/06094451.jpg'),
(28, '2015-10-06', '09:44:51', 1, '2D99831621', 'S19', 'http:///buildings/1510/06094451.jpg'),
(29, '2015-10-06', '09:44:51', 1, '2D99831621', 'S19', 'http:///buildings/1510/06094451.jpg'),
(30, '2015-10-06', '09:44:51', 1, '2D99831621', 'S20', 'http:///buildings/1510/06094451.jpg'),
(31, '2015-10-06', '09:44:51', 1, '2D99831621', 'S20', 'http:///buildings/1510/06094451.jpg'),
(32, '2015-10-26', '09:44:51', 1, '2D99831621', 'S19', 'http:///buildings/1510/06094451.jpg'),
(33, '2015-10-26', '09:44:51', 1, '2D99831621', 'S19', 'http:///buildings/1510/06094451.jpg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `login_attempts`
--

CREATE TABLE IF NOT EXISTS `login_attempts` (
  `user_id` int(11) NOT NULL,
  `time` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `login_attempts`
--

INSERT INTO `login_attempts` (`user_id`, `time`) VALUES
(6, '1446690556'),
(6, '1446691385'),
(6, '1446691758'),
(6, '1446692081'),
(6, '1446693153'),
(6, '1446693468'),
(6, '1446693662'),
(6, '1446693961'),
(6, '1446694315'),
(6, '1446695745'),
(6, '1446695968'),
(6, '1446696173'),
(6, '1446696282'),
(6, '1446696497'),
(6, '1446696600'),
(6, '1446730439'),
(2, '1446928369'),
(2, '1446928889'),
(2, '1446942785'),
(2, '1446945018'),
(2, '1446945592'),
(2, '1446945739'),
(2, '1446949454'),
(2, '1446950126'),
(2, '1446950550'),
(2, '1446952875');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `members`
--

CREATE TABLE IF NOT EXISTS `members` (
`id` int(11) NOT NULL,
  `username` varchar(30) NOT NULL,
  `email` varchar(50) NOT NULL,
  `userrole` varchar(3) NOT NULL,
  `building` int(4) NOT NULL,
  `uf` int(4) DEFAULT NULL,
  `password` char(128) NOT NULL,
  `salt` char(128) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `members`
--

INSERT INTO `members` (`id`, `username`, `email`, `userrole`, `building`, `uf`, `password`, `salt`) VALUES
(1, 'test_user', 'test@example.com', 'adm', 2, NULL, '00807432eae173f652f2064bdca1b61b290b52d40e429a7d295d76a71084aa96c0233b82f1feac45529e0726559645acaed6f3ae58a286b9f075916ebf66cacc', 'f9aab579fc1b41ed0c44fe4ecdbfcdb4cb99b9023abb241a6db833288f4eea3c02f76e0d35204a8695077dcf81932aa59006423976224be0390395bae152d4ef'),
(2, 'Marcelo Castro', 'carlos', 'adm', 1, NULL, 'f44ecbc1c5f23eccdabb37aa1cd4f6c286706e84d17b685e0117bf908193bdcfbda920a4c061ccf0231abf63ca8edf7891122f24f0dd098407655da6f92b2cf1', '398df76ea25c96d1f7656362179d28e28992a1914e4409c1ab0eac5eb1175ef414f402064be91e1cb9a39b88608d24c91d3cbafe96a4bfca2605fffbc8469522'),
(3, 'ariel', 'ariel@ariel', 'adm', 1, NULL, '5e119d0ceaaac51b72568ff16c0d86f2edc2633a96d425c3a3d0b09437f2cd8fdec46821f1cc47cc2bbdadac6d81763bc25a221f675b0170e18c839faca82fd7', '398df76ea25c96d1f7656362179d28e28992a1914e4409c1ab0eac5eb1175ef414f402064be91e1cb9a39b88608d24c91d3cbafe96a4bfca2605fffbc8469522'),
(5, 'Marcelo Castro', 'carlitos', 'usr', 1, 2, '5e119d0ceaaac51b72568ff16c0d86f2edc2633a96d425c3a3d0b09437f2cd8fdec46821f1cc47cc2bbdadac6d81763bc25a221f675b0170e18c839faca82fd7', '398df76ea25c96d1f7656362179d28e28992a1914e4409c1ab0eac5eb1175ef414f402064be91e1cb9a39b88608d24c91d3cbafe96a4bfca2605fffbc8469522'),
(6, 'Yadir', 'yadirhb', 'adm', 1, NULL, '1234567890', 'abcdef');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
`msg_id` int(6) NOT NULL,
  `building_id` int(6) NOT NULL,
  `uf` varchar(4) NOT NULL,
  `msg_author` varchar(45) NOT NULL,
  `msg_date` date NOT NULL,
  `msg_title` varchar(45) NOT NULL,
  `msg_content` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `messages`
--

INSERT INTO `messages` (`msg_id`, `building_id`, `uf`, `msg_author`, `msg_date`, `msg_title`, `msg_content`) VALUES
(1, 1, '1', 'Pablo', '2015-10-13', 'Prueba', 'Hola Granola'),
(2, 1, '2', 'Jose', '2015-10-15', 'Prueba 2', 'Hola Zi'),
(3, 1, '', 'Jose', '2015-10-15', 'Prueba 2', 'Hola Zi'),
(4, 1, '1', 'Pablo', '2015-10-01', 'Prueba', 'Hola Granola BABAY');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

CREATE TABLE IF NOT EXISTS `sessions` (
  `sid` varchar(60) CHARACTER SET latin1 NOT NULL,
  `uid` varchar(36) CHARACTER SET latin1 NOT NULL,
  `host` varchar(128) CHARACTER SET latin1 DEFAULT NULL,
  `timestamp` int(11) NOT NULL DEFAULT '0',
  `user_agent` varchar(128) CHARACTER SET latin1 DEFAULT '',
  `salt` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `expire` int(11) DEFAULT '0',
  `access_token` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `sessions`
--

INSERT INTO `sessions` (`sid`, `uid`, `host`, `timestamp`, `user_agent`, `salt`, `expire`, `access_token`) VALUES
('645rh894eddjgsapblhhnh2lh4', '2', '192.168.137.1', 1446950127, 'Apache-HttpClient/UNAVAILABLE (java 1.4)', '7auyP', 1447554927, '104b033cf9ffbcfad2b08bb2076541c21df6e563'),
('68c8jnavafjc2jhoopjnuevcv7', '6', '192.168.137.1', 1446696463, 'Apache-HttpClient/UNAVAILABLE (java 1.4)', 'v2GGj', 1447301263, '064d437a7a0b341e0af067140da7e3fdc316d715'),
('893ti14t1h7k3mgg6nclvkanu3', '6', '192.168.137.1', 1446696557, 'Apache-HttpClient/UNAVAILABLE (java 1.4)', 'z0vgr', 1447301357, '560d33c9cf3ebe2fde7007eeabc6239a2a47a8d3'),
('98cr0u67k72jhavkng14kq8ua5', '2', '192.168.137.1', 1446945717, 'Apache-HttpClient/UNAVAILABLE (java 1.4)', 'r5o0H', 1447550517, '7998f56d8f484ea44a39e87feb342518b40f5b36'),
('a14b3u9pa9ep5o2e6lsr72jad0', '6', '192.168.137.1', 1446699636, 'Apache-HttpClient/UNAVAILABLE (java 1.4)', 'jbDy0', 1447304436, '3d3bafc1a4627d8566adaec968ce24616a6e28f5'),
('a9db19939k7j7s1f520g4rf8h5', '2', '192.168.137.1', 1446952867, 'Apache-HttpClient/UNAVAILABLE (java 1.4)', 'sSZfV', 1447557667, '15b650e24b2f7f652ced7da4bf30e288be9ffdfe'),
('avh9q5jnh1ld4v4t5f09d1kg76', '2', '192.168.137.1', 1446949454, 'Apache-HttpClient/UNAVAILABLE (java 1.4)', 'TCI0K', 1447554254, '5985f18b647ed00c42b2d01c983f0696b5cca89a'),
('birint6iff4or17obimdp86vp0', '6', '192.168.137.1', 1446696115, 'Apache-HttpClient/UNAVAILABLE (java 1.4)', 'yTPjG', 1447300915, '2f396e9449b05773edca94a7ea9109a42e3c1d35'),
('bmnp4pvguj6t6u9l7r3c801o34', '2', '192.168.137.1', 1446945558, 'Apache-HttpClient/UNAVAILABLE (java 1.4)', 'Pc9Bh', 1447550358, 'cc887e2ff8af1ae8201a3b16f4b320140f3a164b'),
('bv89b5c8ouhspir41sesp1k537', '6', '192.168.137.1', 1446730481, 'Apache-HttpClient/UNAVAILABLE (java 1.4)', 'wjwtA', 1447335281, '4a484c9efefab61faff6feb53be611835e59d306'),
('cl1ur3fss7pma7gouaafqhq4q7', '6', '192.168.137.1', 1446695335, 'Apache-HttpClient/UNAVAILABLE (java 1.4)', 'CzpQI', 1447300135, 'bce9a1237ea7c6dbae01145cd4d6c985a06b630d'),
('faigc6raqbkobrq70daq702vh1', '6', '192.168.137.1', 1446693588, 'Apache-HttpClient/UNAVAILABLE (java 1.4)', 'pHfbY', 1447298388, 'd7ebd3bd1e80d36a7b148e9dfb4b8176b09d891c'),
('hvet2sn07695psv4a9ahnvv622', '6', '192.168.137.1', 1446693101, 'Apache-HttpClient/UNAVAILABLE (java 1.4)', '7MSAA', 1447297901, 'a5355aea5c2c0867ede0874814ae0d9a94e24fc5'),
('k55qpt0umvb04t0hmanv2mdmh4', '6', '192.168.137.1', 1446691336, 'Apache-HttpClient/UNAVAILABLE (java 1.4)', 'kVHMo', 1447296136, '6a6dd8294bf93b51287d503d7c7a6d1cc2d0e613'),
('kt6au22vu6eaups20823716uh7', '6', '192.168.137.1', 1446692059, 'Apache-HttpClient/UNAVAILABLE (java 1.4)', '4H7p4', 1447296859, 'deaecc8cd9454e3f790c92c29a62a80ddf9a0cf6'),
('l58ectrjshbns4e9hltoldoqt2', '2', '192.168.137.1', 1446954916, 'Apache-HttpClient/UNAVAILABLE (java 1.4)', '7FQk9', 1447559716, '970414bb7e03ce702693c924eb4836fa72b5da22'),
('l604pkbb8ks5pd9em07r4j6ou4', '6', '192.168.137.1', 1446693453, 'Apache-HttpClient/UNAVAILABLE (java 1.4)', '7jco9', 1447298253, '16042f58573cb6905a2cd12993e952d029df7f07'),
('mnqon6nd3luqfmnu0k9raeqic0', '6', '192.168.137.1', 1446693902, 'Apache-HttpClient/UNAVAILABLE (java 1.4)', '4xrFa', 1447298702, 'f2cadb152261f5452dd3d99bc458b75658e43687'),
('os3o7fnkilojqppir2451d0n22', '6', '192.168.137.1', 1446691686, 'Apache-HttpClient/UNAVAILABLE (java 1.4)', 'sm6os', 1447296486, '3f48db8aab7defc74663a3a3ac624ce02352ca8c'),
('s6kl26j0ajh41gupd1do3v4gm7', '6', '192.168.137.1', 1446696234, 'Apache-HttpClient/UNAVAILABLE (java 1.4)', 'whmqJ', 1447301034, 'f06766c08b5d9f92fd0d519fb37af9add165058a'),
('slo87iird2q2fhm3uuf2djf761', '6', '192.168.137.1', 1446695926, 'Apache-HttpClient/UNAVAILABLE (java 1.4)', 'BRBO9', 1447300726, 'f1b612afe462f4f14617a7f9b7ee4b9d15c78423'),
('v9d6nop1lcmjem1dlrh2c40it3', '2', '192.168.137.1', 1446948535, 'Apache-HttpClient/UNAVAILABLE (java 1.4)', 'c8d7W', 1447553335, 'b80efdbe86c580a20f843921b9f52f7a9ae3657d'),
('vi0e3obuurambi50rsqe2rqk12', '2', 'localhost', 1446942849, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.49 Safari/537.36', 'kTpAR', 1447547649, '40ec521d6d3d7394491d80f09a5aba1fb18d9306'),
('vi1dfc6bgp9g9ii7rn6m6jnej2', '6', '192.168.137.1', 1446694262, 'Apache-HttpClient/UNAVAILABLE (java 1.4)', '4JbaD', 1447299062, '404ef10ad899b345ffe9f43919a4ae15bb9824a6');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `uf`
--

CREATE TABLE IF NOT EXISTS `uf` (
  `uf_id` int(4) NOT NULL,
  `uf_name` varchar(10) NOT NULL,
  `uf_cont` varchar(45) NOT NULL,
  `uf_mail` varchar(45) DEFAULT NULL,
  `uf_tel` varchar(20) NOT NULL,
  `uf_tel2` varchar(20) DEFAULT NULL,
  `uf_birth` date DEFAULT NULL,
  `uf_hansa` int(6) DEFAULT NULL,
  `uf_in` date DEFAULT NULL,
  `uf_out` date DEFAULT NULL,
  `uf_extra1` varchar(45) DEFAULT NULL,
  `uf_extra2` varchar(45) DEFAULT NULL,
  `uf_extra3` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE IF NOT EXISTS `users` (
`user_id` int(10) NOT NULL,
  `email` varchar(45) NOT NULL,
  `password` varchar(128) NOT NULL,
  `salt` varchar(128) NOT NULL,
  `username` varchar(45) NOT NULL,
  `building_id` int(6) NOT NULL,
  `uf` varchar(4) NOT NULL,
  `userrole` varchar(3) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`user_id`, `email`, `password`, `salt`, `username`, `building_id`, `uf`, `userrole`) VALUES
(1, 'pablo.pereda@gmail.com', '3ec0b250c07b1eb71f2911ae60664e0cf4bbe36772b578a24cb34e0fc49cf3436485d5d74b7301ca1138a63450e4d8902b4baec0534eae9a5082e7f342057022', '86eb7109a24df01dcd2f874a2aca6bdc968b76fdb721ba888407f5c3c98ab525fd0e107f166a880e3656c5c1debf32d485fef673c6aacf377073dd4630f72bdc', 'pablo.pereda@gmail.com', 1, '1', 'usr');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `access`
--
ALTER TABLE `access`
 ADD PRIMARY KEY (`access_id`);

--
-- Indices de la tabla `alerts`
--
ALTER TABLE `alerts`
 ADD PRIMARY KEY (`alert_id`), ADD UNIQUE KEY `alert_id` (`alert_id`);

--
-- Indices de la tabla `buildings`
--
ALTER TABLE `buildings`
 ADD PRIMARY KEY (`building_id`), ADD KEY `hansa` (`bld_hansa`);

--
-- Indices de la tabla `cards`
--
ALTER TABLE `cards`
 ADD PRIMARY KEY (`card_id`), ADD KEY `cards_by_bld` (`building_id`);

--
-- Indices de la tabla `events`
--
ALTER TABLE `events`
 ADD PRIMARY KEY (`evt_id`), ADD KEY `events_by_building` (`building_id`);

--
-- Indices de la tabla `members`
--
ALTER TABLE `members`
 ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `messages`
--
ALTER TABLE `messages`
 ADD PRIMARY KEY (`msg_id`);

--
-- Indices de la tabla `sessions`
--
ALTER TABLE `sessions`
 ADD PRIMARY KEY (`sid`), ADD UNIQUE KEY `access_token` (`access_token`);

--
-- Indices de la tabla `uf`
--
ALTER TABLE `uf`
 ADD PRIMARY KEY (`uf_id`), ADD UNIQUE KEY `uf_id` (`uf_id`), ADD UNIQUE KEY `uf_hansa` (`uf_hansa`), ADD KEY `uf_cont` (`uf_cont`), ADD KEY `uf_mail` (`uf_mail`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
 ADD PRIMARY KEY (`user_id`), ADD KEY `emails` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `access`
--
ALTER TABLE `access`
MODIFY `access_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT de la tabla `alerts`
--
ALTER TABLE `alerts`
MODIFY `alert_id` int(4) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT de la tabla `events`
--
ALTER TABLE `events`
MODIFY `evt_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=34;
--
-- AUTO_INCREMENT de la tabla `members`
--
ALTER TABLE `members`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT de la tabla `messages`
--
ALTER TABLE `messages`
MODIFY `msg_id` int(6) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
MODIFY `user_id` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
