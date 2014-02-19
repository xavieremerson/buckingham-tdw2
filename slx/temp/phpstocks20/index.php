<?php 
/**
* +----------------------------------------------------------------------+
* | $Workfile: $                                                         | 
* +----------------------------------------------------------------------+
* | $Revision: $ (PHP 4)                                                 |
* +----------------------------------------------------------------------+
* | Copyright (c) 2002-2003 Linuxpowered, Inc                             |
* +----------------------------------------------------------------------+
* | Author: Vidyut Luther <vid@linuxpowered.com>                         |
* +----------------------------------------------------------------------+
* 
* $Header: $
**/
	require_once("config.inc"); 


	$smarty->display(TEMPLATE_PATH."/header.html");
	$smarty->display(TEMPLATE_PATH."/main.html") ; 
	$smarty->display(TEMPLATE_PATH."/footer.html") ; 
	

?>
