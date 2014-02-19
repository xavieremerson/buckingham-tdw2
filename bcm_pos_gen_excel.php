<link rel="stylesheet" type="text/css" href="includes/styles.css">


<?
include('includes/functions.php');
include('includes/dbconnect.php');
include('includes/global.php');


////
// Escape single quotes
function esq ($str) {
	return str_replace("'","\\'",$str);
}

// fidelity Files Location (file system access)
//$filelocation_fidelity = "\\\\bucknav\\c$\\CycloneActivator\\data\\sft7212\\binaryin\\fidelity\\"; //"M:\\CycloneActivator\\data\\sft7212\\binaryin\\fidelity\\";
$filelocation_fidelity = "\\\\bucktechsrv\\c$\\CycloneActivator\\data\\sft7212\\binaryin\\fidelity\\"; //"M:\\CycloneActivator\\data\\sft7212\\binaryin\\fidelity\\";


//fidelity Files Storage on TDW Server (?.?.?.?) mapped as ? Drive 
$filelocation_tdw_server = "E:\\_fidelity_data\\"; /* Trailing slash must exist */;


//function (this page debug)
function zdebug ($n,$v) {
	$x = 1;
	if ($x==1) {
		echo "<font color='green'>".$n . " = [".$v."]</font><br>"; 
	}
}

//// Function to write data to file
function zwrite_to_file($location, $file, $data) {
	$filename = $location.$file;
	$fp = fopen ($filename, "w");  
	fwrite ($fp,$data);        
	fclose ($fp);   
}

/*print_r($_GET);
exit;
*/
$time=getmicrotime(); 
echo "<br><br>";
xdebug("&nbsp;&nbsp;&nbsp;Process initiated at ",date('m/d/Y H:i:s a'));


//Parse Symbols
$symbols = strtoupper(str_replace(" ","",$sel_symbol));
if (substr($symbols,strlen($symbols)-1,1) == ",") {$symbols = substr($symbols,0,strlen($symbols)-1);}
echo "<br>&nbsp;&nbsp;Symbols entered: <font color = blue>".str_replace(",",", ",$symbols)."</font><br><br>";
$str_symbol = " ('".str_replace(",","','",$symbols)."') "; 

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//get date with which to work
$date_to_process = $sel_month; //'2009-09-30';
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

$file_name = "FIDPOS".str_replace("-","",$date_to_process).".TXT";
//xdebug("file_name",$file_name);
			
//copy bcm pos file
shell_exec("copy " . $filelocation_fidelity . $file_name . " " . $filelocation_tdw_server ."\\"); 
echo "&nbsp;&nbsp;File <font color = blue>". $file_name . "</font> copied to TDW Server<br><br>";

//import bcm pos file
  //Process FILE
	if (file_exists($filelocation_tdw_server.$file_name)) {
  //*********************************************************************************************
  //*********************************************************************************************
	
	
	//First remove data from table
	$qry = "truncate table bcm_pos_13g";
	$result = mysql_query($qry) or die(mysql_error());
	
	
	
  $row = 0;
	$traderow = 0;
	$fp = fopen ($filelocation_tdw_server.$file_name, "r");  
	while (!feof ($fp)) { 
		$content = fgets( $fp, 8192 ); 
		
		if ($row >= 0 and esq(substr($content,0,9))!= "") {
		
/*							$trad_buy_sell = substr($content,247,1);
							$trad_cusip = substr($content,248,9);
							$trad_strike_price = substr($content,610,9).".".substr($content,619,9);
*/			
							$str_sql = "INSERT INTO bcm_pos_13g ( 
														auto_id,
														Acctnum,
														Accttype,
														Cusip,
														Symbol,
														SecType,
														Secdesc1,
														Secdesc2,
														Secdesc3,
														Secdesc4,
														Secdesc5,
														Secdesc6,
														TDQty,
														SDQty,
														Price,
														SSN,
														Firstname,
														Middlename,
														Lastname,
														SSN1,
														Brokerage,
														process_datetime 
													) VALUES (
													NULL,
														'".esq(substr($content,0,9))."',
														'".esq(substr($content,9,1))."',
														'".esq(substr($content,10,9))."',
														'".esq(substr($content,19,9))."',
														'".esq(substr($content,28,1))."',
														'".esq(substr($content,29,20))."',
														'".esq(substr($content,49,20))."',
														'".esq(substr($content,69,20))."',
														'".esq(substr($content,89,20))."',
														'".esq(substr($content,109,20))."',
														'".esq(substr($content,129,20))."', 
														'".substr($content,149,11).".".substr($content,160,5)."',
														'".substr($content,165,11).".".substr($content,176,5)."',
														'".substr($content,181,9).".".substr($content,190,9)."',
														'".esq(substr($content,199,9))."',
														'".esq(substr($content,208,12))."',
														'".esq(substr($content,220,10))."',
														'".esq(substr($content,230,25))."',
														'".esq(substr($content,255,9))."',
														'".esq(substr($content,264,3))."',
														now() 
													)";
												 
								//echo $str_sql;
								$result = mysql_query($str_sql) or 
													die(
															  zwrite_to_file("D:/tdw/tdw/data/bcm_pos/", date('Ymd')."-err_sql.txt", $str_sql)
														 );
			
		}
	$row = $row + 1;		
	} 
	fclose ($fp); 
	//*********************************************************************************************
	//*********************************************************************************************
	}

echo "&nbsp;&nbsp;Parsed/processed Fidelity File data.<br><br>";

//remove extra data
$qry = "delete from bcm_pos_13g where Symbol not in ".$str_symbol;
//echo $qry;
$result = mysql_query($qry) or die (tdw_mysql_error($qry));

//create excel file
$xlfilename = "FIDPOS_".$date_to_process.".xls";
//$xlfilename = "test.xls";
$fp = fopen("D:/tdw/tdw/data/xls/".$xlfilename, "w");

//$string = "\"Date\",\"Client Code\",\"Client Name\",\"Amount\",\"Type\",\"Reps\",\"Rep#\",\"Comments\",\"Entered By"."\"".chr(13); 

$str = '<html xmlns="http://www.w3.org/1999/xhtml">
				<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head>
				<body>';

//50 60 100 200 10 100 100 10 100 100 10 100 100 100 100				
$str .= '<table border="1" cellspacing="0" cellpadding="0">
				<tr>
					<td width="75"><strong>Acctnum</strong></td>
					<td width="80"><strong>Cusip</strong></td>
					<td width="70"><strong>Symbol</strong></td>
					<td width="220"><strong>Secdesc1</strong></td>
					<td width="150"><strong>Secdesc2</strong></td>
					<td width="150"><strong>Secdesc3</strong></td>
					<td width="80"><strong>Secdesc4</strong></td>
					<td width="80"><strong>Secdesc5</strong></td>
					<td width="80"><strong>Secdesc6</strong></td>
					<td width="70"><strong>TDQty</strong></td>
					<td width="70"><strong>SDQty</strong></td>
					<td width="70"><strong>Price</strong></td>
					<td width="100"><strong>SSN</strong></td>
					<td width="130"><strong>Firstname</strong></td>
					<td width="130"><strong>Middlename</strong></td>
					<td width="130"><strong>Lastname</strong></td>
					<td width="75"><strong>Brokerage</strong></td>
				</tr>';

$qry = "select * from bcm_pos_13g order by Symbol";
$result = mysql_query($qry) or die (tdw_mysql_error($qry));
while ( $row = mysql_fetch_array($result) ) 
{
	$str .= '<tr>'.
						'<td>&nbsp;'.$row["Acctnum"].'</td>'.
						'<td>&nbsp;'.$row["Cusip"].'</td>'.
						'<td>&nbsp;'.$row["Symbol"].'</td>'.
						'<td>&nbsp;'.$row["Secdesc1"].'</td>'.
						'<td>&nbsp;'.$row["Secdesc2"].'</td>'.
						'<td>&nbsp;'.$row["Secdesc3"].'</td>'.
						'<td>&nbsp;'.$row["Secdesc4"].'</td>'.
						'<td>&nbsp;'.$row["Secdesc5"].'</td>'.
						'<td>&nbsp;'.$row["Secdesc6"].'</td>'.
						'<td>&nbsp;'.round($row["TDQty"],3).'</td>'.
						'<td>&nbsp;'.round($row["SDQty"],3).'</td>'.
						'<td>&nbsp;'.round($row["Price"],3).'</td>'.
						'<td>&nbsp;'.$row["SSN"].'</td>'.
						'<td>&nbsp;'.$row["Firstname"].'</td>'.
						'<td>&nbsp;'.$row["Middlename"].'</td>'.
						'<td>&nbsp;'.$row["Lastname"].'</td>'.
						'<td>&nbsp;'.$row["Brokerage"].'</td>'.
	         '</tr>';
}

fputs ($fp, $str);

$str = '</table>';
fputs ($fp, $str);

$str = '</body></html>';
fputs ($fp, $str);

fclose($fp);

//Header("Location: http://192.168.20.63/tdw/data/xls/test.xls");

//show page load time
	echo "&nbsp;&nbsp;Report generated in ". sprintf("%01.2f",((getmicrotime()-$time)/1000))." s.\n<br><br>"; 						

?>
&nbsp;&nbsp;<a href="http://192.168.20.63/tdw/fileserve_xls.php?l=data/xls/&f=<?=$xlfilename?>" target="_blank">Click here to download the generated report (File Format: Excel)</a><br /><br />
<?
xdebug("&nbsp;&nbsp;&nbsp;Process ". $rnd_process_id . " completed at ",date('m/d/Y H:i:s a'));
exit;
?>