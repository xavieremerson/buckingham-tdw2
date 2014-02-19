<?
include("include/cls_db.php");
include("include/cls_column.php");
include("include/cls_datagrid.php");
include("include/cls_control.php");
include("include/cls_arraygrid.php");
require("fpdf/fpdf.php");
?>
<html>
<head>
<title>phpGrid</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<?php
// use associative array as data source
$grid_data = array( array('uid' => 1,'order_id' => 'xyz-1', 'quantity' => 100, 'cost' => 11.8, 'shipping_cost' => 11, 'status' => 'approved'),
          			array('uid' => 2,'order_id' => 'xyz-x', 'quantity' => 0, 'cost' => 10.2, 'shipping_cost' => 22, 'status' => 'shipped'),
          			array('uid' => 3,'order_id' => 'xyz-3', 'quantity' => 200, 'cost' => 6.8, 'shipping_cost' => 33, 'status' => 'onhold'),
          			array('uid' => 4,'order_id' => 'xyz-z', 'quantity' => 300, 'cost' => 14.9, 'shipping_cost' => 42, 'status' => 'approved'),
          			array('uid' => 5,'order_id' => 'xyzds', 'quantity' => 500, 'cost' => 11.2, 'shipping_cost' => 52, 'status' => 'shipped'),
          			array('uid' => 6,'order_id' => 'xyz-2', 'quantity' => 10, 'cost' => 12.8, 'shipping_cost' => 26, 'status' => 'onhold'),
          			array('uid' => 7,'order_id' => 'xyz-q', 'quantity' => 500, 'cost' => 11.8, 'shipping_cost' => 72, 'status' => 'approved'),
          			array('uid' => 8,'order_id' => 'xyz-c', 'quantity' => 1009, 'cost' => 10.2, 'shipping_cost' => 72, 'status' => 'onhold'),
          			array('uid' => 9,'order_id' => 'xyz21', 'quantity' => 1030, 'cost' => 11.8, 'shipping_cost' => 32, 'status' => 'received'),
          			array('uid' =>10,'order_id' => 'xyz26', 'quantity' => 20, 'cost' => 10.9, 'shipping_cost' => 98, 'status' => 'received'),
          			array('uid' =>11,'order_id' => 'xyz-z', 'quantity' => 400, 'cost' => 12.8, 'shipping_cost' => 22, 'status' => 'approved'),
          			array('uid' =>12,'order_id' => 'xyzml', 'quantity' => 100, 'cost' => 10.7, 'shipping_cost' => 32, 'status' => 'received'),
          			array('uid' =>13,'order_id' => 'xyz39', 'quantity' => 100, 'cost' => 10.8, 'shipping_cost' => 200, 'status' => 'approved'),
          			array('uid' =>14,'order_id' => 'xyz32', 'quantity' => 50, 'cost' => 19.3, 'shipping_cost' => 122, 'status' => 'approved'),
          			array('uid' =>15,'order_id' => 'xy231', 'quantity' => 500, 'cost' => 11.7, 'shipping_cost' => 120, 'status' => 'shipped'),
          			array('uid' =>16,'order_id' => 'xvsad', 'quantity' => 10, 'cost' => 10.0, 'shipping_cost' => 22, 'status' => 'shipped'),
          			array('uid' =>17,'order_id' => 'xyk90', 'quantity' => 10, 'cost' => 11.8, 'shipping_cost' => 22, 'status' => 'onhold'),
          			array('uid' =>18,'order_id' => 'xy9op', 'quantity' => 10, 'cost' => 12.8, 'shipping_cost' => 122, 'status' => 'shipped'),
          			array('uid' =>19,'order_id' => 'xyz22', 'quantity' => 10, 'cost' => 13.8, 'shipping_cost' => 223, 'status' => 'rejected'),
          			array('uid' =>20,'order_id' => 'xyz0z', 'quantity' => 600, 'cost' => 14.8, 'shipping_cost' => 123, 'status' => 'rejected')
         		);

$dg = new C_ArrayGrid($grid_data);
$dg -> set_sql_key	("uid");
$dg -> set_sorted_by	("order_id", "DESC");
// $dg -> set_col_img		("prod_img", "", "border:2px black solid;width:140px");
$dg -> set_col_title	("order_id", "Order ID");
$dg -> set_col_title	("shipping_cost", "Shipping Cost");
$dg -> set_col_title	("price", "Price(US$)");
$dg -> set_col_title	("quantity", "Order Quantity");
$dg -> set_col_title	("cost", "Sales");
// $dg -> set_col_title	("status", "Order Status");
$dg -> set_col_style	("cost", "text-align:right");
$dg -> set_col_hidden	("uid");
/*
$dg -> set_col_hidden	("uid");
$dg -> set_col_hidden	("status");
$dg -> set_col_hidden	("order_id");
$dg -> set_col_hidden	("price");
$dg -> set_col_hidden	("shipping_cost");
$dg -> set_col_hidden	("cost");
*/
$dg -> set_page_size	(15);
$dg -> set_col_link		("order_id", $_SERVER["PHP_SELF"]."?order_id=", "order_id");
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
// $dg -> set_fields_readonly("order_id, user_id, cost");
$dg -> set_gridpath("include/");

/*
$dg -> add_control("status", CHECKBOX, array("shipped"=>"Shipped",	
							"approved"=>"Approved",
							"onhold"=>"On hold",
							"received"=>"Received",
							"rejected"=>"Rejected"), ",");
										
$dg -> add_control("price", DROPDOWN,array("11.98"=>"11.98",
							"10.8"=>"10.8",
							"11.3"=>"11.3",
							"10.15"=>"10.15"));
*/

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
