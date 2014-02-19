<?
ob_start();
?>

<!-- this bit goes at the very top of the page: -->
<div id='interstitial'>
      <p style="margin-top: 20px; margin-bottom: 0px">			                  
		<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=5,0,0,0" width="400" height="40" id="cm_front1" align="middle">
<param name="allowScriptAccess" value="sameDomain" />
<param name="movie" value="../images/movie/retrieve.swf" />
<param name="quality" value="High" />
<param name="wmode" value="transparent">
<embed src="../images/movie/retrieve.swf" quality="High" width="400" height="40" name="cm_front1" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" wmode="transparent" /></object>
      </p>
		<p style="margin-top: 0px; margin-bottom: 20px">			                  
		<font size="4" color="#FF0000">
		<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=5,0,0,0" width="400" height="40" id="cm_front2" align="middle">
<param name="allowScriptAccess" value="sameDomain" />
<param name="movie" value="../images/movie/bars.swf" />
<param name="quality" value="High" />
<param name="wmode" value="transparent">
<embed src="../images/movie/bars.swf" quality="High" width="400" height="40" name="cm_front2" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" wmode="transparent" /></object>
					</font>
      </p>
</div>

<!-- This is for those times where you have to [asp: response.end] [php: exit(); ] for some reason-->
<script language="javascript">
    setTimeout('interstitial.style.display="none";',10000);
</script>

<!-- if using PHP: -->
<?
ob_end_flush();
?>
<?
include('../test.html');
?>
<!--
Insert your entire page contents here, including calls to the database
-->

<!-- this bit goes at the very bottom of the page: -->
<script language="javascript">
     interstitial.style.display="none";
</script>

?>