<?
  include('includes/dbconnect.php');
  include('includes/global.php');
  include('includes/functions.php');

$asdasd = 234;
xdebug("asdasd",$asdasd);

exit;
$row = 1;
$handle = fopen("http://ichart.finance.yahoo.com/table.csv?s=AEO&a=03&b=14&c=2009&d=05&e=12&f=2009&g=d&ignore=.csv", "r");
while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
    $num = count($data);
    echo "<p> $num fields in line $row: <br /></p>\n";
    $row++;
    for ($c=0; $c < $num; $c++) {
        echo $data[$c] . "<br />\n";
    }
}
fclose($handle);

exit;
?>