			<script type="text/javascript" src="includes/javascript/calendar/calendar.js"></script>
			<script type="text/javascript" src="includes/javascript/calendar/lang/calendar-en.js"></script>
			<!-- helper script that uses the calendar -->
			<script type="text/javascript" src="includes/javascript/calendar/helper.js"></script>
			<link rel="alternate stylesheet" type="text/css" media="all" href="includes/javascript/calendar/calendar-win2k-2.css" title="win2k-2" />
			<script type="text/javascript">setActiveStyleSheet(document.getElementById("defaultTheme"), "win2k-2");</script>
			
<?
		echo "<center>";
		table_start_percent(100,"Configure Global Constants and Parameters");  
		echo "<br>";

		//Cutoff for Payout by total commission from Client.
		$qry_config = "select var_auto_id, var_name, var_value, var_description, var_date_begin, var_date_end, 
												  var_added_by, var_modified_by, var_isactive   
									 from var_global_parameters 
									 where var_isactive = '1'";
		echo $qry_config;
		$result_config = mysql_query($qry_config) or die (tdw_mysql_error(qry_config));
		?>
			<form name="config_params" action="<?=$PHP_SELF?>" method="post">
		<?
		while($row_config = mysql_fetch_array($result_config))
			{
			?>
			<input type="hidden" name="var_auto_id" value="<?=$row_config["var_auto_id"]?>">
			<input type="text" name="var_value" value="<?=$row_config["var_value"]?>">
			<input type="hidden" name="var_description" value="<?=$row_config["var_description"]?>">
			<input type="hidden" name="var_modified_by" value="<?=$row_config["var_auto_id"]?>">
			<br>			
			
			<?
			}

		table_end_percent();
?>