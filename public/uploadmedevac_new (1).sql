-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 27, 2019 at 08:17 AM
-- Server version: 10.1.38-MariaDB
-- PHP Version: 7.3.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `medevac_new`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`medevac`@`localhost` PROCEDURE `CommisionEngine` ()  MODIFIES SQL DATA
    COMMENT 'HELLO'
BEGIN
-- DECLARE n INT DEFAULT 0;
-- DECLARE i INT DEFAULT 0;
-- SELECT COUNT(*) FROM agentpayment where chargeBackCommision is not null  INTO n;
-- SET i=0;
-- WHILE i<n DO 
 UPDATE agentpayment SET ChargeBackInterest=  ChargeBackInterest + (chargeBackCommision * .01)   where (chargebackcommision is not null or chargebackcommision=0) and paymentdate <  DATE_SUB(NOW(),INTERVAL 1 month) ;
 
 Update agentpayment set Commission =  Commission + (ChargeBackCommision/ chargeBackInstalment)   where (chargebackcommision is not null or chargebackcommision=0) and paymentdate <  DATE_SUB(NOW(),INTERVAL 1 month);

Update agentpayment set  chargeBackInstalment = chargeBackInstalment -1   where (chargebackcommision is not null or chargebackcommision=0) and paymentdate <  DATE_SUB(NOW(),INTERVAL 1 month);

Update agentpayment set  ChargeBackCommision = ChargeBackCommision - (ChargeBackCommision/chargeBackInstalment)   where (chargebackcommision is not null or chargebackcommision=0) and paymentdate <  DATE_SUB(NOW(),INTERVAL 1 month);
 -- SET i = i + 1;
-- END WHILE;

End$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `agentlevels`
--

CREATE TABLE `agentlevels` (
  `levelID` int(11) NOT NULL,
  `LevelName` varchar(50) NOT NULL,
  `FirstYrComRate` float DEFAULT NULL,
  `RenewComRate` float DEFAULT NULL,
  `FiveYrLifeComRate` float DEFAULT NULL,
  `ModBy-varchar` varchar(200) DEFAULT NULL,
  `createdDate` date DEFAULT NULL,
  `modefyDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `agentlevels`
--

INSERT INTO `agentlevels` (`levelID`, `LevelName`, `FirstYrComRate`, `RenewComRate`, `FiveYrLifeComRate`, `ModBy-varchar`, `createdDate`, `modefyDate`) VALUES
(1, '1', 40, 5, 20, NULL, '2019-06-21', '2019-06-25'),
(2, '2', 45, 6, 23, NULL, '2019-06-24', NULL),
(3, '3', 50, 7, 24, NULL, NULL, NULL),
(4, '4', 55, 8, 25, NULL, '2019-06-24', NULL),
(5, '5', 60, 9, 26, NULL, '2019-06-24', NULL),
(6, '6', 70, 10, 27, NULL, '2019-06-24', NULL),
(7, '7', 75, 12, 28, NULL, '2019-06-24', NULL),
(8, '8', 80, 15, 32, NULL, '2019-06-24', NULL),
(9, '9', 85, 16, 31, NULL, '2019-06-24', NULL),
(10, '10', 90, 17, 32, NULL, '0000-00-00', NULL),
(11, '11', 95, 18, 33, NULL, '0000-00-00', NULL),
(12, '12', 100, 20, 35, NULL, '0000-00-00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `agentmanagers`
--

CREATE TABLE `agentmanagers` (
  `id` int(11) NOT NULL,
  `agentId` int(11) NOT NULL,
  `managerId` int(11) NOT NULL,
  `modby` varchar(50) DEFAULT NULL,
  `createdDate` date DEFAULT NULL,
  `modefyDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `agentpayment`
--

CREATE TABLE `agentpayment` (
  `paymentId` int(11) NOT NULL,
  `planId` int(11) DEFAULT NULL,
  `customerId` int(11) DEFAULT NULL,
  `AgentId` int(11) NOT NULL,
  `managerId` int(11) DEFAULT NULL,
  `PercentOrDollar` float DEFAULT NULL,
  `Commission` float DEFAULT NULL,
  `chargeBackCommision` float DEFAULT NULL,
  `chargeBackInstalment` int(11) DEFAULT NULL,
  `relasingDate` date DEFAULT NULL,
  `managerCommission` float DEFAULT NULL,
  `IsAdvance` int(11) DEFAULT NULL,
  `PaymentMode` varchar(20) DEFAULT NULL,
  `PaymentDate` date DEFAULT NULL,
  `ModDate` date DEFAULT NULL,
  `ModUser` varchar(45) DEFAULT NULL,
  `feeAmount` float DEFAULT NULL,
  `newOrRenew` varchar(50) DEFAULT NULL,
  `paymentletterDate` date DEFAULT NULL,
  `recurringPaymentDate` date DEFAULT NULL,
  `IsCustomerActive` varchar(10) DEFAULT NULL,
  `ChargeBackInterest` float DEFAULT NULL,
  `MonthCounter` int(11) DEFAULT NULL,
  `stateManagerId` int(11) DEFAULT NULL,
  `stateManagerCommission` float NOT NULL,
  `ChargeBackInterestForManager` float NOT NULL,
  `ChargeBackInterestForStateManager` float NOT NULL,
  `isPaidManager` enum('0','1') NOT NULL DEFAULT '0',
  `isPaidAgent` enum('0','1') NOT NULL DEFAULT '0',
  `isPaidStateManager` enum('0','1') NOT NULL DEFAULT '0',
  `naration` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `agentpayment`
--

INSERT INTO `agentpayment` (`paymentId`, `planId`, `customerId`, `AgentId`, `managerId`, `PercentOrDollar`, `Commission`, `chargeBackCommision`, `chargeBackInstalment`, `relasingDate`, `managerCommission`, `IsAdvance`, `PaymentMode`, `PaymentDate`, `ModDate`, `ModUser`, `feeAmount`, `newOrRenew`, `paymentletterDate`, `recurringPaymentDate`, `IsCustomerActive`, `ChargeBackInterest`, `MonthCounter`, `stateManagerId`, `stateManagerCommission`, `ChargeBackInterestForManager`, `ChargeBackInterestForStateManager`, `isPaidManager`, `isPaidAgent`, `isPaidStateManager`, `naration`) VALUES
(1, 2, 17, 3, NULL, 0, 260.7, NULL, NULL, NULL, 0, 0, '', '0000-00-00', '2019-08-22', NULL, 474, 'NEW', '2020-08-22', '2019-08-22', NULL, NULL, NULL, NULL, 0, 0, 0, '0', '0', '0', ''),
(2, 1, 26, 3, NULL, 0, 123.75, 123.75, 6, NULL, 0, 0, '', '0000-00-00', '2019-08-23', NULL, 37.5, 'NEW', '2019-09-23', '2019-08-23', NULL, 7.425, NULL, 0, 0, 0, 0, '0', '0', '0', ''),
(3, 3, 27, 3, NULL, 0, 787.5, NULL, NULL, NULL, 0, 0, '', '0000-00-00', '2019-08-23', NULL, 3150, 'NEW', '2020-08-23', '2019-08-23', NULL, NULL, NULL, NULL, 0, 0, 0, '0', '0', '0', ''),
(4, 2, 28, 3, NULL, 0, 260.7, NULL, NULL, NULL, 0, 0, '', '0000-00-00', '2019-08-23', NULL, 474, 'NEW', '2020-08-23', '2019-08-23', NULL, NULL, NULL, NULL, 0, 0, 0, '0', '0', '0', ''),
(5, 2, 29, 3, NULL, 0, 260.7, NULL, NULL, NULL, 0, 0, '', '0000-00-00', '2019-08-23', NULL, 474, 'NEW', '2020-08-23', '2019-08-23', NULL, NULL, NULL, NULL, 0, 0, 0, '0', '0', '0', ''),
(6, 2, 30, 3, NULL, 0, 260.7, NULL, NULL, NULL, 0, 0, '', '0000-00-00', '2019-08-23', NULL, 474, 'NEW', '2020-08-23', '2019-08-23', NULL, NULL, NULL, NULL, 0, 0, 0, '0', '0', '0', ''),
(7, 2, 31, 3, NULL, 0, 260.7, NULL, NULL, NULL, 0, 0, '', '0000-00-00', '2019-08-23', NULL, 474, 'NEW', '2020-08-23', '2019-08-23', NULL, NULL, NULL, NULL, 0, 0, 0, '0', '0', '0', ''),
(8, 2, 32, 3, NULL, 0, 163.35, NULL, NULL, NULL, 0, 0, '', '0000-00-00', '2019-08-23', NULL, 297, 'NEW', '2020-08-23', '2019-08-23', NULL, NULL, NULL, NULL, 0, 0, 0, '0', '0', '0', ''),
(9, 2, 33, 3, 4, 0, 163.35, NULL, NULL, NULL, 44.55, 0, 'ACH', '2019-08-26', '2019-08-26', NULL, 297, 'NEW', '2020-08-23', '2019-08-23', NULL, NULL, NULL, NULL, 0, 0, 0, '0', '0', '0', 'test'),
(12, 3, 35, 9, 6, 0, 630, NULL, NULL, NULL, 189, 0, '', '0000-00-00', '2019-08-26', NULL, 3150, 'NEW', '2020-08-26', '2019-08-26', NULL, NULL, NULL, NULL, 0, 0, 0, '0', '0', '0', ''),
(13, 3, 36, 10, NULL, 0, 630, NULL, NULL, NULL, 0, 0, '', '0000-00-00', '2019-08-26', NULL, 3150, 'NEW', '2020-08-26', '2019-08-26', NULL, NULL, NULL, NULL, 0, 0, 0, '0', '0', '0', ''),
(14, 2, 37, 9, NULL, 0, 189.6, NULL, NULL, NULL, 0, 0, '', '0000-00-00', '2019-08-26', NULL, 474, 'NEW', '2020-08-26', '2019-08-26', NULL, NULL, NULL, NULL, 0, 0, 0, '0', '0', '0', ''),
(15, 1, 38, 5, 6, 0, 59.4, 59.4, 6, NULL, 29.7, 0, '', '0000-00-00', '2019-08-26', NULL, 24.75, 'NEW', '2019-09-26', '2019-08-26', NULL, 3.564, NULL, 0, 0, 1.782, 0, '0', '0', '0', ''),
(16, 2, 39, 8, 13, 0, 189.6, NULL, NULL, NULL, 94.8, 0, '', '0000-00-00', '2019-08-26', NULL, 474, 'NEW', '2020-08-26', '2019-08-26', NULL, NULL, NULL, NULL, 0, 0, 0, '0', '0', '0', ''),
(17, 1, 40, 15, NULL, 0, 81.675, 81.675, 6, NULL, 0, 0, 'Personal Cheque', '2019-08-26', '2019-08-26', NULL, 24.75, 'NEW', '2019-09-26', '2019-08-26', NULL, 4.9005, NULL, 0, 0, 0, 0, '0', '0', '0', 'Test'),
(18, 2, 41, 15, NULL, 0, 163.35, NULL, NULL, NULL, 0, 0, '', '0000-00-00', '2019-08-26', NULL, 297, 'NEW', '2020-08-26', '2019-08-26', NULL, NULL, NULL, NULL, 0, 0, 0, '0', '0', '0', ''),
(19, 2, 42, 7, 13, 0, 118.8, NULL, NULL, NULL, 59.4, 0, '', '0000-00-00', '2019-08-26', NULL, 297, 'NEW', '2020-08-26', '2019-08-26', NULL, NULL, NULL, NULL, 0, 0, 0, '0', '0', '0', ''),
(20, 2, 43, 7, 13, 0, 118.8, NULL, NULL, NULL, 59.4, 0, '', '0000-00-00', '2019-08-26', NULL, 297, 'NEW', '2020-08-26', '2019-08-26', NULL, NULL, NULL, NULL, 0, 0, 0, '0', '0', '0', ''),
(21, 2, 44, 15, 6, 0, 260.7, NULL, NULL, NULL, 23.7, 0, '', '0000-00-00', '2019-08-26', NULL, 474, 'NEW', '2020-08-26', '2019-08-26', NULL, NULL, NULL, NULL, 0, 0, 0, '0', '0', '0', ''),
(22, 2, 45, 15, NULL, 0, 260.7, NULL, NULL, NULL, 0, 0, '', '0000-00-00', '2019-08-26', NULL, 474, 'NEW', '2020-08-26', '2019-08-26', NULL, NULL, NULL, NULL, 0, 0, 0, '0', '0', '0', ''),
(23, 1, 46, 15, 13, 0, 81.675, 81.675, 6, NULL, 7.425, 0, '', '0000-00-00', '2019-08-26', NULL, 24.75, 'NEW', '2019-09-26', '2019-08-26', NULL, 4.9005, NULL, 0, 0, 0.4455, 0, '0', '0', '0', ''),
(24, 2, 47, 10, 13, 0, 189.6, NULL, NULL, NULL, 94.8, 0, '', '0000-00-00', '2019-08-26', NULL, 474, 'NEW', '2020-08-26', '2019-08-26', NULL, NULL, NULL, 18, 189.6, 0, 0, '0', '0', '0', ''),
(25, 2, 48, 10, 13, 0, 189.6, NULL, NULL, NULL, 94.8, 0, '', '0000-00-00', '2019-08-26', NULL, 474, 'NEW', '2020-08-26', '2019-08-26', NULL, NULL, NULL, 18, 189.6, 0, 0, '0', '0', '0', ''),
(26, 1, 49, 14, 18, 0, 135, 135, 6, NULL, 90, 0, '', '0000-00-00', '2019-08-26', NULL, 37.5, 'NEW', '2019-09-26', '2019-08-26', NULL, 8.1, NULL, 0, 0, 5.4, 0, '0', '0', '0', ''),
(27, 1, 50, 15, 13, 0, 81.675, 81.675, 6, NULL, 7.425, 0, '', '0000-00-00', '2019-08-26', NULL, 24.75, 'NEW', '2019-09-26', '2019-08-26', NULL, 4.9005, NULL, 18, 59.4, 0.4455, 3.564, '0', '0', '0', ''),
(28, 2, 51, 13, 18, 0, 178.2, NULL, NULL, NULL, 118.8, 0, '', '0000-00-00', '2019-08-26', NULL, 297, 'NEW', '2020-08-26', '2019-08-26', NULL, NULL, NULL, NULL, 0, 0, 0, '0', '0', '0', ''),
(29, 1, 52, 14, 18, 0, 135, 135, 6, NULL, 90, 0, '', '0000-00-00', '2019-08-27', NULL, 37.5, 'NEW', '2019-09-27', '2019-08-27', NULL, 8.1, NULL, 0, 0, 5.4, 0, '0', '0', '0', '');

-- --------------------------------------------------------

--
-- Table structure for table `agents`
--

CREATE TABLE `agents` (
  `agentId` int(11) NOT NULL,
  `agentName` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `firstName` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lastName` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address1` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address2` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zip` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cellPhone` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `groupId` bigint(20) UNSIGNED DEFAULT NULL,
  `agentStartDate` date DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `api_token` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `levelID` int(11) DEFAULT NULL,
  `isActive` tinyint(1) NOT NULL DEFAULT '0',
  `dob` date DEFAULT NULL,
  `modDate` date DEFAULT NULL,
  `modBy` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `city1` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zip1` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address3` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address4` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state1` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paymentMethod` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_name` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_name` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alt_phone_num` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country1` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `agents`
--

INSERT INTO `agents` (`agentId`, `agentName`, `firstName`, `lastName`, `address1`, `address2`, `email`, `city`, `country`, `zip`, `cellPhone`, `groupId`, `agentStartDate`, `password`, `api_token`, `levelID`, `isActive`, `dob`, `modDate`, `modBy`, `created_at`, `updated_at`, `city1`, `zip1`, `address3`, `address4`, `state1`, `state`, `paymentMethod`, `account_name`, `bank_name`, `account_number`, `alt_phone_num`, `country1`) VALUES
(3, NULL, 'SDFTFGDYHTG', 'CFGDGF', 'ZCDFGCG', 'CXFGCGHF', 'SDFDGHF@SDFFD.CVC', 'CGVHG', 'Antigua', '2342', '+1 (243) 546-5465', NULL, '2019-08-22', '123243', NULL, 4, 1, '2019-08-22', NULL, NULL, '2019-08-22 08:02:35', NULL, 'CGVHG', '2342', 'ZCDFGCG', 'CXFGCGHF', 'CVBFGVBHG', 'CVBFGVBHG', NULL, NULL, NULL, NULL, '+1 (234) 556-5768', 'Antigua'),
(4, NULL, 'Daniel', 'Greg', 'Any', 'Any', 'dan@gmail.com', 'Test', 'USA', '12345', '+1 (456) 123-1233', NULL, '2019-08-23', '12345', NULL, 6, 1, '1980-02-02', NULL, NULL, '2019-08-23 01:15:58', NULL, 'Test', '12345', 'Any', 'Any', 'AX', 'AX', NULL, NULL, NULL, NULL, NULL, 'USA'),
(5, NULL, 'dfgfhg', 'dcvbfvhfg', 'zsfdytg', 'fdghfy', 'xcvfchfgh@cdfgdtgftgf.cvc', 'dgtfyhtg', 'St. Kitts', '13345', '+1 (324) 354-64', NULL, '2019-08-23', 'fdgfdgf', NULL, 1, 0, NULL, NULL, NULL, '2019-08-23 02:26:00', NULL, 'dgtfyhtg', '13345', 'zsfdytg', 'fdghfy', 'dfdytf', 'dfdytf', NULL, NULL, NULL, NULL, '+1 (243) 546-5', 'St. Kitts'),
(6, NULL, 'xdgfhfg', 'sdfdgdfy', 'dfvgf', 'cxgfh', 'xgfdxg@fgfh.cgfyh', 'dfgfh', 'St. Kitts', '23235', '+1 (232) 543-6546', NULL, '2019-08-23', '1231324', NULL, 5, 0, '2019-08-23', NULL, NULL, '2019-08-23 02:29:25', NULL, 'dfgfh', '23235', 'dfvgf', 'cxgfh', 'dgfhfg', 'dgfhfg', NULL, NULL, NULL, NULL, '+1 (243) 65', 'St. Kitts'),
(7, NULL, 'asesrer', 'eretre', 'asdrsrdt', 'sretet', 'saaaa@gmail.com', 'edtry', 'Bermuda', '2343', '+1 (234) 354-6546', NULL, '2019-08-23', 'zdsfdfd', NULL, 1, 0, '2019-08-23', NULL, NULL, '2019-08-23 08:42:34', NULL, 'edtry', '2343', 'asdrsrdt', 'sretet', 'dtrtyrt', 'dtrtyrt', NULL, NULL, NULL, NULL, '+1 (234) 354-5646', 'Bermuda'),
(8, NULL, 'sadfsfdsfgdg', 'dsfsgfdgfr', 'P.O. Box 147 2546 Sociosqu Rd.', 'dfgfyhgfhg', 'admin2@gmail.com', 'Bethlehem', 'Nassau', '02913', '+354 546 5654', NULL, '2019-08-23', '12345678', NULL, 1, 0, '1988-02-22', NULL, NULL, '2019-08-23 08:46:43', NULL, 'Bethlehem', '02913', 'P.O. Box 147 2546 Sociosqu Rd.', 'dfgfyhgfhg', 'Utah', 'Utah', NULL, NULL, NULL, NULL, NULL, 'Nassau'),
(9, NULL, 'cvbb', 'bvchbgvh', 'sdftdrt', 'fdtdr', 'dan123@gmail.com', 'dftgrftyrfy', 'Trinidad', '1213', '+1 (354) 456-4654', NULL, '2019-08-23', 'sdtr6yrt5675', NULL, 1, 0, '2019-08-23', NULL, NULL, '2019-08-23 08:53:53', NULL, 'dftgrftyrfy', '1213', 'sdftdrt', 'fdtdr', 'dgtdft', 'dgtdft', NULL, NULL, NULL, NULL, '+1 (435) 465-46', 'Trinidad'),
(10, NULL, 'dfdg', 'wreret', 'qwewr', 'werere', 'danwewe@gmail.com', 'sret', 'Trinidad', '12134', '+1 (123) 3', NULL, '2019-08-26', '1234', NULL, 1, 0, NULL, NULL, NULL, '2019-08-26 00:51:04', NULL, 'sret', '12134', 'qwewr', 'werere', 'ertetr', 'ertetr', NULL, NULL, NULL, NULL, '+1 (123) 4', 'Trinidad'),
(11, NULL, 'sdfghh', 'drftgfryt', 'xyz street near abc', NULL, 'sdrettr@dsd.ghgh', 'kolkata', 'USA', '70012', '+234343453545434', NULL, '2019-08-26', '123456789', NULL, 1, 0, '2019-08-26', NULL, NULL, '2019-08-26 02:19:38', NULL, 'kolkata', '70012', 'xyz street near abc', NULL, 'west Bengal', 'west Bengal', NULL, NULL, NULL, NULL, '+1 (123) 232', 'USA'),
(12, NULL, 'Rubby', 'Joseph', 'Any', NULL, 'rubby@gmail.com', 'Any', 'USA', '12345', '+1 (111) 111-1111', NULL, '2019-08-26', '12345678', NULL, 8, 1, '1980-02-02', NULL, NULL, '2019-08-26 06:40:19', NULL, 'Any', '12345', 'Any', NULL, 'TX', 'TX', NULL, NULL, NULL, NULL, NULL, 'USA'),
(13, NULL, 'Robert', 'Duhart', 'Any', NULL, 'rubby@gmail.com', 'Any', 'USA', '56451', '+1 (111) 111-1111', NULL, '2019-08-26', '12345678', NULL, 5, 0, '1976-01-02', NULL, NULL, '2019-08-26 06:44:27', NULL, 'Any', '56451', 'Any', NULL, 'CA', 'CA', NULL, NULL, NULL, NULL, NULL, 'USA'),
(14, NULL, 'Robert', 'Duhart', 'Any', NULL, 'rubby@gmail.com', 'Any', 'USA', '56451', '+1 (111) 111-1111', NULL, '2019-08-26', '12345678', NULL, 5, 0, '1976-01-02', NULL, NULL, '2019-08-26 06:44:28', NULL, 'Any', '56451', 'Any', NULL, 'CA', 'CA', NULL, NULL, NULL, NULL, NULL, 'USA'),
(15, NULL, 'Simon', 'Jones', 'Any', NULL, 'rubby@gmail.com', 'Any', 'USA', '12345', '+1 (111) 111-1111', NULL, '2019-08-26', '12345678', NULL, 4, 0, '1978-02-02', NULL, NULL, '2019-08-26 06:49:46', NULL, 'Any', '12345', 'Any', NULL, 'NY', 'NY', NULL, NULL, NULL, NULL, NULL, 'USA'),
(16, NULL, 'sdfsfe', 'wrerer', 'asdsf', 'sdsfd', 'dan12ff3@gmail.com', 'asdad', 'Bermuda', '12345', '+1 (234) 243-43', NULL, '2019-08-26', '12345678', NULL, 1, 0, '2019-08-26', NULL, NULL, '2019-08-26 08:07:03', NULL, 'asdad', '12345', 'asdsf', 'sdsfd', 'adasdsa', 'adasdsa', NULL, NULL, NULL, NULL, NULL, 'Bermuda'),
(17, NULL, 'Sunny', 'Roy', 'Any', NULL, 'rubby@gmail.com', 'Any', 'USA', '12345', '+1 (111) 111-1111', NULL, '2019-08-26', '12345678', NULL, 4, 1, '1980-02-02', NULL, NULL, '2019-08-26 08:53:40', NULL, 'Any', '12345', 'Any', NULL, 'TX', 'TX', NULL, NULL, NULL, NULL, NULL, 'USA'),
(18, NULL, '----------------------------------------', '???????????????????????????????????//', '???????', '????????', '?????@aaa.com', '@@@@@', 'USA', '-1234', '+1 (000) 000-0000', NULL, '2019-08-26', '12345678', NULL, 12, 0, '0001-02-02', NULL, NULL, '2019-08-26 08:56:47', NULL, '@@@@@', '-1234', '???????', '????????', '%%%%%%%', '%%%%%%%', NULL, NULL, NULL, NULL, NULL, 'USA'),
(19, NULL, 'Arup', 'Chakroborty', 'Any', NULL, 'arup@gmail.com', 'Any', 'Bahamas', '12345', '+1 (235) 645-623', NULL, '2019-08-26', '12345678', NULL, 12, 1, '2019-02-02', NULL, NULL, '2019-08-26 09:56:58', NULL, 'Any', '12345', 'Any', NULL, 'Any', 'Any', NULL, NULL, NULL, NULL, NULL, 'Bahamas'),
(20, NULL, 'asderfe', 'sfdtg', 'sdfdg', 'sxfdgdg', 'dan@gmail.com', 'dfdgfg', 'Tobago', '1234', '+1 (132) 435-456', NULL, '2019-08-27', '12345678', NULL, 1, 0, '2019-08-27', NULL, NULL, '2019-08-26 23:36:33', NULL, 'dfdgfg', '12346', 'sdfdg', 'sxfdgdg', 'dgfgf', 'dgfgf', NULL, NULL, NULL, NULL, NULL, 'Tobago'),
(21, NULL, 'esrtetr', 'rfetrty', 'etrtry', NULL, 'dan1@gmail.com', 'sretre', 'Nassau', '12345', '+1 (123) 243-5454', NULL, '2019-08-27', '12345678', NULL, 1, 0, '2019-08-27', NULL, NULL, '2019-08-26 23:43:32', NULL, 'sretre', '12345', 'etrtry', NULL, 'asewsr', 'srerer', NULL, NULL, NULL, NULL, NULL, 'Nassau'),
(22, NULL, 'erytry', 'rtyryty', 'ertrtyry', NULL, 'dan1@gmail.com', 'fgfyhgtfy', 'Bermuda', '12345', '+1 (123) 243-4355', NULL, '2019-08-27', '12345678', NULL, 1, 0, '2019-08-20', NULL, NULL, '2019-08-26 23:58:47', NULL, 'fgfyhgtfy', '12345', 'ertrtyry', NULL, 'trtr', 'trtr', NULL, NULL, NULL, NULL, NULL, 'Bermuda');

-- --------------------------------------------------------

--
-- Table structure for table `agenttotalfee`
--

CREATE TABLE `agenttotalfee` (
  `id` int(11) NOT NULL,
  `AgentId` int(11) NOT NULL,
  `TotalFee` float NOT NULL,
  `IsPaid` varchar(10) NOT NULL,
  `PayDate` date DEFAULT NULL,
  `paymentMethod` varchar(25) NOT NULL,
  `adhocPayment` float NOT NULL,
  `adhocPaymentNaration` text NOT NULL,
  `ModDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `agenttotalfee`
--

INSERT INTO `agenttotalfee` (`id`, `AgentId`, `TotalFee`, `IsPaid`, `PayDate`, `paymentMethod`, `adhocPayment`, `adhocPaymentNaration`, `ModDate`) VALUES
(1, 1, 207.9, '1', '2019-08-21', 'ACH', 0, '08/21/2019 commission paid', '2019-08-21 12:34:46');

-- --------------------------------------------------------

--
-- Stand-in structure for view `agent_commission_details`
-- (See below for the actual view)
--
CREATE TABLE `agent_commission_details` (
`agentId` int(11)
,`customerId` int(11)
,`customerName` varchar(383)
,`client_type` varchar(30)
,`membership_plan` varchar(191)
,`fees` float
,`groupCode` varchar(212)
,`agent_commision` double
,`agent_chargeBack_commision` double
,`agent_interest` double
,`renewal_commision` double
,`manager_Commision` double
,`manager_interest` double
,`state_manager_commission` double
,`state_manager_interest` double
,`earned_commission` double(19,2)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `agent_manager_list`
-- (See below for the actual view)
--
CREATE TABLE `agent_manager_list` (
`agentId` int(11)
,`agent_name` varchar(201)
,`dob` date
,`levelID` int(11)
,`manager_name` varchar(201)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `agent_wise_commission`
-- (See below for the actual view)
--
CREATE TABLE `agent_wise_commission` (
`total_commission` double(19,2)
,`AgentId` int(11)
);

-- --------------------------------------------------------

--
-- Table structure for table `client_claim`
--

CREATE TABLE `client_claim` (
  `claim_id` int(11) NOT NULL,
  `clientId` int(11) NOT NULL,
  `claim_reason` text NOT NULL,
  `calim_doc` varchar(255) DEFAULT NULL,
  `claim_status` enum('0','1','2') NOT NULL DEFAULT '0' COMMENT '0=Open,1=inprogress,2=closed',
  `comments` text NOT NULL,
  `claimed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `claim_closer_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `client_claim`
--

INSERT INTO `client_claim` (`claim_id`, `clientId`, `claim_reason`, `calim_doc`, `claim_status`, `comments`, `claimed_at`, `claim_closer_date`) VALUES
(1, 33, 'Test test', '', '0', 'test', '2019-08-26 10:43:54', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customerId` int(11) NOT NULL,
  `firstName` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `LastName` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `DOB` date DEFAULT NULL,
  `address1` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address2` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country1` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile2` varchar(18) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `clientType` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zip` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mailing_address1` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mailing_address2` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city1` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state1` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zip1` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cellPhone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `writing_agent` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `agent_manager` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `homePhone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `spouseFirstName` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `spouseLastName` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `spouseDOB` date DEFAULT NULL,
  `dependent1FirstName` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dependent1LastName` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dependent1DOB` date DEFAULT NULL,
  `dependent2FirstName` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Dependent2LastName` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dependent2DOB` date DEFAULT NULL,
  `dependent3FirstName` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dependent3LastName` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dependent3DOB` date DEFAULT NULL,
  `dependent4FirstName` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dependent4LastName` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dependent4DOB` date DEFAULT NULL,
  `planId` bigint(20) UNSIGNED DEFAULT NULL,
  `agentId` bigint(20) UNSIGNED DEFAULT NULL,
  `userName` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `autoRenew` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `membershipDate` date DEFAULT NULL,
  `effectiveDate` date DEFAULT NULL,
  `renewaDate` date DEFAULT NULL,
  `isActive` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `modDate` date DEFAULT NULL,
  `ModBy` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `companyName` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `groupId` bigint(20) UNSIGNED DEFAULT NULL,
  `isPaidCustomer` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '0=unpaid and 1=paid'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customerId`, `firstName`, `LastName`, `DOB`, `address1`, `address2`, `email`, `city`, `country`, `country1`, `mobile2`, `clientType`, `location`, `zip`, `mailing_address1`, `mailing_address2`, `city1`, `state1`, `state`, `zip1`, `cellPhone`, `writing_agent`, `agent_manager`, `homePhone`, `spouseFirstName`, `spouseLastName`, `spouseDOB`, `dependent1FirstName`, `dependent1LastName`, `dependent1DOB`, `dependent2FirstName`, `Dependent2LastName`, `dependent2DOB`, `dependent3FirstName`, `dependent3LastName`, `dependent3DOB`, `dependent4FirstName`, `dependent4LastName`, `dependent4DOB`, `planId`, `agentId`, `userName`, `password`, `autoRenew`, `membershipDate`, `effectiveDate`, `renewaDate`, `isActive`, `modDate`, `ModBy`, `created_at`, `updated_at`, `companyName`, `groupId`, `isPaidCustomer`) VALUES
(17, 'DGFH', 'DXFGDGFH', '2019-08-22', 'SFD', 'SDFSRF', 'SDSR@DFDTFDT.CVC', 'SXFDTGDF', 'USA', 'USA', NULL, 'Family', NULL, '13123', 'SFD', 'SDFSRF', 'SXFDTGDF', 'SFDSGDFTG', 'SFDSGDFTG', '13123', '+1 (234) 354-6546', '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 3, NULL, '132435345', NULL, '2019-08-22', NULL, NULL, 'Yes', '2019-08-22', 'Admin', '2019-08-22 08:04:44', NULL, 'DSFD', 1, '0'),
(26, 'dgfdgf', 'dgfhgf', '2019-08-23', 'sdfxfdf', 'xfdfdfg', 'sdsfdfdfg@fghgh.vc', 'xfdxfdf', 'Antigua', 'Antigua', '+1 (354)', 'Family', NULL, '12323', 'sdfxfdf', 'xfdfdfg', 'xfdxfdf', 'xfdgd', 'xfdgd', '12323', '+1 (343) 545-64', '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 3, NULL, 'fdgfdhfg', NULL, '2019-08-23', NULL, NULL, 'Yes', '2019-08-23', 'Admin', '2019-08-23 00:43:39', NULL, 'zcxfxd', 1, '0'),
(27, 'sdxfdfgdg', 'dfdgdfg', '2019-08-23', 'dfgbh', 'cgfvghfgh', 'sddfsfds@sfdrf.vbb', 'dfgfhfg', 'USA', 'USA', '+1 (354) 365-4657', 'Family', NULL, '21342', 'dfgbh', 'cgfvghfgh', 'dfgfhfg', 'gfhgh', 'gfhgh', '21342', '+1 (354) 354-5646', '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 3, 3, NULL, '12324', NULL, '2019-08-23', NULL, NULL, 'Yes', '2019-08-23', 'Admin', '2019-08-23 00:45:16', NULL, 'cgvbcgb', 1, '0'),
(28, 'drdtrt', 'ertrtyry', '2019-08-23', 'xfchghjgj', 'xfghfh', 'dgfdhg@cgcg.vv', 'ghjh', 'USA', 'USA', '+13453565767', 'Family', NULL, '2345', 'xfchghjgj', 'xfghfh', 'ghjh', 'dgfhgh', 'dgfhgh', '2345', '+1 (343) 564-6567', '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 3, NULL, '12324234', NULL, '2019-08-23', NULL, NULL, 'Yes', '2019-08-23', 'Admin', '2019-08-23 00:49:53', NULL, 'fhgj', 2, '0'),
(29, 'xdcghfv', 'xcgfvhgf', '2019-08-23', 'sdsfdf', 'sdsfdf', 'csxfdf@xdsf.xcx', 'sfdfgd', 'USA', 'USA', '+1 (243) 543-545', 'Family', NULL, '12343', 'sdsfdf', 'sdsfdf', 'sfdfgd', 'sdsfdsf', 'sdsfdsf', '12343', '+1 (232) 423-454', '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 3, NULL, '1232324', NULL, '2019-08-23', NULL, NULL, 'Yes', '2019-08-23', 'Admin', '2019-08-23 00:51:07', NULL, 'xzfgfgf', 1, '0'),
(30, 'XFGFH', 'SFDGFDG', '2019-08-23', 'ADSRFD', 'XFDGF', 'ASDSHDBJ@SDBJHDF.CHCFH', 'SFDG', 'USA', 'USA', '+1 (343) 546', 'Family', NULL, '23445', 'ADSRFD', 'XFDGF', 'SFDG', 'DSGDF', 'DSGDF', '23445', '+1 (243) 45', '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 3, NULL, '232434', NULL, '2019-08-23', NULL, NULL, 'Yes', '2019-08-23', 'Admin', '2019-08-23 00:59:10', NULL, 'DSDRESR', 2, '0'),
(31, 'XFGFH', 'SFDGFDG', '2019-08-23', 'ADSRFD', 'XFDGF', 'ASDSHDBJ@SDBJHDF.CHCFH', 'SFDG', 'USA', 'USA', '+1 (343) 546', 'Family', NULL, '23445', 'ADSRFD', 'XFDGF', 'SFDG', 'DSGDF', 'DSGDF', '23445', '+1 (243) 45', '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 3, NULL, '232434', NULL, '2019-08-23', NULL, NULL, 'Yes', '2019-08-23', 'Admin', '2019-08-23 00:59:11', NULL, 'DSDRESR', 2, '0'),
(32, 'Sami', 'Lamb', '1980-04-13', 'Any', 'Any', 'sami@gmail.com', 'Any', 'USA', 'USA', NULL, 'Individual', NULL, '56123', 'Any', 'Any', 'Any', 'TX', 'TX', '56123', '+1 (123) 456-789', '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 3, NULL, '12345', NULL, '2019-08-23', NULL, NULL, 'Yes', '2019-08-23', 'Admin', '2019-08-23 01:08:33', NULL, 'Global', 1, '0'),
(33, 'Tim', 'Green', '1980-02-02', 'Any', NULL, 'tim@globalmedevac.com', 'Any', 'USA', 'USA', NULL, 'Individual', NULL, '12345', 'Any', NULL, 'Any', 'TX', 'TX', '12345', '+1 (456) 123-1232', '4', '4', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 3, NULL, '12345', NULL, '2019-08-23', NULL, NULL, 'Yes', '2019-08-23', 'Admin', '2019-08-23 01:17:50', NULL, 'Global', 1, '1'),
(35, 'satyam', 'singh', '2019-08-26', 'xyz street near abc', NULL, 'qewsredrted@gghshs.xcvo', 'kolkata', 'USA', 'USA', '+1 (123) 425-435', 'Family', NULL, '700129', 'xyz street near abc', NULL, 'kolkata', 'west Bengal', 'west Bengal', '700129', '+82 309 7828 9', '6', '6', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 3, 9, NULL, 'adsdsr', NULL, '2019-08-26', NULL, NULL, 'Yes', '2019-08-26', 'Admin', '2019-08-26 02:40:07', NULL, 'dfgfdt', 2, '0'),
(36, 'dfgtfyt', 'ere5t5ret6', '2019-08-25', 'sdsf', 'sfdtgdf', 'test123@gmail.com', 'serdedtrt', 'USA', 'USA', '+1 (453) 5', 'Family', NULL, '1212', 'sdsf', 'sfdtgdf', 'serdedtrt', 'srfdrtrd', 'srfdrtrd', '1212', '+1 (443) 545-654', '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 3, 10, NULL, '123455', NULL, '2019-08-26', NULL, NULL, 'Yes', '2019-08-26', 'Admin', '2019-08-26 02:41:56', NULL, 'dfgfyhgyhguy', 1, '0'),
(37, 'dfdtrt', 'fghuyu', '2019-08-26', 'sfdtg', 'sdrtdtyry', 'test1dff23@gmail.com', 'erttyt', 'USA', 'USA', '+1 (234) 354-5646', 'Family', NULL, '12345', 'sfdtg', 'sdrtdtyry', 'erttyt', 'drtyrty', 'drtyrty', '12345', '+1 (232) 435-4354', '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 9, NULL, '1234', NULL, '2019-08-26', NULL, NULL, 'Yes', '2019-08-26', 'Admin', '2019-08-26 04:31:25', NULL, 'sdrtfrytry', 2, '0'),
(38, 'Deodra', 'Larsen', '1960-02-02', 'Any', NULL, 'deo@gmail.com', 'Any', 'USA', 'USA', NULL, 'Individual', NULL, '12345', 'Any', NULL, 'Any', 'TX', 'TX', '12345', '+1 (121) 121-2121', '6', '6', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 5, NULL, '12345678', NULL, '2019-08-26', NULL, NULL, 'Yes', '2019-08-26', 'Admin', '2019-08-26 06:42:34', NULL, 'Global', 2, '0'),
(39, 'asadadr', 'adsdsrrfds', '2019-08-26', 'sdfdg', 'sfdfdg', 'sdsfsd@gmai.ggv', 'sfdg', 'Trinidad', 'Trinidad', '+1 (234)', 'Family', NULL, '12345', 'sdfdg', 'sfdfdg', 'sfdg', 'srfdt', 'srfdt', '12345', '+1 (123) 435-4545', '13', '13', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 8, NULL, '12345678', NULL, '2019-08-26', NULL, NULL, 'Yes', '2019-08-26', 'Admin', '2019-08-26 07:06:48', NULL, 'aesrfdrfdrt', 2, '0'),
(40, 'Peter', 'Cat', '1980-02-02', 'Any', NULL, 'pet@gmail.com', 'Any', 'USA', 'USA', NULL, 'Individual', NULL, '12345', 'Any', NULL, 'Any', 'TX', 'TX', '12345', '+1 (111) 111-1111', '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 15, NULL, '12345678', NULL, '2019-08-26', NULL, NULL, 'Yes', '2019-08-26', 'Admin', '2019-08-26 07:17:12', NULL, 'Global', 2, '1'),
(41, 'Peter', 'Burge', '1980-02-02', 'Any', NULL, 'pet@gmail.com', 'Any', 'USA', 'USA', NULL, 'Government', NULL, '12345', 'Any', NULL, 'Any', 'TX', 'TX', '123451111111111', '+1 (111) 111-1111', '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 15, NULL, '12345678', NULL, '2019-08-26', NULL, NULL, 'Yes', '2019-08-26', 'Admin', '2019-08-26 07:19:46', NULL, 'Global', 3, '0'),
(42, 'safdsg', 'sfdsgdfg', '2019-08-26', 'dsfdf', 'fdfgd', 'test123@gmail.com', 'sdfsfdf', 'USA', 'USA', '+1 (343) 543-545', 'Individual', NULL, '12345', 'dsfdf', 'fdfgd', 'sdfsfdf', 'dfdfgd', 'dfdfgd', '12345', '+1 (234) 354-65', '13', '13', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 7, NULL, '12345678', NULL, '2019-08-26', NULL, NULL, 'Yes', '2019-08-26', 'Admin', '2019-08-26 07:21:29', NULL, 'sdfdtfd', 2, '0'),
(43, 'safdsg', 'sfdsgdfg', '2019-08-26', 'dsfdf', 'fdfgd', 'test123@gmail.com', 'sdfsfdf', 'USA', 'USA', '+1 (343) 543-545', 'Individual', NULL, '12345', 'dsfdf', 'fdfgd', 'sdfsfdf', 'dfdfgd', 'dfdfgd', '12345', '+1 (234) 354-65', '13', '13', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 7, NULL, '12345678', NULL, '2019-08-26', NULL, NULL, 'Yes', '2019-08-26', 'Admin', '2019-08-26 07:21:30', NULL, 'sdfdtfd', 2, '0'),
(44, 'dfgdfgfd', 'sdfgdgfgfg', '2019-08-26', 'zsdsfd', 'fdfgdg', 'samiad@gmail.com', 'sdsfsf', 'USA', 'USA', NULL, 'Family', NULL, '12323', 'zsdsfd', 'fdfgdg', 'sdsfsf', 'asdads', 'asdads', '12323', '+1 (234) 354-6546', '6', '6', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 15, NULL, '12345678', NULL, '2019-08-26', NULL, NULL, 'Yes', '2019-08-26', 'Admin', '2019-08-26 08:02:06', NULL, 'asadsd', 1, '0'),
(45, 'john', 'cinna', '2019-08-26', 'any', NULL, 'test123@gmail.com', 'any', 'USA', 'USA', NULL, 'Family', NULL, '12345', 'any', NULL, 'any', 'any', 'any', '123456', '+1 (234) 678-9028', '0', '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 15, NULL, '1234567890', NULL, '2019-08-26', NULL, NULL, 'Yes', '2019-08-26', 'Admin', '2019-08-26 08:50:32', NULL, 'bargad', 5, '0'),
(46, 'rtrytyty', 'ghgjghujyh', '2019-08-26', 'rtr6yt6yt76t767', NULL, 'test12@gmail.com', 'kolkata', 'USA', 'USA', NULL, 'Corporate', NULL, '12334', 'rtr6yt6yt76t767', NULL, 'kolkata', 'west bengal', 'west bengal', '12334', '+82 309 7828 9', '13', '13', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 15, NULL, '12345678', NULL, '2019-08-26', NULL, NULL, 'Yes', '2019-08-26', 'Admin', '2019-08-26 09:29:00', NULL, 'dfdgdfg', 3, '0'),
(47, 'efredrt', 'dsrftret', '2019-08-26', 'sdfsf', 'sdsfsd', 'test123eee@gmail.com', 'dsfddf', 'USA', 'USA', '+1 (234) 243-4', 'Family', NULL, '12345', 'sdfsf', 'sdsfsd', 'dsfddf', 'sfdsfdf', 'sfdsfdf', '12345', '+1 (232) 434-354', '13', '13', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 10, NULL, '12345678', NULL, '2019-08-26', NULL, NULL, 'Yes', '2019-08-26', 'Admin', '2019-08-26 09:37:35', NULL, 'sdfd', 4, '0'),
(48, 'efredrt', 'dsrftret', '2019-08-26', 'sdfsf', 'sdsfsd', 'test123eee@gmail.com', 'dsfddf', 'USA', 'USA', '+1 (234) 243-4', 'Family', NULL, '12345', 'sdfsf', 'sdsfsd', 'dsfddf', 'sfdsfdf', 'sfdsfdf', '12345', '+1 (232) 434-354', '13', '13', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 10, NULL, '12345678', NULL, '2019-08-26', NULL, NULL, 'Yes', '2019-08-26', 'Admin', '2019-08-26 09:37:36', NULL, 'sdfd', 4, '0'),
(49, 'asdff', 'sdfsf', '2019-08-26', 'sfsdfd', 'sdfsfds', 'test@gmail.com', 'sdfsf', 'USA', 'USA', '+1242345', 'Family', NULL, '12312', 'sfsdfd', 'sdfsfds', 'sdfsf', 'sfdsf', 'sfdsf', '12312', '+1 (234) 254-3564', '18', '18', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 14, NULL, '12345678', NULL, '2019-08-26', NULL, NULL, 'Yes', '2019-08-26', 'Admin', '2019-08-26 09:39:38', NULL, 'dfgfy', 2, '0'),
(50, 'Arup', 'Chakroborty', '2019-02-02', 'Any', NULL, 'arup@gmail.com', 'ANY', 'St. Kitts', 'St. Kitts', NULL, 'Individual', NULL, '12345', 'NY', NULL, 'NY', 'NY', 'ANY', '12345', '+1 (333) 344-4444', '13', '13', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 15, NULL, '12345678', NULL, '2019-08-26', NULL, NULL, 'Yes', '2019-08-26', 'Admin', '2019-08-26 09:45:39', NULL, 'BSS', 2, '0'),
(51, 'arup', 'chakroborty', '2019-02-02', 'ANY', NULL, 'arup12@gmail.com', 'ANY', 'Bermuda', 'Bermuda', NULL, 'Individual', NULL, '12345', 'NY', NULL, 'NY', 'NY', 'ANY', '12345', '+1 (333) 333-4444', '18', '18', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 13, NULL, '12345678', NULL, '2019-08-26', NULL, NULL, 'Yes', '2019-08-26', 'Admin', '2019-08-26 09:53:26', NULL, 'BSS', 3, '0'),
(52, 'sdsfddfgdg', 'sfdsfdf', '2019-08-27', 'sdftgfy', NULL, 'arup@gmail.com', 'sfdsfgdg', 'USA', 'USA', NULL, 'Family', NULL, '12345', 'sdftgfy', NULL, 'sfdsfgdg', 'dfdgfdg', 'dfdgfdg', '12345', '+1 (234) 354-3545', '18', '18', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 14, NULL, '12345678', NULL, '2019-08-27', NULL, NULL, 'Yes', '2019-08-27', 'Admin', '2019-08-27 00:27:35', NULL, 'zsdsdf', 1, '0');

-- --------------------------------------------------------

--
-- Table structure for table `customer_receipts`
--

CREATE TABLE `customer_receipts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feetransaction`
--

CREATE TABLE `feetransaction` (
  `TransId` bigint(15) NOT NULL,
  `CutomerId` int(11) NOT NULL,
  `AgentId` int(11) NOT NULL,
  `TransDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `TransHead` varchar(20) DEFAULT NULL,
  `TransAmount` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `frontend_customer_temp`
--

CREATE TABLE `frontend_customer_temp` (
  `customerId` int(11) NOT NULL,
  `firstName` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `LastName` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `DOB` date DEFAULT NULL,
  `address1` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address2` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country1` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile2` varchar(18) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `clientType` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zip` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mailing_address1` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mailing_address2` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city1` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state1` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zip1` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cellPhone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `writing_agent` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `agent_manager` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `homePhone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `spouseFirstName` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `spouseLastName` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `spouseDOB` date DEFAULT NULL,
  `dependent1FirstName` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dependent1LastName` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dependent1DOB` date DEFAULT NULL,
  `dependent2FirstName` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Dependent2LastName` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dependent2DOB` date DEFAULT NULL,
  `dependent3FirstName` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dependent3LastName` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dependent3DOB` date DEFAULT NULL,
  `dependent4FirstName` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dependent4LastName` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dependent4DOB` date DEFAULT NULL,
  `planId` bigint(20) UNSIGNED DEFAULT NULL,
  `agentId` bigint(20) UNSIGNED DEFAULT NULL,
  `userName` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `autoRenew` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `membershipDate` date DEFAULT NULL,
  `effectiveDate` date DEFAULT NULL,
  `renewaDate` date DEFAULT NULL,
  `isActive` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `modDate` date DEFAULT NULL,
  `ModBy` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `companyName` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `groupId` bigint(20) UNSIGNED DEFAULT NULL,
  `price` float NOT NULL,
  `membership_type` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `frontend_customer_temp`
--

INSERT INTO `frontend_customer_temp` (`customerId`, `firstName`, `LastName`, `DOB`, `address1`, `address2`, `email`, `city`, `country`, `country1`, `mobile2`, `clientType`, `location`, `zip`, `mailing_address1`, `mailing_address2`, `city1`, `state1`, `state`, `zip1`, `cellPhone`, `writing_agent`, `agent_manager`, `homePhone`, `spouseFirstName`, `spouseLastName`, `spouseDOB`, `dependent1FirstName`, `dependent1LastName`, `dependent1DOB`, `dependent2FirstName`, `Dependent2LastName`, `dependent2DOB`, `dependent3FirstName`, `dependent3LastName`, `dependent3DOB`, `dependent4FirstName`, `dependent4LastName`, `dependent4DOB`, `planId`, `agentId`, `userName`, `password`, `autoRenew`, `membershipDate`, `effectiveDate`, `renewaDate`, `isActive`, `modDate`, `ModBy`, `created_at`, `updated_at`, `companyName`, `groupId`, `price`, `membership_type`) VALUES
(1, 'Pradosh', 'Mukherjee', NULL, 'NY', NULL, 'pradosh@bargadss.com', 'NY', 'Barbados', NULL, NULL, NULL, NULL, NULL, 'NY', NULL, NULL, NULL, 'NY', '123456', '9674419914', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2019-08-20', NULL, NULL, 'No', '2019-08-20', 'customer', '2019-08-20 18:52:01', NULL, 'BSS', NULL, 37.5, 'Family'),
(2, 'Pradosh', 'Mukherjee', NULL, 'NY', NULL, 'pradosh@bargadss.com', 'NY', 'Barbados', NULL, NULL, NULL, NULL, NULL, 'NY', NULL, NULL, NULL, 'NY', '123456', '9674419914', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 65, NULL, NULL, '123456', NULL, '2019-08-20', NULL, NULL, 'No', '2019-08-20', 'customer', '2019-08-20 18:52:41', NULL, 'BSS', NULL, 37.5, 'Family'),
(3, 'Leilani', 'Boyer', NULL, '557-6308 Lacinia Road', NULL, 'leilani@gmail.com', 'San Bernardino', 'USA', NULL, NULL, NULL, NULL, NULL, '557-6308 Lacinia Road', NULL, NULL, NULL, 'ND', '09289', '+123456789', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 64, NULL, NULL, '123456', NULL, '2019-08-20', NULL, NULL, 'No', '2019-08-20', 'customer', '2019-08-20 19:11:59', NULL, 'Global', NULL, 534, 'Family'),
(4, 'Leilani', 'Boyer', NULL, '557-6308 Lacinia Road', NULL, 'boyer@gmail.com', 'San Bernardino', 'USA', NULL, NULL, NULL, NULL, NULL, '557-6308 Lacinia Road', NULL, NULL, NULL, 'ND', '09289', '12345678', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 63, NULL, NULL, '12345', NULL, '2019-08-20', NULL, NULL, 'No', '2019-08-20', 'customer', '2019-08-20 19:16:31', NULL, 'Global', NULL, 2210, 'Individual'),
(5, 'Himanshu', 'Shah', NULL, '555 1st Street', NULL, 'h@gmail.com', 'San Francisco', 'USA', NULL, NULL, NULL, NULL, NULL, '555 1st Street', NULL, NULL, NULL, 'CA', '94107', '415-555-5555', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 65, NULL, NULL, '12345678', NULL, '2019-08-22', NULL, NULL, 'No', '2019-08-22', 'customer', '2019-08-22 07:54:21', NULL, NULL, NULL, 100, 'Individual');

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `groupId` bigint(20) UNSIGNED NOT NULL,
  `groupName` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `GroupDesc` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zoneRegion` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `groupCode` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `modDate` date DEFAULT NULL,
  `modUser` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`groupId`, `groupName`, `GroupDesc`, `zoneRegion`, `groupCode`, `modDate`, `modUser`, `created_at`, `updated_at`) VALUES
(1, 'US Corporate', 'Corporate employee for US Country', NULL, 'USCORPFAM', NULL, NULL, '2019-08-20 10:38:23', NULL),
(2, 'US Govt.', 'Govt employee for US Country', NULL, 'USGOVTFAM', NULL, NULL, '2019-08-20 10:38:19', NULL),
(3, 'US Personal', 'Personal member for US country', NULL, 'USPER', NULL, NULL, '2019-08-20 10:38:51', NULL),
(4, 'NON US Corporate', 'Corporate employee for NON US country', NULL, 'NONUSCORP', NULL, NULL, '2019-08-20 10:39:07', NULL),
(5, 'Non US Govt.', 'Govt employee for NON US country', NULL, 'NONUSGOVT', NULL, NULL, '2019-08-20 10:39:28', NULL),
(6, 'Non US Personal', 'Personal member for NON US country', NULL, 'NONUSPER', NULL, NULL, '2019-08-20 10:39:46', NULL);

-- --------------------------------------------------------

--
-- Stand-in structure for view `group_wise_total_customer`
-- (See below for the actual view)
--
CREATE TABLE `group_wise_total_customer` (
`groupId` bigint(20) unsigned
,`total_customer` bigint(21)
);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_05_14_053629_create_plans_table', 1),
(4, '2019_05_14_053741_create_services_table', 1),
(5, '2019_05_14_053910_create_plan_vs_services_table', 1),
(6, '2019_05_14_053942_create_customers_table', 1),
(7, '2019_05_14_053116_create_groups_table', 2),
(8, '2019_05_14_054016_create_agents_table', 2),
(9, '2019_05_14_054257_create_writing_agents_table', 2),
(10, '2019_05_14_054527_create_agent_payments_table', 2),
(11, '2019_05_14_054618_create_customer_receipts_table', 2),
(12, '2019_05_14_056942_create_customers_table', 3),
(13, '2019_05_15_102047_adds_api_token_to_users_table', 4),
(14, '2019_05_16_064152_edit_user_table_add_coulomb', 5),
(15, '2019_05_16_093037_add_token_in_agent', 6),
(16, '2019_05_17_111013_add_name', 7),
(17, '2019_06_06_060020_add_coulom_gropid_and_international', 8);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plans`
--

CREATE TABLE `plans` (
  `planId` bigint(20) UNSIGNED NOT NULL,
  `planName` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `frequency` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fee` double(15,2) DEFAULT NULL,
  `familyFee` float DEFAULT NULL,
  `FeeByFrequeny` double(15,2) DEFAULT NULL,
  `initiatonFee` double(15,2) DEFAULT NULL,
  `modDate` date DEFAULT NULL,
  `modUser` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `plans`
--

INSERT INTO `plans` (`planId`, `planName`, `frequency`, `fee`, `familyFee`, `FeeByFrequeny`, `initiatonFee`, `modDate`, `modUser`, `created_at`, `updated_at`) VALUES
(1, 'Monthly', 'Monthly', 24.75, 37.5, 0.00, 0.00, '2019-06-05', NULL, '2019-06-11 23:30:00', NULL),
(2, 'Annually', 'Annually', 297.00, 474, 0.00, 60.00, '2019-06-12', NULL, '2019-06-10 23:30:00', NULL),
(3, 'Every 5 Years', 'Every 5 Years', 2150.00, 3150, 0.00, 60.00, '2019-06-21', NULL, '2019-06-10 23:30:00', NULL),
(4, 'LifeTime', 'LifeTime', 3250.00, 4450, 0.00, 60.00, '2019-06-21', NULL, '2019-06-10 23:30:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `plan_vs_services`
--

CREATE TABLE `plan_vs_services` (
  `planVsServiceId` bigint(20) UNSIGNED NOT NULL,
  `planId` bigint(20) UNSIGNED NOT NULL,
  `serviceId` bigint(20) UNSIGNED NOT NULL,
  `modDate` date NOT NULL,
  `modUser` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `serviceId` bigint(20) UNSIGNED NOT NULL,
  `serviceName` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `serviceDesc` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `modDate` date NOT NULL,
  `modUser` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`serviceId`, `serviceName`, `serviceDesc`, `modDate`, `modUser`, `created_at`, `updated_at`) VALUES
(1, 'Kenneth Hills I', 'hi it is test', '1981-06-21', 'un known', '1997-01-03 18:30:00', '2019-05-17 06:47:43'),
(2, 'Cleve Okuneva', 'hi it is test', '2011-02-24', 'un known', '1982-11-02 18:30:00', '2019-05-17 06:47:43'),
(3, 'Trinity Schoen', 'hi it is test', '2010-05-01', 'un known', '1975-06-26 18:30:00', '2019-05-17 06:47:43'),
(4, 'Glen Tillman', 'hi it is test', '1994-05-17', 'un known', '1972-11-28 18:30:00', '2019-05-17 06:47:43'),
(5, 'Mrs. Melody Beier Sr.', 'hi it is test', '1975-12-31', 'un known', '1973-02-11 18:30:00', '2019-05-17 06:47:43'),
(6, 'test service', 'it is only for testing purpose', '2019-05-17', 'Abscd', '2019-05-17 07:51:41', NULL),
(7, 'edit test 2', 'it is only for testing purpose', '2019-05-17', 'adads', '2019-05-17 07:52:21', '2019-05-17 08:01:27'),
(8, 'edit test', 'it is only for testing purpose', '2019-05-17', 'adads', '2019-05-17 07:53:11', '2019-05-17 08:00:58');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `api_token` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `userName` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `modDate` date DEFAULT NULL,
  `modUser` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `email_verified_at`, `password`, `api_token`, `remember_token`, `created_at`, `updated_at`, `phone`, `userName`, `modDate`, `modUser`, `name`) VALUES
(2, 'admin@gmail.com', NULL, '$2y$10$HXiuNpDqBcQIsuDlSrRXcOGoXGxN7UO5teYVPpjxuan2Z07cur0wW', 'vj3zJDnKxsH4tPml78ZkCNPEKd3VgMOzMEwScm8xz2PzRaPEoJ6xyyDdXaLR', NULL, '2019-05-20 00:41:24', '2019-08-27 00:15:45', NULL, '1', NULL, NULL, 'Admin'),
(6, 'jayna@globalmedevac.com', NULL, '$2y$10$iv/T6sEu0YzuG.lSaA7Nz.dVQaqPZHb1k.FMRXcbV7ByJ/ufX1rAy', '7lfG8WWLVeO651M0X35jrwO8d0Kb3FGoWuL3r8xpNKMnQq8kBhxBHI7gzSSl', NULL, '2019-08-12 15:43:11', '2019-08-14 21:42:18', NULL, '3', NULL, NULL, 'Jayna  West'),
(7, 'brian@globalmedevac.com', NULL, '$2y$10$vxjwYqhN/OcuUQI3o9Ko/ucceyU2J1LYv18lDNKxb0uhxpIxXYTs2', 'QfDR4ddVnnPxcNili4mzLpi2SaaUVWJngX1u5JSquCAqXcfjes7H1SEiz2vl', NULL, '2019-08-12 15:46:14', '2019-08-14 21:43:30', NULL, '2', NULL, NULL, 'Brian Morgan'),
(8, 'deidra@globalmedevac.com', NULL, '$2y$10$sMufTvt/LgbJaz01pUj1IemBEGzi0LIInpmcHm7LBB6IORJwematC', 'V3UsE9mJO8ndrC60FIR0UhWEr6yk5xFHdyKgEyMA7JyRMeBSarrS2sl7tZXi', NULL, '2019-08-12 15:48:42', '2019-08-12 15:48:42', NULL, '4', NULL, NULL, 'Deidra Cowling');

-- --------------------------------------------------------

--
-- Table structure for table `writing_agents`
--

CREATE TABLE `writing_agents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure for view `agent_commission_details`
--
DROP TABLE IF EXISTS `agent_commission_details`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `agent_commission_details`  AS  select `atp`.`AgentId` AS `agentId`,`c`.`customerId` AS `customerId`,concat(`c`.`firstName`,' ',`c`.`LastName`) AS `customerName`,`c`.`clientType` AS `client_type`,`p`.`planName` AS `membership_plan`,`atp`.`feeAmount` AS `fees`,concat(`g`.`groupCode`,' ',`g`.`groupId`) AS `groupCode`,round((case when (`atp`.`chargeBackCommision` <> 0) then 0 else `atp`.`Commission` end),2) AS `agent_commision`,round((case when (`atp`.`chargeBackCommision` <> 0) then `atp`.`chargeBackCommision` else 0 end),2) AS `agent_chargeBack_commision`,round((case when (`atp`.`ChargeBackInterest` <> 0) then `atp`.`ChargeBackInterest` else 0 end),2) AS `agent_interest`,(case when (`atp`.`newOrRenew` = 0) then 0 else `atp`.`Commission` end) AS `renewal_commision`,round((case when (`atp`.`managerCommission` <> 0) then 0 else 0 end),2) AS `manager_Commision`,round((case when (`atp`.`ChargeBackInterestForManager` <> 0) then 0 else 0 end),2) AS `manager_interest`,round((case when (`atp`.`stateManagerCommission` <> 0) then 0 else 0 end),2) AS `state_manager_commission`,round((case when (`atp`.`ChargeBackInterestForStateManager` <> 0) then 0 else 0 end),2) AS `state_manager_interest`,round((case when (`atp`.`chargeBackCommision` <> 0) then (case when (`atp`.`ChargeBackInterest` <> 0) then (`atp`.`chargeBackCommision` - `atp`.`ChargeBackInterest`) else `atp`.`chargeBackCommision` end) else `atp`.`Commission` end),2) AS `earned_commission` from (((`agentpayment` `atp` left join `customers` `c` on((`atp`.`customerId` = `c`.`customerId`))) left join `plans` `p` on((`p`.`planId` = `atp`.`planId`))) left join `groups` `g` on((`g`.`groupId` = `c`.`groupId`))) where ((`atp`.`isPaidAgent` = '0') and (`c`.`isPaidCustomer` <> '0')) union all select `atp`.`managerId` AS `managerId`,`c`.`customerId` AS `customerId`,concat(`c`.`firstName`,' ',`c`.`LastName`) AS `customerName`,`c`.`clientType` AS `client_type`,`p`.`planName` AS `membership_plan`,`atp`.`feeAmount` AS `fees`,concat(`g`.`groupCode`,' ',`g`.`groupId`) AS `groupCode`,round((case when (`atp`.`chargeBackCommision` <> 0) then 0 else 0 end),2) AS `agent_commision`,round((case when (`atp`.`chargeBackCommision` <> 0) then 0 else 0 end),2) AS `agent_chargeBack_commision`,round((case when (`atp`.`ChargeBackInterest` <> 0) then 0 else 0 end),2) AS `agent_interest`,(case when (`atp`.`newOrRenew` = 0) then 0 else `atp`.`Commission` end) AS `renewal_commision`,round((case when (`atp`.`managerCommission` <> 0) then `atp`.`managerCommission` else 0 end),2) AS `manager_Commision`,round((case when (`atp`.`ChargeBackInterestForManager` <> 0) then `atp`.`ChargeBackInterestForManager` else 0 end),2) AS `manager_interest`,round((case when (`atp`.`stateManagerCommission` <> 0) then 0 else 0 end),2) AS `state_manager_commission`,round((case when (`atp`.`ChargeBackInterestForStateManager` <> 0) then 0 else 0 end),2) AS `state_manager_interest`,round((case when (`atp`.`managerCommission` <> 0) then (case when (`atp`.`ChargeBackInterestForManager` <> 0) then (`atp`.`managerCommission` - `atp`.`ChargeBackInterestForManager`) else `atp`.`managerCommission` end) else `atp`.`Commission` end),2) AS `earned_commission` from (((`agentpayment` `atp` left join `customers` `c` on((`atp`.`customerId` = `c`.`customerId`))) left join `plans` `p` on((`p`.`planId` = `atp`.`planId`))) left join `groups` `g` on((`g`.`groupId` = `c`.`groupId`))) where ((`atp`.`isPaidManager` = '0') and (`atp`.`managerId` <> '0') and (`c`.`isPaidCustomer` <> '0')) union all select `atp`.`stateManagerId` AS `statemanagerId`,`c`.`customerId` AS `customerId`,concat(`c`.`firstName`,' ',`c`.`LastName`) AS `customerName`,`c`.`clientType` AS `client_type`,`p`.`planName` AS `membership_plan`,`atp`.`feeAmount` AS `fees`,concat(`g`.`groupCode`,' ',`g`.`groupId`) AS `groupCode`,round((case when (`atp`.`chargeBackCommision` <> 0) then 0 else 0 end),2) AS `agent_commision`,round((case when (`atp`.`chargeBackCommision` <> 0) then 0 else 0 end),2) AS `agent_chargeBack_commision`,round((case when (`atp`.`ChargeBackInterest` <> 0) then 0 else 0 end),2) AS `agent_interest`,(case when (`atp`.`newOrRenew` = 0) then 0 else `atp`.`Commission` end) AS `renewal_commision`,round((case when (`atp`.`managerCommission` <> 0) then 0 else 0 end),2) AS `manager_commision`,round((case when (`atp`.`ChargeBackInterestForManager` <> 0) then 0 else 0 end),2) AS `manager_interest`,round((case when (`atp`.`stateManagerCommission` <> 0) then `atp`.`stateManagerCommission` else 0 end),2) AS `state_manager_commission`,round((case when (`atp`.`ChargeBackInterestForStateManager` <> 0) then `atp`.`ChargeBackInterestForStateManager` else 0 end),2) AS `state_manager_interest`,round((case when (`atp`.`stateManagerCommission` <> 0) then (case when (`atp`.`ChargeBackInterestForStateManager` <> 0) then (`atp`.`stateManagerCommission` - `atp`.`ChargeBackInterestForStateManager`) else `atp`.`stateManagerCommission` end) else `atp`.`Commission` end),2) AS `earned_commission` from (((`agentpayment` `atp` left join `customers` `c` on((`atp`.`customerId` = `c`.`customerId`))) left join `plans` `p` on((`p`.`planId` = `atp`.`planId`))) left join `groups` `g` on((`g`.`groupId` = `c`.`groupId`))) where ((`atp`.`isPaidStateManager` = '0') and (`atp`.`stateManagerId` <> '0') and (`c`.`isPaidCustomer` <> '0')) ;

-- --------------------------------------------------------

--
-- Structure for view `agent_manager_list`
--
DROP TABLE IF EXISTS `agent_manager_list`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `agent_manager_list`  AS  select `ag`.`agentId` AS `agentId`,concat(`ag`.`firstName`,' ',`ag`.`lastName`) AS `agent_name`,`ag`.`dob` AS `dob`,`ag`.`levelID` AS `levelID`,concat(`agm`.`firstName`,' ',`agm`.`lastName`) AS `manager_name` from ((`agents` `ag` left join `agentpayment` `agt` on((`ag`.`agentId` = `agt`.`AgentId`))) left join `agents` `agm` on((`agm`.`agentId` = `agt`.`managerId`))) order by `ag`.`agentId` desc ;

-- --------------------------------------------------------

--
-- Structure for view `agent_wise_commission`
--
DROP TABLE IF EXISTS `agent_wise_commission`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `agent_wise_commission`  AS  select round(sum(`ap`.`earned_commission`),2) AS `total_commission`,`ap`.`agentId` AS `AgentId` from `agent_commission_details` `ap` group by `ap`.`agentId` ;

-- --------------------------------------------------------

--
-- Structure for view `group_wise_total_customer`
--
DROP TABLE IF EXISTS `group_wise_total_customer`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `group_wise_total_customer`  AS  select `customers`.`groupId` AS `groupId`,count(`customers`.`customerId`) AS `total_customer` from `customers` group by `customers`.`groupId` order by `customers`.`groupId` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `agentlevels`
--
ALTER TABLE `agentlevels`
  ADD PRIMARY KEY (`levelID`);

--
-- Indexes for table `agentmanagers`
--
ALTER TABLE `agentmanagers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `agentpayment`
--
ALTER TABLE `agentpayment`
  ADD PRIMARY KEY (`paymentId`);

--
-- Indexes for table `agents`
--
ALTER TABLE `agents`
  ADD PRIMARY KEY (`agentId`);

--
-- Indexes for table `client_claim`
--
ALTER TABLE `client_claim`
  ADD PRIMARY KEY (`claim_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customerId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `agentpayment`
--
ALTER TABLE `agentpayment`
  MODIFY `paymentId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `agents`
--
ALTER TABLE `agents`
  MODIFY `agentId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `client_claim`
--
ALTER TABLE `client_claim`
  MODIFY `claim_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customerId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
