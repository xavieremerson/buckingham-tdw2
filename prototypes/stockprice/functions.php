<?
 

////
// Function to get Company Name given a ticker. User for data entry by tickers in Lists
function get_company_name($symbol) {

	//$symbol = $_POST['symbol']; 
	
	//$symbol = "MSFT,AA,T";

  $quotes = new Quotes(); 
	
	$symbols = explode(",",$symbol) ; 
	
	for ($n=0; $n<count($symbols); $n++)
    {
        $quotes->mSetSymbol(strtoupper($symbols[$n])) ; 
        $quotes->mLoadYahoo() ;
				return $quotes->_strCompany;
		}
		
}


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
   
   
    function Quotes()
    {
    }


    function mSetSymbol($symbol) 
    {
        $this->strSymbol = $symbol ; 
    }

    function mLoadYahoo () 
    {
	    /* if multiple symbols, replace the space with a + */
	    #$allsymbols=ereg_replace( " ", "+", $this->strSymbol );
        $allsymbols = $this->strSymbol ; 
	    $YAHOO_URL = ("http://quote.yahoo.com/d?f=snl1d1t1c1p2va2bapomwerr1dyj1&s=$allsymbols");
	    $file = fopen("$YAHOO_URL","r"); 
	
	    while ($data = fgetcsv($file,4096, ",")) 
		{
            $this->_strSymbol = $data[0] ;
            $this->_strCompany = $data[1] ; 
            $this->_strLastPrice = $data[2] ; 
            $this->_strTradeDate = $data[3] ; 
            $this->_strTradeTime = $data[4] ;             
            $this->_strChange = $data[5] ;
            $this->_strChangePercent = $data[6] ;  
            $this->_strVolume = $data[7] ; 
	    }
        
		//echo "<pre>"; 
		//print_r($hash) ;  

	    return $hash;
    }
}

?>