<?
include('includes/functions.php');
include('includes/global.php');
include('includes/dbconnect.php');

//print_r($_POST);		
//exit;						
								
												
$output_filename = "News_Events.xls";
$fp = fopen($exportlocation.$output_filename, "w");

$str = '<html xmlns="http://www.w3.org/1999/xhtml">
				<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head>
				<body>';
fputs ($fp, $str);


$str = '<table width="880" border="1" cellspacing="0" cellpadding="0">
					<tr>
						<td width="40"><b>#</b></td>
						<td width="80"><b>Date</b></td>
						<td width="80"><b>Type</b></td>
						<td width="80"><b>Symbol</b></td>
						<td width="600"><b>Details</b></td>
					</tr>';
fputs ($fp, $str);

if ($_POST) {
	$sel_datefrom = $datefrom;
	$sel_dateto = $dateto;

	if ($newstype == 'ALL') {
		$str_append = " AND a.news_event like '%' ";
	} else {
		$str_append = " AND a.news_event = '".$newstype."' ";
	}

	if ($symbol == 'Enter Symbol' or $symbol == '') {
		$str_append_symbol = " AND a.news_symbol like '%' ";
	} else {
		$str_append_symbol = " AND a.news_symbol = '". $symbol ."' ";
	}
}


						$str_sql_select = "SELECT a.*, b.Fullname 
																from news_events  a
																left join Users b on a.news_entered_by = b.ID
																	WHERE a.news_isactive = 1 " . $str_append . $str_append_symbol .
																	" and a.news_date between '".format_date_mdy_to_ymd($sel_datefrom) . "' and '" . format_date_mdy_to_ymd($sel_dateto) . "'
                                order by a.news_date desc";

						$result_select = mysql_query($str_sql_select) or die(tdw_mysql_error($str_sql_select));

						$count_row_select = 1;
						while ( $row_select = mysql_fetch_array($result_select) ) 
						{
							$str = '<tr>
												<td>'.$count_row_select . '</td>
												<td>'.format_date_ymd_to_mdy($row_select["news_date"]).'</td>
												<td>'.$row_select["news_event"].'</td>
												<td>'.$row_select["news_symbol"].'</td>
												<td>'.$row_select["news_notes"].'</td>
											</tr>';
							fputs ($fp, $str);					
							$count_row_select = $count_row_select + 1;
						}

$str = '</table>
	</body>
</html>';
fputs ($fp, $str);

fclose($fp);

//Header("Location: data/exports/".$output_filename);
Header("Location: http://192.168.20.63/tdw/fileserve_xls.php?l=data/exports/&f=".$output_filename);
?>