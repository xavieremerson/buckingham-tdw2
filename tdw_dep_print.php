<?
  include('includes/dbconnect.php');
  include('includes/global.php');
	include('includes/functions.php');

//show_array($_GET);
//exit;

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
						<font color="#333333" size="3" face="Arial"><b>&nbsp;Report: TDW Server Dependencies</b></font>
						<br>
						<font color="#333333" size="2" face="Arial">
							&nbsp;Date : '.date('m/d/Y').'
						</font>
					</td>
					<td>&nbsp;</td>
				</tr>
			</table>
			<img src="../../images/border_a.png" width="670" height="2">';
			
			
		$data_to_html_file .= '<br>
													<table width="670" cellpadding="1", cellspacing="0" bgcolor="#EEEEEE" border="0">
														<tr>
															<td valign="top">		
																<table width="100%"  border="0" cellspacing="1" cellpadding="0">';
		
		     $page_header  = '<tr> 
														<td width="20">&nbsp;<font face="Arial" size="1"><b>#</b></font></td>
														<td width="150" align="left"><font face="Arial" size="1"><b>Server/Location</b></font>&nbsp;</td>
														<td width="100" align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font face="Arial" size="1"><b>Type</b></font></td>
														<td width="50"><font face="Arial" size="1"><b>Direction</b></font></td>
														<td width="370"><font face="Arial" size="1"><b>Comments</b></font></td>
													</tr>';
		
		$data_to_html_file .= $page_header;

if ($_GET) {

	//xdebug('datefilterval',$datefilterval);
	$date_from = format_date_mdy_to_ymd($datefrom);
	$date_to = format_date_mdy_to_ymd($dateto);

	$qry_get = "SELECT * 
							FROM tdw_server_dependencies
							WHERE dep_isactive = 1
							order by auto_id desc";
								
	$result_get = mysql_query($qry_get) or die (tdw_mysql_error($qry_get));
	$level_a_count = 1;
	while ( $row_get = mysql_fetch_array($result_get) ){
	$level_a_count = $level_a_count + 1;
  //+++++++++++++++++++++++++++++++++++++++++++++++
	if ($level_a_count % 2) { 
			$class_row = "trdark";
			$bgcolor = "#F2F2F2"; 
	} else { 
			$class_row = "trlight";
			$bgcolor = "#FFFFFF";
	} 
	$data_to_html_file .= '
												<tr class ="'.$class_row.'" bgcolor="'.$bgcolor.'" >
													<td align="left">&nbsp;<font face="Arial" size="1">'.($level_a_count-1).'</font></td>
													<td align="left"><font face="Arial" size="1">'. $row_get['dep_source'] .'</font>&nbsp;</td>
													<td align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font face="Arial" size="1">'.$row_get['dep_type'] .'</font></td>
													<td align="left"><font face="Arial" size="1">'.$row_get['dep_direction'].'</font></td>
													<td align="left"><font face="Arial" size="1">'.$row_get['dep_remarks'].'</font></td>
												</tr>';
		
	//+++++++++++++++++++++++++++++++++++++++++++++++

  }
}

						$data_to_html_file .= '
														</table>
													</td>
												</tr>
											</table>
											<!-- END TABLE 4 -->
										</td>
									</tr>';

							
							$data_to_html_file .= "<br><br>";
							$data_to_html_file .= '<hr align="left" width="670" size="1" noshade color="#0000CC">';
							$data_to_html_file .= '<font face="Arial" size="-3">Report created by '.$u. ' on '.date('m/d/Y').' at '.date('h:i:sa').' from m/c ['.$_SERVER["REMOTE_ADDR"].'] using '.$_SERVER['HTTP_USER_AGENT'].'</font>';
							$data_to_html_file .= '<hr align="left" width="670" size="1" noshade color="#0000CC">';



	$file_name_prefix = 'tdw_dependencies';
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
//shell_exec($cmd);

header("Content-type: application/pdf");
header("Location: http://192.168.20.63/tdw/data/prnt/".$file_pdf_name);
exit();
?>