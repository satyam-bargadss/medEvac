-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 01, 2019 at 09:21 AM
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
-- Database: `medevac2`
--

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
  `recurringPaymentDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `agentpayment`
--

INSERT INTO `agentpayment` (`paymentId`, `planId`, `customerId`, `AgentId`, `managerId`, `PercentOrDollar`, `Commission`, `chargeBackCommision`, `chargeBackInstalment`, `relasingDate`, `managerCommission`, `IsAdvance`, `PaymentMode`, `PaymentDate`, `ModDate`, `ModUser`, `feeAmount`, `newOrRenew`, `paymentletterDate`, `recurringPaymentDate`) VALUES
(15, 65, 1048, 9, 10, 0, NULL, 59.4, 6, NULL, 2.475, 0, 'cash', '2019-06-28', '2019-06-28', NULL, 24.75, 'NEW', '2019-07-28', '2019-06-28');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `agentpayment`
--
ALTER TABLE `agentpayment`
  ADD PRIMARY KEY (`paymentId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `agentpayment`
--
ALTER TABLE `agentpayment`
  MODIFY `paymentId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
