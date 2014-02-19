<?		
include('../../includes/dbconnect.php');				
include('../../includes/functions.php');				
$arr_exclude = array("company",
								"limited",
								"the",
								"a",
								"an",
								"and",
								"of");

//initiate page load time routine
$time=getmicrotime(); 

?>
<style type="text/css">
<!--
.highlight_0 {
	font-family: Verdana;
	font-size: 12px;
	color: #3300FF;
	background-color: #E0F0F8;
	padding: 0px 4px;
	border: 1px solid #0099FF;
}
.highlight_1 {
	font-family: Verdana;
	font-size: 12px;
	color: #FF3300;
	background-color: #FFF2E6;
	padding: 0px 4px;
	border: 1px solid #FF6633;
}
.highlight_2 {
	font-family: Verdana;
	font-size: 12px;
	color: #009933;
	background-color: #ECFFEC;
	padding: 0px 4px;
	border: 1px solid #33CC00;
}
.highlight_3 {
	font-family: Verdana;
	font-size: 12px;
	color: #6600CC;
	background-color: #E6CCFF;
	padding: 0px 4px;
	border: 1px solid #A851FF;
}
.highlight_4 {
	font-family: Verdana;
	font-size: 12px;
	color: #990000;
	background-color: #FFD9D9;
	padding: 0px 4px;
	border: 1px solid #B66D6D;
}
.highlight_other {
	font-family: Verdana;
	font-size: 12px;
	color: #3300FF;
	background-color: #D4D0C8;
	padding: 0px 4px;
	border: 1px solid #FF6600;
}
.general {
	font-family: Verdana;
	font-size: 12px;
	color: #000000;
}
-->
</style>

<form name="srch" action="" method="get">
<input type="text" name="s" value="<?=$s?>" size="40" />
<input type="submit" name="submit" value="Search" />
</form>


<?

function hst ($str, $arr_str) {
	$str_new = "";
	foreach ($arr_str as $k=>$v) {
	  if ($k == 0) {
			$str_new = str_ireplace($v, '<span class="highlight_0">'.strtoupper($v).'</span>',$str);
		} elseif ($k == 1) {
			$str_new = str_ireplace($v, '<span class="highlight_1">'.strtoupper($v).'</span>',$str_new);
		} elseif ($k == 2) {
			$str_new = str_ireplace($v, '<span class="highlight_2">'.strtoupper($v).'</span>',$str_new);
		} elseif ($k == 3) {
			$str_new = str_ireplace($v, '<span class="highlight_3">'.strtoupper($v).'</span>',$str_new);
		} elseif ($k == 4) {
			$str_new = str_ireplace($v, '<span class="highlight_4">'.strtoupper($v).'</span>',$str_new);
		} else {
			$str_new = str_ireplace($v, '<span class="highlight_other">'.strtoupper($v).'</span>',$str_new);
		}
	}
	return $str_new;
}

function get_relevance ($arr_clean, $str) {
	$matched = 6;
	foreach ($arr_clean as $k=>$v) {
		if (stristr($str,$v)) {
			$matched = $matched - 1;
		}	
	}
	return $matched;
}

if ($s) {
					$s = trim($s);
					$arr_str = explode(" ", $s);
					//show_array($arr_str);
					$arr_clean = array();
					$str = " ent_num = 1234567 ";
					foreach ($arr_str as $k=>$v) {
						if (trim($v) != '' and !in_array(strtolower($v),$arr_exclude)) {
						  $arr_clean[] = $v;
							$str = $str . " or " . "concat(sdn_name, ' ', vess_owner, ' ', remarks) like '%".$v."%'" ;
						}
					}
					
					//show_array($arr_clean);
					
					echo '<p class = "general">';
					
					$q_srch = "SELECT *, concat( sdn_name, ' ', vess_owner, ' ', remarks ) as zzzzz
 														FROM ofac_sdn_list  
														WHERE ".$str;
					//xdebug("q_srch", $q_srch);
					$r_srch = mysql_query($q_srch) or die(mysql_error());
					$count_row = 1;
					$arr_display = array();
					while ( $row = mysql_fetch_array($r_srch) ) 
					{
						$arrindex = get_relevance($arr_clean, $row["zzzzz"])*100000 + rand(1,99999);
						$display_str = hst($row["sdn_name"],$arr_clean) . " ^ " . $row["vess_owner"] . " ^ " .$row["remarks"] . " ^ " .$row["program"] . " ^ " .$row["sdn_type"] . "\n<br><br>";
						$arr_display[$arrindex] = $display_str;
						$count_row = $count_row + 1;
					}
					
					asort($arr_display);
					//show_array($arr_display);
					
					echo '<table width="100%" border="1">';
					$display_count = 1;
					foreach($arr_display as $k=>$v) {
						echo '<tr><td>'.$display_count. ".</td><td>".$v.'</td></tr>';
						$display_count = $display_count + 1;					
					}	
					echo '</table>';				
					
					echo $count_row - 1 . " records found<br>";
}

					echo '</p>';
					echo "Search Time: ". sprintf("%01.2f",((getmicrotime()-$time)/1000))." s.\n<br>"; 						

?>
