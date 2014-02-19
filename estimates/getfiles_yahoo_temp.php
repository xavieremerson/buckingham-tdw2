<?
include('../includes/global.php');
include('../includes/dbconnect.php');
include('../includes/functions.php');
include('config.php');

	# SQL Server Connection Information
	$msconnect=mssql_connect("192.168.20.48","BUCKINGHAM_login","BUCKINGHAM_pw");
	$msdb=mssql_select_db("BUCKINGHAM",$msconnect);


	echo "Processing started at ".date('m/d/y h:i:sa')."\n";
	ob_flush();
	flush();

	//initiate page load time routine
	$time=getmicrotime(); 
	
	$filelocation =    "D:\\tdw\\tdw\\estimates\\files\\";

	//Get tickers from Jovus
	
			$ms_qry_rres   = "SELECT dbo.Issuers.IssuerID, dbo.ExchangeSecurities.Ticker as CUSIP, dbo.Issuers.FiscalYearEnd
												FROM  dbo.Issuers
												INNER JOIN dbo.ExchangeSecurities ON dbo.Issuers.IssuerID = dbo.ExchangeSecurities.SecurityID 
												WHERE dbo.ExchangeSecurities.IsActive = 1
												AND dbo.ExchangeSecurities.Ticker like '%'											
												order by dbo.ExchangeSecurities.Ticker;";
												
												//
			//xdebug("ms_qry_rres",$ms_qry_rres);
			$ms_results_rres = mssql_query($ms_qry_rres);
			$v_count_rres = 0;
			while ($row_rres = mssql_fetch_array($ms_results_rres)) {
			
			$v_count_rres = $v_count_rres + 1;
			if ($v_count_rres == 5) { //10000
				exit;
			}
					//show_array($row_rres);
					$val_symbol = $row_rres[1];
					$var_FYE = $arr_FYE[trim($row_rres[2])];
					//xdebug("val_symbol",$val_symbol);
					if ($val_symbol != 'xxx') {
					//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					echo "Processing : ".$val_symbol."\n";
								 //echo "Currently processing : [".$val_symbol."]\n<br>";
					ob_flush();
					flush();
								
									$str_whole = "";
									$lines = array();
									$all_table_data = array();
									
									$loc = "http://finance.yahoo.com/q/ae?s=".str_replace(".","-",$val_symbol);
									$lines = file("http://www.centersys.com/z_buckingham.php?loc=".$loc);
												
									//Get the content of the file into a string
									foreach ($lines as $key=>$value)
										{
										 $str_whole .=$value;
										}
									
										$fpx = fopen($filelocation.str_replace(".","-",$val_symbol).".html", "w");
										fputs ($fpx, $str_whole);
										fputs ($fpx, "ENDOFFILE");
										fclose($fpx);

									
									$lines = array();
						
					//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					}
			}

echo "\nProcessing completed successfully. Time taken: ". sprintf("%01.2f",((getmicrotime()-$time)/1000))." s.";             
exit;																
?>