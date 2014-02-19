<?
//print_r($arr_analysts);
$trade_date_to_process = previous_business_day();

$rep_to_process = '044';
$user_sales = '221';

include('pay_analyst_inc_main.php');
$arr_analysts = create_arr("select ID as k, Fullname as v from users WHERE Role = 1 and user_isactive = 1", 2);

?>

<form name="frm_payout">
<table class="tbl_xl" border="0" cellpadding="0" cellspacing="0" >
<tr><td class="tdx" valign="middle" nowrap="nowrap"><h1>Q1 2008&nbsp;&nbsp;&nbsp;</h1>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
<?
$ccount = 1;
foreach ($arr_clnt_for_rr as $k=>$v) {
?>
<td class="vert_1"><?=$v?></td>
<?
$ccount++;
}
?>
</tr>
<tr><td align="right" class="pplname">Total Commissions</td>
	<?
	for ($i=1; $i<$ccount; $i++) {
	?>
		<td class="tdx" align="right" valign="middle">&nbsp;<div class="pplname" id="tot|<?=$i?>"><?=number_format(rand(100000,900000),2,".",",")?>&nbsp;&nbsp;</div></td>
	<?
	}
echo "</tr>";

$rcount = 1;
foreach ($arr_analysts as $k=>$v) {
?>
<tr><td class="tdx" nowrap="nowrap">
<table width="100%"><tr class="pplname"><td nowrap="nowrap">&nbsp;&nbsp;<?=$v?></td><td align="right" nowrap="nowrap">: $<a id="at|<?=$rcount?>">0.00</a> </td></tr></table>
</td>
	<?
	for ($i=1; $i<$ccount; $i++) {
	?>
		<td class="tdx">
		<table width="100%">
			<tr>
				<td><input type="text" style="border: 0px;" name="<?=$rcount."|".$i?>" id="<?=$rcount."|".$i?>" value="0" size="8" maxlength="3" onchange="xlrecalc('<?=$rcount."|".$i?>')" onkeyup="return xlmove(event, '<?=$rcount."|".$i?>')" /></td>
				<td class="num_1" nowrap="nowrap" id="curnum|<?=$rcount."|".$i?>"></td>
			</tr>
		</table>		
		</td>
	<?
	}
echo "</tr>";
$rcount++;
}
?>
<input type="hidden" name="frmitem_rcount" id="id_rcount" value="<?=($rcount-1)?>" />  
<tr><td class="tdx" nowrap="nowrap">&nbsp;&nbsp;<font face="Verdana, Arial, Helvetica, sans-serif" size="+1">Total %</font></td>
	<?
	for ($i=1; $i<$ccount; $i++) {
	?>
		<td class="tdx"><input type="text" style="border: 0px;" name="total|<?=$i?>" id="total|<?=$i?>" value="0" size="3"/></td>
	<?
	}
echo "</tr>";
?>
</table>
</form>