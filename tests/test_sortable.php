<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
<script type="text/javascript" src="sortable/sortable_us.js"></script>

<style type="text/css">
<!--
a img {
	border: 0;
}
table.sortable {
	border-spacing: 0;
	border-style: solid;
	border-color: #aaa;
	border-width: 1px;
	border-collapse: collapse;
	font-family: Verdana, Arial, Helvetica, sans-serif;
	width: 90%;
	margin: 0 auto;
}

table.sortable th, table.sortable td {
	text-align: left;
	padding: 2px 4px 2px 4px;
	width: 100px;
	border-style: solid;
	border-color: #aaa;
	font-size: 11px;
}
table.sortable th {
	border-width: 1px 0px 1px 0px;
	background-color: #ccc;
}
table.sortable th a {
	text-decoration: none;
	color: #000;
}
table.sortable td {
	border-width: 0px;
}
table.sortable tr.odd td {
	background-color: #fff;
}
table.sortable tr.even td {
	background-color: #ddd;
}
table.sortable tr.sortbottom td {
	border-width: 1px 0px 1px 0px;
	background-color: #ccc;
	font-weight: bold;
}
-->
</style>
</head>

<body>
<table class="sortable" id="sortabletable">
<tr>
<th>Test Date</th>
<th>Dollars</th>
<th>Text</th>
<th class="unsortable">Not Sorted</th>
</tr>

<?
for ($i=0; $i < 100; $i++) {
?>
<tr>
<td><?=date("m/d/Y", time() - (rand(0,300) * 24*60*60))?></td>
<td>$<?=rand(0,9999).".".rand(0,99)?></td>
<td><?=substr(md5(rand(1,9999)),0,10)?></td>
<td><?=substr(md5(rand(1,9999)),0,10)?></td>
</tr>
<?
}
?>
</table>
</body>
</html>
