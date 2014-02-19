<?
include('includes/dbconnect.php');
include('includes/functions.php');

$sql = "insert into zzz(varval) values('".$var."')";
$result = mysql_query($sql);
if ($result) {
echo "Success!".rand(111,999999);
} else {
echo "Failure!".rand(111,999999);
}
?>




