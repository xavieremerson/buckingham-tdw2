<?
include('../includes/dbconnect.php');
include('../includes/global.php'); 
include('../includes/functions.php'); 

//function get user email from Initials
function get_email_for_initials ($Initials) {
	$qry_useremail = "SELECT 
									Email 
									FROM users 
								WHERE Initials = '".$Initials."'";   
	$result_useremail = mysql_query($qry_useremail) or die(tdw_mysql_error($qry_useremail));
	while($row_useremail = mysql_fetch_array($result_useremail)) {
		$useremail = $row_useremail["Email"];
	}
	return $useremail;
}

$acct_str = "EMAIL,COUNTofACCOUNTS".chr(13) . chr(10);
			
$query_rr1 = "SELECT count( clnt_code ) as xcount , trim( clnt_rr1 ) as clnt_rr1 
							FROM int_clnt_clients
							WHERE trim( clnt_rr1 ) != ''
							AND trim( clnt_rr1 ) != '**'
							GROUP BY trim( clnt_rr1 ) ";
$result_rr1= mysql_query($query_rr1) or die(mysql_error());
$arr_one = array();
while($row_rr1 = mysql_fetch_array($result_rr1)) {
	$arr_one[$row_rr1["clnt_rr1"]] = $row_rr1["xcount"];
}

//show_array($arr_one);
//echo "<br><br><br>";

$query_rr2 = "SELECT count( clnt_code ) as xcount , trim( clnt_rr2 ) as clnt_rr2 
							FROM int_clnt_clients
							WHERE trim( clnt_rr2 ) != ''
							AND trim( clnt_rr2 ) != '**'
							GROUP BY trim( clnt_rr2 ) ";
$result_rr2= mysql_query($query_rr2) or die(mysql_error());
$arr_two = array();
while($row_rr2 = mysql_fetch_array($result_rr2)) {
	$arr_two[$row_rr2["clnt_rr2"]] = $row_rr2["xcount"];
}
//show_array($arr_two);
//echo "<br><br><br>";

foreach ($arr_one as $rr_a=>$xcount_a) {
	foreach ($arr_two as $rr_b=>$xcount_b) {
			if ($rr_a == $rr_b) {
			 //echo $client_a . " >> " . ($xcount_a + $xcount_b). "<br>";
			 $xcount_a = $xcount_a + $xcount_b;
			} 
	}
	//echo $rr_a . " >> " .$xcount_a . "<br>";
  $acct_str .= get_email_for_initials($rr_a) . "," . $xcount_a . chr(13) . chr(10);	
}

echo str_replace(chr(10),"<br>",$acct_str);

define("FILE_PATH","C:\\");
$filename = "COUNT_ACCTS.DAT";

$acctFile = fopen(FILE_PATH.$filename, "w" ) ;

  if ( $acctFile )
  {
	 fwrite($acctFile, $acct_str);
   fclose($acctFile);
	 echo "File ".FILE_PATH.$filename . " written successfully<br>";
  }
  else
  {
   die( "File could not be opened for writing" ) ;
  }

$cmd_string = "copy c:\\".$filename." \\\\192.168.20.49\\nfs$\\".$filename;
shell_exec($cmd_string);

?>