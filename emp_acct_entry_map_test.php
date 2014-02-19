<?
include('includes/dbconnect.php');
include('includes/global.php');
include('includes/functions.php');

 $qry = "SELECT min(ID) as ID, min(Lastname) as Lastname from users group by Lastname";
 $result = mysql_query($qry) or die(tdw_mysql_error($qry));
	
while ($row = mysql_fetch_array($result)) {

echo $row["ID"] . ">>>>". $row["Lastname"]."<BR>";


		$qry_1 = "select auto_id, emp_user_id,  emp_name_and_address_1, emp_name_and_address_2 
								 FROM emp_employee_accounts_master  
								 where emp_acct_status = 1 
								 AND 
								 (
										(
											emp_name_and_address_1 LIKE '%".trim(strtoupper(str_replace("'","\\'",$row["Lastname"])))."%'			 
										)
										OR
										(
											emp_name_and_address_2 LIKE '%".trim(strtoupper(str_replace("'","\\'",$row["Lastname"])))."%'			 
										)
								 )
								 ORDER BY emp_name_and_address_1"; 

			$result_1 = mysql_query($qry_1) or die(tdw_mysql_error($qry_1));
			while ($row_1 = mysql_fetch_array($result_1)) {
			
				echo "[".$row_1["emp_user_id"] ."] >>>> ". $row_1["auto_id"] . ">>>>". $row_1["emp_name_and_address_1"]. ">>>>". $row_1["emp_name_and_address_2"];
				
				$result_2 = mysql_query("update emp_employee_accounts_master set emp_user_id = '".$row["ID"]."' where auto_id = '".$row_1["auto_id"]."'" );
			
			}

			echo "<BR><BR>";
 }
?>