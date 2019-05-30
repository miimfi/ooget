-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.7.26-0ubuntu0.18.04.1 - (Ubuntu)
-- Server OS:                    Linux
-- HeidiSQL Version:             10.1.0.5464
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Dumping database structure for ooget
DROP DATABASE IF EXISTS `ooget`;
CREATE DATABASE IF NOT EXISTS `ooget` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `ooget`;

-- Dumping structure for table ooget.applicants
DROP TABLE IF EXISTS `applicants`;
CREATE TABLE IF NOT EXISTS `applicants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_id` int(11) NOT NULL DEFAULT '0',
  `jobseeker_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table ooget.applicants: ~0 rows (approximately)
DELETE FROM `applicants`;
/*!40000 ALTER TABLE `applicants` DISABLE KEYS */;
/*!40000 ALTER TABLE `applicants` ENABLE KEYS */;

-- Dumping structure for table ooget.BankDetail
DROP TABLE IF EXISTS `BankDetail`;
CREATE TABLE IF NOT EXISTS `BankDetail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(50) DEFAULT NULL,
  `short_name` varchar(10) DEFAULT NULL,
  `bank_code` varchar(10) DEFAULT NULL,
  `hint` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

-- Dumping data for table ooget.BankDetail: ~9 rows (approximately)
DELETE FROM `BankDetail`;
/*!40000 ALTER TABLE `BankDetail` DISABLE KEYS */;
INSERT INTO `BankDetail` (`id`, `full_name`, `short_name`, `bank_code`, `hint`) VALUES
	(1, 'The Hongkong & Shanghai Banking Corporation Ltd', 'HSBC', '7232', '<div>The account number consists of 9 digits. The ACH branch code is normally incorporated into the account. The first 3 digits is the ACH branch code and the subsequent 9 digits is the account number.</div><br/><div>E.g. If the account is 146225193001, the ACH branch code will be 146 and the account number 225193001.'),
	(2, 'United Overseas Bank Ltd', 'UOB', '7375', '<div>The account number consists of 10 digits. Please use the first 3 digits of the account number and refer to Appendix A of <a style=\'color: #021def;\' href=\'http://www.uobgroup.com.sg/pages/business/cashmgmt/achcode.html\' target=\'_blank\'>http://www.uobgroup.com.sg/pages/business/cashmgmt/achcode.html</a> to retrieve the corresponding ACH branch code.</div><br/><div>E.g. If the account number is 9102031012, the corresponding ACH branch code will be 030 and the account number 9102031012'),
	(3, 'DBS Bank Ltd', 'DBS', '7171', '<div>The account number consists of 10 digits. Please use the first 3 digits of the account number as the ACH branch code.</div><br/><div>E.g. If the account number is 0290188891, the ACH branch code will be 029 and the account number 0290188891.'),
	(4, 'POSB', 'POSB', '7171', '<div>The account number consists of 9 digits. All POSB accounts must route to their head office using ACH branch code 081.'),
	(5, 'Oversea-Chinese Banking Corporation Ltd', 'OCBC', '7339', '<div>The account number consists of 7 or 9 digits. The ACH branch code is normally incorporated into the account. The first 3 digits is the ACH branch code and the subsequent 7 or 9 digits is the account number.</div><br/><div>E.g. If the account is 501101899001, the ACH branch code will be 501 and the account number 101899001.'),
	(6, 'Standard Chartered Bank', 'SCB', '7144', '<div>The account number consists of 10 digits. The ACH branch code is normally derived from the first 2 digits of the account number and adding a zero in front.</div><br/><div>E.g. If the account number is 0123456789, the ACH branch code will be 001 and the account number 0123456789.'),
	(7, 'Citibank', 'Citibank', '7214', '<div>The account number consists of 10 digits. The ACH branch code varies for corporate and personal accounts.</div><br/><div>E.g. If the branch name is ShentonWay-IB, this will be for corporate account. The ACH branch code will be 001.</div><br/><div>If the branch name is ShentonWay-CSG, this will be for personal account. The ACH branch code will be 011 and the account number 1012345670.'),
	(9, 'indesint1', 'indddd', 'IND001', 'te4st test test');
/*!40000 ALTER TABLE `BankDetail` ENABLE KEYS */;

-- Dumping structure for table ooget.company
DROP TABLE IF EXISTS `company`;
CREATE TABLE IF NOT EXISTS `company` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(70) DEFAULT NULL,
  `profile` text,
  `uen` varchar(20) DEFAULT NULL,
  `companycode` varchar(20) DEFAULT NULL,
  `industry` int(11) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `address1` varchar(100) DEFAULT NULL,
  `address2` varchar(100) DEFAULT NULL,
  `website` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `imgpath` varchar(100) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  `createby` int(11) DEFAULT NULL,
  `log` varchar(20) DEFAULT NULL,
  `lat` varchar(20) DEFAULT NULL,
  `TermsConditions` int(11) DEFAULT '0',
  `TermsConditions_file` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `industry` (`industry`),
  CONSTRAINT `industry` FOREIGN KEY (`industry`) REFERENCES `industry` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;

-- Dumping data for table ooget.company: ~9 rows (approximately)
DELETE FROM `company`;
/*!40000 ALTER TABLE `company` DISABLE KEYS */;
INSERT INTO `company` (`id`, `name`, `profile`, `uen`, `companycode`, `industry`, `country`, `city`, `address1`, `address2`, `website`, `email`, `phone`, `imgpath`, `status`, `createby`, `log`, `lat`, `TermsConditions`, `TermsConditions_file`) VALUES
	(5, 'max3', 'test1', '54545554k', '33', 2, 'rr', '2', '2', '2', '2', '2', '2', '2', 2, 4, '2', '2', 0, '0'),
	(6, 'max1', 'test', '4565', 'max33', 1, 'india', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 4, NULL, NULL, 0, '0'),
	(7, 'updat1', 'test profile', 'D445', NULL, 1, 'india', 'chennai', NULL, NULL, NULL, NULL, NULL, NULL, 2, 4, NULL, NULL, 0, '0'),
	(8, 'doss company', 'test profile', 'D4565', 'D445', 1, 'india', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 2, NULL, NULL, 0, '0'),
	(9, 'doss1 company', 'test profile', 'D45654', 'D445', 1, 'india', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 4, NULL, NULL, 0, NULL),
	(10, 'doss1 company', 'test profile', 'D456541', 'D445', 1, 'india', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 4, NULL, NULL, 0, NULL),
	(11, 'doss company', 'test profile', 'D45659', 'D445', 1, 'india', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, NULL, NULL, NULL, 0, NULL),
	(12, 'dfdf', 'fdsfds', '54545654K', 'dsfdfdsf', 1, 'fdsfdf', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 4, NULL, NULL, 0, NULL),
	(13, 'emp1', 'emp1 profile', '123456789A', 'fdfds', 1, 'sgfgfdg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, NULL, NULL, NULL, 0, NULL);
/*!40000 ALTER TABLE `company` ENABLE KEYS */;

-- Dumping structure for table ooget.contracts
DROP TABLE IF EXISTS `contracts`;
CREATE TABLE IF NOT EXISTS `contracts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_id` int(11) DEFAULT NULL,
  `jobseeker_id` int(11) DEFAULT NULL,
  `applied_on` datetime DEFAULT NULL,
  `offered_on` datetime DEFAULT NULL,
  `offer_rejected` datetime DEFAULT NULL,
  `offer_accepted` datetime DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- Dumping data for table ooget.contracts: ~0 rows (approximately)
DELETE FROM `contracts`;
/*!40000 ALTER TABLE `contracts` DISABLE KEYS */;
INSERT INTO `contracts` (`id`, `job_id`, `jobseeker_id`, `applied_on`, `offered_on`, `offer_rejected`, `offer_accepted`, `user_id`, `deleted`) VALUES
	(1, 1, 9, '2019-05-28 17:33:52', '2019-05-28 17:35:21', NULL, '2019-05-28 17:43:50', 14, 0);
/*!40000 ALTER TABLE `contracts` ENABLE KEYS */;

-- Dumping structure for table ooget.faq
DROP TABLE IF EXISTS `faq`;
CREATE TABLE IF NOT EXISTS `faq` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `data` text NOT NULL,
  `type` int(11) NOT NULL DEFAULT '3',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table ooget.faq: ~0 rows (approximately)
DELETE FROM `faq`;
/*!40000 ALTER TABLE `faq` DISABLE KEYS */;
/*!40000 ALTER TABLE `faq` ENABLE KEYS */;

-- Dumping structure for table ooget.holiday
DROP TABLE IF EXISTS `holiday`;
CREATE TABLE IF NOT EXISTS `holiday` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `country` varchar(50) DEFAULT 'singapore',
  PRIMARY KEY (`id`),
  UNIQUE KEY `date` (`date`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

-- Dumping data for table ooget.holiday: ~5 rows (approximately)
DELETE FROM `holiday`;
/*!40000 ALTER TABLE `holiday` DISABLE KEYS */;
INSERT INTO `holiday` (`id`, `name`, `date`, `country`) VALUES
	(1, 'test holiday', '2019-06-05', 'singapore'),
	(2, 'doss 126', '2019-05-06', 'singapore'),
	(5, 'car12', '2019-05-24', 'singapore'),
	(6, 'test holiday 2019-06-21', '2019-05-31', 'singapore');
/*!40000 ALTER TABLE `holiday` ENABLE KEYS */;

-- Dumping structure for table ooget.industry
DROP TABLE IF EXISTS `industry`;
CREATE TABLE IF NOT EXISTS `industry` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;

-- Dumping data for table ooget.industry: ~14 rows (approximately)
DELETE FROM `industry`;
/*!40000 ALTER TABLE `industry` DISABLE KEYS */;
INSERT INTO `industry` (`id`, `name`) VALUES
	(1, 'Aerospace'),
	(2, 'Creative Industries'),
	(3, 'Energy & Chemicals'),
	(4, 'Logistics & Supply Chain Mangement'),
	(5, 'Medical Technology'),
	(6, 'Pharmaceutical & Biotechnology'),
	(7, 'Professional Services'),
	(8, 'Consumer Business'),
	(9, 'Electronics'),
	(10, 'Information & Communications Technology'),
	(11, 'Oil & Gas Equipment and Services'),
	(12, 'Natural Resources'),
	(13, 'Precision Engineering'),
	(14, 'Urban Solutions & Sustainability');
/*!40000 ALTER TABLE `industry` ENABLE KEYS */;

-- Dumping structure for table ooget.jobseeker
DROP TABLE IF EXISTS `jobseeker`;
CREATE TABLE IF NOT EXISTS `jobseeker` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `status` int(11) DEFAULT '0',
  `country` varchar(50) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `lastlogin` timestamp NULL DEFAULT NULL,
  `timezone` varchar(50) DEFAULT NULL,
  `theme` int(11) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `mobile` varchar(15) DEFAULT NULL,
  `address` varchar(150) DEFAULT NULL,
  `imgpath` varchar(200) DEFAULT NULL,
  `city` varchar(60) DEFAULT NULL,
  `gender` int(11) DEFAULT NULL,
  `nric` varchar(20) DEFAULT NULL,
  `race` int(11) DEFAULT NULL,
  `nationality` int(11) DEFAULT NULL,
  `employment_type` char(8) DEFAULT NULL,
  `region` varchar(50) DEFAULT NULL,
  `location` varchar(200) DEFAULT NULL,
  `experience_in` varchar(200) DEFAULT NULL,
  `experience_year` int(11) DEFAULT '0',
  `experience_details` text,
  `notification` int(11) DEFAULT NULL,
  `notification_off_from` date DEFAULT NULL,
  `notification_off_to` date DEFAULT NULL,
  `bank_id` int(11) DEFAULT NULL,
  `branch_code` varchar(20) DEFAULT NULL,
  `account_no` varchar(20) DEFAULT NULL,
  `specializations` varchar(200) DEFAULT NULL,
  `working_environment` varchar(200) DEFAULT NULL,
  `id_imgpath1` varchar(200) DEFAULT NULL,
  `id_imgpath2` varchar(200) DEFAULT NULL,
  `residency_type` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

-- Dumping data for table ooget.jobseeker: ~5 rows (approximately)
DELETE FROM `jobseeker`;
/*!40000 ALTER TABLE `jobseeker` DISABLE KEYS */;
INSERT INTO `jobseeker` (`id`, `firstname`, `lastname`, `email`, `password`, `status`, `country`, `dob`, `lastlogin`, `timezone`, `theme`, `phone`, `mobile`, `address`, `imgpath`, `city`, `gender`, `nric`, `race`, `nationality`, `employment_type`, `region`, `location`, `experience_in`, `experience_year`, `experience_details`, `notification`, `notification_off_from`, `notification_off_to`, `bank_id`, `branch_code`, `account_no`, `specializations`, `working_environment`, `id_imgpath1`, `id_imgpath2`, `residency_type`) VALUES
	(2, 'gfdgf555', 'doss', 'doss2@gmail.com', 'doss', 1, 'Singapore', '1990-12-31', '2019-05-22 18:39:41', 'india', 5, '999', '4545435435', 'frder', 'teast/.srtt', NULL, 0, 'G5455444K', 4, 1, '1', '1,2,4', '35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,29,30,31,32,33,34,9,10,11,12,13,14,15', '2,3,5', 4, '[{\'previouscompanyname\':\'tertr\',\'previouscompanyposition\':\'tret\',\'previousjobresponsibility\':\'retretre\',\'previousjobfrom\':\'2019/05/14\',\'previousjobto\':\'2019/05/21\'}]', 0, '2019-05-22', '2019-05-29', 1, '54543', '435435', '1,3,66,67', '1,2', NULL, NULL, NULL),
	(4, 'chidambaradoss', 'doss', 'doss3@gmail.com', 'doss', 1, 'ind1', '2019-05-09', '2019-05-09 11:55:46', 'india', 5, '999', '88', 'ad1', 'teast/.srtt', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(5, 'jobseeker bank test', NULL, 'jobseekerbanktest@gmail.com', 'doss', 0, 'ind', '1989-07-12', '2019-05-14 16:35:10', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'SSri123', 1, 1, '1', '5,6', '6,7', '1,2,3', 5, '{111{abc},{123}}', 1, NULL, NULL, 123, 'cdr123', '99945969', '8,9', '10,11', NULL, NULL, NULL),
	(6, 'doss', NULL, 'dos@gmail.com', 'test', 0, 'india', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(9, 'js1dfd', NULL, 'js1@og.net', 'Js1@12345', 1, 'Singapore', '1990-10-05', '2019-05-29 18:03:53', '', 0, NULL, '456454', '5435435', 'media/profile/jobseeker/9.png', NULL, 1, 'S2343435H', 2, 1, '2', '2,3', '29,30,31,32,33,34,16,17,18,19,20,21,22,23,24,25,26,27,28', '1,2', 4, '[{"previouscompanyname":"fgfdg","previouscompanyposition":"gdfgfd","previousjobresponsibility":"gfdgfdg","previousjobfrom":"2019/05/06","previousjobto":"2019/05/22"},{"previouscompanyname":"dsfdsf","previouscompanyposition":"fdsfsdfs","previousjobresponsibility":"fsdfsdf","previousjobfrom":"2019/05/01","previousjobto":"2019/05/22"}]', 1, NULL, NULL, 1, '435435', '454354354', '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63', '1,2,3', NULL, NULL, 0),
	(10, 'doss', NULL, 'sq@sq.com', 'test', 0, 'india', NULL, '2019-05-28 12:13:01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
/*!40000 ALTER TABLE `jobseeker` ENABLE KEYS */;

-- Dumping structure for table ooget.jobseeker_bankdetail_tmp
DROP TABLE IF EXISTS `jobseeker_bankdetail_tmp`;
CREATE TABLE IF NOT EXISTS `jobseeker_bankdetail_tmp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `jobseekerid` int(11) DEFAULT NULL,
  `bank_id` int(11) DEFAULT NULL,
  `branch_code` varchar(20) DEFAULT NULL,
  `account_no` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- Dumping data for table ooget.jobseeker_bankdetail_tmp: ~0 rows (approximately)
DELETE FROM `jobseeker_bankdetail_tmp`;
/*!40000 ALTER TABLE `jobseeker_bankdetail_tmp` DISABLE KEYS */;
INSERT INTO `jobseeker_bankdetail_tmp` (`id`, `jobseekerid`, `bank_id`, `branch_code`, `account_no`) VALUES
	(1, 5, 123, 'cdr123', '99945969');
/*!40000 ALTER TABLE `jobseeker_bankdetail_tmp` ENABLE KEYS */;

-- Dumping structure for table ooget.jobseeker_experience_tmp
DROP TABLE IF EXISTS `jobseeker_experience_tmp`;
CREATE TABLE IF NOT EXISTS `jobseeker_experience_tmp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `jobseekerid` int(11) NOT NULL DEFAULT '0',
  `company_name` varchar(50) NOT NULL DEFAULT '0',
  `position` varchar(50) NOT NULL DEFAULT '0',
  `responsibility` text NOT NULL,
  `from` date NOT NULL,
  `to` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table ooget.jobseeker_experience_tmp: ~0 rows (approximately)
DELETE FROM `jobseeker_experience_tmp`;
/*!40000 ALTER TABLE `jobseeker_experience_tmp` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobseeker_experience_tmp` ENABLE KEYS */;

-- Dumping structure for table ooget.job_break_list
DROP TABLE IF EXISTS `job_break_list`;
CREATE TABLE IF NOT EXISTS `job_break_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_id` int(11) NOT NULL,
  `break_name` varchar(50) NOT NULL,
  `from` varchar(10) NOT NULL,
  `to` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

-- Dumping data for table ooget.job_break_list: ~8 rows (approximately)
DELETE FROM `job_break_list`;
/*!40000 ALTER TABLE `job_break_list` DISABLE KEYS */;
INSERT INTO `job_break_list` (`id`, `job_id`, `break_name`, `from`, `to`) VALUES
	(1, 1, 'b1', '11', '55'),
	(2, 1, 'b1', '11', '55'),
	(3, 2, 'b1', '11', '55'),
	(4, 2, 'b1', '11', '55'),
	(5, 3, 'b1', '11', '55'),
	(6, 3, 'b1', '11', '55'),
	(7, 4, 'b1', '11', '55'),
	(8, 4, 'b1', '11', '55'),
	(9, 5, 'b1', '11', '55'),
	(10, 5, 'b1', '11', '55'),
	(11, 6, 'rtret', '18:59', '19:59'),
	(12, 7, 'b1', '10:30', '11:30');
/*!40000 ALTER TABLE `job_break_list` ENABLE KEYS */;

-- Dumping structure for table ooget.job_list
DROP TABLE IF EXISTS `job_list`;
CREATE TABLE IF NOT EXISTS `job_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_name` varchar(100) DEFAULT NULL,
  `job_name` varchar(100) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `employement_type` int(11) NOT NULL DEFAULT '1',
  `description` text,
  `status` int(11) DEFAULT '1',
  `specializations` varchar(200) DEFAULT NULL,
  `working_environment` varchar(200) DEFAULT NULL,
  `pax_total` int(11) DEFAULT NULL,
  `grace_period` int(11) DEFAULT NULL,
  `over_time_rounding` int(11) DEFAULT NULL,
  `over_time_minimum` int(11) DEFAULT NULL,
  `from` date DEFAULT NULL,
  `to` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `work_days_type` int(11) DEFAULT NULL,
  `postal_code` varchar(15) DEFAULT NULL,
  `address` varchar(200) DEFAULT NULL,
  `unit_no` varchar(15) DEFAULT NULL,
  `region` varchar(50) DEFAULT NULL,
  `location` varchar(200) DEFAULT NULL,
  `charge_rate` int(11) DEFAULT '0',
  `markup_rate` int(11) DEFAULT '0',
  `markup_in` varchar(10) DEFAULT NULL,
  `jobseeker_salary` int(11) DEFAULT '0',
  `markup_amount` int(11) DEFAULT '0',
  `auto_offered` int(11) DEFAULT '0',
  `auto_accepted` int(11) DEFAULT '0',
  `required` int(11) DEFAULT '0',
  `recruitment_open` int(11) DEFAULT '1',
  `employer_id` int(11) DEFAULT '0',
  `created_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `job_no` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

-- Dumping data for table ooget.job_list: ~5 rows (approximately)
DELETE FROM `job_list`;
/*!40000 ALTER TABLE `job_list` DISABLE KEYS */;
INSERT INTO `job_list` (`id`, `project_name`, `job_name`, `department`, `employement_type`, `description`, `status`, `specializations`, `working_environment`, `pax_total`, `grace_period`, `over_time_rounding`, `over_time_minimum`, `from`, `to`, `start_time`, `end_time`, `work_days_type`, `postal_code`, `address`, `unit_no`, `region`, `location`, `charge_rate`, `markup_rate`, `markup_in`, `jobseeker_salary`, `markup_amount`, `auto_offered`, `auto_accepted`, `required`, `recruitment_open`, `employer_id`, `created_on`, `job_no`) VALUES
	(1, 'job 1', 'job 1', 'it', 1, 'test insert', 2, '1', '3,4', 2, 14, 15, 20, '2019-05-28', '2019-05-31', '13:13:00', '23:30:00', 1, '623501', 'test address', '9', '1', '11', 100, 15, '%', 85, 15, 0, 0, 1, 1, 13, '2019-05-28 18:35:41', 'OOGET-2019-0001'),
	(2, 'job 2', 'job 2', 'mec', 1, 'test insert', 2, '2', '3,4', 1, 14, 15, 20, '2019-05-29', '2019-05-28', '09:00:00', '12:00:00', 1, '623501', 'test address', '9', '5', '11', 100, 15, '%', 85, 15, 0, 0, 0, 1, 6, '2019-05-29 12:39:20', 'OOGET-2019-0002'),
	(3, 'job 3', 'job 3', 'tech', 1, 'test insert', 2, '1', '3,4', 1, 14, 15, 20, '2019-05-30', '2019-06-14', '10:30:00', '13:30:00', 1, '623501', 'test address', '9', '3', '11', 100, 15, '%', 85, 15, 0, 0, 1, 1, 13, '2019-05-24 12:46:18', 'OOGET-2019-0003'),
	(4, 'job 4', 'job 4', 'hardware', 1, 'test insert', 2, '2', '3,4', 1, 14, 15, 20, '2019-06-05', '2019-06-21', '12:30:00', '17:30:00', 1, '623501', 'test address', '9', '2', '11', 100, 15, '%', 85, 15, 0, 0, 0, 1, 13, '2019-05-24 17:27:57', 'OOGET-2019-0004'),
	(5, 'job 5', 'job 5', 'driver', 1, 'test insert', 2, '3', '3,4', 1, 14, 15, 20, '2019-06-22', '2019-06-30', '14:30:00', '18:30:00', 1, '623501', 'test address', '9', '1', '9', 100, 15, '%', 85, 15, 0, 0, 0, 1, 13, '2019-05-24 17:44:52', 'OOGET-2019-0005'),
	(6, 'job 6 ', 'job 6', 'trtr', 1, 'ret', 2, '12', '2,4,5', 3, 5, 15, 30, '2019-05-03', '2019-05-07', '17:49:00', '02:49:00', 1, '454545', 'rtrt', 'tret', '1', '8', 45, 4, 'sgdollar', 41, 4, 1, 1, 0, 1, 13, '2019-05-25 17:49:43', 'OOGET-2019-0006'),
	(7, 'job 1', 'job 1', 'it', 1, 'test insert', 2, '12', '3,4', 2, 5, 15, 30, '2019-05-25', '2019-05-26', '09:30:00', '14:30:00', 1, '623501', 'test address', '9', '1', '38', 100, 15, '%', 85, 15, 0, 0, 0, 1, 13, '2019-05-28 14:53:50', 'OOGET-2019-0007');
/*!40000 ALTER TABLE `job_list` ENABLE KEYS */;

-- Dumping structure for table ooget.location
DROP TABLE IF EXISTS `location`;
CREATE TABLE IF NOT EXISTS `location` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `region` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=latin1;

-- Dumping data for table ooget.location: ~56 rows (approximately)
DELETE FROM `location`;
/*!40000 ALTER TABLE `location` DISABLE KEYS */;
INSERT INTO `location` (`id`, `name`, `region`) VALUES
	(1, 'Central Water Catchment', '5'),
	(2, 'Lim Chu Kang', '5'),
	(3, 'Mandai', '5'),
	(4, 'Sembawang', '5'),
	(5, 'Simpang', '5'),
	(6, 'Sungei Kadut', '5'),
	(7, 'Woodlands', '5'),
	(8, 'Yishun', '5'),
	(9, 'Ang Mo Kio', '4'),
	(10, 'Hougang', '4'),
	(11, 'North Eastern Islands', '4'),
	(12, 'Punggol', '4'),
	(13, 'Seletar', '4'),
	(14, 'Sengkang', '4'),
	(15, 'Serangoon', '4'),
	(16, 'Boon Lay', '3'),
	(17, 'Bukit Batok', '3'),
	(18, 'Bukit Panjang', '3'),
	(19, 'Choa Chu Kang', '3'),
	(20, 'Clementi', '3'),
	(21, 'Jurong East', '3'),
	(22, 'Jurong West', '3'),
	(23, 'Pioneer', '3'),
	(24, 'Tengah', '3'),
	(25, 'Tuas', '3'),
	(26, 'Western Islands', '3'),
	(27, 'Western Water', '3'),
	(28, 'Catchment', '3'),
	(29, 'Bedok', '2'),
	(30, 'Changi', '2'),
	(31, 'Changi Bay', '2'),
	(32, 'Pasir Ris', '2'),
	(33, 'Paya Lebar', '2'),
	(34, 'Tampines', '2'),
	(35, 'Bishan', '1'),
	(36, 'Bukit Merah', '1'),
	(37, 'Bukit Timah', '1'),
	(38, 'Downtown Core', '1'),
	(39, 'Geylang', '1'),
	(40, 'Kallang', '1'),
	(41, 'Marina East', '1'),
	(42, 'Marina South', '1'),
	(43, 'Marine Parade', '1'),
	(44, 'Museum', '1'),
	(45, 'Newton', '1'),
	(46, 'Novena', '1'),
	(47, 'Orchard', '1'),
	(48, 'Outram', '1'),
	(49, 'Queenstown', '1'),
	(50, 'River Valley', '1'),
	(51, 'Rochor', '1'),
	(52, 'Singapore River', '1'),
	(53, 'Southern Islands', '1'),
	(54, 'Straits View', '1'),
	(55, 'Tanglin', '1'),
	(56, 'Toa Payoh', '1');
/*!40000 ALTER TABLE `location` ENABLE KEYS */;

-- Dumping structure for table ooget.ModuleMode
DROP TABLE IF EXISTS `ModuleMode`;
CREATE TABLE IF NOT EXISTS `ModuleMode` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(50) DEFAULT NULL,
  `mode` varchar(50) DEFAULT NULL,
  `status` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table ooget.ModuleMode: ~0 rows (approximately)
DELETE FROM `ModuleMode`;
/*!40000 ALTER TABLE `ModuleMode` DISABLE KEYS */;
/*!40000 ALTER TABLE `ModuleMode` ENABLE KEYS */;

-- Dumping structure for table ooget.PartTimeSpecializations
DROP TABLE IF EXISTS `PartTimeSpecializations`;
CREATE TABLE IF NOT EXISTS `PartTimeSpecializations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(70) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

-- Dumping data for table ooget.PartTimeSpecializations: ~8 rows (approximately)
DELETE FROM `PartTimeSpecializations`;
/*!40000 ALTER TABLE `PartTimeSpecializations` DISABLE KEYS */;
INSERT INTO `PartTimeSpecializations` (`id`, `name`) VALUES
	(1, 'Admin'),
	(2, 'Beautician Wellness'),
	(3, 'Customer Service'),
	(4, 'Drivers/Delivery'),
	(5, 'Event'),
	(6, 'Food & Beverage'),
	(7, 'Packer/Mover'),
	(8, 'Retails');
/*!40000 ALTER TABLE `PartTimeSpecializations` ENABLE KEYS */;

-- Dumping structure for table ooget.Races
DROP TABLE IF EXISTS `Races`;
CREATE TABLE IF NOT EXISTS `Races` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

-- Dumping data for table ooget.Races: ~4 rows (approximately)
DELETE FROM `Races`;
/*!40000 ALTER TABLE `Races` DISABLE KEYS */;
INSERT INTO `Races` (`id`, `name`) VALUES
	(1, 'indian'),
	(2, 'chinese'),
	(3, 'malay'),
	(4, 'others');
/*!40000 ALTER TABLE `Races` ENABLE KEYS */;

-- Dumping structure for table ooget.region
DROP TABLE IF EXISTS `region`;
CREATE TABLE IF NOT EXISTS `region` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- Dumping data for table ooget.region: ~4 rows (approximately)
DELETE FROM `region`;
/*!40000 ALTER TABLE `region` DISABLE KEYS */;
INSERT INTO `region` (`id`, `name`) VALUES
	(1, 'Central '),
	(2, 'East'),
	(3, 'West'),
	(4, 'North East'),
	(5, 'North');
/*!40000 ALTER TABLE `region` ENABLE KEYS */;

-- Dumping structure for table ooget.saved_job
DROP TABLE IF EXISTS `saved_job`;
CREATE TABLE IF NOT EXISTS `saved_job` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_id` int(11) DEFAULT NULL,
  `jobseeker_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `job_id` (`job_id`,`jobseeker_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;

-- Dumping data for table ooget.saved_job: ~2 rows (approximately)
DELETE FROM `saved_job`;
/*!40000 ALTER TABLE `saved_job` DISABLE KEYS */;
INSERT INTO `saved_job` (`id`, `job_id`, `jobseeker_id`) VALUES
	(13, 1, 9),
	(1, 1, 10),
	(10, 6, 10);
/*!40000 ALTER TABLE `saved_job` ENABLE KEYS */;

-- Dumping structure for table ooget.specializations
DROP TABLE IF EXISTS `specializations`;
CREATE TABLE IF NOT EXISTS `specializations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=latin1;

-- Dumping data for table ooget.specializations: ~65 rows (approximately)
DELETE FROM `specializations`;
/*!40000 ALTER TABLE `specializations` DISABLE KEYS */;
INSERT INTO `specializations` (`id`, `name`, `type`) VALUES
	(1, 'Actuarial Science/Statistics', 1),
	(2, 'Advertising/Media Planning', 1),
	(3, 'Agriculture/Forestry/Fisheries', 1),
	(4, 'Architecture/Interior Design', 1),
	(5, 'Arts/Creative/Graphics Design', 1),
	(6, 'Aviation/Aircraft Maintenance', 1),
	(7, 'Banking/Financial Services', 1),
	(8, 'Biotechnology', 1),
	(9, 'Chemistry', 1),
	(10, 'Clerical/Administrative Support', 1),
	(11, 'Corporate Strategy/Top Management', 1),
	(12, 'Customer Service', 3),
	(13, 'Education', 1),
	(14, 'Engineering - Chemical', 1),
	(15, 'Engineering - Civil/Construction/Structural', 1),
	(16, 'Engineering - Electrical', 1),
	(17, 'Engineering - Electronics/Communication', 1),
	(18, 'Engineering - Environmental/Health/Safety', 1),
	(19, 'Engineering - Industrial', 1),
	(20, 'Engineering - Mechanical/Automotive', 1),
	(21, 'Engineering - Oil/Gas', 1),
	(22, 'Entertainment/Performing Arts', 1),
	(23, 'Finance - Audit/Taxation', 1),
	(24, 'Finance - Corporate Finance/Investment/Merchant Banking', 1),
	(25, 'Finance - General/Cost Accounting', 1),
	(26, 'Food Technology/Nutritionist', 1),
	(27, 'Food/Beverage/Restaurant Service', 3),
	(28, 'General Worker (Housekeeper, Driver, Dispatch, Messenger, etc', 1),
	(29, 'Geology/Geophysics', 1),
	(30, 'Healthcare - Doctor/Diagnosis', 1),
	(31, 'Healthcare - Nurse/Medical Support & Assistant', 1),
	(32, 'Healthcare - Pharmacy', 1),
	(33, 'Hotel Management/Tourism Services', 1),
	(34, 'Human Resources', 1),
	(35, 'IT/Computer - Hardware', 1),
	(36, 'IT/Computer - Network/System/Database Admin', 1),
	(37, 'IT/Computer - Software', 1),
	(38, 'Journalist/Editor', 1),
	(39, 'Law/Legal Services', 1),
	(40, 'Logistics/Supply Chain', 1),
	(41, 'Maintenance/Repair (Facilities & Machinery', 1),
	(42, 'Manufacturing/Productions Operations', 1),
	(43, 'Marketing/Business Development', 1),
	(44, 'Merchandising', 1),
	(45, 'Personal Care/Beauty/Fitness Service', 1),
	(46, 'Process Design & Control/Instrumentation', 1),
	(47, 'Property/Real Estate', 1),
	(48, 'Public Relations/Communications', 1),
	(49, 'Publishing/Printing', 1),
	(50, 'Purchasing/Inventory/Material & Warehouse Management', 1),
	(51, 'Quality Control/Assurance', 1),
	(52, 'Quantity Surveying', 1),
	(53, 'Sales - Corporate', 1),
	(54, 'Sales - Engineering/Technical/IT', 1),
	(55, 'Sales - Finance Services (Insurance, Unit Trust, etc', 1),
	(56, 'Sales - Retail/General', 1),
	(57, 'Sales - Telesales/Telemarketing', 1),
	(58, 'Science & Technology/Laboratory', 1),
	(59, 'Secretarial/Executive & Personal Assistant', 1),
	(60, 'Security/Armed Forces/Protective Services', 1),
	(61, 'Social & Counselling Service', 1),
	(62, 'Technical & Helpdesk Support', 1),
	(63, 'Training & Development', 1),
	(64, 'Admin', 2),
	(65, 'Beautician Wellness', 2),
	(66, 'Drivers/Delivery', 2),
	(67, 'Event', 2),
	(68, 'Packer/Mover', 2),
	(69, 'Retails', 2);
/*!40000 ALTER TABLE `specializations` ENABLE KEYS */;

-- Dumping structure for table ooget.time_sheet
DROP TABLE IF EXISTS `time_sheet`;
CREATE TABLE IF NOT EXISTS `time_sheet` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_id` int(11) DEFAULT NULL,
  `job_seeker_id` int(11) DEFAULT NULL,
  `contracts_id` int(11) DEFAULT NULL,
  `clock_in` timestamp NULL DEFAULT NULL,
  `clock_out` timestamp NULL DEFAULT NULL,
  `over_time_min` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `day` varchar(10) DEFAULT NULL,
  `holiday` char(1) DEFAULT 'N',
  `clock_verified_in` datetime DEFAULT NULL,
  `clock_verified_out` datetime DEFAULT NULL,
  `clock_verified_by` int(11) DEFAULT NULL,
  `ot_1_salary` int(11) DEFAULT NULL,
  `ot_2_salary` int(11) DEFAULT NULL,
  `salary` int(11) DEFAULT NULL,
  `salary_total` int(11) DEFAULT NULL,
  `sheet_verified` int(11) DEFAULT NULL,
  `sheet_verified_by` int(11) DEFAULT NULL,
  `invoice_no` varchar(20) DEFAULT NULL,
  `contract_status` int(11) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

-- Dumping data for table ooget.time_sheet: ~4 rows (approximately)
DELETE FROM `time_sheet`;
/*!40000 ALTER TABLE `time_sheet` DISABLE KEYS */;
INSERT INTO `time_sheet` (`id`, `job_id`, `job_seeker_id`, `contracts_id`, `clock_in`, `clock_out`, `over_time_min`, `date`, `day`, `holiday`, `clock_verified_in`, `clock_verified_out`, `clock_verified_by`, `ot_1_salary`, `ot_2_salary`, `salary`, `salary_total`, `sheet_verified`, `sheet_verified_by`, `invoice_no`, `contract_status`) VALUES
	(1, 1, 9, 1, NULL, NULL, NULL, '2019-05-28', 'Tue', 'N', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
	(2, 1, 9, 1, '2019-05-29 16:34:18', '2019-05-29 16:53:25', NULL, '2019-05-29', 'Wed', 'N', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
	(3, 1, 9, 1, NULL, NULL, NULL, '2019-05-30', 'Thu', 'N', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1),
	(4, 1, 9, 1, NULL, NULL, NULL, '2019-05-31', 'Fri', 'P', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1);
/*!40000 ALTER TABLE `time_sheet` ENABLE KEYS */;

-- Dumping structure for table ooget.UserRole
DROP TABLE IF EXISTS `UserRole`;
CREATE TABLE IF NOT EXISTS `UserRole` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `access` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table ooget.UserRole: ~0 rows (approximately)
DELETE FROM `UserRole`;
/*!40000 ALTER TABLE `UserRole` DISABLE KEYS */;
/*!40000 ALTER TABLE `UserRole` ENABLE KEYS */;

-- Dumping structure for table ooget.users
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'admin',
  `createdby` varchar(50) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  `dob` varchar(20) DEFAULT NULL,
  `imgpath` varchar(300) DEFAULT NULL,
  `lastlogin` timestamp NULL DEFAULT NULL,
  `timezone` varchar(20) NOT NULL DEFAULT '+08:00',
  `theme` varchar(20) NOT NULL DEFAULT '1',
  `role` varchar(20) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `companyid` int(11) DEFAULT NULL,
  `mobile` varchar(15) DEFAULT NULL,
  `address1` varchar(150) DEFAULT NULL,
  `address2` varchar(150) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `lat` varchar(20) DEFAULT NULL,
  `log` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=latin1;

-- Dumping data for table ooget.users: ~14 rows (approximately)
DELETE FROM `users`;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `firstname`, `lastname`, `email`, `password`, `type`, `createdby`, `status`, `dob`, `imgpath`, `lastlogin`, `timezone`, `theme`, `role`, `phone`, `companyid`, `mobile`, `address1`, `address2`, `city`, `state`, `country`, `lat`, `log`) VALUES
	(4, 'FNAME', 'LNAME', 'doss@sqindia.net', 'test', 'admin', '100', 1, '12-07-1989', 'media/profile/user/4.png', '2019-05-28 13:29:32', '+08:00', '1', '1', '9994596906', NULL, '99944', 'AD1', 'AD2', 'CITY', 'STATE', 'COUNTRY', 'LAT', 'LOG'),
	(7, NULL, NULL, 'sdfsdf', NULL, 'sdsdf', '4', 1, NULL, NULL, NULL, '+08:00', '1', 'sdfsf', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(8, 'cuser', NULL, 'cuser@sqindia.com', NULL, 'admin', '4', 1, NULL, NULL, NULL, '+08:00', '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(10, 'cuser', NULL, 'cuser@sqindia.com1', NULL, 'admin', '4', 1, NULL, NULL, NULL, '+08:00', '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(11, 'cuser', NULL, 'cuser1@sqindia.in', NULL, 'admin', '4', 1, NULL, NULL, NULL, '+08:00', '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(12, 'cuser', NULL, 'cuser1a@sqindia.net', NULL, 'admin', '4', 1, NULL, NULL, NULL, '+08:00', '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(13, 'cuser', NULL, 'cuser1a@sqindia1.net', NULL, 'admin', '4', 1, NULL, NULL, NULL, '+08:00', '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(14, 'cuser', NULL, 'doss123@sqindia.net', 'test', 'employer', '4', 1, NULL, NULL, '2019-05-28 16:33:28', '+08:00', '1', NULL, NULL, 13, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(16, 'oogetadmincheck@sq.net', NULL, 'oogetadmincheck@sq.net', NULL, 'admin', '4', 1, NULL, NULL, NULL, '+08:00', '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(17, 'oogetadmincheck@sq.net', NULL, 'oogetadmincheck1@sq.net', 'test', 'admin', '4', 1, NULL, NULL, NULL, '+08:00', '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(18, 'oogetadmincheck@sq.net', NULL, 'oogetadmincheck12@sq.net', 'test', 'employer', '15', 1, NULL, NULL, '2019-05-10 13:31:20', '+08:00', '1', NULL, NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(19, 'oogetadmincheck@sq.net', NULL, 'oogetadmincheck123@sq.net', 'test', 'employer', '15', 1, NULL, NULL, '2019-05-11 12:08:54', '+08:00', '1', NULL, NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(20, 'doss', NULL, 'dosstest123@gmail.com', 'testman', 'employer', NULL, 1, NULL, NULL, NULL, '+08:00', '1', NULL, NULL, 11, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(21, 'dfgg', NULL, 'dosss@sqindia.net', 'ASfg@fd565', 'employer', '4', 1, NULL, NULL, NULL, '+08:00', '1', NULL, NULL, 12, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(22, 'emp1', NULL, 'emp1@og.net', 'Emp1@12345', 'employer', NULL, 1, NULL, 'media/profile/user/22.png', '2019-05-28 15:31:41', '+08:00', '1', NULL, NULL, 13, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
	(23, 'dfsdf', NULL, 'fdf@fsdf.fd', 'Emp1@12345', 'employer', '22', 1, NULL, NULL, NULL, '+08:00', '1', NULL, NULL, 13, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

-- Dumping structure for table ooget.WorkingEnvironments
DROP TABLE IF EXISTS `WorkingEnvironments`;
CREATE TABLE IF NOT EXISTS `WorkingEnvironments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

-- Dumping data for table ooget.WorkingEnvironments: ~8 rows (approximately)
DELETE FROM `WorkingEnvironments`;
/*!40000 ALTER TABLE `WorkingEnvironments` DISABLE KEYS */;
INSERT INTO `WorkingEnvironments` (`id`, `name`) VALUES
	(1, 'Office'),
	(2, 'Factory'),
	(3, 'Restaurant'),
	(4, 'Hotel'),
	(5, 'Warehouse'),
	(6, 'Supermarket'),
	(7, 'Retail'),
	(8, 'Aircon'),
	(9, 'Non Aircon');
/*!40000 ALTER TABLE `WorkingEnvironments` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
