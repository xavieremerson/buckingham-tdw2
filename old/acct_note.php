<?
include ('includes/dbconnect.php');
include ('includes/global.php');
?>

<title>Account Note</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="includes/styles.css" rel="stylesheet" type="text/css">
<body onunload="window.opener.location.reload()">

<? echo $table_start; ?>
<table>
	<tr>
		<td>
			<a class="csys_regtext">The account holder could be on the US Treasury Money Laundering List.</a>
		</td>
	</tr>
	
	<tr><td><Br></td></tr>
	
	<tr>
		<td>
			<a class="csys_regtext">To get more info. <a href="http://www.ustreas.gov/offices/enforcement/ofac/sdn/data.shtml" target="_blank">click here</a>.</a>
		</td>
	</tr>


</table>

<? echo $table_end; ?>

</body>


