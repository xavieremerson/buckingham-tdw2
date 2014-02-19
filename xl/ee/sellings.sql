# MySQL-Front Dump 2.4
#
# Host: localhost   Database: test
#--------------------------------------------------------
# Server version 3.23.55-nt

USE test;


#
# Table structure for table 'tsellings'
#

CREATE TABLE `tsellings` (
  `Region` varchar(20) NOT NULL default '0',
  `City` varchar(20) NOT NULL default '',
  `Seller` varchar(30) NOT NULL default '',
  `Product` varchar(20) NOT NULL default '',
  `Units` tinyint(2) unsigned NOT NULL default '0',
  `TotalPrice` int(6) unsigned NOT NULL default '0'
) TYPE=MyISAM COMMENT='Test Data for EasyExcel';



#
# Dumping data for table 'tsellings'
#
INSERT INTO tsellings VALUES("MADRID", "MADRID", "JUAN", "BOOK", "2", "45");
INSERT INTO tsellings VALUES("MADRID", "MOSTOLES", "RAFA", "COMPUTER", "1", "900");
INSERT INTO tsellings VALUES("MADRID", "MADRID", "RAFA", "MOUSE", "10", "100");
INSERT INTO tsellings VALUES("BARCELONA", "BARCELONA", "JUAN", "BOOK", "10", "180");
INSERT INTO tsellings VALUES("CATALUNYA", "TARRAGONA", "JORDI", "BOOK", "23", "234");
INSERT INTO tsellings VALUES("ASTURIAS", "GIJON", "RAFA", "COMPUTER", "3", "1567");
INSERT INTO tsellings VALUES("ASTURIAS", "OVIEDO", "JUAN", "MOUSE", "10", "99");
INSERT INTO tsellings VALUES("ASTURIAS", "GIJON", "NICANOR", "COMPUTER", "2", "1758");
INSERT INTO tsellings VALUES("ANDALUCIA", "SEVILLA", "ANTONIO", "COMPUTER", "1", "890");
INSERT INTO tsellings VALUES("ANDALUCIA", "SEVILLA", "ANTONIO", "COMPUTER", "3", "600");
