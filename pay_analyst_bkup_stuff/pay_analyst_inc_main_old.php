<?
function create_arr ($q, $i=1) {
  $arr_created = array();
	$result = mysql_query($q) or die(tdw_mysql_error($q));
	if ($i == 1) {
		while ( $row = mysql_fetch_array($result) )
		{
			$arr_created[] = $row["v"];
		}
	} else {
		while ( $row = mysql_fetch_array($result) )
		{
			$arr_created[$row["k"]] = $row["v"];
		}
	}
	return $arr_created;
}

//Create Lookup Array of Client Code / Client Name

	$qry_clients = "select * from int_clnt_clients";
	$result_clients = mysql_query($qry_clients) or die (tdw_mysql_error($qry_clients));
	$arr_clients = array();
	while ( $row_clients = mysql_fetch_array($result_clients) ) 
	{
		$arr_clients[$row_clients["clnt_code"]] = trim($row_clients["clnt_name"]);
	}
	
	
	//temporary MUST CHANGE THIS LATER
	function look_up_client($clnt) {
		global $arr_clients;
		if ($arr_clients[$clnt] == '') {
		   return $clnt;
		} else {
		   return $arr_clients[$clnt];
		}
	}


//Create Array of all clients to show here
$arr_clnt_for_rr = array();
$qry_clnt_for_rr = "SELECT distinct(trad_advisor_code) 
										FROM mry_comm_rr_trades 
										WHERE trad_trade_date <= '".$trade_date_to_process."' 
											and trad_is_cancelled = 0
											and trad_rr = '".$rep_to_process."' 
										order by trad_advisor_code limit 20"; 

$result_clnt_for_rr = mysql_query($qry_clnt_for_rr) or die (tdw_mysql_error($qry_clnt_for_rr));
while ( $row_clnt_for_rr = mysql_fetch_array($result_clnt_for_rr) ) 
{
	$arr_clnt_for_rr[$row_clnt_for_rr["trad_advisor_code"]] = $row_clnt_for_rr["trad_advisor_code"];
}

//show_array($arr_clnt_for_rr);
//get initials for the user
$user_initials = db_single_val("select Initials as single_val from users where rr_num = '".$rep_to_process."'");

$qry_clnt_for_rr = "SELECT distinct(a.chek_advisor) as chek_advisor
									FROM chk_chek_payments_etc a
                   left join int_clnt_clients b on a.chek_advisor = b.clnt_code 
								  WHERE a.chek_date <= '".$trade_date_to_process."' 
									  AND a.chek_isactive = 1
										AND (b.clnt_rr1 = '".$user_initials."' AND (b.clnt_rr2 = '' or b.clnt_rr2 is NULL))
								 ORDER BY a.chek_advisor";
$result_clnt_for_rr = mysql_query($qry_clnt_for_rr) or die (tdw_mysql_error($qry_clnt_for_rr));
while ( $row_clnt_for_rr = mysql_fetch_array($result_clnt_for_rr) ) 
{
	$arr_clnt_for_rr[$row_clnt_for_rr["chek_advisor"]] = $row_clnt_for_rr["chek_advisor"];
}

//show_array($arr_clnt_for_rr);

//also check client history table for information
$qry_clnt_for_rr = "SELECT distinct(a.chek_advisor) as chek_advisor
									FROM chk_chek_payments_etc a
                   left join int_clnt_clients_history b on a.chek_advisor = b.clnt_code 
								  WHERE a.chek_date <= '".$trade_date_to_process."' 
									  AND a.chek_isactive = 1
										AND (b.clnt_rr1 = '".$user_initials."' AND (b.clnt_rr2 = '' or b.clnt_rr2 is NULL))
								 ORDER BY a.chek_advisor";
$result_clnt_for_rr = mysql_query($qry_clnt_for_rr) or die (tdw_mysql_error($qry_clnt_for_rr));
while ( $row_clnt_for_rr = mysql_fetch_array($result_clnt_for_rr) ) 
{
	$arr_clnt_for_rr[$row_clnt_for_rr["chek_advisor"]] = $row_clnt_for_rr["chek_advisor"];
}

//show_array($arr_clnt_for_rr);

//+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+
//get the client names sorted
$arr_sorted_clients = array();
/*

*/

foreach($arr_clnt_for_rr as $code=>$codeval) {
	$arr_sorted_clients[$code] = look_up_client($code);
}

$arr_clnt_for_rr = $arr_sorted_clients;
asort($arr_clnt_for_rr);
//+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+--+

//show_array($arr_clnt_for_rr);
?>

<script language="javascript">

	function check_frm_criteria() {
	//alert("Are you sure?");
	return true;
	}

      /*
			37:"Arrow Left";
			38:"Arrow Up";
      39:"Arrow Right";
      40:"Arrow Down";
			*/
			
	function xlmove(evt, itemid){
	//alert(itemid);
	var k=evt.keyCode;
	 if (k==40) {
		var rc = itemid.split("|");
		var nextid;
		nextid = (parseInt(rc[0]) + 1) + "|" + rc[1];

			if (document.getElementById(nextid)) {
				document.getElementById(nextid).focus();
				document.getElementById(nextid).select();
			} else {
				nextid = 1 + "|" + rc[1];
				document.getElementById(nextid).focus();
				document.getElementById(nextid).select();
			}

	 } else if (k==38) { 
		var rc = itemid.split("|");
		var nextid;
		nextid = (parseInt(rc[0]) - 1) + "|" + rc[1];

			if (document.getElementById(nextid)) {
				document.getElementById(nextid).focus();
				document.getElementById(nextid).select();
			} else {
				nextid = 1 + "|" + rc[1];
				document.getElementById(nextid).focus();
				document.getElementById(nextid).select();
			}

	 } else if (k==39) { 
		var rc = itemid.split("|");
		var nextid;
		nextid = rc[0] + "|" + (parseInt(rc[1]) + 1);

			if (document.getElementById(nextid)) {
				document.getElementById(nextid).focus();
				document.getElementById(nextid).select();
			} else {
				nextid = 1 + "|" + rc[1];
				document.getElementById(nextid).focus();
				document.getElementById(nextid).select();
			}

	 } else if (k==37) { 
		var rc = itemid.split("|");
		var nextid;
		nextid = rc[0] + "|" + (parseInt(rc[1]) - 1);

			if (document.getElementById(nextid)) {
				document.getElementById(nextid).focus();
				document.getElementById(nextid).select();
			} else {
				nextid = 1 + "|" + rc[1];
				document.getElementById(nextid).focus();
				document.getElementById(nextid).select();
			}

	 } else if (k==13) { 
		var rc = itemid.split("|");
		var nextid;
		nextid = (parseInt(rc[0]) + 1) + "|" + rc[1];

			if (document.getElementById(nextid)) {
				document.getElementById(nextid).focus();
				document.getElementById(nextid).select();
			} else {
				nextid = 1 + "|" + rc[1];
				document.getElementById(nextid).focus();
				document.getElementById(nextid).select();
			}

	 }
	return k!=13;
	}

	function xlrecalc(itemid){

		var rcount = parseInt(document.getElementById('id_rcount').value);
		var ccount = parseInt(document.getElementById('id_ccount').value);
		var rc = itemid.split("|");
		var crow, ccol, ctotal, currid, nextid, totalid, totalanalystid;
		crow = parseInt(rc[0]);
		ccol = parseInt(rc[1]);
		ctotal = 0;
		for (i=1; i < rcount + 1 ; i++) {
			nextid = i+"|"+ccol;
			if (document.getElementById(nextid)) {
			  ctotal = ctotal + parseInt(document.getElementById(nextid).value);
			}
		}
	
		//totals for the column, must add to 100%
		totalid = "total|"+ ccol;
		if (isNaN(ctotal)) ctotal = 'Error';
		
		document.getElementById(totalid).value = ctotal;
		if (ctotal == 100) {
			document.getElementById(totalid).className  = "valgreen";
		} else {
			document.getElementById(totalid).className  = "valred";
		}

		//put total for analyst.
		id_totalanalyst = "at|"+ crow;
		id_coltotalval = "tot|" + ccol;


		var val_analyst_total=0;
		for (k=1; k<(ccount+1); k++) {
			id_cur_item = crow + "|" + k;
			//alert(id_cur_item);
			val_analyst_total = val_analyst_total + ( ( parseInt(document.getElementById(id_cur_item).value) / 100 ) * parseFloat(document.getElementById(id_coltotalval).innerHTML.replace(",","")) );
		}

		if (isNaN(val_analyst_total)) val_analyst_total = 'Error';  //on error the previous value is preserved.
		document.getElementById(id_totalanalyst).innerHTML  = addCommas(val_analyst_total.toFixed(2));

		cursat_id = "sat|" + crow;
		document.getElementById(cursat_id).innerHTML  = addCommas(val_analyst_total.toFixed(2));
		
		//get the total as a percentage.
		gtotal_val =  parseFloat(document.getElementById('id_gtotal').value);
		percent_of_total = ((val_analyst_total / gtotal_val)*100).toFixed(2);
		idval_sap = 'sap|' + crow;
		document.getElementById(idval_sap).innerHTML  = percent_of_total;
		

		//put the calc val
		curval_id = "curnum|" + itemid;
		curval_val = addCommas((( parseInt(document.getElementById(itemid).value) / 100 ) * parseFloat(document.getElementById(id_coltotalval).innerHTML.replace(",",""))).toFixed(2));
		//alert(curval_id + "..." + curval_val);
		document.getElementById(curval_id).innerHTML  = curval_val;
		
		//populate X
		var val_summ_analyst_total=0;
		for (k=1; k<(rcount+1); k++) { //sat|8
			id_cur_item = "sat|" + k;
			val_summ_analyst_total = val_summ_analyst_total + parseFloat(document.getElementById(id_cur_item).innerHTML.replace(",",""));
			//alert(val_summ_analyst_total);
		}
		document.getElementById('sum_sat_total').innerHTML  = val_summ_analyst_total;
				
		//populate Y
		var val_summ_percent_total=0;
		for (k=1; k<(rcount+1); k++) { //sat|8
			id_cur_item = "sap|" + k;
			val_summ_percent_total = val_summ_percent_total + parseFloat(document.getElementById(id_cur_item).innerHTML.replace(",",""));
			//alert(val_summ_analyst_total);
		}
		document.getElementById('sum_sap_total').innerHTML  = val_summ_percent_total.toFixed(0);
		
		return false;
	}

function addCommas(nStr)
{
	nStr += '';
	x = nStr.split('.');
	x1 = x[0];
	x2 = x.length > 1 ? '.' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1)) {
		x1 = x1.replace(rgx, '$1' + ',' + '$2');
	}
	return x1 + x2;
}

</script>

<style>
<!--
.vert_1 {
	writing-mode: tb-rl;
	filter: flipv fliph;
	border-collapse: collapse;
	border: .05em solid #ccc;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10px;
	font-weight: bold;
	color: 666;
}

.text_xl {
	border-collapse: collapse;
	border-top-style: none;
	border-right-style: none;
	border-bottom-style: none;
	border-left-style: none;
}

.tbl_xl {
	border-collapse: collapse; 
	border: .05em solid #ccc; 
}

td.tdx { 
	border-collapse: collapse; 
	border: .05em solid #ccc; 
} 
.valred {
color:#FF0000;
}
.valgreen {
	color:#009900;
	font-weight: bold;
}
.pplname{
	font-family: Arial, Helvetica, sans-serif;
	font-size: 11px;
	font-weight: bold;
	color: 666;
}
input.text{
	font-family: Arial, Helvetica, sans-serif;
	font-size: 11px;
	font-weight: bold;
	color: 666;
}
.num_1{
	font-family: Arial, Helvetica, sans-serif;
	font-size: 11px;
	font-weight: bold;
	color: 666;
	text-align: right;
}
#scrollGrid_1 {
	width: 800px;
	height: 500px;
	padding: 1px;
	border: 0px solid #cc0000;
	overflow: scroll; 
}
-->
</style> 
