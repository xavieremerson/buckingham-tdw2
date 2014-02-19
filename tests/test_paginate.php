<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
<link href="includes/styles.css" rel="stylesheet" type="text/css" />
<script language="javascript1.2">
function procRow() {


}

</script>

<style type="text/css">
<!--
tr.test {
	color: #FFFFFF;
	background-color: #000000;
}

tr.selrow {
	color: #FFFFFF;
	background-color: #0000FF;
}

-->
</style>
</head>

<body>
<?
  include('includes/dbconnect.php');
  include('includes/global.php');
	include('includes/functions.php');


	function mysql_first_data($q, $rowname)
	{
		$result = mysql_query ($q);
		if (mysql_num_rows($result) > 0)
		{
			$rowname = explode("|", $rowname);
			for ($i = 0; $i < count($rowname); $i++)
			{
				global $$rowname[$i];
				$$rowname[$i] = mysql_result ($result, 0, $rowname[$i]);
			}
		}	
	}

	function show_number($numberperpage, $limit, $alltotal, $currenttotal, $link)
	{
		$class_field = "h4";
		
		if (($limit + $currenttotal) > $numberperpage)
		{	
			$next_limit = $limit - $numberperpage;
			?>
			<a href="<?=$link?>&limit=<?=$next_limit?>" class="<?=$class_field?>">&lt;&lt; Previous</a>&nbsp;&nbsp;
			<?
		}	
		
		$x = $alltotal;
		
		$limit_x = 0;
		$x_count = 1;
		$numlinks = 0;
		while ($x > 0 and $numlinks < 11)
		{
			if ($limit != $limit_x)
				print (' | <a href="'.$link.'&limit='.$limit_x.'" class="'.$class_field.'">'.$x_count.'</a>');
			else
				print (' | <i class="'.$class_field.'">'.$x_count.'</i>');
			$x_count++; 
			$limit_x = $limit_x + $numberperpage;
			$x = $x - $numberperpage;
			$numlinks = $numlinks + 1;
		}
		
		if ($alltotal > ($limit + $currenttotal))
		{	
			$next_limit = $limit + $currenttotal;
			print (' |&nbsp;&nbsp;<a href="'.$link.'&limit='.$next_limit.'" class="'.$class_field.'">Next &gt;&gt;</a>');
		}	
		else
			print (" | ");
		print ("<br><br>");
	}


  ?>
	<form name="acct_search" action="" method="post">
	<input type="text" name="srchval" size="30" maxlength="30">
	<input type="submit" name="search" value="Search" />
	</form>
	<?
	
	if ($_POST) {
	//print_r($_POST);
			$limit = 0;
			$_GET = array();

	}

	
	
	$q1 = "SELECT nadd_full_account_number 
	      FROM mry_nfs_nadd WHERE
				nadd_branch like '%".$srchval."%' or 
				nadd_full_account_number like '%".$srchval."%' or 
				nadd_advisor like '%".$srchval."%' or 
				nadd_short_name like '%".$srchval."%' or 
				nadd_rr_owning_rep like '%".$srchval."%' or 
				nadd_rr_exec_rep like '%".$srchval."%'";
	//xdebug("q1",$q1);
	mysql_first_data ($q1, "nadd_full_account_number");
	

	if (!isset($limit)) 
		$limit = 0;

	$alltotal = 0;
	$q2 = "SELECT COUNT(*) AS alltotal FROM mry_nfs_nadd WHERE
				nadd_branch like '%".$srchval."' or 
				nadd_full_account_number like '%".$srchval."%' or 
				nadd_advisor like '%".$srchval."%' or 
				nadd_short_name like '%".$srchval."%' or 
				nadd_rr_owning_rep like '%".$srchval."%' or 
				nadd_rr_exec_rep like '%".$srchval."%'";
	//xdebug("q2",$q2);
	mysql_first_data ($q2, "alltotal");

	$numberperpage = 20;
	$q3 = "SELECT nadd_advisor,
								nadd_short_name, 
								nadd_full_account_number
	      FROM mry_nfs_nadd WHERE
				nadd_branch like '%".$srchval."%' or 
				nadd_full_account_number like '%".$srchval."%' or 
				nadd_advisor like '%".$srchval."%' or 
				nadd_short_name like '%".$srchval."%' or 
				nadd_rr_owning_rep like '%".$srchval."%' or 
				nadd_rr_exec_rep like '%".$srchval."%'
				ORDER BY nadd_full_account_number DESC LIMIT $limit, $numberperpage";
	//xdebug("q3",$q3);
	$result = mysql_query ($q3);
	$currenttotal = mysql_num_rows ($result);

	?>

	<table width=100% class=h4>
		<tr>
			<td colspan=2 class=h5>
				<?=($limit + $currenttotal)?> from <?=$alltotal?> total data<br>&nbsp;
			</td>
		</tr>
	</table>

	<?
	tsp("","Search Results");
	?>	
		<table width="100%" height="100%" cellpadding="1", cellspacing="0" bgcolor="#AAAAAA">
			<tr>
				<td>		
						<script language="JavaScript" src="includes/javascript/sorttable.js" type="text/javascript"></script>
						<table class="sortable" preserve_style="cell" width="100%" border="0" cellspacing="1" cellpadding="1">
							<thead class="datadisplay"> <!--  class="datadisplay" -->
								<tr bgcolor="#CCCCCC">
									<td width="150">Account Code</td>
									<td width="250"> Account Number</td>
									<td width="150">Short Name</td>
								</tr>
							</thead>
							<tbody id="offTblBdy" class="datadisplay"> 
							<script type="text/javascript">
								var dt = new Array()
				
							<? 
							$count_row = 0;
							while ($row = mysql_fetch_object($result))
							{
								echo 'dt ['.$count_row.'] = "'.trim($row->nadd_advisor).'^'.
																							 trim($row->nadd_full_account_number).'^'.
																							 trim($row->nadd_short_name).'"'.";\n";
							
								$count_row = $count_row + 1;
							}
							?>						
									for (i=0;i<dt.length;i++)
									{
									var rowtrades_array = new Array()
									var rowclass
									var research_link
									if (i%2 == 0) {
										rowclass = "trdark";
									} else {
										rowclass = "trlight";
									}
									
									rowtrades_array=dt[i].split("^");
									
									document.write(
													"<tr class='" + rowclass + "' " +	"onmouseover=\"this.className = 'test'\" " + "onmouseout=\"this.className = '" + rowclass + "'\" " +	"onClick=\"this.className = 'selrow'\">"+"<td>&nbsp;&nbsp;&nbsp;"+rowtrades_array[0]+"</td>"+
													"<td><div align='left'>&nbsp; &nbsp; &nbsp; "+rowtrades_array[1]+"</div></td>"+
													"<td align='left'>&nbsp;" + rowtrades_array[2] + "&nbsp;&nbsp;&nbsp;</td></tr>");
									}
									</script>
						</tbody>
					</table>
				</td>
			</tr>
		</table>

	<?
	tep();
	show_number($numberperpage, $limit, $alltotal, $currenttotal, "test_paginate.php?srchval=".$srchval);

?>
</body>
</html>