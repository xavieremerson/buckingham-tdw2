<?
include("include/cls_db.php");
include("include/cls_column.php");
include("include/cls_datagrid.php");
include("include/cls_control.php");
include("include/cls_arraygrid.php");
require("fpdf/fpdf.php");
require("adodb/adodb.inc.php");

$hostName = "localhost";
$userName = "root";
$password = "";
$dbName	  = "demo";
?>
<html>
<head>
<title>phpGrid Enterprise</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<?php
$dg = new C_DataGrid($hostName, $userName, $password, $dbName);
$dg -> set_sql			("SELECT uid, prod_img, status, order_id, shipping_cost, price, quantity, cost FROM demo_orders");
$dg -> set_sql_key		("uid");
$dg -> set_sorted_by	("order_id", "DESC");
// $dg -> set_col_img		("prod_img", "", "border:2px black solid;width:140px");
$dg -> set_col_title	("shipping_cost", "Shipping");
$dg -> set_col_title	("price", "Price(US$)");
$dg -> set_col_title	("quantity", "Quantity");
$dg -> set_col_title	("cost", "Sales");
$dg -> set_col_style	("cost", "text-align:right");
/*
$dg -> set_col_hidden	("uid");
$dg -> set_col_hidden	("uid");
$dg -> set_col_hidden	("status");
$dg -> set_col_hidden	("order_id");
$dg -> set_col_hidden	("status");
$dg -> set_col_hidden	("price");
$dg -> set_col_hidden	("shipping_cost");
$dg -> set_col_hidden	("cost");
*/
$dg -> set_page_size	(15);
$dg -> set_col_link		("order_id", $_SERVER["PHP_SELF"]."?order_id=", "order_id", , "target='_new'");
$dg -> set_allow_actions(true);
$dg -> set_allow_export	(true);
$dg -> set_ok_nl2br		(false);
$dg -> set_ok_rowindex	(true);
$dg -> set_alt_bgcolor  ("#E4D0FF, #F3E9FF", 1);
$dg -> set_inlineedit_enabled(true);
$dg -> set_multidel_enabled(true);
$dg -> set_col_sum_enabled(true);
$dg -> set_col_sum      ("quantity");
$dg -> set_col_sum      ("cost");
$dg -> set_col_sum      ("shipping_cost");
$dg -> set_toolbar_enabled(true);
$dg -> set_ok_showcredit(true);
$dg -> set_action_type("VED");
$dg -> set_fields_readonly("order_id, user_id, cost");
$dg -> set_gridpath("include/");


$dg -> add_control("status", CHECKBOX, array("shipped"=>"Shipped",
										  "approved"=>"Approved",
										  "onhold"=>"On hold",
										  "received"=>"Received",
										  "rejected"=>"Rejected"), ",");
$dg -> add_control("price", DROPDOWN,array("11.98"=>"11.98",
										  "10.8"=>"10.8",
										  "11.3"=>"11.3",
										  "10.15"=>"10.15"));


$dg -> display();
$dg -> debug();
$dg = NULL;
?>

</body>
</html>



























<?
$template =
	"<tr><td rowspan='4' width='150' align='center'><img src='{DataField:picture}' width='75' border='0' /></td>
		<td><b><font color='red'>Total:</font></d> {DataField:total_quantity} {DataField:price_per_000}</td>
		<td rowspan='4' align='center' style='font-size:7pt;font-family:verdana,arial;padding:5pt'>
			<a href='{Admin:View}'>View</a> |
			<a href='{Admin:Edit}'>Edit</a> |
			<a href='{Admin:Delete}'>Remove</a>
	</td>
	</tr>
	<tr><td><b>Cost:</d> {DataField:cost}</td></tr>
	<tr><td><b>Product:</d> <i>{DataField:procut_type}</i></td></tr>
	<tr><td><b>By:</b><a href='grid_regular_test.php?actressID={DataField:check_in_by}'>Visit {DataField:proof_return_status}</a></td></tr>";
// $dg -> set_template_layout($template);
?>
