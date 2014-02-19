<?
  include('includes/dbconnect.php');
	include('includes/global.php');
	include('includes/functions.php');


mysql_connect("localhost", "newadmin", "newpassword") or die(mysql_error());  
mysql_select_db("mantis") or die(mysql_error());

$count_user_mantis = db_single_val("select count(*) as single_val from mantis_user_table where email = '".trim(strtolower("jdoe"))."'");

if ($count_user_mantis == 0) {
					$qry_insert = "INSERT INTO mantis_user_table (
															id,
															username,
															realname,
															email,
															password,
															date_created,
															last_visit,
															enabled,
															protected,
															access_level,
															login_count,
															lost_password_request_count,
															failed_login_count,
															cookie_string) VALUES
															(3,
															'brg',
															'BRG Users',
															'pprasad@centersys.com',
															'a4c50154124e3f5d42e4ce41af928ad1',
															'2008-07-02 18:01:27',
															'2008-07-02 18:11:33',
															1,
															0,
															25,
															1,
															0,
															0,
															'bbd40f84a2bf7c2aa9b091259b18c690cebecc3c7020df890fd594bac6c9d90b')";
					$result = mysql_query($qry_insert) or die (tdw_mysql_error($qry_insert));
}
?>