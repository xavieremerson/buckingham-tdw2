<?
  include('../includes/dbconnect.php');
  include('../includes/global.php');
	include('../includes/functions.php');

function cs($str) {
	return ereg_replace("[^A-Za-z]", "", $str);
}

echo cs("dsfsd___ff0  234@#$%^&*");

?>