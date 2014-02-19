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


  //Most recent research data from Jovus
	 
		$arr_rres = array();
		$arr_rres_symbols = array();

		$ms_qry_rres   = 	"SELECT dbo.Prod_Issuers.IssuerID, dbo.Issuers.CUSIP, Max(dbo.Products.CreationDateTime) AS MaxOfCreationDateTime
											FROM (dbo.Prod_Issuers INNER JOIN dbo.Products ON dbo.Prod_Issuers.ProductID = dbo.Products.ProductID) 
											INNER JOIN dbo.Issuers ON dbo.Prod_Issuers.IssuerID = dbo.Issuers.IssuerID
											WHERE dbo.Issuers.CUSIP <> ''
											GROUP BY dbo.Prod_Issuers.IssuerID, dbo.Issuers.CUSIP order by dbo.Issuers.CUSIP;
											";
 									 //AND CAST(FLOOR(CAST(dbo.Prod_Statuses.DateTime AS float)) AS datetime) = '".$trade_date_to_process."'

		//xdebug("ms_qry_rres",$ms_qry_rres);
		$ms_results_rres = mssql_query($ms_qry_rres);
		
		$v_count_rres = 0;
		while ($row_rres = mssql_fetch_array($ms_results_rres)) {
					
					$symbol = $row_rres[1];
					$rres_date = $row_rres[2];
					$arr_rres_symbols[$symbol] = $rres_date;

		}

	 //Array of relevant rres data
   show_array($arr_rres_symbols);

exit;






























  //First get all ratings/target changes for the previous business day (if any)
	 
		$arr_rres = array();
		$arr_rres_symbols = array();

		$ms_qry_rres = "SELECT dbo.Prod_Issuers.ProductID, 
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

		//xdebug("ms_qry_rres",$ms_qry_rres);
		$ms_results_rres = mssql_query($ms_qry_rres);
		
		$v_count_rres = 0;
		while ($row_rres = mssql_fetch_array($ms_results_rres)) {
					
					//show_array($row_rres);
					$symbol = $row_rres[3];
					$rating = $row_rres[4];
					$rating_change = $row_rres[6]; 
					$target = $row_rres[7];

					if ($rating_change == "Increase") {
					  $img_show = '<img src="images/themes/standard/arrow_up.gif" border="0">';
						$arr_rres[$v_count_rres] = $symbol."<###>".$rating."<###>".$rating_change."<###>".$img_show."<###>".$target;
						$arr_rres_symbols[$v_count_rres] = $symbol;
						$v_count_rres = $v_count_rres + 1;
					} elseif ($rating_change == "Decrease"){
					  $img_show = '<img src="images/themes/standard/arrow_down.gif" border="0">';
						$arr_rres[$v_count_rres] = $symbol."<###>".$rating."<###>".$rating_change."<###>".$img_show."<###>".$target;
						$arr_rres_symbols[$v_count_rres] = $symbol;
						$v_count_rres = $v_count_rres + 1;
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
   show_array($arr_rres_symbols);


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