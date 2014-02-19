<?
  include('includes/dbconnect.php');
  include('includes/global.php'); 
	include('includes/functions.php');

$output_filename = "citta_list.xls";
$fp = fopen($exportlocation.$output_filename, "w");

$str = '<html xmlns="http://www.w3.org/1999/xhtml">
				<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head>
				<body>';
fputs ($fp, $str);

$str = '<table width="100%"  border="1" cellspacing="1" cellpadding="1">
				<tr>
					<td><strong>Fund</strong></td>
					<td><strong>Clnt. Code</strong></td>
					<td><strong>Acct. Name</strong></td>
					<td><strong>Date</strong></td>
					<td><strong>Source</strong></td>
					<td><strong>Update</strong></td>
					<td><strong>Is CI</strong></td>
					<td><strong>Company</strong></td>
					<td><strong>Symbol</strong></td>
					<td><strong>Person</strong></td>
					<td><strong>Title</strong></td>
					<td><strong>B/D Aff.</strong></td>
					<td><strong>Affiliate Name</strong></td>
					<td><strong>Insider Name</strong></td>
					<td><strong>Insider Title</strong></td>
					<td><strong>Is Fin. Serv.</strong></td>
					<td><strong>Company</strong></td>
					<td><strong>Person</strong></td>
					<td><strong>Type</strong></td>
					<td><strong>Entity Type</strong></td>
					<td><strong>Inv. Type</strong></td>
					<td><strong>Active Since</strong></td>
					<td><strong>Comments</strong></td>
					<td><strong>&nbsp;</strong></td>
			</tr>';
fputs ($fp, $str);

if ($mode == "active") {
	$str_filter = " where citta_isactive = 1 ";
} else {
	$str_filter = "";
}

 $qry_clist = "SELECT * FROM citta_list ".$str_filter." ORDER BY auto_id desc"; 
 $result_clist = mysql_query($qry_clist) or die(tdw_mysql_error($qry_clist));
	

 while ($row = mysql_fetch_array($result_clist)) {

   $str = '<tr>
						<td>'. $row["citta_fund_name"] .'</td>
						<td>'. $row["citta_client_code"] .'</td>
						<td>'. $row["citta_account_name"] .'</td>
						<td>'. format_date_ymd_to_mdy($row["citta_date_received"]) .'</td>
						<td>'. $row["citta_information_source"] .'</td>
						<td>'. $row["citta_update_information"] .'</td>
						<td>'. $row["citta_is_corporate_insider"] .'</td>
						<td>'. $row["citta_company_name"] .'</td>
						<td>'. $row["citta_company_symbol"] .'</td>
						<td>'. $row["citta_company_person"] .'</td>
						<td>'. $row["citta_company_person_title"] .'</td>
						<td>'. $row["citta_broker_dealer_affiliate"] .'</td>
						<td>'. $row["citta_affiliate_name"] .'</td>
						<td>'. $row["citta_insider_name"] .'</td>
						<td>'. $row["citta_insider_title"] .'</td>
						<td>'. $row["citta_is_financial_services"] .'</td>
						<td>'. $row["citta_finserv_company_name"] .'</td>
						<td>'. $row["citta_finserv_company_person"] .'</td>
						<td>'. $row["citta_finserv_type"] .'</td>
						<td>'. $row["citta_finserv_entity_type"] .'</td>
						<td>'. $row["citta_finserv_investment_type"] .'</td>
						<td>'. $row["citta_active_since"] .'</td>
						<td>'. $row["citta_comments"] .'</td>
						<td>&nbsp;</td>
					</tr>';
		fputs ($fp, $str);

	}

$str = '</table>
	</body>
</html>';
fputs ($fp, $str);


fclose($fp);

Header("Location: http://192.168.20.63/tdw/fileserve_xls.php?l=data/exports/&f=".$output_filename);
?>