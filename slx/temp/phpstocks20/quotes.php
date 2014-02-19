<?
/**
* +----------------------------------------------------------------------+
* | $Workfile: $                                                         | 
* +----------------------------------------------------------------------+
* | $Revision: $ (PHP 4)                                                 |
* +----------------------------------------------------------------------+
* | Copyright (c) 2002-2003 Billing Concepts                             |
* +----------------------------------------------------------------------+
* | Author: Vidyut Luther <vid@linuxpowered.com>                         |
* +----------------------------------------------------------------------+
* 
* $Header: $
**/

class Quotes {

    var $_strSymbol  ; 
    var $_strCompany;
    var $_strLastPrice ; 
    var $_strTradeDate ; 
    var $_strTradeTime ; 
    var $_strChange ; 
    var $_strPercentChange ; 
    var $_strVolume ; 
    var $_strBid ; 
    var $_strAsk ; 
    var $_strPrevClose ; 
    var $_strOpen ; 
    var $_strYield ; 
    var $_strDivShare  ; 
    var $_strMarketCap ; 
   
   
 /**
 * Quotes:
 * 
 * Description: The Constructor  
 * 
 * 
 * Return value: 
 **/
   
    function Quotes()
    {
    }


    function mSetSymbol($symbol) 
    {
        $this->strSymbol = $symbol ; 
    }


/**
 * mLoadYahoo :
 * @ : 
 * 
 * Description: Gets the values for the stock symbol from Yahoo  
 * 
 * 
 * Return value: 
 **/
    function mLoadYahoo () 
    {
	    /* if multiple symbols, replace the space with a + */
	    #$allsymbols=ereg_replace( " ", "+", $this->strSymbol );
        $allsymbols = $this->strSymbol ; 
	    $YAHOO_URL = ("http://quote.yahoo.com/d?f=snl1d1t1c1p2va2bapomwerr1dyj1&s=$allsymbols");
	    $file = fopen("$YAHOO_URL","r"); 
	
	    while ($data = fgetcsv($file,4096, ",")) {
		    /* Go and build the hash, which will be $hash[$i][name] or $hash[0][symbol] to
		    /* get the symbol of the ticker..this returns an associative array like Ghassan requested
		    /* could also be $hash[$data[0]], which would let you access via $hash[SYMBOL]..
		    /* but i can't find a clean way to do this.. maybe someone else can.. will surely
		    /* make easier to read code.. 				*/
/*		    $hash[$data[0]] = array (
                symbol => $data[0],  
			  	company => $data[1],
			  	lastprice => $data[2],
        		tradedate => $data[3], 
        		tradetime => $data[4], 
        		change => $data[5], 
        		changepercent => $data[6],
        		volume => $data[7],
	        	avgvolume => $data[8],
	        	bid => $data[9],
	        	ask => $data[10], 
	        	yesterdaysclose => $data[11],
	        	open => $data[12],
 	        	dayrange => $data[13],
        		yearrange => $data[14],   
        		earnpershare => $data[15],
        		pe => $data[16], 	
   			 	divdate => $data[17],  
   			 	yield => $data[18], 
        		divshr => $data[19], 
        		marketcap => $data[20]
			  	
			  	
	    	);	 */


            $this->_strSymbol = $data[0] ;
            $this->_strCompany = $data[1] ; 
            $this->_strLastPrice = $data[2] ; 
            $this->_strTradeDate = $data[3] ; 
            $this->_strTradeTime = $data[4] ;             
            $this->_strChange = $data[5] ;
            $this->_strChangePercent = $data[6] ;  
            $this->_strVolume = $data[7] ; 
	    }
        
   /*     echo "<pre>"; 
        
        print_r($hash) ;  

    */
	    return $hash;
    }

}
?>
