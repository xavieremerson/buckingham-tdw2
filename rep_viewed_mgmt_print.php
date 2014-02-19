<?
  include('includes/dbconnect.php');
  include('includes/global.php');
	include('includes/functions.php');

if ($_GET) {

		$data_to_html_file = "";

		$data_to_html_file .= '
			<style type="text/css">
			<!--
			.data_black {font-family: "Courier New", Courier, mono;	font-size: 10px;	color: #000000;}
			tr.trlight {
				font-family: Arial;
				font-size: 11px;
				color: #000000;
			}
			tr.trdark {
				font-family: Arial;
				font-size: 11px;
				color: #000000;
			}
			-->
			</style>
			<table width="670" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td valign="top" width="50"><img src="../../images/logo_small.gif" width="47"></td>
					<td valign="top" align="left" width="500">
						<font color="#333333" size="3" face="Arial"><b>&nbsp;Compliance Report (Notes)</b></font>
						<br>
						<font color="#333333" size="2" face="Arial">
							&nbsp;Dates From: '.format_date_ymd_to_mdy($fd).' to '.format_date_ymd_to_mdy($td).'
						</font>
					</td>
					<td>&nbsp;</td>
				</tr>
			</table>
			<img src="../../images/border_a.png" width="670" height="2">';
			
			
			$data_to_html_file .= '
												<table width="670" cellpadding="1", cellspacing="0" bgcolor="#FFFFFF" border="1">
													<tr>
														<td valign="top">		
														<table width="100%"  border="0" cellspacing="1" cellpadding="0">
															<tr> 
																<td colspan="2" bgcolor="#ffffff" width="120"><font face="Arial" size="1">&nbsp;&nbsp;Trade Date</font></td>
																<td bgcolor="#ffffff" colspan="4" align="left"><font face="Arial" size="1">Notes</font></td>
															<tr>';
															
															
			$query_show_notes = "SELECT c.auto_id, c.msrv_trade_date, a.*, DATE_FORMAT( a.msrn_notes_datetime, '%c/%e/%y %l:%i %p' ) as note_time, b.Fullname
													 FROM mgmt_reports_creation c, mgmt_reports_notes a, users b
													 WHERE c.auto_id = a.msrn_rep_auto_id
														AND a.msrn_userid = b.ID
														AND c.msrv_trade_date between '".$fd."' and '".$td."'
														AND c.msrv_rep_id = 'DCARV2'
													 ORDER BY a.auto_id desc";


			$result_show_notes = mysql_query($query_show_notes) or die(tdw_mysql_error($query_show_notes));

			$count_row = 0;
			while ($row_show_notes = mysql_fetch_array($result_show_notes)) {
						
						if ($count_row%2 == 0) {
								$class_row = "trdark";
								$bgcolor = "#FFFFFF";  //F2F2F2
						} else { 
								$class_row = "trlight";
								$bgcolor = "#FFFFFF";
						}
		
						if ($row_show_notes['msrv_trade_date'] == $hold_trade_date) {
							$show_trade_date = '';
						} else {
							$show_trade_date = format_date_ymd_to_mdy($row_show_notes['msrv_trade_date']);
						}
						
						$noteval_display = str_replace("\n",'<br>',$row_show_notes["msrn_notes"]);
						
						$str_notes = '<table bgcolor="white" width="100%" border="0" cellspacing="0" cellpadding="4">
													<tr>
														<td>'.$row_show_notes["Fullname"].' ('.$row_show_notes["note_time"].')&nbsp;&nbsp;&nbsp;</td>
													</tr>
													<tr>
														<td><p align="justify">'.$noteval_display.'</p></td>
													</tr></table>';
						
						$data_to_html_file .= '
																	<tr class ="'.$class_row.'" bgcolor="'.$bgcolor.'" >
																		<td valign="top"><font face="Arial" size="1">&nbsp;'.$show_trade_date.'</font></td>
																		<td><font face="Arial" size="1">&nbsp;&nbsp;'.$str_notes.'</font></td>
																	</tr>';

						$count_row = $count_row + 1;
						$hold_trade_date = $row_show_notes['msrv_trade_date'];
			
			}													

			$data_to_html_file .= '</table></td><tr></table>';
				
			$data_to_html_file .= "<br><br>";
			$data_to_html_file .= '<hr align="left" width="670" size="1" noshade color="#0000CC">';
			$data_to_html_file .= '<font face="Arial" size="-3">Report created by '.$info_str. ' on '.date('m/d/Y').' at '.date('h:i:sa').' from m/c ['.$_SERVER["REMOTE_ADDR"].'] using '.$_SERVER['HTTP_USER_AGENT'].'</font>';
			$data_to_html_file .= '<hr align="left" width="670" size="1" noshade color="#0000CC">';
			
	$file_name_prefix = rand(0,9);
	$file_name = $file_name_prefix.".html";    
	$file_pdf_name = $file_name_prefix.".pdf"; 

//production
/*
	$file_name = $trade_date_to_process."_dcar.html";     
	$file_pdf_name = $trade_date_to_process."_dcar.pdf";     
*/

	$fp = fopen ("d:\\tdw\\tdw\\data\\prnt\\".$file_name, "w");  
	fwrite ($fp,$data_to_html_file);        
	fclose ($fp); 

$cmd_pdf = "d:\\tdw\\tdw\\includes\\createpdf_args.bat ". $file_pdf_name. " " . $file_name;
//echo $cmd_pdf."<br>";
shell_exec($cmd_pdf);

//delete the temp html file
$cmd = "del d:\\tdw\\tdw\\data\\prnt\\".$file_name;
shell_exec($cmd);

header("Content-type: application/pdf");
header("Location: http://192.168.20.63/tdw/data/prnt/".$file_pdf_name);
exit();
} else {
echo "You have tried to access the report without proper privileges! Please login into TDW and print the report.";
}
?>