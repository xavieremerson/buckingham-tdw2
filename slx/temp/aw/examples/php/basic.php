<html>
<head>
	<title>ActiveWidgets Grid :: PHP Example</title>
	<style> body, html {margin:0px; padding: 0px; overflow: hidden;font: menu;border: none;} </style>

	<!-- ActiveWidgets stylesheet and scripts -->
	<link href="../../runtime/styles/xp/grid.css" rel="stylesheet" type="text/css" ></link>
	<script src="../../runtime/lib/grid.js"></script>

	<!-- ActiveWidgets PHP functions -->
	<?php include("activewidgets.php") ?>

</head>
<body>

<?php

	// grid object name
	$name = "obj";

	// SQL query
	$query = "select * from `nfs_trades` limit 0,20";

	// database connection
	$connection = mysql_connect("localhost", "newadmin", "newpassword");
	mysql_select_db("warehouse");

	// query results
	$data = mysql_query($query, $connection);

	// add grid to the page
	echo activewidgets_grid($name, $data);

?>

</body>
</html>