<?
//error_reporting(E_ALL);

include('pay_analyst_js.php');
include('pay_analyst_css.php');
?>

<script language="JavaScript" src="includes/js/ajax_tbx.js"></script>
<script language ="Javascript">
<!--

    function procx(str) {
        var arr_str = new Array();
        arr_str = str.split("^");
        if (arr_str[0] == "b") {
            newStr = "You have chosen to UNLOCK Analyst Allocations for " + arr_str[5] + "\n" + "for Q" + arr_str[2] + " " + arr_str[3] + ".";
        } else {
            newStr = "You have chosen to SEND EMAIL REMINDER to " + arr_str[5] + "\n" + "for completion of Analyst Allocations for period Q" + arr_str[2] + " " + arr_str[3] + ".";
        }
        input_box = confirm(newStr + "\n\n" + "Are you sure?");
        if (input_box == true) {
            AjaxRequest.get(
                    {
                        'url': 'pay_analyst_summ_ajax.php?str=' + str
                        , 'onSuccess': function(req) {
                            parse_req(req.responseText);
                        }
                        , 'onError': function(req) {
                            document.getElementById('notify').innerHTML = 'Program Error! Please contact Technical Support.';
                        }
                    }
            );
        } else {
            return false;
        }
    }

    function parse_req(response) {
        var arr_response = new Array();
        arr_response = response.split("^");
        //scount is [2] passed back
        if (arr_response[0] == "c") {
            document.getElementById('notify').innerHTML = arr_response[1];
            document.getElementById('notify').className = "notify_x";
            var citemid = 'c' + arr_response[2];
            document.getElementById(citemid).innerHTML = "&nbsp;&nbsp;&nbsp;Email Sent";
        } else {
            document.getElementById('notify').innerHTML = arr_response[1];
            document.getElementById('notify').className = "notify_x";
            var bitemid = 'b' + arr_response[2];
            document.getElementById(bitemid).innerHTML = "&nbsp;&nbsp;&nbsp;Unlocked";
            var aitemid = 'a' + arr_response[2];
            document.getElementById(aitemid).innerHTML = "&nbsp;&nbsp;&nbsp;<strong><font color='red'>No</font></strong>";

        }
        //alert($response);
    }

    function noenter() {
    return !(window.event && window.event.keyCode == 13);}
-->
</script>

<?
tsp(100, "Payout Reconciliation Summary and Reporting");
?>
<style type="text/css">
    <!--
    .notify_x {
        font-family: verdana;
        font-size: 12px;
        font-weight: bold;
        color: #009900;
        text-decoration: none;
        background-color: #E6FFE6;
        border-top-width: 1px;
        border-bottom-width: 1px;
        border-top-style: solid;
        border-right-style: none;
        border-bottom-style: solid;
        border-left-style: none;
        border-top-color: #00CC33;
        border-bottom-color: #00CC33;
    }
    -->
</style>

<table width="100%" border="0" cellpadding="4" cellspacing="0"> 
    <tr>
        <td>
            <!-- Top Menu -->		
            <form name="frm_criteria" action="<?= $PHP_SELF ?>" method="get"><!-- onsubmit="return check_frm_criteria()"-->
                <select name="sel_qtr">
                    <option value="">Select Quarter</option>
                    <option value="1" <?
                    if ($sel_qtr == 1) {
                        echo "selected";
                    }
                    ?>>Qtr. 1</option>
                    <option value="2" <?
                    if ($sel_qtr == 2) {
                        echo "selected";
                    }
                    ?>>Qtr. 2</option>
                    <option value="3" <?
                    if ($sel_qtr == 3) {
                        echo "selected";
                    }
                    ?>>Qtr. 3</option>
                    <option value="4" <?
                    if ($sel_qtr == 4) {
                        echo "selected";
                    }
                    ?>>Qtr. 4</option>
                </select>		
                &nbsp;&nbsp;&nbsp;
                <select name="sel_year">
                    <option value="">Select Year</option>
                    <?
                    $arr_yrs = array();
                    for ($i = 0; $i < 8; $i++) {
                        $arr_yrs[] = date('Y') - $i;
                    }

                    foreach ($arr_yrs as $k => $v) {
                        ?>
                        <option value="<?= $v ?>" <?
                        if ($sel_year == $v) {
                            echo "selected";
                        }
                        ?> ><?= $v ?></option>
                                <?
                            }
                            ?>
                </select>		
                &nbsp;&nbsp;&nbsp;
                <input type="image" src="images/lf_v1/form_submit.png"/>
                &nbsp;&nbsp;&nbsp;
                <?
                if ($sel_qtr != "" AND $sel_year != "") {
                    $str_xl = $user_id . "^" . $rr_num . "^" . $sel_qtr . "^" . $sel_year;
                    ?>
                                    <!-- <a href="pay_analyst_excel.php?xl=<?= $str_xl ?>" target="_blank"><img src="images/lf_v1/exp2excel.png" border="0" /></a> -->
                    <?
                }
                ?>
            </form>
            <br><a href="<?= $PHP_SELF ?>?<?= $_SERVER['QUERY_STRING'] ?>&action=excel" target="_blank"><img src="images/lf_v1/exp2excel.png" alt="Export to Excel" border="0"></a>
            <!-- End Top Menu -->		
        </td>
    </tr>
</table>
<?
if ($sel_qtr != "" AND $sel_year != "") {

    include('payout_reconciliation_inc_main_v2.php');
    include('payout_reconciliation_main_more_v2.php');

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    // ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ 
    //SHOW THE DATA
    ?>
    <div id="notify">&nbsp;</div>
    <? ob_start(); ?>
    <table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
        <tr>
            <td>
                <!--TABLE 2 START-->
                <script language="JavaScript" type="text/javascript"></script>

                <table width="100%"  border="0" cellspacing="1" cellpadding="0">
                    <tr>
                        <td bgcolor="#222222" width="120"><a class="tblhead_a">Analyst</a></td>
                        <td bgcolor="#222222" width="120"><a class="tblhead_a">Sales</a></td>
                        <td bgcolor="#222222" width="120"><a class="tblhead_a">Diff</a></td>
                        <td bgcolor="#222222" width="400"><a class="tblhead_a">Client</a></td>
                        <td bgcolor="#222222" width="50"><a class="tblhead_a">REP 1</a></td>
                        <td bgcolor="#222222" width="50"><a class="tblhead_a">REP 2</a></td>
                        <td bgcolor="#222222">&nbsp;</td>
                    </tr>

                    <?
                    $arr_quarter_brok_dates = get_quarter_dates($sel_qtr, $sel_year, "B");
                    $arr_quarter_cal_dates = get_quarter_dates($sel_qtr, $sel_year, "C");
                    //Sql query
                    $pay_adv_q = "SELECT pay_advisor_code as pac, sum(pay_percent) as total_percent FROM pay_analyst_allocations WHERE pay_year='" . $sel_year . "' and  pay_qtr='" . $sel_qtr . "' and pay_isactive = 1 
									group by pay_advisor_code order by pay_advisor_code";
                    $result_adv_q = mysql_query($pay_adv_q) or die(tdw_mysql_error($pay_adv_q));
                    while ($row = mysql_fetch_array($result_adv_q)) {
                        $pay_advisor_code[] = $row["pac"];
                        $pay_advisor_percent[$row["pac"]] = $row["total_percent"];
                    }

                    $advisor_codes = implode(",", $pay_advisor_code);
                    $advisor_codes = str_replace(",", '","', $advisor_codes);
                    $advisor_codes = '("' . $advisor_codes . '")';


                    $commissions_q = "SELECT trad_advisor_code, sum(trad_commission) as commission
                        FROM mry_comm_rr_trades
                        WHERE trad_trade_date between '" . $arr_quarter_brok_dates[0] . "' AND '" . $arr_quarter_brok_dates[1] . "' and trad_is_cancelled = 0 
                        GROUP by trad_advisor_code";
                    $result_comm = mysql_query($commissions_q) or die(tdw_mysql_error($commissions_q));
                    $arr_clnt_comm = array();
                    while ($row = mysql_fetch_array($result_comm)) {
                        $arr_clnt_comm[$row["trad_advisor_code"]] = $row["commission"];
                    }
                    //AND trad_advisor_code in ".$advisor_codes."

                    $checks_q = "SELECT chek_advisor, sum(chek_amount) as sum_checks
                        FROM chk_chek_payments_etc
                        WHERE chek_date between '" . $arr_quarter_cal_dates[0] . "' AND '" . $arr_quarter_cal_dates[1] . "' and chek_isactive = 1 AND
                        chek_advisor in " . $advisor_codes . "
                        GROUP by chek_advisor";
                    $result_checks = mysql_query($checks_q) or die(tdw_mysql_error($checks_q));
                    $arr_clnt_checks = array();
                    while ($row = mysql_fetch_array($result_checks)) {
                        $arr_clnt_checks[$row["chek_advisor"]] = $row["sum_checks"];
                    }
                    //AND chek_advisor in ".$advisor_codes."

                    $scount = 1;
                    foreach ($pay_advisor_code as $k => $v) {
                        $analyst_payout = ($pay_advisor_percent[$v] * ($arr_clnt_comm[$v] + $arr_clnt_checks[$v])) / 100;
                        $sales_payout = $arr_clnt_comm[$v] + $arr_clnt_checks[$v];
                        //Check data logic for fable row
                        $trcCssClass = 'trdark';
                        if ($scount % 2) {
                            $trcCssClass = 'trlight';
                        }
                        $rowCssStyle = '';
                        $comments = array();
                        if (round($sales_payout, 2) != round($analyst_payout, 2)) {
                            $rowCssStyle = 'style="color: red"';
                        }
                        if ((round($sales_payout, 2) * 2) == round($analyst_payout, 2)) {
                            $comments[] = 'Possible REP change in the middle of the quarter';
                        }
                        if ($arr_clients_rr1[$v] == 'BRG') {
                            $comments = array('House account');
                        } else {
                            if (!$analyst_payout) $comments[] = 'Wrong REP code';
                            if (!$arr_clients_rr1[$v] && !$arr_clients_rr2[$v]) $comments[] = 'No REP code';
                        }
                        ?>
                        <tr <?= $rowCssStyle ?> class="<?= $trcCssClass ?>">
                            <td align="right"><?= number_format($analyst_payout, 2, ".", ",") ?></td>
                            <td align="right"><?= number_format($sales_payout, 2, ".", ",") ?></td>
                            <td align="right"><?= number_format(($analyst_payout - $sales_payout), 2, ".", ",") ?></td>
                            <td><?= $arr_clients[$v] . ' (' . $v . ')' ?></td>
                            <td><?= $arr_clients_rr1[$v] ?></td>
                            <td><?= $arr_clients_rr2[$v] ?></td>
                            <td><?= (count($comments)) ? implode(', ', $comments) : '&nbsp;'; ?></td>
                        </tr>			
                        <?
                        $scount++;
                    }

                    foreach ($arr_clnt_comm as $k => $v) {
                        if (!in_array($k, $pay_advisor_code)) {
                            $analyst_payout = 0;
                            $sales_payout = $v + $arr_clnt_checks[$k];
                            //Check data logic for fable row
                            $trcCssClass = 'trdark';
                            if ($scount % 2) {
                                $trcCssClass = 'trlight';
                            }
                            $rowCssStyle = '';
                            $comments = array();
                            if (round($sales_payout, 2) != round($analyst_payout, 2)) {
                                $rowCssStyle = 'style="color: red"';
                            }
                            if ((round($sales_payout, 2) * 2) == round($analyst_payout, 2)) {
                                $comments[] = 'Possible REP change in the middle of the quarter';
                            }
                            if ($arr_clients_rr1[$k] == 'BRG') {
                                $comments = array('House account');
                            } else {
                                if (!$analyst_payout)
                                    $comments[] = 'Wrong REP code';
                                if (!$arr_clients_rr1[$k] && !$arr_clients_rr2[$k])
                                    $comments[] = 'No REP code';
                            }
                            ?>
                            <tr <?= $rowCssStyle ?> class="<?= $trcCssClass ?>">
                                <td align="right"><?= number_format($analyst_payout, 2, ".", ",") ?></td>
                                <td align="right"><?= number_format($sales_payout, 2, ".", ",") ?></td>
                                <td align="right"><?= number_format(($analyst_payout - $sales_payout), 2, ".", ",") ?></td>
                                <td><?= $arr_clients[$k] . ' (' . $k . ')' ?></td>
                                <td><?= $arr_clients_rr1[$k] ?></td>
                                <td><?= $arr_clients_rr2[$k] ?></td>              
                                <td><?= (count($comments)) ? implode(', ', $comments) : '&nbsp;'; ?></td>
                            </tr>		
                            <?
                            $scount++;
                        }
                    }

                    foreach ($arr_clnt_checks as $k => $v) {
                        if (!in_array($k, $pay_advisor_code) && !array_key_exists($k, $arr_clnt_comm)) {
                            $analyst_payout = 0;
                            $sales_payout = $arr_clnt_checks[$k];
                            //Check data logic for fable row
                            $trcCssClass = 'trdark';
                            if ($scount % 2) {
                                $trcCssClass = 'trlight';
                            }
                            $rowCssStyle = '';
                            $comments = array();
                            if (round($sales_payout, 2) != round($analyst_payout, 2)) {
                                $rowCssStyle = 'style="color: red"';
                            }
                            if ((round($sales_payout, 2) * 2) == round($analyst_payout, 2)) {
                                $comments[] = 'Possible REP change in the middle of the quarter';
                            }
                            if ($arr_clients_rr1[$k] == 'BRG') {
                                $comments = array('House account');
                            } else {
                                if (!$analyst_payout)
                                    $comments[] = 'Wrong REP code';
                                if (!$arr_clients_rr1[$k] && !$arr_clients_rr2[$k])
                                    $comments[] = 'No REP code';
                            }
                            ?>
                            <tr <?= $rowCssStyle ?> class="<?= $trcCssClass ?>">
                                <td align="right"><?= number_format($analyst_payout, 2, ".", ",") ?></td>
                                <td align="right"><?= number_format($sales_payout, 2, ".", ",") ?></td>
                                <td align="right"><?= number_format(($analyst_payout - $sales_payout), 2, ".", ",") ?></td>
                                <td><?= $arr_clients[$k] . ' (' . $k . ')' ?></td>
                                <td><?= $arr_clients_rr1[$k] ?></td>
                                <td><?= $arr_clients_rr2[$k] ?></td>              
                                <td><?= (count($comments)) ? implode(', ', $comments) : '&nbsp;'; ?></td>
                            </tr>			
                            <?
                            $scount++;
                        }
                    }


                    if ($_GET["action"] == "excel") {
                        $output_filename = "rr_list.xls";
                        $fp = fopen($exportlocation . $output_filename, "w");
                        fputs($fp, ob_get_contents());
                        Header("Location: http://192.168.20.63/tdw/fileserve_xls.php?l=data/exports/&f=" . $output_filename);
                        exit;
                    }

                    // ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ 
                    //@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@
                } else {
                    echo "&nbsp;&nbsp;Please select Quarter and Year.<br /><br />";
                }

                tep();
                ?>