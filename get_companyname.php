<?
include('includes/functions.php');
$companyname = get_company_name(strtoupper($symbol));
if ($companyname == strtoupper($symbol) or $companyname == '') {
echo 'PROBABLE ERROR';
} else {
echo $companyname;
}
?>
