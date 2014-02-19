<?
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// outputs chart for trailing 6 years with argument $clnt
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

include('includes/dbconnect.php');
include('includes/functions.php');
include('includes/global.php');
require_once("includes/cd_win32/lib/phpchartdir.php");

if (!$rep) {
$rep = 'BE';
} else {
$rep = strtoupper(trim($rep));
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title># of Clients by Year and Tier for <?=$rep?></title>
</head>
<body>
<img src="chart_mult_years_by_rep_img.php?rep=<?=$rep?>" border="0" />
</body>
</html>
