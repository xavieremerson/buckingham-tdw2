<?
include("include/cls_db.php");
include("include/cls_column.php");
include("include/cls_datagrid.php");
include("include/cls_control.php");
include("include/cls_arraygrid.php");
require("fpdf/fpdf.php");
require("adodb/adodb.inc.php");

$hostName = "localhost";
$userName = "dev";
$password = "1234";
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
$dg -> set_sql			("SELECT * FROM actresses");
$dg -> set_sql_key		("actressID");
$dg -> set_sorted_by	("age", "DESC");
// $dg -> set_col_img		("prod_img", "", "border:2px black solid;width:140px");
$dg -> set_col_title	("lastName", "Last Name");
$dg -> set_col_title	("firstName", "First Name");
$dg -> set_col_title	("age", "Age");
$dg -> set_col_title	("Intro", "Introduction");
$dg -> set_col_hidden	("uid");
$dg -> set_col_hidden	("uid");
$dg -> set_col_hidden	("status");
$dg -> set_col_hidden	("order_id");
$dg -> set_col_hidden	("status");
$dg -> set_col_hidden	("price");
$dg -> set_col_hidden	("shipping_cost");
$dg -> set_col_hidden	("cost");



$dg -> set_page_size	(15);
$dg -> set_col_link		("actressID", $_SERVER["PHP_SELF"]."?actressID=", "actressID", "target='_new'");
$dg -> set_allow_actions(true);
$dg -> set_allow_export	(true);
$dg -> set_ok_nl2br		(false);
$dg -> set_ok_rowindex	(true);
$dg -> set_alt_bgcolor  ("#E4D0FF, #F3E9FF", 1);
$dg -> set_inlineedit_enabled(true);
$dg -> set_multidel_enabled(true);
$dg -> add_column		("age", "", "Chart:Bar"); 
$dg -> set_col_sum_enabled(false);
$dg -> set_col_sum      ("quantity");
$dg -> set_col_sum      ("cost");
$dg -> set_col_sum      ("shipping_cost");
$dg -> set_toolbar_enabled(true);
$dg -> set_ok_showcredit(true);
$dg -> set_action_type("VED");
$dg -> set_fields_readonly("age, intro");
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
/*
$dg -> set_sql			("SELECT om.order_id, om.shipping_charge, om.total_quantity, om.status, od.ship_business, om.cost FROM demo_orders om
                            INNER JOIN demo_order_details od ON om.order_id = od.order_id");
*/
// $dg -> set_grid_class	("grid_css");
// $dg -> set_grid_body_style	("border:1pt solid black;overflow:auto; height: 200px;width:750px");	// this is for screen style, print style is {} so no scroll bars
// $dg -> set_table_class	("table_css");
// $dg -> set_th_class		("th_css1");
// $dg -> set_tr_class		("tr_css");
// $dg -> set_alt_bgcolor	("white, #E9EFF2");
// $dg -> set_onmouseover	("yellow");
// $dg -> set_cell_style	("height:13px; overflow: auto; cursor:hand;cursor:pointer;padding:2px");
// $dg -> set_col_img		("picture", "", "border:1pt solid black");
// $dg -> set_col_txt		("picture");
// $dg -> set_col_link		("lastName", $_SERVER["SCRIPT_NAME"]."?actressID=", "actressID");
// $dg -> add_column		("price_per_000", "$$$/M", "price_per_000");
// $dg -> set_cell_prtstyle("height:20px; overflow: auto");

// ********************************* TEMPLATE  ***************************
// Code reference. DO NOT DELETE
// Note: Following works but less convinient:
//	<a href=\\\"{Admin:View}\\\">View</a> |
// 	<a href=" . addslashes("\"{Admin:View}\"") .">View</a>
// ///////////////////////////////////////////////////////////
// psudoe code for even more advanced template layout
// Note:see ASP.NET for more reference
// default Value is View
// ToDo: how to parse the grid tag?
// <grid:Admin URL="xxx.php" Img="img.jpg" or Button="true" Mode="View" Value="View" style="blahblah" /></grid> = {Admin:View}
// <grid:DataField Name="xxx"></grid> = {DataField:xxx}
// ***********************************************************************
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
