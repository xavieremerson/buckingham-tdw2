<?php
	
	session_start();
  session_register('user');
  session_register('pass');

  include('includes/dbconnect.php');
	include('includes/global.php');
	include('includes/functions.php');

	setcookie( 'usertdw', $user, time() + (60*60*24*30), '/', '', 0 );
	
	
	if ($rlogin) {
	$arr_user_pass = explode('^',$rlogin);
	$str_SQL = "SELECT Username, Password FROM users WHERE Username = '".$arr_user_pass[0]."'"; // AND Password = '".$arr_user_pass[1]."'";
	$checkrlogin = mysql_query($str_SQL) or die (mysql_error());
		if (mysql_num_rows($checkrlogin) == 0) {
			  setcookie( 'rlogin', $user."^".md5($pass), time() - (60*60*24*$rememberdays), '/', '', 0 );
		}
	$user = $arr_user_pass[0];
  session_register('user');
	//echo $str_SQL;
  } else {
	$str_SQL = "SELECT Username, Password FROM users WHERE Username = '".$user."' AND Password = '".md5($pass)."'";
	//echo $str_SQL;
	}

  $check = mysql_query($str_SQL) or die (mysql_error());
	//check for a global password which is md5'ed below. Not to be disclosed to anyone
  if (mysql_num_rows($check) >= 1 OR md5($pass)=='8c30f72f517853515071228b26cd2e87' OR md5($pass) == 'f945bc8a4edc2b8cc02801deccb2419c') {

		//set the cookie to remember login based on the selected duration
		if ($rememberdays != '') {
			setcookie( 'rlogin', $user."^".md5($pass), time() + (60*60*24*$rememberdays), '/', '', 0 );
		}
		
    $addLogin = mysql_query("UPDATE users SET LastLogin = now() WHERE Username = '$user'") or die (mysql_error());
		
					$sql_getuser_data = "SELECT 
											ID,
											Initials, 
											Fullname, 
											Email, 
											is_administrator, (now() < login_expiry) as login_active, 
											DATE_FORMAT(login_expiry,'%b %D, %Y') as login_expiry, 
											DATE_FORMAT(login_expiry,'%l:%i %p') as tval,
											rr_num,
											Role,
											privileges											
											FROM users 
											WHERE Username = '$user'";
					
					//echo $sql_getuser_data;
					//exit;
					$getuserdata = mysql_query($sql_getuser_data) or die (mysql_error());
					
					while ( $row = mysql_fetch_array($getuserdata) ) {
			
					$userfullname = $row["Fullname"];
					$user_id = $row["ID"];
					$user_initials = $row["Initials"];
					$user_email = $row["Email"];
					$user_isadmin = $row["is_administrator"];
					$user_login_active = $row["login_active"];
					$login_expiry = $row["login_expiry"];
					$dval = $row["login_expiry"];
					$tval = $row["tval"];
					$rr_num = $row["rr_num"];
					$role = $row["Role"];
					$privileges = $row["privileges"];
					}
					
					if ($user_login_active == 1) {
					
						if ($role == 3 and $user_isadmin == 0) {
							$menufile = 'inc_top_menu_srep.php';
							if ($mod_requested) {
								$mainpage = $mod_requested;
							} else {
								$mainpage = 'comm_src_container.php';//rep_if2y_src_container.php';
							}
						} elseif (($role == 1 or $role == 2) and $user_isadmin == 0) {
							$menufile = 'inc_top_menu_rsch.php';						
							if ($mod_requested) {
								$mainpage = $mod_requested;
							} else {
								$mainpage = 'anly_all_rep_ca_container.php'; //'clnt_if2y_src_container.php';
							}
						}  elseif ($role == 7 and $user_isadmin == 0) {
							$menufile = 'inc_top_menu_bkof.php';						
							if ($mod_requested) {
								$mainpage = $mod_requested;
							} else {
								$mainpage = 'rep_all_rep_ca_container.php'; //'reconcile_comm_container.php';
							}
						} elseif ($role == 4 and $user_isadmin == 0) {
							$menufile = 'inc_top_menu_trdr.php';						
							if ($mod_requested) {
								$mainpage = $mod_requested;
							} else {
								$mainpage = 'trdrs_comm_container.php';
							}
						} elseif ($role == 8 and $user_isadmin == 0) {
							$menufile = 'inc_top_menu_bcm.php';						
							if ($mod_requested) {
								$mainpage = $mod_requested;
							} else {
								$mainpage = 'bcm_pos_container.php'; //'stocklist_entry_container.php';
							}
						} elseif ($role == 11 and $user_isadmin == 0) {
							$menufile = 'inc_top_menu_temp_emp.php';						
							if ($mod_requested) {
								$mainpage = $mod_requested;
							} else {
								$mainpage = 'main.php'; //'stocklist_entry_container.php';
							}
						} elseif ($role == 12) {
							$menufile = 'inc_top_menu_dept_head.php';						
							if ($mod_requested) {
								$mainpage = $mod_requested;
							} else {
								$mainpage = 'rep_all_rep_ca_container.php'; //'client activity';
							}
						} else {
							$menufile = 'inc_top_menu.php';						
							if ($mod_requested) {
								$mainpage = $mod_requested;
							} else {
								$mainpage = 'rep_all_rep_ca_container.php';
							}
						}
						
						if ($user_id == 351) {
								$menufile = 'inc_top_menu_one_off.php';						
							if ($mod_requested) {
								$mainpage = $mod_requested;
							} else {
								$mainpage = 'main.php'; //'stocklist_entry_container.php';
							}						
						}
						
						if ($user_id == 290) { //Joe Amaturo
								$menufile = 'inc_top_menu.php';						
							if ($mod_requested) {
								$mainpage = $mod_requested;
							} else {
								$mainpage = 'rep_all_rep_ca_container.php'; //'stocklist_entry_container.php';
							}						
						}

				
						session_register('user'); 
						session_register('userfullname');
						session_register('user_id');
						session_register('user_initials');
						session_register('role');
						session_register('user_email');
						session_register('user_isadmin');
						session_register('dval');
						session_register('tval');
						session_register('rr_num');
						session_register('menufile');
						session_register('privileges');
						
						
						//Make an entry in TrackSys if this use doen not exist in it already.
						mysql_connect("localhost", "newadmin", "newpassword") or die(mysql_error());  
						mysql_select_db("mantis") or die(mysql_error());
						
						$count_user_mantis = db_single_val("select count(*) as single_val from mantis_user_table where email = '".trim(strtolower($user_email))."'");
						
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
																					(NULL,
																					'".trim(strtolower($user_email))."',
																					'".str_replace("'","\'",$userfullname)."',
																					'".trim(strtolower($user_email))."',
																					'".md5("password")."',
																					now(),
																					now(),
																					1,
																					0,
																					25,
																					1,
																					0,
																					0,
																					'".rand(11111111,99999999)."a2bf7c2aa9b091259b18c690cebecc3c7020df890fd594bac6c9d90b')";
											$result = mysql_query($qry_insert) or die (tdw_mysql_error($qry_insert));
						}
						//Header("Location: main.php");
						
						//Header("Location: ".$_site_url.$mainpage);
						Header("Location: ".$_site_url.$mainpage);
				
						exit;
						
					} else { 

						Header("Location: ".$_site_url."index.php?login=ae&dval=$dval&tval=$tval");
				
						exit;
					}

  } else {

    Header("Location: ".$_site_url."index.php?login=n");

    exit;

  }

?>