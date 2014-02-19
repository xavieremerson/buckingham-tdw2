<?
	include('./includes/functions.php');

	$str_company_detail = get_company_detail('gesdd.x');
	//echo $str_company_detail . "<br />\n";
	$acd = explode("^", $str_company_detail);
	
	show_array($acd);
	
	//get first space after
	
	$str = 'GES Apr 2009 20.0';
	echo ">".strpos($str," ");
	echo substr($str,0,strpos($str," "));
	
	
	
?>