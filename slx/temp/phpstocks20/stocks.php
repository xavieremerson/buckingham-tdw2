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

 	require_once("/home/vluther/websites/phpcult/htdocs/demo/config.inc"); 
   
	$smarty->display(TEMPLATE_PATH."/header.html");
	$smarty->display(TEMPLATE_PATH."/main.html") ; 

    $smarty->display(TEMPLATE_PATH."/basic.html") ; 

	$symbol = $_POST['symbol']; 

    require_once("quotes.php") ;
    
    $quotes = new Quotes();     
    
    /* If there is more than one symbol, we need to call the same function n number of times. This means we will make a hit to Yahoo n number of times.
    Therefore, we must make sure all symbols are unique */

    $symbols = explode(" ",$symbol) ; 
    
    for ($n=0; $n<count($symbols); $n++)
    {
        $quotes->mSetSymbol(strtoupper($symbols[$n])) ; 
        $quotes->mLoadYahoo() ;
        $smarty->assign("symbol",strtoupper($symbols[$n])) ; 

        $smarty->assign("company",$quotes->_strCompany) ; 
        $smarty->assign("lastprice",$quotes->_strLastPrice) ; 
        $smarty->assign("tradetime",$quotes->_strTradeTime) ; 
        $smarty->assign("tradedate",$quotes->_strTradeDate) ; 
        $smarty->assign("change",$quotes->_strChange) ; 
        $smarty->assign("volume",$quotes->_strVolume) ; 
        $smarty->assign("stocks",$hash) ; 
	    $smarty->assign("domain",$domain);

        $smarty->display(TEMPLATE_PATH."/line.html") ; 
    }
    

    	$smarty->display(TEMPLATE_PATH."/footer.html") ; 
	


?>
