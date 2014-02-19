<?
include('../includes/dbconnect.php');
include('../includes/functions.php');
include('config.php');



	$msconnect=mssql_connect("192.168.20.48","BUCKINGHAM_login","BUCKINGHAM_pw");
	$msdb=mssql_select_db("BUCKINGHAM",$msconnect);

	$arr_rating = array();
 
 	$ms_qry_maxid = "SELECT dbo.Prod_Issuers.IssuerID, 
											max(dbo.Prod_Issuers.ProductID)
										FROM (
											(dbo.Prod_Issuers INNER JOIN dbo.Products ON dbo.Prod_Issuers.ProductID = dbo.Products.ProductID) 
											INNER JOIN dbo.Issuers ON dbo.Prod_Issuers.IssuerID = dbo.Issuers.IssuerID
													) INNER JOIN dbo.Prod_Statuses ON dbo.Products.ProductID = dbo.Prod_Statuses.ProductID 
											INNER JOIN dbo.ExchangeSecurities ON dbo.Issuers.IssuerID = dbo.ExchangeSecurities.SecurityID 
										WHERE (((dbo.Issuers.CUSIP)<>'') AND (dbo.Products.CreationDateTime 
											BETWEEN ( CAST(FLOOR(CAST(convert(datetime,'".date('m/d/Y')."',120) AS float)) as datetime)-365) 
												AND CAST(FLOOR(CAST(convert(datetime,'".date('m/d/Y')."',120) AS float)) as datetime) ) 
										AND ((dbo.Prod_Statuses.StatusTypeID)=3))
										AND dbo.Prod_Issuers.Recommendation is not null
										GROUP BY dbo.Prod_Issuers.IssuerID
										ORDER BY dbo.Prod_Issuers.IssuerID";
										
	$ms_results_maxid = mssql_query($ms_qry_maxid);
	while ($row_maxid = mssql_fetch_array($ms_results_maxid)) {

      $ms_qry_rating = "select IssuerID, Recommendation from dbo.Prod_Issuers
			                  where IssuerID = '".$row_maxid[0]."' and ProductID = '".$row_maxid[1]."'";
			if($row_maxid[0]==286 OR $row_maxid[0]==279) {
			xdebug("ms_qry_rating",$ms_qry_rating);
			}
			$ms_results_rating = mssql_query($ms_qry_rating);
			while ($row_rating = mssql_fetch_array($ms_results_rating)) {
		  	$arr_rating[$row_maxid[0]] = $row_rating[1]; 
			} 
	}

show_array($arr_rating);
?>