<?
	error_reporting(0);

  include('../includes/dbconnect.php');
  include('../includes/global.php');
	include('../includes/functions.php'); 

	//Create array of docs
/*	$getXMLString = file_get_contents('https://buckresearch.bluematrix.com/sellside/ChangeFeed.action?handler=LoginHandler&firmId=67811&login=aMAi1s98&password=a02Akws&mode=content&param=full&timeframe='.$trade_date_to_process);
	$bm_data = simplexml_load_string($getXMLString);

	$arr_return = xmlObjToArr($bm_data);
	
	echo $arr_return[][][][]
	
	print_r($arr_return);*/
	
	//TEST MODE FLUSH TMP TABLES.
	//$flush_a = mysql_query('truncate table etpa_mri_rsch_tmp');
	//flush_b = mysql_query('truncate table etpa_restricted_list_tmp');
	
	

function xmlObjToArr($obj) { 
        $namespace = $obj->getDocNamespaces(true); 
        $namespace[NULL] = NULL; 
        
        $children = array(); 
        $attributes = array(); 
        $name = strtolower((string)$obj->getName()); 
        
        $text = trim((string)$obj); 
        if( strlen($text) <= 0 ) { 
            $text = NULL; 
        } 
        
        // get info for all namespaces 
        if(is_object($obj)) { 
            foreach( $namespace as $ns=>$nsUrl ) { 
                // atributes 
                $objAttributes = $obj->attributes($ns, true); 
                foreach( $objAttributes as $attributeName => $attributeValue ) { 
                    $attribName = strtolower(trim((string)$attributeName)); 
                    $attribVal = trim((string)$attributeValue); 
                    if (!empty($ns)) { 
                        $attribName = $ns . ':' . $attribName; 
                    } 
                    $attributes[$attribName] = $attribVal; 
                } 
                
                // children 
                $objChildren = $obj->children($ns, true); 
                foreach( $objChildren as $childName=>$child ) { 
                    $childName = strtolower((string)$childName); 
                    if( !empty($ns) ) { 
                        $childName = $ns.':'.$childName; 
                    } 
                    $children[$childName][] = xmlObjToArr($child); 
                } 
            } 
        } 
        
        return array( 
            'name'=>$name, 
            'text'=>$text, 
            'attributes'=>$attributes, 
            'children'=>$children 
        ); 
    } 

//exit;

function DateAdd($interval, $number, $date) {

    $date_time_array = getdate($date);
    $hours = $date_time_array['hours'];
    $minutes = $date_time_array['minutes'];
    $seconds = $date_time_array['seconds'];
    $month = $date_time_array['mon'];
    $day = $date_time_array['mday'];
    $year = $date_time_array['year'];

    switch ($interval) {
    
        case 'yyyy':
            $year+=$number;
            break;
        case 'q':
            $year+=($number*3);
            break;
        case 'm':
            $month+=$number;
            break;
        case 'y':
        case 'd':
        case 'w':
            $day+=$number;
            break;
        case 'ww':
            $day+=($number*7);
            break;
        case 'h':
            $hours+=$number;
            break;
        case 'n':
            $minutes+=$number;
            break;
        case 's':
            $seconds+=$number; 
            break;            
    }
       $timestamp= mktime($hours,$minutes,$seconds,$month,$day,$year);
       return $timestamp;
}

	
	//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	// BEGIN BLUEMATRIX SECTION
	//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	//2012-06-20T05:53:02.00

	//Get the docs published after the last get date/time.
	//$trade_date_to_process = date('Y-m-d')."T05:00:00.00"; //date('Y-m-d')."T07:00:00.00";
	$str_previous_day = previous_business_day();
	$trade_date_to_process = $str_previous_day."T05:00:00.00"; //date('Y-m-d')."T07:00:00.00";
	ydebug("trade_date_to_process",$trade_date_to_process);
	//exit;
	
	$nextday = business_day_forward(strtotime(date('Y-m-d')),1);
	
	echo "<pre>"."https://buckresearch.bluematrix.com/sellside/ChangeFeed.action?firmId=67811&login=aMAi1s98&password=a02Akws&mode=Content&param="."</pre>";
  //exit;
	//Create array of docs
	$url_string = "https://buckresearch.bluematrix.com/sellside/ChangeFeed.action?handler=LoginHandler&firmId=67811&login=aMAi1s98&password=a02Akws&mode=content&param=full&timeframe=".$trade_date_to_process;
	xdebug("url_string",$url_string);
	$getXMLString = file_get_contents($url_string);
	$bm_data = simplexml_load_string($getXMLString);

	xdebug("now",$bm_data->now);
	
	//print_r($bm_data);
	$arr_docid_to_process = array();
	$arr_docinfo = array();
	foreach ($bm_data->Content as $Content) {
			foreach ($Content->ContentData as $ContentData) {
				//echo $ContentData["bluematrixDocId"]."<br>";
				$arr_docid_to_process[] = $ContentData["bluematrixDocId"]."^".
																	$ContentData["isReleased"]."^".
																	$ContentData["extendedStatus"]."^".
																	$ContentData["statusType"];
			}
	}
	//show_array($arr_docid_to_process);
	
	$arr_doc_id = array();
	foreach($arr_docid_to_process as $k=>$vals) {
		$tmp_arr = explode("^",$vals);
		$arr_doc_id[] = $tmp_arr[0]; 
	}

	//show_array($arr_doc_id);

	//Get the document XML Link
	
	?>
  <table border="1">
  <tr><td>Document Type</td><td>Main Subject</td><td>Symbol</td><td>Rating</td><td>Action</td><td>Target</td><td>Publish Time</td><td>Previous Rating</td></tr>
  <?
	
	foreach ($arr_doc_id as $k=>$v) {
		//xdebug("docid_param", $v);
		$docURL_string = "https://buckresearch.bluematrix.com/sellside/ChangeFeed.action?firmId=67811&login=aMAi1s98&password=a02Akws&mode=Content&param=".$v;
		$getXMLString = file_get_contents($docURL_string);
		$doc_xml = new SimpleXMLElement($getXMLString);
		$result = $doc_xml->xpath('Content/ContentData/ContentVersion/Url[@type=\'XML\']');	
		//echo $result[0]."<br>";
		
			//Get Product Details
			$getDetailXMLString = file_get_contents($result[0]);
			$detail_xml = new SimpleXMLElement($getDetailXMLString);
			$doc_type    = $detail_xml->xpath('/Product/ProductStatus/@bluematrixXmlDescr');	
			$doc_subject = $detail_xml->xpath('/Product/Context/ProductClassifications/Tagging/Tag[@attributeId=20]');	
			$doc_symbol  = $detail_xml->xpath('/Product/Context/IssuerDetails/Issuer/SecurityDetails/Security/SecurityID/@idValue');	
			$doc_rating  = $detail_xml->xpath('/Product/Context/IssuerDetails/Issuer/SecurityDetails/Security/Clusters/FinancialValue[@BM_ID=16]/CurrentValue/@displayValue');	
			$doc_rating_change = $detail_xml->xpath('/Product/Context/ProductClassifications/Tagging/Tag[@attributeId=9]');	
			$doc_target  = $detail_xml->xpath('/Product/Context/IssuerDetails/Issuer/SecurityDetails/Security/Clusters/FinancialValue[@BM_ID=27]/CurrentValue/@displayValue');
			$doc_time =  $detail_xml->xpath('/Product/ProductStatus/@displayDateTime'); 
			$prev_rating = $detail_xml->xpath('/Product/Context/IssuerDetails/Issuer/SecurityDetails/Security/Clusters/FinancialValue[@BM_ID=16]/PreviousValue/@displayValue'); 
			$prev_target = $detail_xml->xpath('/Product/Context/IssuerDetails/Issuer/IssuerChanges[@type="Target Price"]/@previous'); 

			if ($doc_type[0] == 'Industry Update') {
				$ind_update_companies = $detail_xml->xpath('/Product/Context/RelatedCompanies/Company[@type=1]/@ticker'); 
				$ind_update_companies_rating = $detail_xml->xpath('/Product/Context/RelatedCompanies/Company[@type=1]/@rating'); 
				$ind_update_companies_target = $detail_xml->xpath('/Product/Context/RelatedCompanies/Company[@type=1]/@target'); 
				//show_array($ind_update_companies);
				$arr_ind_companies = array();
				$arr_ind_companies_rating = array();
				$arr_ind_companies_target = array();
				//if (count($ind_update_companies) > 0) {
					foreach($ind_update_companies as $k=>$v) {
						$arr_ind_companies[] = $v;
					}
					foreach($ind_update_companies_rating as $k=>$v) {
						$arr_ind_companies_rating[] = $v;
					}
					foreach($ind_update_companies_target as $k=>$v) {
						$arr_ind_companies_target[] = $v;
					}
				//}
				$str_companies = implode(",",$arr_ind_companies);
				//print_r($doc_subject);
			}
			
			if ($doc_type[0] == 'Industry Update') {
				echo "<tr><td>".$v."</td><td>".$doc_type[0]."</td><td>".$doc_subject[0]."</td><td>".$str_companies."</td><td>".$doc_rating[0]."</td><td>".$doc_rating_change[0]."</td><td>".$doc_target[0]."</td><td>".$doc_time[0]."</td><td>".$prev_rating[0]."</td></tr>";
			} else {
				echo "<tr><td>".$v."</td><td>".$doc_type[0]."</td><td>".$doc_subject[0]."</td><td>".$doc_symbol[0]."</td><td>".$doc_rating[0]."</td><td>".$doc_rating_change[0]."</td><td>".$doc_target[0]."</td><td>".$doc_time[0]."</td><td>".$prev_rating[0]."</td></tr>";
			}

						 	
			//echo "<tr><td>".$doc_type[0]."</td><td>".$doc_subject[0]."</td><td>".$doc_symbol[0]."</td><td>".$doc_rating[0]."</td><td>".$doc_rating_change[0]."</td><td>".$doc_target[0]."</td><td>".$doc_time[0]."</td><td>".$prev_rating[0]."</td></tr>";
			
			$target = str_replace("$","",$doc_target[0]);
			$target_prev = str_replace("$","",$prev_target[0]);
			
			if ($doc_type[0] == 'Industry Update') {
				foreach($arr_ind_companies as $i=>$ticker) {
					$qry_insert = "INSERT INTO mri_research_ioc_raw (
													auto_id, 							reference_number,		symbol, 					report_date, 				report_type, 
													report_main_subject, 	report_datetime, 	rating, 						rating_previous, 
													rating_action, 				target, 					target_previous, 		is_processed, 
													record_isactive ) 
													VALUES (
													NULL,
													'".$v."', 
													'".$ticker."', 
													'".date ("Y-m-d", strtotime($doc_time[0]))."', 
													'".$doc_type[0]."', 
													'".$doc_subject[0]."', 
													'".date ("Y-m-d H:i:s", strtotime($doc_time[0]))."', 
													'".$arr_ind_companies_rating[$i]."', 
													'".$arr_ind_companies_rating[$i]."',  
													'', 
													'', 
													'', 
													'0', 
													'1')";
					$result_insert = mysql_query($qry_insert); // or die (tdw_mysql_error($qry_insert));
				}
			} elseif ($doc_type[0] != 'Industry Update' && $doc_type[0] != 'Database Update' && $doc_type[0] != 'Weekly Update') {			
					$qry_insert = "INSERT INTO mri_research_ioc_raw (
													auto_id, 							reference_number,		symbol, 					report_date, 				report_type, 
													report_main_subject, 	report_datetime, 	rating, 						rating_previous, 
													rating_action, 				target, 					target_previous, 		is_processed, 
													record_isactive ) 
													VALUES (
													NULL,
													'".$v."', 
												'".$doc_symbol[0]."', 
												'".date ("Y-m-d", strtotime($doc_time[0]))."', 
												'".$doc_type[0]."', 
												'".$doc_subject[0]."', 
												'".date ("Y-m-d H:i:s", strtotime($doc_time[0]))."', 
												'".$doc_rating[0]."', 
												'".$prev_rating[0]."', 
												'".$doc_rating_change[0]."', 
												'".$target."', 
												'".$target_prev."', 
												'0', 
												'1')";
					$result_insert = mysql_query($qry_insert); // or die (tdw_mysql_error($qry_insert));
			}

}

echo "<br>Done Processing Blue Matrix Data!";

//auto_id  reference_number  symbol  report_date  report_type  report_main_subject  report_datetime  rating  rating_previous  rating_action  target  target_previous  is_processed  record_isactive 
$result_unprocessed = mysql_query("select * from mri_research_ioc_raw where is_processed = 0");
while ( $row = mysql_fetch_array($result_unprocessed) ) {

	if ($row["report_main_subject"] == "Initiating Coverage") {
		$str_type = "IOC";
		$str_comment = "Initiating Coverage";
	} elseif ($row["report_main_subject"] == "Price Target Change"){
		$str_type = "MRI";
		$str_comment = "Price Target Change from $".$row["target_previous"]." to $".$row["target"];
	} elseif ($row["report_main_subject"] == "Recommendation Change"){
		$str_type = "MRI";
		$str_comment = "Recommendation Change from ".$row["rating_previous"]." to ".$row["rating"];
	} elseif ($row["report_main_subject"] == "Dropping Coverage"){
		$str_type = "DOC";
		$str_comment = "Dropping Coverage";
	} else {
		$str_type = "RES";
		$str_comment = "Research published.";
	}
	
	$qry_process = "INSERT INTO mri_research_ioc 
									(auto_id, symbol, report_date, report_type, report_main_subject, report_datetime, rating, rating_previous, rating_action, target, target_previous, system_comments, record_isactive) VALUES
									(NULL,
									'".$row["symbol"]."',
									'".$row["report_date"]."',
									'".$str_type."',
									'".$row["report_main_subject"]."',
									'".$row["report_datetime"]."',
									'".$row["rating"]."',
									'".$row["rating_previous"]."',
									'".$row["rating_action"]."',
									'".$row["target"]."',
									'".$row["target_previous"]."',
									'".$str_comment."', 
									1);";
	$result_process = mysql_query($qry_process);
	$result_mark_processed = mysql_query("update mri_research_ioc_raw set is_processed = 1 where auto_id = '".$row["auto_id"]."'");

} 
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// END BLUE MATRIX SECTION
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
?>