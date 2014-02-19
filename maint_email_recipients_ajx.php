<?
  include('includes/dbconnect.php');
  include('includes/global.php');
  include('includes/functions.php');

ini_set('max_execution_time', 7200);
//ini_set('memory_limit','256M');
//ini_set("display_errors", 0); 
	
//print_r($_GET);

if ($mod_request == "ADDNEWLIST") {
	$arr_pieces = explode(" ",$list_name);
	$gencode = "";
	foreach($arr_pieces as $k=>$v) {
		$gencode .= trim(strtoupper(substr($v,0,2)));
	}
	//echo $gencode;
	$count_row = db_single_val("select count(*) as single_val from email_recipient_feature where email_feature_code = '".$gencode."'");
	if ($count_row > 0) {
		$insert = mysql_query("INSERT INTO email_recipient_feature (auto_id, email_feature_name, email_feature_code, email_feature_edited_by, email_feature_edited_on, email_feature_is_active) 
													 VALUES (NULL , '".strtoupper($list_name)."', '".$gencode."', '".$user_id."', NOW( ) , '1')");
	} else {
		$insert = mysql_query("INSERT INTO email_recipient_feature (auto_id, email_feature_name, email_feature_code, email_feature_edited_by, email_feature_edited_on, email_feature_is_active) 
													 VALUES (NULL , '".strtoupper($list_name)."', '".$gencode.rand(1,9)."', '".$user_id."', NOW( ) , '1')");
	}
}

if ($mod_request == "POPLIST") {
	create_dataset ($listtype);
}

if ($mod_request == "DELITEM") {

	$remove = mysql_query("update email_recipient_list set 
												 email_is_active = 0,
												 email_edited_by = '".$user_id."',
												 email_edited_on = now( ) 
												 where auto_id = '".$auto_id."'");
	
	create_dataset ($listtype);
}

if ($mod_request == "ADDTOLIST") {
	
 $add_result = mysql_query("INSERT INTO email_recipient_list (
														auto_id ,
														email_feature ,
														email_recipient_address ,
														email_edited_by ,
														email_edited_on ,
														email_is_active 
														)
														VALUES (NULL , '".$listtype."', '".$add_email."', '".$user_id."', NOW( ) , '1')");	
	
	create_dataset ($listtype);
}



?>
<?
/*INSERT INTO warehouse.email_recipient_list (
auto_id ,
email_feature ,
email_recipient_address ,
email_edited_by ,
email_edited_on ,
email_is_active 
)
VALUES (
NULL , 'COMPV2', 'compliance@buckresearch.com', '79', NOW( ) , '1'
);*/

function create_dataset ($listtype) {
			$qry = "select
								auto_id ,
								email_feature ,
								email_recipient_address ,
								email_edited_by ,
								email_edited_on ,
								email_is_active from email_recipient_list
							WHERE
								email_feature = '".$listtype."' 
							  AND email_is_active = 1
							ORDER BY auto_id DESC";
			$result = mysql_query($qry) or die(tdw_mysql_error($qry));
			$count_row = 1;
			echo '<br><br>';
			$mod_name = db_single_val("select email_feature_name as single_val from email_recipient_feature where email_feature_code ='".$listtype."'");
			tsp_b_px(100, "Email Recipients Maintenance: ".strtoupper($mod_name));
			echo '<div class="ilt">';
			while ($row = mysql_fetch_array($result)) {
				echo $count_row . ".&nbsp;&nbsp;&nbsp;" . $row["email_recipient_address"] . '&nbsp;&nbsp;&nbsp;<a href="#"><img src="images/themes/standard/delete.gif" border="0" onclick="del_item('.$row["auto_id"].');"></a><br><br>';
				$count_row++;
			}
			echo '<input type="hidden" id="curlist" value="'.$listtype.'">
						</div>';
			tep_b_px(); 
}

function create_list () {
			$qry = "select
								email_feature_name, email_feature_code
							FROM email_recipient_feature
							WHERE
							  email_feature_is_active = 1
							  ORDER BY email_feature_name";
			$result = mysql_query($qry) or die(tdw_mysql_error($qry));
			$count_row = 1;
			echo '<select id="sel_function" name="sel_function">
							<option value="">Select from List</option>';
			while ($row = mysql_fetch_array($result)) {
				echo '<option value="'.$row["email_feature_code"].'">'.$row["email_feature_name"].'</option>'; //$count_row . ".&nbsp;&nbsp;&nbsp;" . $row["email_recipient_address"] . '&nbsp;&nbsp;&nbsp;<a href="#"><img src="images/themes/standard/delete.gif" border="0" onclick="del_item('.$row["auto_id"].');"></a><br><br>';
				$count_row++;
			}
			echo '</select>';
}

?>