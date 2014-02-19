<?
// pop_acct_delete
// Calling string
/* <a href="javascript:CreateWnd('pop_acctdel.php?Name=<?=$row["trdm_auto_id"]?>&user_id=<?=$user_id?>', 360, 300, false);"> */

include ('includes/dbconnect.php');
include ('includes/global.php');
include ('includes/functions.php');

?>

<?
function get_quotes($param_symbol) {

	$quotes = new Quotes(); 
  $quotes->mSetSymbol(strtoupper($param_symbol)) ; 
	$quotes->mLoadYahoo() ;

	$outputquote = array($quotes->_strCompany,$quotes->_strLastPrice,$quotes->_strTradeDate,$quotes->_strTradeTime,$quotes->_strChange,$quotes->_strChangePercent,$quotes->_strVolume);

return $outputquote;	
}
?>

<?
$quoteval = get_quotes($param_symbol);
?>


<title>Quote: (Delayed ~20 mins.)_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _:</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="includes/styles.css" rel="stylesheet" type="text/css">
<body>



<? table_start_percent(100, $param_symbol." (".$quoteval[0].")"); ?>



<table class="links12" border="0" cellspacing="0" cellpadding="5">
	<tr>
		<td>
		<table class="quotes">
 		<tr>
			<td colspan="2">&nbsp;<br><br><!-- <a class="links12_quote"><?=$param_symbol?> (<?=$quoteval[0]?>)</a><hr> --></td>
		</tr>
		<tr> 
			<td width="120">Last Price: </td>
			<td width="120"><?=$quoteval[1]?></td>
		</tr>
		<tr> 
			<td>Trade Date: </td>
			<td><?=$quoteval[2]?> </td>
		</tr>
		<tr> 
			<td>Trade Time: </td>
			<td><?=$quoteval[3]?></td>
		</tr>
		<tr> 
			<td>Change: </td>
			<td><?=$quoteval[4]?></td>
		</tr>
		<tr> 
			<td>% Change: </td>
			<td><?=$quoteval[5]?></td>
		</tr>
		<tr> 
			<td>Volume: </td>
			<td><?=$quoteval[6]?></td>
		</tr>
		</table>
	</td>
	<td><img src="http://ichart.finance.yahoo.com/t?s=<?=$param_symbol?>" border="0"></td>
	</tr>
</table>

<? table_end_percent(); ?>


</body>








