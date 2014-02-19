<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
</head>
<body>
<?
include('includes/dbconnect.php');
include('includes/functions.php');

if (trim($citta_fund_name) != "") {

//print_r($_GET);

//exit;
/*
    [citta_fund_name] => ASDASD
    [citta_client_code] => 
    [citta_account_name] => 
    [citta_date_received] => 09/12/2011
    [citta_information_source] => 
    [citta_update_information] => Y
    [citta_is_corporate_insider] => Y
    [citta_company_symbol] => 
    [citta_company_name] => 
    [citta_company_person] => 
    [citta_company_person_title] => 
    [citta_broker_dealer_affiliate] => Y
    [citta_affiliate_name] => 
    [citta_insider_name] => 
    [citta_insider_title] => 
    [citta_is_financial_services] => Y
    [citta_finserv_company_name] => 
    [citta_finserv_company_person] => 
    [citta_finserv_type] => 
    [citta_finserv_entity_type] => 
    [citta_finserv_investment_type] => 
    [citta_active_since] => 09/12/2011
    [citta_comments] => 
    [Submit] => Â Â Â SAVEÂ Â Â 
    [venteredby] => 79
*/

	if (trim($citta_fund_name) != "") {
				
      $qry = "INSERT INTO citta_list (
							auto_id ,
							citta_fund_name ,
							citta_client_code ,
							citta_account_name ,
							citta_date_received ,
							citta_information_source ,
							citta_update_information ,
							citta_is_corporate_insider ,
							citta_company_name ,
							citta_company_symbol ,
							citta_company_person ,
							citta_company_person_title ,
							citta_broker_dealer_affiliate ,
							citta_affiliate_name ,
							citta_insider_name ,
							citta_insider_title ,
							citta_is_financial_services ,
							citta_finserv_company_name ,
							citta_finserv_company_person ,
							citta_finserv_type ,
							citta_finserv_entity_type ,
							citta_finserv_investment_type ,
							citta_active_since ,
							citta_deactivated_on ,
							citta_deactivated_by ,
							citta_deactivated_datetime ,
							citta_entered_by ,
							citta_entered_on ,
							citta_comments ,
							citta_isactive 
							)
							VALUES (
							NULL ,
							'".$citta_fund_name."',
							'".$citta_client_code."',
							'".$citta_account_name."',
							'".format_date_mdy_to_ymd($citta_date_received)."',
							'".$citta_information_source."',
							'".$citta_update_information."',
							'".$citta_is_corporate_insider."',
							'".$citta_company_name."',
							'".$citta_company_symbol."',
							'".$citta_company_person."',
							'".$citta_company_person_title."',
							'".$citta_broker_dealer_affiliate."',
							'".$citta_affiliate_name."',
							'".$citta_insider_name."',
							'".$citta_insider_title."',
							'".$citta_is_financial_services."',
							'".$citta_finserv_company_name."',
							'".$citta_finserv_company_person."',
							'".$citta_finserv_type."',
							'".$citta_finserv_entity_type."',
							'".$citta_finserv_investment_type."',
							'".format_date_mdy_to_ymd($citta_active_since)."',
							NULL,
							NULL,
							NULL,
							'".$venteredby."',
							now(),
							'".str_replace("'","\\'",$citta_comments)."',
							1
							)";

			$result = mysql_query($qry) or die(tdw_mysql_error($qry));
			$str_status = "<font color='green'>Data saved.</font>";
		} else {
			$str_status = "<font color='red'>Data not saved. Fund Name is missing. Please try again.</font>";
		}



$success_str = "<img src='./images/blinkbox.gif' border='0'> ".$str_status;
}
?>
<script language="JavaScript" src="includes/js/popup.js"></script>
<link rel="stylesheet" type="text/css" href="includes/styles.css">
					<table width="100%" border="0" cellspacing="1" cellpadding="1">
						<tr>
						  <td align="center"><a class="ghm"><?=$success_str?></a><!--<?=rand(1000000000,9999999999)?>--></td>
						</tr>
						</table>

	<? tsp(100, "CITTA List"); ?>
		
    <table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
			<tr>
				<td>
					<!--TABLE 2 START-->
					<script language="JavaScript" src="includes/javascript/sorttable.js" type="text/javascript"></script>

					<table class="sortable" preserve_style="cell" width="100%"  border="0" cellspacing="1" cellpadding="1">
					<tr>
							<td>Edit</td>
							<td>Fund</td>
							<td>Clnt. Code</td>
							<td>Acct. Name</td>
							<td>Date</td>
							<td>Source</td>
							<td>Update</td>
							<td>Is CI</td>
							<td>Company</td>
							<td>Symbol</td>
							<td>Person</td>
							<td>Title</td>
							<td>B/D Aff.</td>
							<td>Affiliate Name</td>
							<td>Insider Name</td>
							<td>Insider Title</td>
							<td>Is Fin. Serv.</td>
							<td>Company</td>
							<td>Person</td>
							<td>Type</td>
							<td>Entity Type</td>
							<td>Inv. Type</td>
							<td>Active Since</td>
							<td>Comments</td>
							<td>&nbsp;</td>
					</tr>

				 <?
				 $max_row_id = db_single_val("select max(auto_id) as single_val from citta_list");
				
				 $qry_clist = "SELECT * FROM citta_list 
											 where citta_isactive = 1 
											 ORDER BY auto_id desc"; //bcm_cusip, bcm_datetime_stop 
				 $result_clist = mysql_query($qry_clist) or die(tdw_mysql_error($qry_clist));
					
				 $hold_symbol = "";
				 $count_row = 0;
				 while ($row = mysql_fetch_array($result_clist)) {
								if ($count_row%2 == 0) {
									$rowclass = " class=\"trlight\"";
								} else {
									$rowclass = " class=\"trdark\"";
								}
						?>
						<tr <?=$rowclass?>>
							<td><a href="javascript:CreateWnd('citta_entry_edit.php?cid=<?=$row["auto_id"]?>&uid=<?=$uid?>', 745, 410, false);">
              			<img src="images/themes/standard/edit.gif" border="0" alt="Edit" />
                  </a>
              </td>
							<td>&nbsp;&nbsp;<?=$row["citta_fund_name"]?></td>
							<td>&nbsp;&nbsp;<?=$row["citta_client_code"]?></td>
							<td nowrap="nowrap">&nbsp;&nbsp;<?=$row["citta_account_name"]?></td>
							<td>&nbsp;&nbsp;<?=format_date_ymd_to_mdy($row["citta_date_received"])?></td>
							<td>&nbsp;&nbsp;<?=$row["citta_information_source"]?></td>
							<td>&nbsp;&nbsp;<?=$row["citta_update_information"]?></td>
							<td>&nbsp;&nbsp;<?=$row["citta_is_corporate_insider"]?></td>
							<td nowrap="nowrap">&nbsp;&nbsp;<?=$row["citta_company_name"]?></td>
							<td>&nbsp;&nbsp;<?=$row["citta_company_symbol"]?></td>
							<td nowrap="nowrap">&nbsp;&nbsp;<?=$row["citta_company_person"]?></td>
							<td>&nbsp;&nbsp;<?=$row["citta_company_person_title"]?></td>
							<td>&nbsp;&nbsp;<?=$row["citta_broker_dealer_affiliate"]?></td>
							<td nowrap="nowrap">&nbsp;&nbsp;<?=$row["citta_affiliate_name"]?></td>
							<td nowrap="nowrap">&nbsp;&nbsp;<?=$row["citta_insider_name"]?></td>
							<td nowrap="nowrap">&nbsp;&nbsp;<?=$row["citta_insider_title"]?></td>
							<td>&nbsp;&nbsp;<?=$row["citta_is_financial_services"]?></td>
							<td nowrap="nowrap">&nbsp;&nbsp;<?=$row["citta_finserv_company_name"]?></td>
							<td nowrap="nowrap">&nbsp;&nbsp;<?=$row["citta_finserv_company_person"]?></td>
							<td nowrap="nowrap">&nbsp;&nbsp;<?=$row["citta_finserv_type"]?></td>
							<td>&nbsp;&nbsp;<?=$row["citta_finserv_entity_type"]?></td>
							<td>&nbsp;&nbsp;<?=$row["citta_finserv_investment_type"]?></td>
							<td>&nbsp;&nbsp;<?=$row["citta_active_since"]?></td>
							<td nowrap="nowrap"><?=$row["citta_comments"]?></td>
							<td>&nbsp;</td>
						</tr>
						<?
						$count_row++;
					}
					?>
					</table>
					</div>
					</div>
					<? tep(); ?>
          
		</td>
	</tr>
</table>
		<? tep(); ?>
		</body>
</html>