<?
//**********************************************************************************
include('../includes/dbconnect.php');
include('../includes/functions.php');
include('../includes/global.php');
require_once("./cd_win32/lib/phpchartdir.php");
include('chart_data_functions.php');

//**********************************************************************************
//**********************************************************************************
# SQL Server Connection Information
$msconnect=mssql_connect("192.168.20.48","BUCKINGHAM_login","BUCKINGHAM_pw");
$msdb=mssql_select_db("BUCKINGHAM",$msconnect);

$trade_date_to_process = previous_business_day();
$nextday = '2009-06-17';

		$arr_mri = array();
		$arr_mri_symbols = array();


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
														 CAST(FLOOR(CAST(convert(datetime,'".format_date_ymd_to_mdy($trade_date_to_process)."',120) AS float)) as datetime)-180) 
												 AND CAST(FLOOR(CAST(convert(datetime,'".format_date_ymd_to_mdy($nextday)."',120) AS float)) as datetime)
											) 
											AND ((dbo.Prod_Statuses.StatusTypeID)=3))
										ORDER BY dbo.ExchangeSecurities.Ticker, dbo.Prod_Statuses.DateTime DESC;";	


		//xdebug("ms_qry_mri",$ms_qry_mri);
		$ms_results_mri = mssql_query($ms_qry_mri);
		
		$v_count_mri = 0;
		while ($row_mri = mssql_fetch_array($ms_results_mri)) {
					
					//show_array($row_mri);
					$symbol = $row_mri[0];
					$mri_date = $row_mri[1];
					$rating = $row_mri[3];
					$rating_change = $row_mri[5]; 
					$target = $row_mri[6];
					$rating_previous = $row_mri[4];

					$img_show = '';
					$arr_mri[$v_count_mri] = $symbol."<###>".$mri_date."<###>".$rating."<###>".$rating_change."<###>".$img_show."<###>".$target."<###>".$rating_previous;
					$arr_mri_symbols[$v_count_mri] = $symbol;
					$v_count_mri = $v_count_mri + 1;

		}
   
	 
	 $arr_mri = array_reverse($arr_mri);
	 //show_array($arr_mri_symbols);
	 show_array($arr_mri);

	 //Array of relevant MRI data
	 $arr_recent_mri = array();
	 
   //show_array($arr_mri_symbols);
	 foreach($arr_mri as $key=>$value) {
           if ($key == 0) {
						 $arr_data = explode("<###>",$value);
						 $str_symbol_old = $arr_data[0];
						 $str_date_old = $arr_data[1];
						 $str_rating_old = $arr_data[2];
						 $str_rating_prev_old = $arr_data[6];
						 $str_compare_old = $arr_data[2].$arr_data[5];
						 $str_target_old = $arr_data[5];
						 //xdebug("First Row",'');
					 } else {
							//xdebug("Row Number",$key);	
							$arr_data = explode("<###>",$value);
							$str_symbol_new = $arr_data[0];
							$str_date_new = $arr_data[1];
							$str_rating_new = $arr_data[2];
							$str_rating_prev_new = $arr_data[6];
							$str_compare_new = $arr_data[2].$arr_data[5];
						  $str_target_new = $arr_data[5];
							//show_array($arr_data);
							//Compare with old and then proceed
							
							if ($str_rating_new != '')	{
								if ($str_rating_new != $str_rating_prev_new) {
									$arr_recent_mri[$str_date_new] = date('Y-m-d',strtotime($str_date_new));
								} elseif ($str_target_new != $str_target_old AND $str_target_old != "") {
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
			 
//*******************************************************************************************
//*******************************************************************************************

//http://ichart.finance.yahoo.com/table.csv?s=ORCL&a=02&b=2&c=2008&d=04&e=28&f=2009&g=d&ignore=.csv

# The XY points for the scatter chart for price
//$dataX0 = array(10, 15, 6, 12, 14, 8, 13, 13, 16, 12, 10.5);
//$dataY0 = array(130, 150, 80, 110, 110, 105, 130, 115, 170, 125, 125);
?>