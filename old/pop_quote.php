<?
// pop_acct_delete
// Calling string
/* <a href="javascript:CreateWnd('pop_acctdel.php?Name=<?=$row["trdm_auto_id"]?>&user_id=<?=$user_id?>', 360, 300, false);"> */

include ('includes/dbconnect.php');
include ('includes/global.php');
include ('includes/functions.php');

?>

<?

			// ADD WATERMARK
			 include_once ('includes/Thumbnail.class.php');
			$thumb=new Thumbnail("http://ichart.finance.yahoo.com/t?s=".$param_symbol);	        	// Contructor and set source image file
			//$thumb->size_auto(500);					    							// [OPTIONAL] set the biggest width or height for thumbnail
			$thumb->quality=85;                         				// [OPTIONAL] default 75 , only for JPG format
			$thumb->output_format='JPG';                				// [OPTIONAL] JPG | PNG
			$thumb->img_watermark='images/copyright.png';	    	// [OPTIONAL] set watermark source file, only PNG format [RECOMENDED ONLY WITH GD 2 ]
			$thumb->img_watermark_Valing='TOP';   	    			// [OPTIONAL] set watermark vertical position, TOP | CENTER | BOTTON
			$thumb->img_watermark_Haling='RIGHT';   	    			// [OPTIONAL] set watermark horizonatal position, LEFT | CENTER | RIGHT
			$thumb->process();   				        								// generate image
			$thumb->save("/var/www/html/demo/needham/data/charts/".$param_symbol.".jpg");										// save your thumbnail to file





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
	<td><!-- <img src="http://ichart.finance.yahoo.com/t?s=<?=$param_symbol?>" border="0"><br> --><img src="data/charts/<?=$param_symbol?>.jpg" border="0"></td>
	</tr>
</table>

<? table_end_percent(); ?>


</body>








