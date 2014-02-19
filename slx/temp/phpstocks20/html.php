<? 
 class HTML {

/* Soon all the html will be placed in .tmpl files (templates */
/* this is really ugly php code :( 			      */

	function header($info) {
		$num = count($info);
?>
<body bgcolor="white">
  <table width="100%" border="1">
  <tr bgcolor="#5b69a6"> 
    <td width="10%"> 
      <div align="center"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"><b><font color="#FFFFFF" face="Arial, Helvetica, sans-serif" size="2">SYMBOL</font></b></font></div>
    </td>
    <td width="19%" bgcolor="#5b69a6"> 
      <div align="center"><font color="#FFFFFF" face="Arial, Helvetica, sans-serif" size="2"><b>COMPANY</b></font></div>
    </td>
    <td width="11%"> 
      <div align="center"><b><font face="Arial, Helvetica, sans-serif" size="2" color="#FFFFFF">LAST 
        TRADE </font></div>
    </td>
    <td width="11%">
      <div align="center"><b><font face="Arial, Helvetica, sans-serif" size="2" color="#FFFFFF">TIME OF TRADE </font></b></div>
    </td>
    <td width="11%">
      <div align="center"><b><font face="Arial, Helvetica, sans-serif" size="2" color="#FFFFFF"> DATE of TRADE
        </font></b></div>
    </td>
    <td width="17%"> 
      <div align="center"><font color="#FFFFFF" face="Arial, Helvetica, sans-serif" size="2"><b>CHANGE</b></font></div>
    </td>
    <td width="13%"> 
      <div align="center"><b><font face="Arial, Helvetica, sans-serif" size="2" color="#FFFFFF">VOLUME</font></b></div>
    </td>
  </tr>
<? for ( $c=0; $c<$num; $c++ ) {  
?>
  <tr> 
    <td width="10%"><? echo $info[$c][symbol];?></td>
    <td width="19%"><? echo $info[$c][company];?></td>
    <td width="11%"><? echo $info[$c][lastprice];?></td>
    <td width="11%"><? echo $info[$c][tradetime];?></td>
    <td width="11%"><? echo $info[$c][tradedate]; ?></td>
    <td width="17%"><? echo $info[$c][change];?></td>
    <td width="13%"><? echo $info[$c][volume];?></td>
  </tr>
  
<? 	
	}  	
echo "</table>";
}
	function detailed($info) {
		
		$num = count($info); /* Count # of items inside the array */
		for ( $c=0; $c<$num; $c++ ) { 
?>

<table width=100% border=1>
    <tr> 
    <td width="10%"><? echo $info[$c][symbol];?></td>
    <td width="19%"><? echo $info[$c][company];?></td>
    <td width="11%"><? echo $info[$c][lastprice];?></td>
    <td width="11%"><? echo $info[$c][tradetime];?></td>
    <td width="11%"><? echo $info[$c][tradedate]; ?></td>
    <td width="17%"><? echo $info[$c][change];?></td>
    <td width="13%"><? echo $info[$c][volume];?></td>
  </tr>
</table>  
<center>
  <table width="65%" border="0">
    <tr bgcolor="#FFFFFF"> 
      <td height="25" width="35%">
      	<font face="Arial, Helvetica, sans-serif" color="#0066CC" size="2"><b>Open</b></font></td>
      <td height="25" width="15%"><?=$info[$c][open]?></td>
      <td height="25" width="31%">
      	<font face="Arial, Helvetica, sans-serif" size="2" color="#0066FF"><b>Previous 
      	Close</b> </font></td>
      <td height="25" width="19%"><?=$info[$c][yesterdaysclose]?></td>
    </tr>
    <tr> 
      <td width="35%">
      	<font color="#0066CC" face="Arial, Helvetica, sans-serif" size="2"><b>Today's 
        Range</b></font></td>
      <td width="25%"><?=$info[$c][dayrange]?></td>
      <td width="31%"><b><font face="Arial, Helvetica, sans-serif" size="2" color="#0066CC">Market Cap
       </font></b></td>
      <td width="19%"><?=$info[$c][marketcap]?></td>
    </tr>
    <tr> 
      <td width="35%"><font color="#0066CC" size="2"><b><font face="Arial, Helvetica, sans-serif">Year 
        Range</font></b></font></td>
      <td width="25%"><?=$info[$c][yearrange]?></td>
      <td width="31%"><font face="Arial, Helvetica, sans-serif" size="2" color="#0066CC"><b>Yield 
        </b></font></td>
      <td width="19%"><?=$info[$c][yield]?></td>
    </tr>
    <tr> 
      <td width="35%"><font color="#0066CC" size="2" face="Arial, Helvetica, sans-serif"><b>Bid 
        </b></font></td>
      <td width="15%"><?=$info[$c][bid]?></td>
      <td width="31%"><font face="Arial, Helvetica, sans-serif" size="2" color="#0066CC"><b>Ask</b></font></td>
      <td width="19%"><?=$info[$c][ask]?></td>
    </tr>
    <tr> 
      <td width="35%"><font face="Arial, Helvetica, sans-serif" size="2" color="#0066CC"><b>Div 
        Date</b></font></td>
      <td width="15%"><?=$info[$c][divdate]?></td>
      <td width="31%"><font face="Arial, Helvetica, sans-serif" size="2" color="#0066CC"><b>Div/Shr</b></font></td>
      <td width="19%"><?=$info[$c][divshr]?></td>
    </tr>
    <tr>
      <td width="35%"><font color="#0066CC" face="Arial, Helvetica, sans-serif" size="2"><b>Earn/Share</b></font></td>
      <td width="15%"><?=$info[$c][earnpershare]?></td>
      <td width="31%"><font face="Arial, Helvetica, sans-serif" size="2" color="#0066CC"><b>P/E</b></font></td>
      <td width="19%"><?=$info[$c][pe]?></td>
    </tr>
    
  </table>
</center>
<center>
<font size="-1" face="Arial, Helvetica, sans-serif">Information from www.yahoo.com</font><br>
				<hr width=400>

</center>






<?                
}		
	}	
	
	
	
	function basic($info) {
		/* first sort the data properly */
	$num = count($info); /* Count # of items inside the array */
	for ( $c=0; $c<$num; $c++ ) {  
?>
  <!-- <tr> 
    <td width="10%"><? echo $info[$c][symbol];?></td>
    <td width="19%"><? echo $info[$c][company];?></td>
    <td width="11%"><? echo $info[$c][lastprice];?></td>
    <td width="11%"><? echo $info[$c][tradetime];?></td>
    <td width="11%"><? echo $info[$c][tradedate]; ?></td>
    <td width="17%"><? echo $info[$c][change];?></td>
    <td width="13%"><? echo $info[$c][volume];?></td>
  </tr> -->
<? 	}

	echo "</table>";
?>
<?php 
?>		

<center>
<font size="-1" face="Arial, Helvetica, sans-serif">Information from www.yahoo.com</font><br>
				<hr width=400>

</center>






<?                
		
	}	
	
}
?>
