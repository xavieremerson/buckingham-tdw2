<?
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set("display_errors", 1);

/**
 * Class to fetch stock data from Yahoo! Finance
 *
 */
class YahooStock {
    /**
     * Array of stock code
     */
    private $stocks = array();
    /**
     * Parameters string to be fetched   
     */
    private $format;
    /**
     * Populate stock array with stock code
     *
     * @param string $stock Stock code of company    
     * @return void
     */
    public function addStock($stock)
    {
        $this->stocks[] = $stock;
    }
    /**
     * Populate parameters/format to be fetched
     *
     * @param string $param Parameters/Format to be fetched
     * @return void
     */
    public function addFormat($format)
    {
        $this->format = $format;
    }
    /**
     * Get Stock Data
     *
     * @return array
     */
    public function getQuotes()
    {        
        $result = array();      
        $format = $this->format;
        foreach ($this->stocks as $stock)
        {            
            /**
             * fetch data from Yahoo!
             * s = stock code
             * f = format
             * e = filetype
             */
            $s = file_get_contents("http://finance.yahoo.com/d/quotes.csv?s=$stock&f=$format&e=.csv");
            /** 
             * convert the comma separated data into array
             */
            $data = explode( ',', $s);
            /** 
             * populate result array with stock code as key
             */
            $result[$stock] = $data;
        }
        return $result;
    }
} 

function get_company_name_yhoo($symbol) {
//**************************************************************************
	$objYahooStock = new YahooStock; 
	/** 
			Add format/parameters to be fetched 
			s = Symbol, n = Name, l1 = Last Trade (Price Only), d1 = Last Trade Date, t1 = Last Trade Time, c = Change and Percent Change, v = Volume 
	 */ 
	$objYahooStock->addFormat("n"); //snl1d1t1cv 
	$objYahooStock->addStock($symbol); 
	/** 
	 * Printing out the data 
	 */ 
	foreach( $objYahooStock->getQuotes() as $code => $stock) 
	{ 
			return strtoupper(trim(str_replace('"','',$stock[0])));
	} 
//**************************************************************************
}

echo get_company_name_yhoo("MNK");
?>