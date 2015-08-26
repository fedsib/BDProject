-- phpMyAdmin SQL Dump
-- version 3.3.2deb1ubuntu1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generato il: 25 ago, 2015 at 12:47 PM
-- Versione MySQL: 5.1.73
-- Versione PHP: 5.3.2-1ubuntu4.30

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `mleorato-PR`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `ACCOUNT`
--

DROP TABLE IF EXISTS `ACCOUNT`;
CREATE TABLE IF NOT EXISTS `ACCOUNT` (
  `CodFiscale` char(16) COLLATE latin1_general_ci NOT NULL,
  `UserName` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `Admin` tinyint(1) NOT NULL DEFAULT '0',
  `Hash` varchar(255) COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`CodFiscale`),
  UNIQUE KEY `UserName` (`UserName`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dump dei dati per la tabella `ACCOUNT`
--

INSERT INTO `ACCOUNT` (`CodFiscale`, `UserName`, `Admin`, `Hash`) VALUES
('0123450123456789', 'admin', 1, 'd033e22ae348aeb5660fc2140aec35850c4da997'),
('1234567890123456', 'user', 0, '12dea96fec20593566ab75692c9949596833adc9'),
('CL82272192LE98I4', 'Tarlor', 1, '1fe50b9d1b0309357b10a3d489ecf4d2cf2b43e7'),
('DO17674591283KSW', 'Walfer', 0, '3ce5713fc642abc9c7314fa55ac55f2d21573f73'),
('DX44391686ALF756', 'Isamar', 0, '7dc1b85063c69eddb08ae9f01aecf3efd788d231'),
('GK4950736629IDKL', 'Adeluc', 0, '179e8147fdb8124a420f8c17764f60462c4cf4a0'),
('GQ5460162028ILKS', 'Vinben', 0, '55ae7260de3e1e0de2b6e49633702ca1064f55a2'),
('HE96288477ALDK43', 'Ausfal', 0, '2ce0de8380f843acc838a7934656d5c9377faa14'),
('HP1736972423GHT7', 'Pricre', 0, 'ab03877093956b41259ac5cf5c4d0fca7ded66c9'),
('NE34267554ALG938', 'Anglon', 0, 'e72b1e0166b136837dcc9ed91bc960854b5da563'),
('OP41172696928KJF', 'Edizet', 0, '811b74351cff69001c22acad6722e013208544d1'),
('QL7412357094KJSL', 'Clatos', 0, '50e2836f4ba3ba1fb34438d0f04395cc3e412d4b'),
('QP82934726298KER', 'Damman', 1, '3c74039c54df606e4db5ca175dbde5ed8f75020c'),
('SS0288743SKL2931', 'Bricat', 0, '897ddc030580ff4039558a7a89af42eefe58245b'),
('TC9005367829KDJR', 'Flolor', 1, '5afd046c791271b3a087dd356881358e607f1d12'),
('TH62203615293KGI', 'Trapug', 1, 'eb47e7ad62d714f1517f495e37d077ded46dfa47'),
('WV40597218APO564', 'Alvfer', 0, '92205497ad3f3397ee8847309dd05cb701c891f4'),
('ZO11191448AJ85H4', 'Batfio', 0, '4b6639823b835e4e306a8a85b69afe058acd9b2a');

-- --------------------------------------------------------

--
-- Struttura della tabella `CAMPO`
--

DROP TABLE IF EXISTS `CAMPO`;
CREATE TABLE IF NOT EXISTS `CAMPO` (
  `CodCampo` tinyint(4) NOT NULL,
  `TipoSup` enum('Terra Rossa','Erba Sintetica','PlayIt') COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`CodCampo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dump dei dati per la tabella `CAMPO`
--

INSERT INTO `CAMPO` (`CodCampo`, `TipoSup`) VALUES
(1, 'Terra Rossa'),
(2, 'Erba Sintetica'),
(3, 'PlayIt'),
(4, 'Terra Rossa');

-- --------------------------------------------------------

--
-- Struttura della tabella `CORSO`
--

DROP TABLE IF EXISTS `CORSO`;
CREATE TABLE IF NOT EXISTS `CORSO` (
  `CodCorso` int(11) NOT NULL AUTO_INCREMENT,
  `NomeCorso` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `TipoCorso` enum('Principiante','Intermedio','Avanzato') COLLATE latin1_general_ci NOT NULL,
  `Attivo` tinyint(1) NOT NULL DEFAULT '0',
  `CodFiscale` char(16) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`CodCorso`),
  KEY `CodFiscale` (`CodFiscale`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=20 ;

--
-- Dump dei dati per la tabella `CORSO`
--

INSERT INTO `CORSO` (`CodCorso`, `NomeCorso`, `TipoCorso`, `Attivo`, `CodFiscale`) VALUES
(2, 'Corso Estivo di Base Tennis', 'Principiante', 1, 'CL82272192LE98I4'),
(4, 'Corso Estivo di Tennis Intermedio', 'Intermedio', 1, '0123450123456789'),
(5, 'Corso Estivo di Tennis Avanzato', 'Avanzato', 1, 'QP82934726298KER'),
(14, 'Corso Primaverile di Tennis Intermedio', 'Intermedio', 0, NULL),
(15, 'Corso Primaverile di Base Tennis', 'Principiante', 0, 'TC9005367829KDJR'),
(16, 'Corso Primaverile di Tennis Avanzato', 'Avanzato', 0, NULL),
(19, 'Corso Speciale Invernale', 'Avanzato', 0, 'CL82272192LE98I4');

-- --------------------------------------------------------

--
-- Struttura della tabella `ISCRITTOCORSO`
--

DROP TABLE IF EXISTS `ISCRITTOCORSO`;
CREATE TABLE IF NOT EXISTS `ISCRITTOCORSO` (
  `CodCorso` int(11) NOT NULL,
  `CodFiscale` char(16) COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`CodCorso`,`CodFiscale`),
  KEY `CodFiscale` (`CodFiscale`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dump dei dati per la tabella `ISCRITTOCORSO`
--

INSERT INTO `ISCRITTOCORSO` (`CodCorso`, `CodFiscale`) VALUES
(2, '1234567890123456'),
(5, '1234567890123456'),
(2, 'DO17674591283KSW'),
(4, 'DO17674591283KSW'),
(2, 'DX44391686ALF756'),
(2, 'GK4950736629IDKL'),
(2, 'GQ5460162028ILKS'),
(4, 'GQ5460162028ILKS'),
(4, 'HE96288477ALDK43'),
(2, 'HP1736972423GHT7'),
(2, 'NE34267554ALG938'),
(2, 'OP41172696928KJF'),
(5, 'OP41172696928KJF'),
(2, 'QL7412357094KJSL'),
(2, 'SS0288743SKL2931'),
(2, 'WV40597218APO564'),
(5, 'ZO11191448AJ85H4');

-- --------------------------------------------------------

--
-- Struttura della tabella `ISTRUTTORE`
--

DROP TABLE IF EXISTS `ISTRUTTORE`;
CREATE TABLE IF NOT EXISTS `ISTRUTTORE` (
  `CodFiscale` char(16) COLLATE latin1_general_ci NOT NULL,
  `Qualifica` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
  `Retribuzione` int(11) NOT NULL,
  `DataAssunzione` date NOT NULL,
  PRIMARY KEY (`CodFiscale`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dump dei dati per la tabella `ISTRUTTORE`
--

INSERT INTO `ISTRUTTORE` (`CodFiscale`, `Qualifica`, `Retribuzione`, `DataAssunzione`) VALUES
('0123450123456789', NULL, 3000, '2015-10-05'),
('CL82272192LE98I4', 'Istrutttrice di tennis', 1500, '2015-08-25'),
('QP82934726298KER', NULL, 1800, '2015-08-25'),
('TC9005367829KDJR', NULL, 1700, '2015-08-25'),
('TH62203615293KGI', 'Istruttore di Tennis', 1400, '2015-08-25');

-- --------------------------------------------------------

--
-- Struttura della tabella `LEZIONE`
--

DROP TABLE IF EXISTS `LEZIONE`;
CREATE TABLE IF NOT EXISTS `LEZIONE` (
  `CodLezione` int(11) NOT NULL,
  `CodCorso` int(11) NOT NULL,
  PRIMARY KEY (`CodCorso`,`CodLezione`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dump dei dati per la tabella `LEZIONE`
--

INSERT INTO `LEZIONE` (`CodLezione`, `CodCorso`) VALUES
(1, 2),
(2, 2),
(3, 2),
(4, 2),
(5, 2),
(6, 2),
(7, 2),
(8, 2),
(9, 2),
(10, 2),
(11, 2),
(12, 2),
(14, 2),
(15, 2),
(16, 2),
(17, 2),
(18, 2),
(1, 4),
(2, 4),
(3, 4),
(4, 4),
(1, 5),
(2, 5),
(3, 5),
(4, 5),
(5, 5),
(6, 5),
(7, 5),
(8, 5),
(9, 5),
(10, 5),
(11, 5),
(1, 19),
(2, 19),
(3, 19),
(4, 19),
(5, 19),
(6, 19),
(7, 19),
(8, 19),
(9, 19),
(10, 19);

-- --------------------------------------------------------

--
-- Struttura della tabella `PERSONA`
--

DROP TABLE IF EXISTS `PERSONA`;
CREATE TABLE IF NOT EXISTS `PERSONA` (
  `CodFiscale` char(16) COLLATE latin1_general_ci NOT NULL,
  `Nome` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `Cognome` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `DataNasc` date NOT NULL,
  `LuogoNasc` varchar(40) COLLATE latin1_general_ci NOT NULL,
  `Telefono` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `Mail` varchar(40) COLLATE latin1_general_ci NOT NULL,
  `Sesso` enum('Maschio','Femmina') COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`CodFiscale`),
  UNIQUE KEY `Mail` (`Mail`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dump dei dati per la tabella `PERSONA`
--

INSERT INTO `PERSONA` (`CodFiscale`, `Nome`, `Cognome`, `DataNasc`, `LuogoNasc`, `Telefono`, `Mail`, `Sesso`) VALUES
('0123450123456789', 'NomeAdmin', 'CognomeAdmin', '2013-02-15', 'NascitaAdmin', '12345678', 'asd@asd.asd', 'Maschio'),
('1234567890123456', 'NomeUtente', 'CognomeUtente', '2013-02-15', 'NascitaUtente', '12345678', 'mail@utente.com', 'Maschio'),
('CL82272192LE98I4', 'Tarquinia', 'Lorenzo', '1977-02-05', 'Affori MI ', '03654858089', 'TarquiniaLorenzo@mail.com', 'Femmina'),
('DO17674591283KSW', 'Walter', 'Ferri', '1995-09-15', 'Reno Centese FE', '03808351100', 'WalterFerri@mail.com', 'Maschio'),
('DX44391686ALF756', 'Isacco', 'Marchesi', '1959-01-11', 'Atrani SA ', NULL, 'IsaccoMarchesi@mail.com', 'Maschio'),
('GK4950736629IDKL', 'Adelfina', 'Lucchesi', '1999-09-01', 'Pellizzano TN ', '03804325023', 'AdelfinaLucchesi@mail.com', 'Femmina'),
('GQ5460162028ILKS', 'Vincenza', 'Beneventi', '1990-07-05', 'Albano Alessandro BG', '123846327', 'VincenzaBeneventi@mail.com', 'Femmina'),
('HE96288477ALDK43', 'Ausonia', 'Fallaci', '1976-02-14', 'Vespolate NO', '03738236963', 'AusoniaFallaci@mail.com', 'Femmina'),
('HP1736972423GHT7', 'Principio', 'Cremonesi', '1978-06-14', 'Piedelpoggio RI', NULL, 'PrincipioCremonesi@mail.com', 'Maschio'),
('NE34267554ALG938', 'Angelica', 'Longo', '1986-05-21', 'Casale PR', '49285039590', 'AngelicaLongo@mail.com', 'Femmina'),
('OP41172696928KJF', 'Editta', 'Zetticci', '1985-06-29', 'Petritoli AP ', '03848402546', 'EdittaZetticci@mail.com', 'Femmina'),
('QL7412357094KJSL', 'Claudio', 'Toscano', '1962-12-05', 'Carbonate CO ', '03734201868', 'ClaudioToscano@mail.com', 'Maschio'),
('QP82934726298KER', 'Damiano', 'Manna', '1954-04-06', 'Romans Isonzo GO ', '03191347085', 'DamianoManna@mail.com', 'Maschio'),
('SS0288743SKL2931', 'Brigida', 'Cattaneo', '1995-05-27', 'Cotone LI', NULL, 'BrigidaCattaneo@mail.com', 'Femmina'),
('TC9005367829KDJR', 'Flora', 'Lorenzo', '1950-05-16', 'Anitrella FR', '4171167', 'FloraLorenzo@mail.com', 'Femmina'),
('TH62203615293KGI', 'Tranquillino', 'Pugliesi', '1987-05-28', 'Villaggio Santa Barbara CL ', '03247244105', 'TranquillinoPugliesi@mail.com', 'Maschio'),
('WV40597218APO564', 'Alvisa', 'Ferrari', '1970-09-19', 'Borgata Paradiso Di Collegno TO', '0424958677', 'AlvisaFerrari@mail.com', 'Femmina'),
('ZO11191448AJ85H4', 'Batilda', 'Fiorentino', '1969-05-20', 'Fagnano Alto AQ', NULL, 'BatildaFiorentino@mail.com', 'Femmina');

-- --------------------------------------------------------

--
-- Struttura della tabella `PRENOTAZIONE`
--

DROP TABLE IF EXISTS `PRENOTAZIONE`;
CREATE TABLE IF NOT EXISTS `PRENOTAZIONE` (
  `CodCorso` int(11) DEFAULT NULL,
  `CodLezione` int(11) DEFAULT NULL,
  `CodFiscale` char(16) COLLATE latin1_general_ci DEFAULT NULL,
  `CodCampo` tinyint(4) NOT NULL,
  `Data` date NOT NULL,
  `Ora` int(11) NOT NULL,
  PRIMARY KEY (`CodCampo`,`Data`,`Ora`),
  KEY `CodCorso` (`CodCorso`,`CodLezione`),
  KEY `CodFiscale` (`CodFiscale`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dump dei dati per la tabella `PRENOTAZIONE`
--

INSERT INTO `PRENOTAZIONE` (`CodCorso`, `CodLezione`, `CodFiscale`, `CodCampo`, `Data`, `Ora`) VALUES
(NULL, NULL, 'QL7412357094KJSL', 1, '2015-08-28', 9),
(NULL, NULL, 'TH62203615293KGI', 1, '2015-08-29', 13),
(NULL, NULL, 'QP82934726298KER', 1, '2015-08-29', 14),
(NULL, NULL, 'CL82272192LE98I4', 1, '2015-08-30', 12),
(5, 1, NULL, 1, '2015-08-30', 13),
(NULL, NULL, '0123450123456789', 1, '2015-08-30', 14),
(NULL, NULL, 'DX44391686ALF756', 1, '2015-08-31', 12),
(NULL, NULL, 'WV40597218APO564', 1, '2015-08-31', 14),
(NULL, NULL, 'QL7412357094KJSL', 1, '2015-08-31', 15),
(NULL, NULL, 'DO17674591283KSW', 1, '2015-09-01', 14),
(NULL, NULL, 'SS0288743SKL2931', 1, '2015-09-01', 17),
(NULL, NULL, 'OP41172696928KJF', 1, '2015-09-02', 10),
(NULL, NULL, 'GK4950736629IDKL', 1, '2015-09-02', 13),
(NULL, NULL, '1234567890123456', 1, '2015-09-02', 14),
(NULL, NULL, 'SS0288743SKL2931', 1, '2015-09-02', 15),
(NULL, NULL, 'TH62203615293KGI', 1, '2015-09-02', 16),
(2, 2, NULL, 1, '2015-09-03', 12),
(NULL, NULL, '1234567890123456', 1, '2015-09-03', 14),
(2, 4, NULL, 1, '2015-09-04', 9),
(NULL, NULL, 'OP41172696928KJF', 1, '2015-09-04', 12),
(NULL, NULL, 'HP1736972423GHT7', 1, '2015-09-04', 14),
(NULL, NULL, 'NE34267554ALG938', 1, '2015-09-04', 15),
(NULL, NULL, 'QL7412357094KJSL', 1, '2015-09-04', 17),
(NULL, NULL, 'OP41172696928KJF', 1, '2015-09-05', 12),
(4, 3, NULL, 1, '2015-09-05', 14),
(NULL, NULL, 'TH62203615293KGI', 1, '2015-09-05', 15),
(NULL, NULL, 'GQ5460162028ILKS', 1, '2015-09-06', 12),
(NULL, NULL, 'ZO11191448AJ85H4', 1, '2015-09-06', 13),
(NULL, NULL, '0123450123456789', 1, '2015-09-06', 15),
(NULL, NULL, 'DX44391686ALF756', 1, '2015-09-06', 16),
(NULL, NULL, 'TC9005367829KDJR', 1, '2015-09-06', 17),
(NULL, NULL, 'NE34267554ALG938', 1, '2015-09-07', 13),
(2, 10, NULL, 1, '2015-09-11', 14),
(4, 1, NULL, 1, '2015-09-14', 9),
(2, 3, NULL, 1, '2015-10-15', 15),
(NULL, NULL, '0123450123456789', 2, '2015-08-24', 9),
(NULL, NULL, '0123450123456789', 2, '2015-08-24', 10),
(NULL, NULL, '1234567890123456', 2, '2015-08-28', 10),
(NULL, NULL, 'DO17674591283KSW', 2, '2015-08-28', 15),
(NULL, NULL, 'TC9005367829KDJR', 2, '2015-08-29', 10),
(NULL, NULL, 'QP82934726298KER', 2, '2015-08-29', 12),
(NULL, NULL, 'QL7412357094KJSL', 2, '2015-08-29', 13),
(NULL, NULL, 'HE96288477ALDK43', 2, '2015-08-29', 14),
(NULL, NULL, 'SS0288743SKL2931', 2, '2015-08-30', 14),
(NULL, NULL, 'TH62203615293KGI', 2, '2015-08-30', 15),
(NULL, NULL, '1234567890123456', 2, '2015-08-31', 12),
(NULL, NULL, 'DO17674591283KSW', 2, '2015-08-31', 13),
(NULL, NULL, 'HE96288477ALDK43', 2, '2015-08-31', 14),
(NULL, NULL, 'GK4950736629IDKL', 2, '2015-08-31', 15),
(NULL, NULL, 'OP41172696928KJF', 2, '2015-09-01', 11),
(NULL, NULL, 'DX44391686ALF756', 2, '2015-09-01', 12),
(NULL, NULL, 'CL82272192LE98I4', 2, '2015-09-01', 13),
(4, 2, NULL, 2, '2015-09-01', 15),
(5, 2, NULL, 2, '2015-09-02', 9),
(NULL, NULL, 'ZO11191448AJ85H4', 2, '2015-09-02', 10),
(NULL, NULL, 'GQ5460162028ILKS', 2, '2015-09-02', 13),
(NULL, NULL, 'NE34267554ALG938', 2, '2015-09-02', 14),
(NULL, NULL, 'ZO11191448AJ85H4', 2, '2015-09-02', 15),
(NULL, NULL, 'TC9005367829KDJR', 2, '2015-09-02', 17),
(NULL, NULL, 'GQ5460162028ILKS', 2, '2015-09-03', 9),
(NULL, NULL, 'NE34267554ALG938', 2, '2015-09-03', 10),
(NULL, NULL, 'SS0288743SKL2931', 2, '2015-09-03', 15),
(NULL, NULL, 'TH62203615293KGI', 2, '2015-09-04', 11),
(NULL, NULL, 'QP82934726298KER', 2, '2015-09-04', 13),
(NULL, NULL, 'QP82934726298KER', 2, '2015-09-04', 14),
(NULL, NULL, 'HE96288477ALDK43', 2, '2015-09-05', 12),
(NULL, NULL, '1234567890123456', 2, '2015-09-05', 13),
(NULL, NULL, 'WV40597218APO564', 2, '2015-09-05', 14),
(NULL, NULL, 'GK4950736629IDKL', 2, '2015-09-06', 11),
(5, 4, NULL, 2, '2015-09-08', 13),
(NULL, NULL, 'HP1736972423GHT7', 2, '2015-09-09', 12),
(NULL, NULL, 'GK4950736629IDKL', 2, '2015-09-09', 13),
(2, 17, NULL, 2, '2015-09-14', 14),
(5, 5, NULL, 2, '2015-09-15', 15),
(2, 18, NULL, 2, '2015-09-15', 16),
(5, 10, NULL, 2, '2015-09-16', 12),
(5, 9, NULL, 2, '2015-09-16', 13),
(5, 6, NULL, 2, '2015-09-18', 14),
(5, 7, NULL, 2, '2015-09-19', 13),
(5, 11, NULL, 2, '2015-09-20', 13),
(5, 8, NULL, 2, '2015-09-22', 13),
(NULL, NULL, '1234567890123456', 3, '2015-08-22', 9),
(NULL, NULL, '1234567890123456', 3, '2015-08-22', 10),
(NULL, NULL, '0123450123456789', 3, '2015-08-23', 14),
(NULL, NULL, 'CL82272192LE98I4', 3, '2015-08-28', 14),
(NULL, NULL, 'CL82272192LE98I4', 3, '2015-08-29', 9),
(NULL, NULL, 'ZO11191448AJ85H4', 3, '2015-08-29', 11),
(NULL, NULL, 'WV40597218APO564', 3, '2015-08-29', 13),
(NULL, NULL, 'HE96288477ALDK43', 3, '2015-08-30', 9),
(NULL, NULL, 'DX44391686ALF756', 3, '2015-08-30', 12),
(NULL, NULL, 'TC9005367829KDJR', 3, '2015-08-30', 13),
(NULL, NULL, 'QP82934726298KER', 3, '2015-08-30', 14),
(NULL, NULL, 'SS0288743SKL2931', 3, '2015-08-31', 9),
(NULL, NULL, '0123450123456789', 3, '2015-08-31', 13),
(NULL, NULL, 'DO17674591283KSW', 3, '2015-08-31', 14),
(NULL, NULL, 'WV40597218APO564', 3, '2015-08-31', 16),
(NULL, NULL, 'GQ5460162028ILKS', 3, '2015-09-01', 11),
(NULL, NULL, 'CL82272192LE98I4', 3, '2015-09-01', 12),
(NULL, NULL, 'OP41172696928KJF', 3, '2015-09-01', 14),
(NULL, NULL, 'CL82272192LE98I4', 3, '2015-09-01', 15),
(NULL, NULL, 'DO17674591283KSW', 3, '2015-09-01', 16),
(NULL, NULL, 'HP1736972423GHT7', 3, '2015-09-02', 13),
(NULL, NULL, 'QL7412357094KJSL', 3, '2015-09-03', 11),
(NULL, NULL, '0123450123456789', 3, '2015-09-03', 13),
(NULL, NULL, 'TC9005367829KDJR', 3, '2015-09-05', 12),
(NULL, NULL, 'QL7412357094KJSL', 3, '2015-09-05', 13),
(NULL, NULL, 'GQ5460162028ILKS', 3, '2015-09-05', 15),
(NULL, NULL, 'HP1736972423GHT7', 3, '2015-09-06', 12),
(NULL, NULL, 'OP41172696928KJF', 3, '2015-09-06', 13),
(2, 11, NULL, 3, '2015-09-06', 14),
(NULL, NULL, 'TH62203615293KGI', 3, '2015-09-06', 15),
(5, 3, NULL, 3, '2015-09-07', 13),
(NULL, NULL, 'DX44391686ALF756', 3, '2015-09-07', 14),
(NULL, NULL, '0123450123456789', 3, '2015-09-07', 15),
(NULL, NULL, 'GQ5460162028ILKS', 3, '2015-09-07', 16),
(NULL, NULL, 'GK4950736629IDKL', 3, '2015-09-08', 12),
(2, 14, NULL, 3, '2015-09-13', 13),
(4, 4, NULL, 3, '2015-09-16', 13),
(2, 15, NULL, 3, '2015-09-16', 15),
(2, 16, NULL, 3, '2015-09-17', 14),
(2, 1, NULL, 3, '2015-10-20', 14),
(NULL, NULL, 'ZO11191448AJ85H4', 4, '2015-08-29', 12),
(NULL, NULL, 'DX44391686ALF756', 4, '2015-08-29', 15),
(NULL, NULL, 'WV40597218APO564', 4, '2015-08-30', 11),
(NULL, NULL, 'TC9005367829KDJR', 4, '2015-08-30', 12),
(NULL, NULL, '1234567890123456', 4, '2015-08-31', 15),
(NULL, NULL, 'HP1736972423GHT7', 4, '2015-09-01', 12),
(NULL, NULL, 'DO17674591283KSW', 4, '2015-09-01', 13),
(NULL, NULL, 'GK4950736629IDKL', 4, '2015-09-01', 14),
(NULL, NULL, 'DX44391686ALF756', 4, '2015-09-01', 15),
(NULL, NULL, 'QP82934726298KER', 4, '2015-09-03', 12),
(NULL, NULL, 'HE96288477ALDK43', 4, '2015-09-03', 13),
(NULL, NULL, 'SS0288743SKL2931', 4, '2015-09-03', 14),
(NULL, NULL, '0123450123456789', 4, '2015-09-03', 16),
(NULL, NULL, 'GK4950736629IDKL', 4, '2015-09-04', 11),
(NULL, NULL, 'ZO11191448AJ85H4', 4, '2015-09-04', 13),
(NULL, NULL, 'WV40597218APO564', 4, '2015-09-05', 12),
(NULL, NULL, 'HE96288477ALDK43', 4, '2015-09-06', 12),
(NULL, NULL, 'HP1736972423GHT7', 4, '2015-09-08', 11),
(NULL, NULL, 'NE34267554ALG938', 4, '2015-09-08', 13),
(2, 12, NULL, 4, '2015-09-12', 14);

-- --------------------------------------------------------

--
-- Struttura della tabella `SOCIO`
--

DROP TABLE IF EXISTS `SOCIO`;
CREATE TABLE IF NOT EXISTS `SOCIO` (
  `CodFiscale` char(16) COLLATE latin1_general_ci NOT NULL,
  `DataIscrizione` date NOT NULL,
  `Livello` enum('Principiante','Intermedio','Esperto') COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`CodFiscale`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dump dei dati per la tabella `SOCIO`
--

INSERT INTO `SOCIO` (`CodFiscale`, `DataIscrizione`, `Livello`) VALUES
('1234567890123456', '2015-08-10', 'Esperto'),
('DO17674591283KSW', '2015-08-25', 'Esperto'),
('DX44391686ALF756', '2015-08-25', 'Principiante'),
('GK4950736629IDKL', '2015-08-25', 'Principiante'),
('GQ5460162028ILKS', '2015-08-25', 'Intermedio'),
('HE96288477ALDK43', '2015-08-25', 'Intermedio'),
('HP1736972423GHT7', '2015-08-25', 'Intermedio'),
('NE34267554ALG938', '2015-08-25', 'Intermedio'),
('OP41172696928KJF', '2015-08-25', 'Esperto'),
('QL7412357094KJSL', '2015-08-25', 'Principiante'),
('SS0288743SKL2931', '2015-08-25', 'Principiante'),
('WV40597218APO564', '2015-08-25', 'Principiante'),
('ZO11191448AJ85H4', '2015-08-25', 'Esperto');

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `ACCOUNT`
--
ALTER TABLE `ACCOUNT`
  ADD CONSTRAINT `ACCOUNT_ibfk_1` FOREIGN KEY (`CodFiscale`) REFERENCES `PERSONA` (`CodFiscale`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `CORSO`
--
ALTER TABLE `CORSO`
  ADD CONSTRAINT `CORSO_ibfk_1` FOREIGN KEY (`CodFiscale`) REFERENCES `ISTRUTTORE` (`CodFiscale`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Limiti per la tabella `ISCRITTOCORSO`
--
ALTER TABLE `ISCRITTOCORSO`
  ADD CONSTRAINT `ISCRITTOCORSO_ibfk_1` FOREIGN KEY (`CodCorso`) REFERENCES `CORSO` (`CodCorso`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ISCRITTOCORSO_ibfk_2` FOREIGN KEY (`CodFiscale`) REFERENCES `SOCIO` (`CodFiscale`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `ISTRUTTORE`
--
ALTER TABLE `ISTRUTTORE`
  ADD CONSTRAINT `ISTRUTTORE_ibfk_1` FOREIGN KEY (`CodFiscale`) REFERENCES `PERSONA` (`CodFiscale`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `LEZIONE`
--
ALTER TABLE `LEZIONE`
  ADD CONSTRAINT `LEZIONE_ibfk_1` FOREIGN KEY (`CodCorso`) REFERENCES `CORSO` (`CodCorso`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `PRENOTAZIONE`
--
ALTER TABLE `PRENOTAZIONE`
  ADD CONSTRAINT `PRENOTAZIONE_ibfk_4` FOREIGN KEY (`CodCampo`) REFERENCES `CAMPO` (`CodCampo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `PRENOTAZIONE_ibfk_1` FOREIGN KEY (`CodCorso`, `CodLezione`) REFERENCES `LEZIONE` (`CodCorso`, `CodLezione`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `PRENOTAZIONE_ibfk_3` FOREIGN KEY (`CodFiscale`) REFERENCES `PERSONA` (`CodFiscale`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `SOCIO`
--
ALTER TABLE `SOCIO`
  ADD CONSTRAINT `SOCIO_ibfk_1` FOREIGN KEY (`CodFiscale`) REFERENCES `PERSONA` (`CodFiscale`) ON DELETE CASCADE ON UPDATE CASCADE;

DELIMITER $$
--
-- Funzioni
--
DROP FUNCTION IF EXISTS `ControlloPrenotazione`$$
CREATE FUNCTION `ControlloPrenotazione`(cod CHAR(16), d DATE, h int, c int) RETURNS char(100) CHARSET latin1
BEGIN	
	DECLARE presente int;
	SELECT COUNT(*) INTO presente FROM PRENOTAZIONE LEFT JOIN CORSO ON PRENOTAZIONE.CodCorso = CORSO.CodCorso LEFT JOIN ISCRITTOCORSO ON PRENOTAZIONE.CodCorso = ISCRITTOCORSO.CodCorso
	WHERE PRENOTAZIONE.Data = d AND PRENOTAZIONE.Ora = h AND (PRENOTAZIONE.CodFiscale = cod || CORSO.CodFiscale = cod || ISCRITTOCORSO.CodFiscale = cod);
	
		IF presente > 0 THEN
			RETURN CONCAT('Errore, hai gia\' una prenotazione nella data ',d,' alle ',h);
		ELSE
			INSERT INTO PRENOTAZIONE (CodFiscale, CodCampo, Data, Ora) VALUES (cod, c, d, h);
			RETURN 'Prenotazione aggiunta con successo';
		END IF;
END$$

DROP FUNCTION IF EXISTS `ControlloPrenotazioneCorso`$$
CREATE FUNCTION `ControlloPrenotazioneCorso`(cod CHAR(16), d DATE, h int, c int, corso int) RETURNS char(120) CHARSET latin1
BEGIN	
	DECLARE presente INT;
	DECLARE num INT;
	SELECT COUNT(*) INTO presente FROM PRENOTAZIONE LEFT JOIN CORSO ON PRENOTAZIONE.CodCorso = CORSO.CodCorso WHERE PRENOTAZIONE.Data = d AND PRENOTAZIONE.Ora = h AND (PRENOTAZIONE.CodFiscale = cod || CORSO.CodFiscale = cod);
	SELECT CodLezione INTO num FROM LEZIONE WHERE CodCorso = corso ORDER BY CodLezione DESC LIMIT 1;
		IF (ISNULL(num)) THEN
			SET num = 0;
		END IF;
		IF presente > 0 THEN
			RETURN CONCAT('Errore, questo istruttore ha gia\' una prenotazione il ',d,' alle ',h);
		ELSE
			
			SET num = num + 1;
			INSERT INTO LEZIONE (CodCorso, CodLezione) VALUES (corso, num);
			INSERT INTO PRENOTAZIONE (CodCorso, CodLezione, CodCampo, Data, Ora) VALUES (corso, num, c, d, h);
			RETURN 'Prenotazione aggiunta con successo';
		END IF;
END$$

DROP FUNCTION IF EXISTS `PossoIscrivermi`$$
CREATE FUNCTION `PossoIscrivermi`(usr VARCHAR(40), corso int) RETURNS char(120) CHARSET latin1
BEGIN 
	DECLARE difficolta char(20);
	DECLARE d int;
	DECLARE abilita char(20);
	DECLARE a int;
	SET difficolta = (SELECT TipoCorso FROM CORSO WHERE CodCorso = corso);
	IF difficolta = "Avanzato" THEN
		SET d = 3;
	ELSE
		IF difficolta = "Intermedio" THEN
		SET d = 2;
		ELSE
		SET d = 1;
		END IF;
	END IF;
	
	SET abilita = (SELECT SOCIO.Livello FROM SOCIO JOIN ACCOUNT ON SOCIO.CodFiscale = ACCOUNT.CodFiscale WHERE ACCOUNT.UserName = usr);
	IF abilita = "Esperto" THEN
		SET a = 3;
	ELSE
		IF abilita = "Intermedio" THEN
		SET a = 2;
		ELSE
		SET a = 1;
		END IF;
	END IF;
	
	IF d > a THEN
		RETURN 'Questo corso richiede un livello di abilita\' maggiore';
	ELSE
		RETURN 'Iscriviti';
	END IF;
END$$

DELIMITER ;
SET FOREIGN_KEY_CHECKS=1;

--
-- Trigger
--

DROP TRIGGER IF EXISTS InserimentoRetribuzione;
DELIMITER |
CREATE TRIGGER  InserimentoRetribuzione BEFORE INSERT ON ISTRUTTORE
	FOR EACH ROW
	BEGIN
		IF NEW.Retribuzione<0
		THEN SET NEW.Retribuzione = 800;
		END IF;
	END;
|
DELIMITER ;

DROP TRIGGER IF EXISTS AggiornaRetribuzione;
DELIMITER |
CREATE TRIGGER AggiornaRetribuzione BEFORE UPDATE ON ISTRUTTORE
	FOR EACH ROW
	BEGIN
		IF NEW.Retribuzione<0
		THEN SET NEW.Retribuzione = OLD.Retribuzione;
	END IF;
	END;
|
DELIMITER ;

DROP TRIGGER IF EXISTS CorsoAttivoIns;
DELIMITER |
CREATE TRIGGER  CorsoAttivoIns BEFORE INSERT ON CORSO
	FOR EACH ROW
	BEGIN
		IF NEW.CodFiscale IS NULL
		THEN SET NEW.Attivo = 0;
		END IF;
	END;
|
DELIMITER ;

DROP TRIGGER IF EXISTS CorsoAttivoUpd;
DELIMITER |
CREATE TRIGGER  CorsoAttivoUpd BEFORE UPDATE ON CORSO
	FOR EACH ROW
	BEGIN
		IF NEW.CodFiscale IS NULL
		THEN SET NEW.Attivo = 0;
		END IF;
	END;
|
DELIMITER ;

DROP TRIGGER IF EXISTS CorsoAttivoElim;
DELIMITER |
CREATE TRIGGER CorsoAttivoElim BEFORE DELETE ON PERSONA
FOR EACH ROW
	BEGIN
		DECLARE cod int;
		SELECT COUNT(1) INTO cod FROM CORSO
        WHERE CodFiscale = OLD.CodFiscale;
		IF cod > 0 THEN
		UPDATE CORSO AS c SET c.Attivo = 0 WHERE c.CodFiscale = OLD.CodFiscale;
		END IF;
	END;
|
DELIMITER ;	