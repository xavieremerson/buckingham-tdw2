<?

  include('includes/dbconnect.php');
  include('includes/global.php'); 

$fp = fopen($exportlocation."AccountsMaster.csv", "w");

$result = mysql_query("SELECT * FROM nfs_nadd ORDER BY nadd_short_name, nadd_rr_exec_rep, nadd_full_account_number") or die (mysql_error());

$string = "\"ADVISOR\",\"SHORT NAME\",\"REGISTERED REP.\", \"ACCOUNT NUMBER\", \"ADDRESS LINE 1\", \"ADDRESS LINE 2\", \"ADDRESS LINE 3\", \"ADDRESS LINE 4\", \"ADDRESS LINE 5\", \"ADDRESS LINE 6\"\n";
fputs ($fp, $string);

	while ( $row = mysql_fetch_array($result) ) {

		$string = "\"".$row["nadd_advisor"]."\",\"".$row["nadd_short_name"]."\",\"".$row["nadd_rr_exec_rep"]."\",\"".$row["nadd_full_account_number"]."\",\"".$row["nadd_address_line_1"]."\",\"".$row["nadd_address_line_2"]."\",\"".$row["nadd_address_line_3"]."\",\"".$row["nadd_address_line_4"]."\",\"".$row["nadd_address_line_5"]."\",\"".$row["nadd_address_line_6"]."\"\n";
		fputs ($fp, $string);
	}

fclose($fp);

//echo "Location: data/exports/"."EmployeeAccounts_".Date("m-d-Y").".csv";

//This works!
Header("Location: data/exports/AccountsMaster.csv");


//Trying this (works too, has some issues)

//header('Content-type: application/pdf');

/*
header('Content-type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="data/exports/"."AccountsMaster.csv"');
readfile("data/exports/"."AccountsMaster.csv");
*/

?>