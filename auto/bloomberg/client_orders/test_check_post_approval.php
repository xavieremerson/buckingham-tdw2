<?
include('../../includes/dbconnect.php');
include('../../includes/functions.php'); 
include('../../includes/global.php'); 

$test_var = 'BMONX';


include('client_rep_functions.php');
			
			//Get some client details
			//Client Name
			$c_client_name = db_single_val("select max(trim(clnt_name)) as single_val from int_clnt_clients where clnt_alt_code = trim('".$test_var."')");
			if ($c_client_name == "") { $c_client_name_string = "";} else {$c_client_name_string = "(".$c_client_name.")";}
			xdebug("c_client_name",$c_client_name);
			
			//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			// If alt_code is not found insert alt code based on first 4 letters of the client code
			$zzqry = "update int_clnt_clients set clnt_alt_code = '".$test_var. "' where clnt_code = '".substr($test_var,0,4)."'";
			$result = mysql_query($zzqry);
			$c_client_name = db_single_val("select max(trim(clnt_name)) as single_val from int_clnt_clients where clnt_alt_code = trim('".$test_var."')");
			if ($c_client_name == "") { $c_client_name_string = "";} else {$c_client_name_string = "(".$c_client_name.")";}
			//xdebug("zzqry",$zzqry);

      $qry_update_record_history = "insert into int_clnt_clients_tradeware values (NULL, 'Updated to value ".$test_var."',now())";
			//xdebug("qry_update_record_history",$qry_update_record_history);
			$result_update_record_history = mysql_query($qry_update_record_history);			
			
			//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

			//Client Rep
			$c_client_rep = db_single_val("select concat(clnt_rr1,'^',clnt_rr2) as single_val from int_clnt_clients where clnt_alt_code = trim('".$test_var."')");
			xdebug("c_client_rep",$c_client_rep);
			
			$arr_c_reps = explode("^",$c_client_rep);
			print_r($arr_c_reps);
			
			if (trim($arr_c_reps[1])=="") { //single

				$c_rep_num = get_rr_num (get_userid_for_initials ($arr_c_reps[0]) );
				$c_rep_name = db_single_val("select Fullname as single_val from users where ID = '".get_userid_for_initials ($arr_c_reps[0])."'");
	
				$c_str_sales_rep = $c_rep_name . " [".$c_rep_num."]";
	
				xdebug("c_str_sales_rep",$c_str_sales_rep);

			} else { //shared
			
				xdebug("arr_c_reps[0]",$arr_c_reps[0]);
				xdebug("arr_c_reps[1]",$arr_c_reps[1]);

				$c_shared_rep_rep_num = get_shared_rr_num ($arr_c_reps[0], $arr_c_reps[1]);
				
				$c_rep_0 = db_single_val("select Fullname as single_val from users where ID = '".get_userid_for_initials ($arr_c_reps[0])."'");
				$c_rep_1 = db_single_val("select Fullname as single_val from users where ID = '".get_userid_for_initials ($arr_c_reps[1])."'");
				
				$c_str_sales_rep = $c_rep_0." / ".$c_rep_1;
				xdebug("c_str_sales_rep",$c_str_sales_rep);
				
			}
			
			$email_log .= '<br><a class="bodytext12">'.
												"Client ID: <strong>".$test_var." ".$c_client_name_string."</strong><br>
												RR: <strong>".$c_str_sales_rep."</strong><br>
												Buy/Sell: <strong>".$arr_tradeware_buy_sell[$row_c["buy_sell"]]."</strong><br>
												Time: <strong>".date('H:i:sa',strtotime($row_c["manual_time"]))."</strong><br>
												Transaction ID: <strong>".$row_c["parent_id"].'</strong><br><br><hr><br><br>';

echo $email_log;

?>