<?
//function get user_id from Initials
function get_userid_for_initials ($Initials) {
  //dupe initials caused problems
	$qry = "SELECT ID as single_val FROM users WHERE Initials = '".$Initials."' and Role < 5";
	$user_id = db_single_val($qry);   
	//xdebug("qry",$qry);
	//xdebug("user_id",$user_id);
	return $user_id;
}
?>