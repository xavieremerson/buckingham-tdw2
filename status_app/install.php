<?
include "connect.php";
$installmessages="CREATE TABLE ch_messages (
  ID bigint(21) NOT NULL auto_increment,
  poster varchar(255) NOT NULL default '',
  message mediumtext NOT NULL,
  registered int(11) NOT NULL default '0',
  time bigint(21) default NULL,
  PRIMARY KEY  (ID)
)";
mysql_query($installmessages) or die(mysql_error());
$installchatters="CREATE TABLE ch_chatters (
  ID bigint(20) NOT NULL auto_increment,
  chatter varchar(255) NOT NULL default '',
  password varchar(255) NOT NULL default '',
  email varchar(255) NOT NULL default '',
  PRIMARY KEY  (ID)
)";
mysql_query($installchatters) or die("Could not install chatters table");
$installadmins="CREATE TABLE ch_admins (
  ID int(11) NOT NULL auto_increment,
  adminname varchar(255) NOT NULL default '',
  password varchar(255) NOT NULL default '',
  PRIMARY KEY  (ID)
)";
mysql_query($installadmins) or die("Could not install admin table");
$installonline="CREATE TABLE ch_online (
  ID bigint(21) NOT NULL auto_increment,
  sessionname varchar(255) NOT NULL default '',
  time int(11) NOT NULL default '0',
  PRIMARY KEY  (ID)
)";
mysql_query($installonline) or die("Could not install whose online");

?>