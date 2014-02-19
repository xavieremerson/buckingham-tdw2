<?php
Class yahoo
{
function get_stock_quote($symbol)
{
$url = sprintf("http://finance.yahoo.com/d/quotes.csv?s=%s&f=snl1d1t1c1ohgv" ,$symbol);
//$YAHOO_URL = ("http://quote.yahoo.com/d?f=snl1d1t1c1p2va2bapomwerr1dyj1&s=$allsymbols");
$fp = fopen($url, "r");
if(!fp)
{
echo "error : cannot recieve stock quote information";
}
else
{
$array = fgetcsv($fp , 4096 , ', ');
fclose($fp);
$this->symbol = $array[0];
$this->name = $array[1];
$this->last = $array[2];
$this->date = $array[3];
$this->time = $array[4];
$this->change = $array[5];
$this->open = $array[6];
$this->high = $array[7];
$this->low = $array[8];
$this->volume = $array[9];
}
}
}
$quote = new yahoo;
$quote->get_stock_quote("MSFT");
echo ("<B>$quote->symbol</B><br>");
echo ("<B>$quote->name</B><br>");
echo ("<B>$quote->time</B><br>");
echo ("<B>$quote->date</B><br>");
echo ("<B>$quote->last</B><br>");
echo ("<B>$quote->change</B><br>");
echo ("<B>$quote->high</B><br>");
echo ("<B>$quote->low</B><br>");

?>	