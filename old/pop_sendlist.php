<?
include ('includes/dbconnect.php');
include ('includes/global.php');
include ('includes/functions.php');
include ('includes/functions_email.php');
?>

<title>Sending List via Email: _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _:</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="includes/styles.css" rel="stylesheet" type="text/css">
<body>

<? table_start_percent(100, "Sending List via Email"); ?>
<?
//xdebug('list_type',$list_type);
//xdebug('action',$action);

//get list name from type
	$query_list_name = "SELECT alis_title_name FROM alis_admin_lists WHERE alis_auto_id = '".$list_type."' AND alis_isactive = '1'";
	$result_list_name = mysql_query($query_list_name) or die(mysql_error());
	$arr_list_name = mysql_fetch_array($result_list_name);

	echo $arr_list_name[0];

//create stock list to email

$email_data = '<table width="100%"  border="0" cellspacing="1" cellpadding="1">
					<tr class="background_heading_row"> 
						<td width="80">&nbsp;&nbsp;&nbsp;&nbsp;Symbol</td>
						<td width="250">&nbsp;&nbsp;&nbsp;&nbsp;Description</td>
						<td width="150">&nbsp;&nbsp;&nbsp;&nbsp;Date Added</td>
						<td align="left">&nbsp;&nbsp;&nbsp;&nbsp;Days on List</td>
					</tr>';
					
	$result_stocklist = mysql_query("SELECT adll_auto_id,adll_symbol,adll_description, DATE_FORMAT(adll_date_added, '%m/%d/%y %h:%i %p') as adll_date_added, TO_DAYS(NOW()) - TO_DAYS(adll_date_added) + 1 as 'adll_days_on_list'  FROM adll_admin_list_lists WHERE adll_id = '".$list_type."' AND adll_isactive = 1 ORDER BY adll_symbol") or die (mysql_error());
	$i = 1;
	while ( $row = mysql_fetch_array($result_stocklist) ) 
	{
	if ($i%2==0) {$rowclass="background_data_row_color";} else {$rowclass="background_data_row_white";}
	$email_data .= 
	'<tr class="'.$rowclass.'"> 								
		<td>&nbsp;&nbsp;&nbsp;&nbsp;'.$row["adll_symbol"].'</td>
		<td>&nbsp;&nbsp;&nbsp;&nbsp;'.$row["adll_description"].'</td>
		<td>&nbsp;&nbsp;&nbsp;&nbsp;'.$row["adll_date_added"].'</td>
		<td>&nbsp;&nbsp;&nbsp;&nbsp;'.$row["adll_days_on_list"].'</td>
	</tr>';
	$i = $i + 1;
	}
	$email_data .= '</table>';

  $userlist = mysql_query("SELECT Fullname, Email FROM Users WHERE user_isactive = 1") or die (mysql_error());

	while ( $row = mysql_fetch_array($userlist) ) {
			
					$user_fullname = $row["Fullname"];
					$user_email = $row["Email"];
					
					
	}



				$mailsubject = date("D, m/d/Y h:i a"). " " . $arr_list_name[0] ;
				$email_heading = "Stock Lists Generated on ".date("D, m/d/Y h:i a");
				xdebug("email_heading",$email_heading);
				$fileattach = "";
				$control_id = gen_control_number();
				$mailbodysubinfo = $email_data;
				//html_emails($user_email, $mailsubject, $mailbodysubinfo, $email_heading, $fileattach, $control_id); 
				html_email_system('pprasad@centersys.com, pprasad007@hotmail.com', $mailsubject, $mailbodysubinfo, $email_heading, $fileattach, $control_id); 


?>
<? table_end_percent(); ?>


</body>








