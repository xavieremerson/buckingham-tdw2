<?
  include('../includes/dbconnect.php');
  include('../includes/global.php');
	include('../includes/functions.php');

	ydebug("\n".'Process Start Time', date('m/d/Y H:i:s a'));

////
// REPS
$result_cleanup = mysql_query("TRUNCATE TABLE lkup_rrep") or die(mysql_error());

$qry_insert = "insert into lkup_rrep
								(ID,
								rr_num,
								rep_name,
								trad_rr) 
								SELECT
									a.ID, a.rr_num, concat(a.Lastname, ', ', a. Firstname) as rep_name, b.trad_rr 
									from users a, mry_comm_rr_trades b
								WHERE a.rr_num = b.trad_rr
								AND b.trad_rr like '0%'
								GROUP BY b.trad_rr";
$result_insert = mysql_query($qry_insert) or die(mysql_error());
ydebug("\n".'Reps Lookup created.', date('m/d/Y H:i:s a'));
////
// CLIENTS
$result_cleanup = mysql_query("TRUNCATE TABLE lkup_clients") or die(mysql_error());

$qry_insert = "insert into lkup_clients
								(comm_advisor_code,
								comm_advisor_name) 
					     SELECT clnt_code, clnt_name
								FROM int_clnt_clients
								WHERE clnt_code NOT LIKE '&%'
								AND clnt_isactive =1";
								/*SELECT comm_advisor_code, max( comm_advisor_name ) as comm_advisor_name 
									FROM rep_comm_rr_level_a
									WHERE comm_advisor_code NOT LIKE '&%'
									GROUP BY comm_advisor_code";*/
									
$result_insert = mysql_query($qry_insert) or die(mysql_error());
ydebug("\n".'Clients Lookup created.', date('m/d/Y H:i:s a'));
////
// SYMBOLS
$result_cleanup = mysql_query("TRUNCATE TABLE lkup_symbols") or die(mysql_error());

$qry_getsymbol = "SELECT DISTINCT(trad_symbol)
								  FROM rep_comm_rr_trades where trad_symbol != ''
									ORDER BY trad_symbol DESC";
$result_getsymbol = mysql_query($qry_getsymbol) or die(mysql_error());
$str_final = "";
while($row_getsymbol = mysql_fetch_array($result_getsymbol)){
$str_final = $row_getsymbol["trad_symbol"] . "^" . $str_final;
}

$qry_insert = "insert into lkup_symbols
								(trad_symbol) 
								values(
								'".$str_final."')";
$result_insert = mysql_query($qry_insert) or die(mysql_error());
ydebug("\n".'Symbols Lookup created.', date('m/d/Y H:i:s a'));



ydebug('Process Finish Time', date('m/d/Y H:i:s a'));

?>
