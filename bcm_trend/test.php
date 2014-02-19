<?  
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// outputs chart for trailing 12 months with argument $clnt
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ 

include('../includes/dbconnect.php');
include('../includes/functions.php');
include('../includes/global.php');
require_once("./cd_win32/lib/phpchartdir.php");
include('chart_data_functions.php');

if (!$date_start) {
$date_start = "2010-06-02";
$date_end = "2010-11-02";
$symbol = "ARO";
}

//**********************************************************************************
//**********************************************************************************
# SQL Server Connection Information

/*$msconnect=mssql_connect("1Z92.168.20.48","BUCKINGHAM_login","BUCKINGHAM_pw");
$msdb=mssql_select_db("BUCKINGHAM",$msconnect);
*/
$msconnect=mssql_connect("192.168.1.78","buckinghamtwo_db","9eFah9fe");
$msdb=mssql_select_db("BuckinghamTwo",$msconnect);

$arr_mri = array();
$arr_mri_symbols = array();


//Robert Daniel pointed out Missed MRI's, reason was the start date being passed to this program.
//start date needs to be set back by a few months. 3 in this case.

//echo date('m/d/Y',strtotime($date_start) - 7776000);

$new_date_start = date('Y-m-d',strtotime($date_start) - 7776000);
//echo $new_date_start;
//exit;

$ms_qry_mri = "SELECT 
									dbo.ExchangeSecurities.Ticker as CUSIP,
									dbo.Prod_Statuses.DateTime, 
									dbo.Prod_Issuers.IssuerID, 
									dbo.Prod_Issuers.Recommendation, 
									dbo.Prod_Issuers.PreviousRecommendation, 
									dbo.Prod_Issuers.RecommendationAction, 
									dbo.Prod_Issuers.TargetPrice, 
									dbo.Prod_Statuses.StatusTypeID
								FROM ((dbo.Prod_Issuers INNER JOIN dbo.Products ON dbo.Prod_Issuers.ProductID = dbo.Products.ProductID) 
								INNER JOIN dbo.Issuers ON dbo.Prod_Issuers.IssuerID = dbo.Issuers.IssuerID) 
								INNER JOIN dbo.Prod_Statuses ON dbo.Products.ProductID = dbo.Prod_Statuses.ProductID
								INNER JOIN dbo.ExchangeSecurities ON dbo.Issuers.IssuerID = dbo.ExchangeSecurities.SecurityID
								WHERE (((dbo.Issuers.CUSIP)<>'') AND (dbo.Products.CreationDateTime BETWEEN 
									(
												 CAST(FLOOR(CAST(convert(datetime,'".format_date_ymd_to_mdy($new_date_start)."',120) AS float)) as datetime)-1) 
										 AND CAST(FLOOR(CAST(convert(datetime,'".format_date_ymd_to_mdy($date_end)."',120) AS float)) as datetime)
									) 
									AND ((dbo.Prod_Statuses.StatusTypeID)=3))
									AND dbo.ExchangeSecurities.Ticker = '".$symbol."'
								ORDER BY dbo.ExchangeSecurities.Ticker, dbo.Prod_Statuses.DateTime DESC;";	


//xdebug("ms_qry_mri",$ms_qry_mri);
//exit; 
$ms_results_mri = mssql_query($ms_qry_mri);

$v_count_mri = 0;
while ($row_mri = mssql_fetch_array($ms_results_mri)) {
			//show_array($row_mri);
			$symbol_mri = $row_mri[0];
			$mri_date = $row_mri[1];
			$rating = $row_mri[3];
			$rating_change = $row_mri[5]; 
			$target = $row_mri[6];
			$rating_previous = $row_mri[4];

			$img_show = '';
			$arr_mri[$v_count_mri] = $symbol_mri."<###>".$mri_date."<###>".$rating."<###>".$rating_change."<###>".$img_show."<###>".$target."<###>".$rating_previous;
			$arr_mri_symbols[$v_count_mri] = $symbol_mri;
			$v_count_mri = $v_count_mri + 1;
}

	 $arr_mri = array_reverse($arr_mri);

	 show_array($arr_mri);
	 
	 //Array of relevant MRI data
	 $arr_recent_mri = array();
	 $arr_mri_support = array();
	 foreach($arr_mri as $key=>$value) {
           if ($key == 0) {
						 $arr_data = explode("<###>",$value);
						 $str_symbol_old = $arr_data[0];
						 $str_date_old = $arr_data[1];
						 $str_rating_old = $arr_data[2];
						 $str_rating_prev_old = $arr_data[6];
						 $str_compare_old = $arr_data[2].$arr_data[5];
						 $str_target_old = $arr_data[5];
					 } else {
							$arr_data = explode("<###>",$value);
							$str_symbol_new = $arr_data[0];
							$str_date_new = $arr_data[1];
							$str_rating_new = $arr_data[2];
							$str_rating_prev_new = $arr_data[6];
							$str_compare_new = $arr_data[2].$arr_data[5];
						  $str_target_new = $arr_data[5];
							//Compare with old and then proceed
							if ($str_rating_new != '')	{
								if ($str_rating_new != $str_rating_prev_new) {
									echo $str_rating_new ."////". $str_rating_prev_new."<br>";
									$arr_mri_support[date('Y-m-d',strtotime($str_date_new))] = "Rating change from ".$str_rating_prev_new." to ".$str_rating_new."."; 
									$arr_recent_mri[$str_date_new] = date('Y-m-d',strtotime($str_date_new));
								} elseif ($str_target_new != $str_target_old AND $str_target_old != "") {
									echo $str_target_new ."////". $str_target_old."<br>";
									$arr_mri_support[date('Y-m-d',strtotime($str_date_new))] = "Target change from ".$str_target_old." to ".$str_target_new."."; 
									$arr_recent_mri[$str_date_new] = date('Y-m-d',strtotime($str_date_new));
								} else {
								  $dummy = "xyz";
								}
								//set old values
									$str_symbol_old = $str_symbol_new;
									$str_date_old = $str_date_new;
									$str_rating_old = $str_rating_new;
									$str_rating_prev_old = $str_rating_prev_new;
									$str_compare_old = $str_compare_new;
						      $str_target_old = $str_target_new;
							} else {
								//don't set old values
								$str_symbol_old = $str_symbol_new;
							}
						}
       }

$arr_mri_raw = array();
foreach($arr_recent_mri as $k=>$v) {
$arr_mri_raw[] = $v;
}

show_array($arr_mri_support);
show_array($arr_mri_raw);
?>