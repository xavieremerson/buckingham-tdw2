<?
# SQL Server Connection Information
$msconnect=mssql_connect("192.168.20.48","BUCKINGHAM_login","BUCKINGHAM_pw");
$msdb=mssql_select_db("BUCKINGHAM",$msconnect);

  include('includes/dbconnect.php');
  include('includes/global.php');
	include('includes/functions.php');

	//Previous Business Day should be applied here.
	//$trade_date_to_process = previous_business_day();
	$trade_date_to_process = '2006-08-02';
	$date_match_val = date("M j Y",strtotime('2006-08-02'));

	xdebug('Connecting to Jovus Server @ Buckingham','Successful');


  //First get all ratings/target changes for the previous business day (if any)
	 
		$arr_mri = array();
		$arr_mri_symbols = array();

		$ms_qry_mri = 	"SELECT 
										dbo.Issuers.CUSIP, 
										dbo.Products.CreationDateTime, 
										dbo.Prod_Issuers.IssuerID, 
										dbo.Prod_Issuers.Recommendation, 
										dbo.Prod_Issuers.PreviousRecommendation, 
										dbo.Prod_Issuers.RecommendationAction, 
										dbo.Prod_Issuers.TargetPrice
										FROM (dbo.Prod_Issuers INNER JOIN dbo.Products ON dbo.Prod_Issuers.ProductID = dbo.Products.ProductID) 
										INNER JOIN dbo.Issuers ON dbo.Prod_Issuers.IssuerID = dbo.Issuers.IssuerID
										WHERE (((dbo.Issuers.CUSIP)<> '') AND ((dbo.Products.CreationDateTime)> GETDATE()-180))
										ORDER BY dbo.Issuers.CUSIP, dbo.Products.CreationDateTime DESC;";
 									 //AND CAST(FLOOR(CAST(dbo.Prod_Statuses.DateTime AS float)) AS datetime) = '".$trade_date_to_process."'

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

					if ($rating_change == "Increase") {
					  $img_show = '<img src="images/themes/standard/arrow_up.gif" border="0">';
						$arr_mri[$v_count_mri] = $symbol."<###>".$mri_date."<###>".$rating."<###>".$rating_change."<###>".$img_show."<###>".$target;
						$arr_mri_symbols[$v_count_mri] = $symbol;
						$v_count_mri = $v_count_mri + 1;
					} elseif ($rating_change == "Decrease"){
					  $img_show = '<img src="images/themes/standard/arrow_down.gif" border="0">';
						$arr_mri[$v_count_mri] = $symbol."<###>".$mri_date."<###>".$rating."<###>".$rating_change."<###>".$img_show."<###>".$target;
						$arr_mri_symbols[$v_count_mri] = $symbol;
						$v_count_mri = $v_count_mri + 1;
					} else {
					  $img_show = '';
						$arr_mri[$v_count_mri] = $symbol."<###>".$mri_date."<###>".$rating."<###>".$rating_change."<###>".$img_show."<###>".$target;
						$arr_mri_symbols[$v_count_mri] = $symbol;
						$v_count_mri = $v_count_mri + 1;
					}

		}
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
						 $str_compare_old = $arr_data[2].$arr_data[5];
						 xdebug("First Row",'');
					 } else {
					 	 //xdebug("Row Number",$key);	
						 $arr_data = explode("<###>",$value);
						 $str_symbol_new = $arr_data[0];
						 $str_date_new = $arr_data[1];
						 $str_rating_new = $arr_data[2];
						 $str_compare_new = $arr_data[2].$arr_data[5];
						 //show_array($arr_data);
						 //Compare with old and then proceed
						 if ($str_symbol_new == $str_symbol_old) { //within the same ticker
						 		if($str_compare_new != $str_compare_old && $str_rating_new != '' && $str_rating_old != '' && $ignore != $str_symbol_old) { //compare new to old 
										if ($ignore == $str_symbol_old) {
												//do nothing for this symbol anymore
												$ignore = $str_symbol_new;
										} else {
												//xdebug("strings", $str_compare_new."||".$str_compare_old);
												echo "marked => ". $str_symbol_old . " on " . $str_date_old . "<br>";
												//CAPTURE THE VALUE IN AN ARRAY
												$arr_recent_mri[$str_symbol_old] = $str_date_old;
												$ignore = $str_symbol_new;
										}
						    } else {
										if ($ignore == $str_symbol_old) {
												$ignore = $str_symbol_new;
										} else {
												$ignore = "";
										}
									//echo "nothing 1: " . $str_symbol_old . " on " . $str_date_old . "<br>";
									//xdebug("strings compare", $str_compare_new."||".$str_compare_old);
									//xdebug("strings rating", $str_rating_new."||".$str_rating_old);
									// do nothing and proceed
								}
								
								if ($str_rating_new == '')	{
								 //don't set old values
								 $str_symbol_old = $str_symbol_new;
								} else {
								 //set old values
								 $str_symbol_old = $str_symbol_new;
								 $str_date_old = $str_date_new;
								 $str_rating_old = $str_rating_new;
								 $str_compare_old = $str_compare_new;
								}
							
							} else {
									// do nothing and proceed
									//xdebug("symbols", $str_symbol_new."||".$str_symbol_old);
									//echo "nothing 2: " . $str_symbol_new . " on " . $str_date_new . "<br>";
								 //set old values
								 $str_symbol_old = $str_symbol_new;
								 $str_date_old = $str_date_new;
								 $str_rating_old = $str_rating_new;
								 $str_compare_old = $str_compare_new;
							}
						 
						 }

       }
show_array($arr_recent_mri);
exit;






























  //First get all ratings/target changes for the previous business day (if any)
	 
		$arr_mri = array();
		$arr_mri_symbols = array();

		$ms_qry_mri = "SELECT dbo.Prod_Issuers.ProductID, 
									dbo.Research.DocID, 
									dbo.Issuers.IssuerName, 
									dbo.Issuers.CUSIP, 
									dbo.Prod_Issuers.Recommendation, 
									dbo.Prod_Issuers.PreviousRecommendation, 
									dbo.Prod_Issuers.RecommendationAction, 
									dbo.Prod_Issuers.TargetPrice, 
									dbo.Prod_Statuses.DateTime, 
									dbo.Prod_Statuses.StatusTypeID
									FROM ((dbo.Issuers INNER JOIN dbo.Prod_Issuers ON dbo.Issuers.IssuerID = dbo.Prod_Issuers.IssuerID) 
												 INNER JOIN dbo.Prod_Statuses ON dbo.Prod_Issuers.ProductID = dbo.Prod_Statuses.ProductID) 
									INNER JOIN dbo.Research ON dbo.Prod_Issuers.ProductID = dbo.Research.ResearchID
									WHERE (((dbo.Prod_Statuses.StatusTypeID)=3))";
 									 //AND CAST(FLOOR(CAST(dbo.Prod_Statuses.DateTime AS float)) AS datetime) = '".$trade_date_to_process."'

		//xdebug("ms_qry_mri",$ms_qry_mri);
		$ms_results_mri = mssql_query($ms_qry_mri);
		
		$v_count_mri = 0;
		while ($row_mri = mssql_fetch_array($ms_results_mri)) {
					
					//show_array($row_mri);
					$symbol = $row_mri[3];
					$rating = $row_mri[4];
					$rating_change = $row_mri[6]; 
					$target = $row_mri[7];

					if ($rating_change == "Increase") {
					  $img_show = '<img src="images/themes/standard/arrow_up.gif" border="0">';
						$arr_mri[$v_count_mri] = $symbol."<###>".$rating."<###>".$rating_change."<###>".$img_show."<###>".$target;
						$arr_mri_symbols[$v_count_mri] = $symbol;
						$v_count_mri = $v_count_mri + 1;
					} elseif ($rating_change == "Decrease"){
					  $img_show = '<img src="images/themes/standard/arrow_down.gif" border="0">';
						$arr_mri[$v_count_mri] = $symbol."<###>".$rating."<###>".$rating_change."<###>".$img_show."<###>".$target;
						$arr_mri_symbols[$v_count_mri] = $symbol;
						$v_count_mri = $v_count_mri + 1;
					} else {
					  $img_show = '';
					}

					//echo "<hr>".$symbol." >> ".$rating." >> ".$rating_change." >> ".$img_show." >> ".$target; 

					/*
					echo "<hr>";
										echo "<b>".$row[0].".pdf</b><br>";
										echo "<b>Headline: </b>".$row[1]."<br>";
										echo "<b>Document Type: </b>".$row[2]."<br>";
										echo "<b>Date/Time: </b>".$row[3]."<br>";
										echo "<b>Status: </b>".$row[4]."<br>";
					*/
		}
   show_array($arr_mri_symbols);


 	$arr_symbols = array(); 
	xdebug ('Processing Documents for date', $trade_date_to_process);

	$msquery = "exec prGetAllPublishedDocIds '".$trade_date_to_process."'";
	$msresults= mssql_query($msquery);
	
	$v_count_docs = 0;
	$str_symbols = "";

	while ($row = mssql_fetch_array($msresults)) {
				
				$date_matched = strripos("prefix".$row[3], $date_match_val);
				
				if ($date_matched) {
        /*
				echo "<hr>";
				echo "<b>".$row[0].".pdf</b><br>";
				echo "<b>Headline: </b>".$row[1]."<br>";
				echo "<b>Document Type: </b>".$row[2]."<br>";
				echo "<b>Date/Time: </b>".$row[3]."<br>";
				echo "<b>Status: </b>".$row[4]."<br>";
				*/
							 // Getting Ticker(s)
								$msquery2 = "EXEC prGetAllTickersInNote '".$row[0]."'";
								$msresults2= mssql_query($msquery2);
									while ($row2 = mssql_fetch_array($msresults2)) {
										$str_symbols .= $row2[0].",";
									}
									//echo "<b>Symbols: </b>".$str_symbols;
									$v_count_docs = $v_count_docs + 1;

				} else {
					 //do nothing;
				}
	}
	$arr_symbols = explode(",",$str_symbols);
   show_array($arr_symbols);
	xdebug('Found document(s) in Jovus.',$v_count_docs);


					/*
					0 = [8450]
					ProductID = [8450]
					1 = [IP_20060711_MMN]
					DocID = [IP_20060711_MMN]
					2 = [International Paper]
					IssuerName = [International Paper]
					3 = [IP]
					CUSIP = [IP]
					4 = [Neutral]
					Recommendation = [Neutral]
					5 = [Neutral]
					PreviousRecommendation = [Neutral]
					6 = [ ]
					RecommendationAction = [ ]
					7 = [$32.00]
					TargetPrice = [$32.00]
					8 = [Aug 2 2006 8:37AM]
					DateTime = [Aug 2 2006 8:37AM]
					9 = [3]
					StatusTypeID = [3]
					*/
		
?>