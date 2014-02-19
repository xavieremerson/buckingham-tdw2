<?
include('includes/global.php');
include('includes/dbconnect.php');
include('includes/functions.php');

//show_array($_GET);
//exit;

//=====================================================================================================
//Check conditions and report back.
$report_str = "";
$report_error = 0;
if ( $sel_exp_type == 'Client' || $sel_exp_type == 'Prospect') {

	if (is_array($exp_pfname) && count($exp_pfname) > 0 && trim($exp_pfname[0]) == "") {
		$report_str = "For expense type of Client or Prospect, you must enter at least one Person/Contact.<br>";
		$report_error = 1;
	}
}


if ($report_error == 1) {
	echo $report_str;
	exit;
}
//=====================================================================================================

function extract_client ($str) {
	if (strpos($str,"[")) {
		return substr($str,strpos($str,"[")+1,4); //Code is always 4 characters long.
	} else {
		return "";
	}
}

if (strlen($val_client) > 0) {

//xdebug("extract",extract_client($val_client));

		if (strlen(extract_client($val_client)) > 0) {
			$clnt_id = db_single_val("select clnt_auto_id as single_val from int_clnt_clients where clnt_code = '".extract_client($val_client)."'");
			$clnt_code = extract_client($val_client);
			$clnt_name = db_single_val("select clnt_name as single_val from int_clnt_clients where clnt_code = '".extract_client($val_client)."'");
		} else {
			$clnt_id = "";
			$clnt_code = "";
			$clnt_name = strtoupper($val_client);
		}

}

$max_id = db_single_val("select max(auto_id) as single_val from exp_expense_items");


/*$qry = "INSERT INTO exp_expense_items (
										auto_id ,
										exp_user_id ,
										exp_date ,
										exp_type ,
										exp_client_id ,
										exp_client_code ,
										exp_c_p_name ,
										exp_description ,
										exp_accomodation ,
										exp_transport ,
										exp_fuel ,
										exp_food ,
										exp_phone ,
										exp_entertainment ,
										exp_misc ,
										exp_approver ,
										exp_approval_date ,
										exp_approver_comment ,
										exp_datetimestamp ,
										exp_isactive 
										)
										VALUES (
										'".($max_id+1)."', 
										'". $user_id ."', 
										'".format_date_mdy_to_ymd($exp_date)."' , 
										'".$sel_exp_type."', 
										'23', 
										'BARR', 
										'BARR', 
										'another test', 
										'".$exp_hotel."', 
										'".$exp_transport."', 
										'".$exp_fuel."', 
										'".$exp_meals."', 
										'".$exp_entertainment."', 
										'".$exp_misc."', 
										'', 
										'', 
										'', 
										'', 
										NOW(), 
										'1'
										)";*/


if ($exp_hotel == '') { $exp_hotel = 0; };
if ($exp_air == '') { $exp_air = 0; };
if ($exp_train == '') { $exp_train = 0; };
if ($exp_cab == '') { $exp_cab = 0; };
if ($exp_rental == '') { $exp_rental = 0; };
if ($exp_mileage == '') { $exp_mileage = 0; };
if ($exp_other == '') { $exp_other = 0; };
if ($exp_meals == '') { $exp_meals = 0; };
if ($exp_phone == '') { $exp_phone = 0; };
if ($exp_entertainment == '') { $exp_entertainment = 0; };
if ($exp_misc == '') { $exp_misc = 0; };
if ($clnt_id == '') { $clnt_id = 0; };
if ($have_receipt) { $val_receipt = 1; } else { $val_receipt = 0; };

$qry = 	"INSERT INTO warehouse.exp_expense_items (
				auto_id ,								exp_user_id ,						exp_date_from ,     exp_date_to ,
				exp_type ,							exp_division,						exp_client_id ,					exp_client_code ,
				exp_c_p_name ,					exp_c_p_symbol,         exp_description ,				exp_accomodation ,
				exp_transport_air ,			exp_transport_train ,		exp_transport_cab ,
				exp_transport_rental ,	exp_transport_mileage,	exp_transport_other ,
				exp_food ,							exp_phone ,							exp_entertainment ,
				exp_misc ,							exp_have_receipt,       exp_approver ,					exp_approval_date ,
				exp_approver_comment ,	exp_datetimestamp ,			exp_isactive 
				)
				VALUES (
				'".($max_id+1)."','".$user_id."', '".format_date_mdy_to_ymd($exp_date_f)."', '".format_date_mdy_to_ymd($exp_date_t)."', 
				'".$sel_exp_type."', '".$sel_exp_division."','".$clnt_id."', '".$clnt_code."', 
				'".$clnt_name."', '".$exp_symbol."', '".str_replace("'","\\'",$exp_desc)."', '".$exp_hotel."', 
				'".$exp_air."', '".$exp_train."', '".$exp_cab."',
				'".$exp_rental."', '".$exp_mileage."', '".$exp_other."',
				'".$exp_meals."', '".$exp_phone."', '".$exp_entertainment."',
				'".$exp_misc."', '".$val_receipt."', NULL , NULL , 
				NULL , now(), '1')";

//xdebug("qry",$qry);

$result = mysql_query($qry) or die (tdw_mysql_error($qry));

if (is_array($exp_pfname) && count($exp_pfname) > 0 && trim($exp_pfname[0]) != "") {
	foreach($exp_pfname as $k=>$v) {
		if (trim($exp_pfname[$k]) != "") {
		$qry = "INSERT INTO warehouse.exp_expense_contacts (
												exp_parent_id, exp_first_name, exp_last_name, 
												exp_person_note, exp_added_on, exp_isactive  )
												VALUES (
												'".($max_id+1)."', '".str_replace("'","\\'",$exp_pfname[$k])."', '". str_replace("'","\\'",$exp_plname[$k]) ."', '". 
												str_replace("'","\\'",$exp_pnote[$k]). "', now(), '1')";  
		$result = mysql_query($qry) or die (tdw_mysql_error($qry));
		}
	}
}


?>
