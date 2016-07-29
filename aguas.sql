-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 29-07-2016 a las 01:15:50
-- Versión del servidor: 5.5.49-0ubuntu0.14.04.1
-- Versión de PHP: 5.5.9-1ubuntu4.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `aguas`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `expedientes`
--

CREATE TABLE IF NOT EXISTS `expedientes` (
  `contrato` char(8) NOT NULL,
  `cliente` varchar(150) NOT NULL,
  `direccion` varchar(150) NOT NULL,
  `gps` varchar(100) NOT NULL,
  UNIQUE KEY `clave` (`contrato`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='cabecera de expedientes';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `__menus`
--

CREATE TABLE IF NOT EXISTS `__menus` (
  `menuId` int(11) NOT NULL AUTO_INCREMENT,
  `menuText` varchar(50) NOT NULL,
  `menuURL` varchar(150) DEFAULT NULL,
  `menuParent` int(11) NOT NULL,
  PRIMARY KEY (`menuId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='System menus' AUTO_INCREMENT=4 ;

--
-- Volcado de datos para la tabla `__menus`
--

INSERT INTO `__menus` (`menuId`, `menuText`, `menuURL`, `menuParent`) VALUES
(1, 'Expedientes', '', 0),
(3, 'Consultar', 'expedientes', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `__usersControl`
--

CREATE TABLE IF NOT EXISTS `__usersControl` (
  `userId` int(11) NOT NULL,
  `userName` varchar(150) NOT NULL,
  `userEmail` varchar(150) DEFAULT NULL,
  `userPassword` varchar(60) DEFAULT NULL,
  `userStatus` char(1) DEFAULT NULL,
  PRIMARY KEY (`userName`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
