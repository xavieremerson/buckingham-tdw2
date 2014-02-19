<?php
session_start(); 
$DOCROOT = $_SERVER['DOCUMENT_ROOT']; 
define(TEMPLATE_PATH,"$DOCROOT/demo/templates/");
define(INCLUDE_PATH,"$DOCROOT/../includes/"); 
$today = date("l, F d Y H:m:s"); 

require_once("Smarty.class.php");

$smarty = new Smarty;
$smarty->assign("today","$today") ; 




?>
