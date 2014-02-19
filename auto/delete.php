<?

  include('../includes/dbconnect.php');
  include('../includes/global.php');
	include('../includes/functions.php');
	include('../includes/functions_eq_opts.php');

	 $result_alt = mysql_query("select * from _jovus_migration_mri"); 
	 while ($row_alt = mysql_fetch_array($result_alt))  {
			$arr_explode = explode("<###>",$row_alt["mri_data"]);
	 		echo 	$arr_explode[1]."============".date('Y-m-d H:i:s',strtotime($arr_explode[1]))."<BR>";
	    $result_upd = mysql_query("update _jovus_migration_mri set mri_date = '".date('Y-m-d H:i:s',strtotime($arr_explode[1]))."' where auto_id = '".$row_alt["auto_id"]."'"); 
			
	 }



?>