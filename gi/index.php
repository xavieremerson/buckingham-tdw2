<?

function getmicrotime(){ 
    list($usec, $sec) = explode(" ",microtime()); 
    return ((float)$usec + (float)$sec*1000); 
}

//initiate page load time routine
$time=getmicrotime(); 

?>
<body onload="document.temp.symbol.focus();">
<form name="temp" method="post" action="">
<input type="text" name="symbol" size="20" />
<input type="submit" name="Get Industry" value="Get Industry"/>
</form>
</body>
<?



  if (!$symbol) {
	  $symbol = 'IBM';
	} 
	$fd = fopen ("http://finance.yahoo.com/q/in?s=".strtoupper($symbol), "r");
	$lines = array();
	$buffer = "";
	$i=0;
	while ($i < 1)  //!feof ($fd)
		{
			 $buffer = fgets($fd, 4096);
			 $lines[] = $buffer;
			 //echo $buffer;
			 $i = $i+1;
		}
fclose ($fd);
		
//Get the content of the file into a string
$str_whole = "";
foreach ($lines as $key=>$value)
	{
	 $str_whole .=$value;
	}

//strip everything before the first <title>
$str_whole_a = substr($str_whole, strpos($str_whole, "<title>", 0)+7, 400);

//strip everything after the first </
$str_whole_b = substr($str_whole_a, 0, strpos($str_whole_a, "</title>", 0));

//strip everything before the first Industry: 
$str_whole_c = substr($str_whole_b, strpos($str_whole_b, "Industry:", 0)+10, 400);

//get company name
$str_cname_1 = substr($str_whole_c, strpos($str_whole_c, "for ", 0)+4, 400);
$str_cname_2 = substr($str_cname_1, 0, strpos($str_cname_1, " - ", 0));

if ( $str_cname_2 == ' Symbol') {
 $str_cname_2 = "Invalid Symbol Entered!";
}
//strip everything after the first  for 
$str_whole_d = substr($str_whole_c, 0, strpos($str_whole_c, " for", 0));

echo "Symbol: <font color='blue'>".strtoupper($symbol)."</font><br>Company Name: <font color='blue'>".$str_cname_2."</font><br>Industry: <font color='blue'>".$str_whole_d."</font><br><br>";


echo " Time taken to perform retrieval of Industry ". sprintf("%01.7f",((getmicrotime()-$time)/1000))." s."; 
?>