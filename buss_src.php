<?
include('buss_src_inc_common.php');

function get_clnt_total_per_year ($year) {
		$qry = "select yrt_year, yrt_advisor_code, yrt_rr, yrt_commission from yrt_yearly_total_lookup where yrt_year = '".$year."'";
		$result = mysql_query($qry) or die(tdw_mysql_error($qry));
		$arr_year_total = array();
		while ( $row = mysql_fetch_array($result) )  {
		//echo $row["yrt_advisor_code"]." >> ".$row["yrt_rr"]." >> ".$row["yrt_commission"]."<br>";
			if ($arr_year_total[$row["yrt_advisor_code"]."^".$row["yrt_rr"]]) {
				$arr_year_total[$row["yrt_advisor_code"]."^".$row["yrt_rr"]] = $arr_year_total[$row["yrt_advisor_code"]] + $row["yrt_commission"];
			} else {
				$arr_year_total[$row["yrt_advisor_code"]."^".$row["yrt_rr"]] = $row["yrt_commission"];
			}
		}
		return $arr_year_total;
}


$arr_master_history = array();
$arr_2_years = array(substr($trade_date_to_process,0,4)-1,substr($trade_date_to_process,0,4)-2); //,substr($trade_date_to_process,0,4)-3
//show_array($arr_3_years);
foreach ($arr_2_years as $k=>$v) {
	$arr_master_history[$v] = get_clnt_total_per_year ($v);
}


//master array of client with rep number
//get all reps initials and rep numbers
$arr_reps_initials = array();
$qry = "SELECT distinct(clnt_rr1) as reps from int_clnt_clients where trim(clnt_rr1) != ''"; //, yrt_advisor_code, yrt_rr, yrt_commission from yrt_yearly_total_lookup where yrt_year = '".$year."'";
$result = mysql_query($qry) or die(tdw_mysql_error($qry));
$arr_prim_rep_year_total = 0;
while ( $row = mysql_fetch_array($result) )  {
	$arr_reps_initials[$row["reps"]] = $row["reps"];
}
$qry = "SELECT distinct(clnt_rr2) as reps from int_clnt_clients where trim(clnt_rr2) != ''"; //, yrt_advisor_code, yrt_rr, yrt_commission from yrt_yearly_total_lookup where yrt_year = '".$year."'";
$result = mysql_query($qry) or die(tdw_mysql_error($qry));
$arr_prim_rep_year_total = 0;
while ( $row = mysql_fetch_array($result) )  {
	$arr_reps_initials[$row["reps"]] = $row["reps"];
}

$str_initials = " ('". implode("','",$arr_reps_initials)  ."') ";  //  

$qry = "SELECT Initials, rr_num from users where trim(Initials) in ".$str_initials; //
$result = mysql_query($qry) or die(tdw_mysql_error($qry));
$arr_initials_rr_num = array();
while ( $row = mysql_fetch_array($result) )  {
	$arr_initials_rr_num[$row["Initials"]] = $row["rr_num"];
}

//shared reps number with initials
$qry = "SELECT a.srep_rrnum, trim(b.Initials) as initials FROM sls_sales_reps a left join users b on a.srep_user_id = b.ID where a.srep_isactive = 1  order by a.srep_rrnum";
$result = mysql_query($qry) or die(tdw_mysql_error($qry));
$arr_shrd_rr_num_initials = array();
$hold_val = "";
while ( $row = mysql_fetch_array($result) )  {
	if ($hold_val == "" || $hold_val != $row["srep_rrnum"]) {
		$arr_shrd_rr_num_initials[$row["srep_rrnum"]] = $row["initials"];
	} else {
		$arr_shrd_rr_num_initials[$row["srep_rrnum"]] = $arr_shrd_rr_num_initials[$row["srep_rrnum"]]."^".$row["initials"];
	}
	$hold_val = $row["srep_rrnum"]; 
}

$arr_shrd_rr_num_initials = array_flip($arr_shrd_rr_num_initials);
//show_array($arr_shrd_rr_num_initials);

//get all clients code and rep nums, shared situation


//get all clients code rrnum from initials
$qry = "SELECT clnt_code, trim(clnt_rr1) as rep1, trim(clnt_rr2) as rep2 from int_clnt_clients where  clnt_status = 'A' and clnt_isactive = 1 order by clnt_code"; //
$result = mysql_query($qry) or die(tdw_mysql_error($qry));
$arr_clnt_code_rr_num = array();
while ( $row = mysql_fetch_array($result) )  {
	if ($row["rep2"] == '') { //primary rep client
	$arr_clnt_code_rr_num[$row["clnt_code"]] = $arr_initials_rr_num[$row["rep1"]];
	} else {
		if ($arr_shrd_rr_num_initials[$row["rep1"]."^".$row["rep2"]] != "") {
			$arr_clnt_code_rr_num[$row["clnt_code"]] = $arr_shrd_rr_num_initials[$row["rep1"]."^".$row["rep2"]]; 
		} else {
			$arr_clnt_code_rr_num[$row["clnt_code"]] = $arr_shrd_rr_num_initials[$row["rep2"]."^".$row["rep1"]]; 
		}
	}
}

//show_array($arr_clnt_code_rr_num);

//if one initial then primary rep number
//if multiple initials then get from sales rep table

function yr_total_rep_prim($rr_num, $year, $arr_master_history, $sumtype = 'SUMMARY') { 
		$initials = db_single_val("select Initials as single_val from users where rr_num = '".$rr_num."'");
		$qry = "select clnt_code from int_clnt_clients where clnt_rr1 = '".$initials."' and trim(clnt_rr2) = ''"; 
		$result = mysql_query($qry) or die(tdw_mysql_error($qry));
		if ($sumtype == "SUMMARY") {
			$arr_prim_rep_year_total = array($year=>0);
			while ( $row = mysql_fetch_array($result) )  {
				$arr_prim_rep_year_total[$year] = $arr_prim_rep_year_total[$year] + $arr_master_history[$year][$row["clnt_code"]."^".$rr_num];
			}
		} else {
			while ( $row = mysql_fetch_array($result) )  {
				$arr_prim_rep_year_total[$row["clnt_code"]] = $arr_master_history[$year][$row["clnt_code"]."^".$rr_num]; 
			}
		}
			return $arr_prim_rep_year_total; 
}    

function yr_total_rep_shared($initials, $year, $arr_master_history, $sumtype = 'SUMMARY') { 
		$qry = "select clnt_code from int_clnt_clients where ( (trim(clnt_rr1) = '".$initials."' and trim(clnt_rr2) != '') or trim(clnt_rr2) = '".$initials."' ) and clnt_status ='A'"; 
		$result = mysql_query($qry) or die(tdw_mysql_error($qry));
		if ($sumtype == "SUMMARY") {
			$arr_shrd_rep_year_total = array($year=>0);
			while ( $row = mysql_fetch_array($result) )  {
				$arr_shrd_rep_year_total[$year] = $arr_shrd_rep_year_total[$year] + $arr_master_history[$year][$row["clnt_code"]."^".$arr_clnt_code_rr_num[$row["clnt_code"]]];
			}
		} else {
			while ( $row = mysql_fetch_array($result) )  {
				$arr_shrd_rep_year_total[$row["clnt_code"]] = $arr_master_history[$year][$row["clnt_code"]."^".$arr_clnt_code_rr_num[$row["clnt_code"]]];
			}
		}
		return $arr_shrd_rep_year_total; 
}    

//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
//show_array($_POST);
if ($_POST) {
  $trade_date_to_process = format_date_mdy_to_ymd($datefilterval);
} else {
  $trade_date_to_process = previous_business_day();
}

$arr_master_history = array();
$arr_3_years = array(substr($trade_date_to_process,0,4)-1,substr($trade_date_to_process,0,4)-2,substr($trade_date_to_process,0,4)-3);
//show_array($arr_3_years);
foreach ($arr_3_years as $k=>$v) {
	$arr_master_history[$v] = get_clnt_total_per_year ($v);
}
//show_array($arr_master_history);

/*if ($user_id != 79) {
	echo "<strong>Module in the process of being updated. You will be notified when this module is available.</strong>";
	exit;
}*/

tsp(100,"Business Summary : As of ".format_date_ymd_to_mdy($trade_date_to_process));
?>
        <!-- START TABLE 3 -->
          <table width="100%" cellpadding="1", cellspacing="0">
            <tr>
              <td valign="top"> 
                <!-- START TABLE 4 -->
                <!-- class="tablewithdata" -->
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                              <tr>
                                <form name="selectionfilter" id="idselectionfilter" action="" method="post">
                                <td width="5">&nbsp;</td>
                                <td width="100"><font class="ilt">As of Date:</font></td>
                                <td width="150">
                                  <SCRIPT LANGUAGE="JavaScript" SRC="includes/calendarpopup/CalendarPopup.js"></SCRIPT>
                                  <SCRIPT LANGUAGE="JavaScript">document.write(getCalendarStyles());</SCRIPT>
                                  <SCRIPT LANGUAGE="JavaScript">
                                  var cal = new CalendarPopup("divfrom");
                                  cal.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
                                  </SCRIPT>                                
                                    <input type="text" id="iddatefilterval" class="Text" name="datefilterval" size="12" maxlength="12" value="<?=format_date_ymd_to_mdy($trade_date_to_process)?>">
                                    <A HREF="#" onClick="cal.select(document.forms['selectionfilter'].datefilterval,'anchor1','MM/dd/yyyy'); return false;" NAME="anchor1" ID="anchor1"><img src="images/lf_v1/sel_date.png" border="0"></A>
                                    <input type="image" src="images/lf_v1/form_submit.png">
                                    </form>
                                </td>
                                <td width="10">&nbsp;</td>
                                <td width="200" valign="top"><a href="buss_src_excel.php?xl=<?=md5(rand(1111,9999))?>^^<?=$trade_date_to_process?>" target="_blank"><img src="images/lf_v1/exp2excel.png" border="0"></a></td>
                                <td>&nbsp;</td>
                              </tr>
                            </table>

                    <table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
                      <tr>
                        <td valign="top">    
                        <table width="100%" border="0" cellspacing="1" cellpadding="0">
                          <tr> 
                            <td bgcolor="#ffffff" width="270"><a class="ghm">&nbsp;&nbsp;"Brokerage Month Basis"</a></td>
                            <td bgcolor="#222222" colspan="4" align="center"><a class="tblhead_a">C O M M I S S I O N S</a></td>
                            <td bgcolor="#888888" colspan="3" align="center"><a class="tblhead_a">C H E C K S</a></td>
                            <td bgcolor="#222222" colspan="3" align="center"><a class="tblhead_a">T O T A L</a></td>
                            <td bgcolor="#222222" align="center"><a class="tblhead_a">YEAR</a></td>
                            <td bgcolor="#222222" align="center"><a class="tblhead_a">YEAR</a></td>
                            <td bgcolor="#222222">&nbsp;</td>
                          <tr bgcolor="#333333"> 
                            <td width="270"><a class="tblhead_a">&nbsp;&nbsp;&nbsp;&nbsp;ADVISOR / CLIENT (PRIMARY)</a></td>
                            <td width="70" align="right"><a class="tblhead_a"><?=substr(format_date_ymd_to_mdy($trade_date_to_process),0,5)?>&nbsp;&nbsp;&nbsp;&nbsp;</a></td>
                            <td width="100" align="right"><a class="tblhead_a">MTD&nbsp;&nbsp;</a></td>
                            <td width="100" align="right"><a class="tblhead_a">QTD&nbsp;&nbsp;</a></td>
                            <td width="100" align="right"><a class="tblhead_a">YTD&nbsp;&nbsp;</a></td>
                            <td width="70" bgcolor="#888888" align="right"><a class="tblhead_a">MTD&nbsp;&nbsp;</a></td>
                            <td width="70" bgcolor="#888888" align="right"><a class="tblhead_a">QTD&nbsp;&nbsp;</a></td>
                            <td width="70" bgcolor="#888888" align="right"><a class="tblhead_a">YTD&nbsp;&nbsp;</a></td>
                            <td width="70" bgcolor="#222222" align="right"><a class="tblhead_a">MTD&nbsp;&nbsp;</a></td>
                            <td width="70" bgcolor="#555555" align="right"><a class="tblhead_a">QTD&nbsp;&nbsp;</a></td>
                            <td width="80" bgcolor="#888888" align="right"><a class="tblhead_a">YTD&nbsp;&nbsp;</a></td>
                            <td width="80" bgcolor="#222222" align="center"><a class="tblhead_a"><?=substr($trade_date_to_process,0,4)-1?></a></td>
                            <td width="80" bgcolor="#222222" align="center"><a class="tblhead_a"><?=substr($trade_date_to_process,0,4)-2?></a></td>
                            <td>&nbsp;</td>
                          </tr>
                        </table>
                        </td>
                      </tr>
                    </table>
                        <?
                        //set the running totals for this section
                        $running_total_comm_day = 0;
                        $running_total_comm_mtd  = 0;
                        $running_total_comm_qtd  = 0;
                        $running_total_comm_ytd  = 0;
                        $running_total_chek_mtd  = 0;
                        $running_total_chek_qtd  = 0;
                        $running_total_chek_ytd  = 0;
                        $running_total_checksum = 0;
                        ?>

                        <?
                        //get the names of registered reps which have active trades in THIS YEAR and have it ordered by lastname
                        $qry_get_reps = "SELECT
                                            a.ID, a.rr_num, concat(a.Lastname, ', ', a. Firstname) as rep_name, b.trad_rr 
                                            from users a, mry_comm_rr_trades b
                                          WHERE a.rr_num = b.trad_rr
                                          AND b.trad_rr like '0%'
                                          AND b.trad_trade_date > '".substr($trade_date_to_process,0,4)."-01-01'
                                          AND b.trad_is_cancelled = 0 
                                          GROUP BY b.trad_rr
                                          ORDER BY a.Lastname";
													//xdebug("qry_get_reps",$qry_get_reps);
                          $result_get_reps = mysql_query($qry_get_reps) or die (tdw_mysql_error($qry_get_reps));
                          while($row_get_reps = mysql_fetch_array($result_get_reps))
                          {
                          $mk_id = md5(rand(1000000000,9999999999));
                          //for tradesfor shared rep, do a reverse lookup in the users table to get the id and then the shared reps
                          $rep_to_process = $row_get_reps["rr_num"];
                          $srep_user_id = $row_get_reps["ID"];                           
                          $show_rr = $rep_to_process;

                          $tmp_rep = $rep_to_process;
													
                          include('buss_src_inc_each_rep.php');
                          include('buss_src_inc_each_rep_shrd.php');
                          
                          
                          
                          if ($arr_ytd_comm[$show_rr]+$arr_ytd_check[$show_rr] > 0) {
                          ?>
                            <table width="100%">
                              <tr>
                                <td class="name_heading"><?=$row_get_reps["rep_name"]?></td>
                              </tr>
                            </table>
                          <?
                          }
                          ?>

                            <?
                            if ($arr_ytd_comm[$show_rr]+$arr_ytd_check[$show_rr] > 0) {
														
														//get the totals for relevant clients for last year and year before that.
														$arr_detail_ly1 = yr_total_rep_prim($show_rr, substr($trade_date_to_process,0,4)-1, $arr_master_history, "DETAIL");
														$arr_detail_ly2 = yr_total_rep_prim($show_rr, substr($trade_date_to_process,0,4)-2, $arr_master_history, "DETAIL");
														$total_ly1 = 0;
														$total_ly2 = 0;
														foreach($arr_clnt_for_rr as $k=>$v) {
															$total_ly1 = $total_ly1 + $arr_detail_ly1[$v];
															$total_ly2 = $total_ly2 + $arr_detail_ly2[$v];
														}
																												
                            ?>
                            <table width="100%" border="0" cellspacing="1" cellpadding="0" >  <!--class="tbl_test" -->
                              <tr class="trlight" onDblClick="javascript:populate_div_primary('<?=$mk_id?>','<?=$show_rr?>','<?=$trade_date_to_process?>')"> 
                                <td width="270" valign="left">&nbsp;
                                <a href="javascript:populate_div_primary('<?=$mk_id?>','<?=$show_rr?>','<?=$trade_date_to_process?>')">
                                <img id="img<?=$mk_id?>" src="images/lf_v1/expand.png" border="0"></a> 
                                <?=$row_get_reps["rep_name"]?> (Acct Rep: <?=$show_rr?>)</td>
                                <td width="70"  align="right"><?=show_numbers($arr_day_comm[$show_rr])?>&nbsp;&nbsp;</td>
                                <td width="100" align="right"><?=show_numbers($arr_mtd_comm[$show_rr])?>&nbsp;&nbsp;</td>
                                <td width="100" align="right"><?=show_numbers($arr_qtd_comm[$show_rr])?>&nbsp;&nbsp;</td>
                                <td width="100" align="right"><?=show_numbers($arr_ytd_comm[$show_rr])?>&nbsp;&nbsp;</td>
                                <td width="70" align="right"><?=show_numbers($arr_mtd_check[$show_rr])?>&nbsp;&nbsp;</td>
                                <td width="70" align="right"><?=show_numbers($arr_qtd_check[$show_rr])?>&nbsp;&nbsp;</td>
                                <td width="70" align="right"><?=show_numbers($arr_ytd_check[$show_rr])?>&nbsp;&nbsp;</td>
                                <td width="70" align="right"><?=show_numbers($arr_mtd_comm[$show_rr]+$arr_mtd_check[$show_rr])?>&nbsp;&nbsp;</td>
                                <td width="70" align="right"><?=show_numbers($arr_qtd_comm[$show_rr]+$arr_qtd_check[$show_rr])?>&nbsp;&nbsp;</td>
                                <td width="80" align="right"><?=show_numbers($arr_ytd_comm[$show_rr]+$arr_ytd_check[$show_rr])?>&nbsp;&nbsp;</td>
                                <td width="80" align="right"><?=show_numbers($total_ly1)?>&nbsp;&nbsp;</td>
                                <td width="80" align="right"><?=show_numbers($total_ly2)?>&nbsp;&nbsp;</td> 
                                <td>&nbsp;</td>
                              </tr>
                            </table>
                            <div name="div_<?=$mk_id?>" id="div_<?=$mk_id?>"></div>
                            <?
                            $running_total_comm_day  = $running_total_comm_day + $arr_day_comm[$show_rr];
                            $running_total_comm_mtd  = $running_total_comm_mtd + $arr_mtd_comm[$show_rr];
                            $running_total_comm_qtd  = $running_total_comm_qtd + $arr_qtd_comm[$show_rr];
                            $running_total_comm_ytd  = $running_total_comm_ytd + $arr_ytd_comm[$show_rr];
                            $running_total_chek_mtd  = $running_total_chek_mtd + $arr_mtd_check[$show_rr];
                            $running_total_chek_qtd  = $running_total_chek_qtd + $arr_qtd_check[$show_rr];
                            $running_total_chek_ytd  = $running_total_chek_ytd + $arr_ytd_check[$show_rr];
                            }
														
														//xdebug("running_total_comm_day",$running_total_comm_day);

                            //_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@
                            //get shared rep data (sls_sales_reps)
                            //fields are  srep_user_id  srep_rrnum  srep_percent
                            
                            //initialize running total for shared rep
                              $shrd_running_total_comm = 0;
                              $shrd_running_total_mtd  = 0;
                              $shrd_running_total_qtd  = 0;
                              $shrd_running_total_ytd  = 0;
                              
                              $show_row = 1;  
  

                            if ($show_row == 1) {
                          
                            $mk_sid = md5(rand(1000000000,9999999999));
                            
                                //echo "show row = 1...<br>";
                                if ($arr_ytd_comm_shrd[$show_rr]+$arr_ytd_check_shrd[$show_rr] > 0) {
                                  //echo "and sum of stuff is > 0<br>";
																														
																	//get the totals for relevant shared clients for last year and year before that.
																	$hold_usr_initials = db_single_val("select Initials as single_val from users where rr_num = '".$show_rr."'");
																	$arr_detail_ly1 = yr_total_rep_shared($hold_usr_initials, substr($trade_date_to_process,0,4)-1, $arr_master_history, "DETAIL");
																	$arr_detail_ly2 = yr_total_rep_shared($hold_usr_initials, substr($trade_date_to_process,0,4)-2, $arr_master_history, "DETAIL");
																	$total_ly1 = 0;
																	$total_ly2 = 0;
																	foreach($arr_rel_shrd_clnts as $k=>$v) {
																		$total_ly1 = $total_ly1 + $arr_detail_ly1[$v];
																		$total_ly2 = $total_ly2 + $arr_detail_ly2[$v];
																	}
	
																	
																	
                            ?>
                                  <table width="100%" border="0" cellspacing="1" cellpadding="0">
                                    <tr class="trlight" onDblClick="javascript:populate_div('<?=$mk_sid?>','<?=$srep_user_id?>','<?=$trade_date_to_process?>')"> 
                                      <td width="270" valign="left">&nbsp;
                                      <a href="javascript:populate_div('<?=$mk_sid?>','<?=$srep_user_id?>','<?=$trade_date_to_process?>')">
                                      <img id="img<?=$mk_sid?>" src="images/lf_v1/expand.png" border="0"></a> 
                                      <?=$row_get_reps["rep_name"]?> (Shared)</td>
                                      <td width="70"  align="right"><?=show_numbers($arr_day_comm_shrd[$show_rr])?>&nbsp;&nbsp;</td>
                                      <td width="100" align="right"><?=show_numbers($arr_mtd_comm_shrd[$show_rr])?>&nbsp;&nbsp;</td>
                                      <td width="100" align="right"><?=show_numbers($arr_qtd_comm_shrd[$show_rr])?>&nbsp;&nbsp;</td>
                                      <td width="100" align="right"><?=show_numbers($arr_ytd_comm_shrd[$show_rr])?>&nbsp;&nbsp;</td>
                                      <td width="70" align="right"><?=show_numbers($arr_mtd_check_shrd[$show_rr])?>&nbsp;&nbsp;</td>
                                      <td width="70" align="right"><?=show_numbers($arr_qtd_check_shrd[$show_rr])?>&nbsp;&nbsp;</td>
                                      <td width="70" align="right"><?=show_numbers($arr_ytd_check_shrd[$show_rr])?>&nbsp;&nbsp;</td>
                                      <td width="70" align="right"><?=show_numbers($arr_mtd_comm_shrd[$show_rr]+$arr_mtd_check_shrd[$show_rr])?>&nbsp;&nbsp;</td>
                                      <td width="70" align="right"><?=show_numbers($arr_qtd_comm_shrd[$show_rr]+$arr_qtd_check_shrd[$show_rr])?>&nbsp;&nbsp;</td>
                                      <td width="80" align="right"><?=show_numbers($arr_ytd_comm_shrd[$show_rr]+$arr_ytd_check_shrd[$show_rr])?>&nbsp;&nbsp;</td>
                                      <td width="80" align="right"><?=show_numbers($total_ly1)?>&nbsp;&nbsp;</td>
                                			<td width="80" align="right"><?=show_numbers($total_ly2)?>&nbsp;&nbsp;</td> 
                                      <td>&nbsp;</td>
                                    </tr>
                                  </table>
                                  <div name="div_<?=$mk_sid?>" id="div_<?=$mk_sid?>"></div>
                            <?
                                $running_total_comm_day  = $running_total_comm_day + $arr_day_comm_shrd[$show_rr]/2;
                                $running_total_comm_mtd  = $running_total_comm_mtd + $arr_mtd_comm_shrd[$show_rr]/2;
                                $running_total_comm_qtd  = $running_total_comm_qtd + $arr_qtd_comm_shrd[$show_rr]/2;
                                $running_total_comm_ytd  = $running_total_comm_ytd + $arr_ytd_comm_shrd[$show_rr]/2;
                                $running_total_chek_mtd  = $running_total_chek_mtd + $arr_mtd_check_shrd[$show_rr]/2;
                                $running_total_chek_qtd  = $running_total_chek_qtd + $arr_qtd_check_shrd[$show_rr]/2;
                                $running_total_chek_ytd  = $running_total_chek_ytd + $arr_ytd_check_shrd[$show_rr]/2;
                                $running_total_checksum = $running_total_checksum + $arr_mtd_comm_shrd[$show_rr];
                                }
                            }
                            //_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@
														//xdebug("running_total_comm_day",$running_total_comm_day);

                          } //end while looking for reps
                        
                        $running_total_comm_day_calc = db_single_val("SELECT sum( trad_commission ) as single_val 
                                                                       FROM mry_comm_rr_trades 
                                                                       WHERE trad_trade_date = '".$trade_date_to_process."'
                                                                       AND trad_is_cancelled = 0");
                        
                        $running_total_comm_mtd_calc = db_single_val("SELECT sum( trad_commission ) as single_val 
                                                                       FROM mry_comm_rr_trades 
                                                                       WHERE trad_trade_date between '".$global_qry_date_start_mtd."' and '".$trade_date_to_process."'
                                                                       AND trad_is_cancelled = 0");
                        
                        //echo $global_qtr_start_date;
                        $running_total_comm_qtd_calc = db_single_val("SELECT sum( trad_commission ) as single_val 
                                                                       FROM mry_comm_rr_trades 
                                                                       WHERE trad_trade_date between '".$global_qtr_start_date."' and '".$trade_date_to_process."'
                                                                       AND trad_is_cancelled =0");
                        
                        $running_total_comm_ytd_calc = db_single_val("SELECT sum( trad_commission ) as single_val 
                                                                       FROM mry_comm_rr_trades 
                                                                       WHERE trad_trade_date between '".$global_year_start_date."' and '".$trade_date_to_process."'
                                                                       AND trad_is_cancelled = 0");
                        
                        $running_total_chek_mtd_calc = db_single_val("SELECT sum(chek_amount) as single_val
                                                                        FROM chk_chek_payments_etc
                                                                        WHERE chek_date between '".$global_chk_qry_date_start_mtd."' and '".$trade_date_to_process."'
                                                                          AND chek_type = 1
                                                                          AND chek_isactive = 1");
                                                                       //AND a.chek_reps_and like '%".$user_initials."%'
												/*echo "SELECT sum(chek_amount) as single_val
                                                                        FROM chk_chek_payments_etc
                                                                        WHERE chek_date between '".$global_chk_qry_date_start_mtd."' and '".$trade_date_to_process."'
                                                                          AND chek_type = 1
                                                                          AND chek_isactive = 1";*/
												//xdebug("running_total_chek_mtd_calc",$running_total_chek_mtd_calc);																							 

                        $running_total_comm_mtd_calc = db_single_val("SELECT sum( trad_commission ) as single_val 
                                                                       FROM mry_comm_rr_trades 
                                                                       WHERE trad_trade_date between '".$global_qry_date_start_mtd."' and '".$trade_date_to_process."'
                                                                       AND trad_is_cancelled = 0");
                        
                        $running_total_comm_mtd_calc = db_single_val("SELECT sum( trad_commission ) as single_val 
                                                                       FROM mry_comm_rr_trades 
                                                                       WHERE trad_trade_date between '".$global_qry_date_start_mtd."' and '".$trade_date_to_process."'
                                                                       AND trad_is_cancelled = 0");
                        ?>
                          <hr width="100%" size="2" noshade color="#660000">
                           <table width="100%" border="0" cellspacing="1" cellpadding="0">
                           <tr class="display_totals"> 
                            <td width="270"><div align="left">&nbsp;&nbsp;TOTALS:</div></td>
                            <td width="70" align="right"><?=number_format($running_total_comm_day_calc,0,'.',",")?>&nbsp;&nbsp;</td>
                            <td width="100" align="right"><?=number_format($running_total_comm_mtd_calc,0,'.',",")?>&nbsp;&nbsp;</td>
                            <td width="100" align="right"><?=number_format($running_total_comm_qtd_calc,0,'.',",")?>&nbsp;&nbsp;</td>
                            <td width="100" align="right"><?=number_format($running_total_comm_ytd_calc,0,'.',",")?>&nbsp;&nbsp;</td>
<!--                            <td width="70" align="right"><?=number_format($running_total_chek_mtd,0,'.',",")?>&nbsp;&nbsp;</td>
-->                            
														<td width="70" align="right"><?=number_format($running_total_chek_mtd,0,'.',",")?>&nbsp;&nbsp;</td>
                            <td width="70" align="right"><?=number_format($running_total_chek_qtd,0,'.',",")?>&nbsp;&nbsp;</td>
                            <td width="70" align="right"><?=number_format($running_total_chek_ytd,0,'.',",")?>&nbsp;&nbsp;</td>
                            <td width="70" align="right"><?=number_format(($running_total_comm_mtd+$running_total_chek_mtd),0,'.',",")?>&nbsp;</td>
                            <td width="70" align="right"><?=number_format(($running_total_comm_qtd+$running_total_chek_qtd),0,'.',",")?>&nbsp;</td>
                            <td width="80" align="right"><?=number_format(($running_total_comm_ytd+$running_total_chek_ytd),0,'.',",")?>&nbsp;</td>
                            <td width="80" align="right">&nbsp;</td>
                            <td width="50" align="right">&nbsp;</td>
                            <td>&nbsp;</td>
                          </tr>
                        </table>
                  <!-- END TABLE 4 -->
                </td>
              </tr>
            </table>
            <!-- END TABLE 3 -->
<?
tep();
?>
<DIV ID="divfrom" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>
