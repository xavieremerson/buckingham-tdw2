<?
  include('includes/dbconnect.php');
  include('includes/global.php');
  include('includes/functions.php');

	$arr[1][2] = array(1,'test');

	show_array($arr[1][2]);
	
	$arr[1][2][0] = $arr[1][2][0] + 1;
	$arr[1][2][1] = $arr[1][2][1]."','"."newtest";
	
	$arr[1][2][2] = $arr[1][2][2]."','"."newtest";
	
	show_array($arr);	
	
?>