<?
////
// function to get the underlying security and some other information for an Option.

function gOptSymbol ($str) {

		// input is an option ticker in unformatted form so gotto clean it.
		$str_option = ereg_replace("[^A-Za-z]", "", $str);
		$str_option = $str_option.".x";
		

		//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		//echo "Processing : ".$str_option."\n";
		$str_whole = "";
		$lines = array();					
		$lines = file("http://finance.yahoo.com/q?s=".$str_option);
					
		//Get the content of the file into a string
		foreach ($lines as $key=>$value)
		{
			 if ($key == 1) {
			 $str_whole .=$value;
			 } //
		}
		
		//strip everything before the 1st <title
		$str_whole_a = substr($str_whole, strposnth($str_whole, "<title", 1, 1), 100000);
		
		//strip everything after the first </title
		$str_whole_b = substr($str_whole_a, 0, strposnth($str_whole_a, "</title", 1, 0)+8);
		$str_whole_c = strip_tags($str_whole_b);
		$arr_f = explode(" ", $str_whole_c);
		/* [3] => URBN[4] => Dec[5] => 2008[6] => 20.0000[7] => put	*/
		
		if ($arr_f[3] == '') {
		
				$arr_fourth = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","X");
				$arr_fifth =  array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","X","Y","Z");
				$arr_combined_letter = array();
				foreach($arr_fourth as $k=>$v) {
					foreach($arr_fifth as $kk=>$vv) {
						$arr_combined_letter[] = substr($str_option,0,3).$v.$vv.".x";
					}
				}
				
				//print_r($arr_combined_letter);
				$access_count = 0;
				$exit_condition = "";
				for ($access_count = 0; $access_count < 100; $access_count++) {
					if ($exit_condition == "") {
					//echo "start".$access_count;
					//******************************************************************************
					$str_option = $arr_combined_letter[$access_count];
					//echo $str_option . "<br>";
					
					$str_whole = "";
					$lines = array();
					$arr_f = array();					
					$lines = file("http://finance.yahoo.com/q?s=".$str_option);
								
					//Get the content of the file into a string
					foreach ($lines as $key=>$value)
					{
						 if ($key == 1) {
						 $str_whole .=$value;
						 } //
					}
					
					//strip everything before the 1st <title
					$str_whole_a = substr($str_whole, strposnth($str_whole, "<title", 1, 1), 100000);
					
					//strip everything after the first </title
					$str_whole_b = substr($str_whole_a, 0, strposnth($str_whole_a, "</title", 1, 0)+8);
					$str_whole_c = strip_tags($str_whole_b);
					$arr_f = explode(" ", $str_whole_c);
					//print_r($arr_f);
					$exit_condition = $arr_f[3];
					//echo $access_count . "<br>";
					//echo "[". $exit_condition ."]" . "<br>";
					/* [3] => URBN[4] => Dec[5] => 2008[6] => 20.0000[7] => put	*/
					///echo "end".$access_count;
					//******************************************************************************
					}
				}
		
		}
		
		
		$str_option_symbol = $arr_f[3];

		$lines = array();
		return $str_option_symbol;
		//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
}

function strposnth($haystack, $needle, $nth=1, $insenstive=1)
{
	 if ($insenstive) {
			 $haystack=strtolower($haystack);
			 $needle=strtolower($needle);
	 }
	 $count=substr_count($haystack,$needle);
	 if ($count<1 || $nth > $count) return false;
	 for($i=0,$pos=0,$len=0;$i<$nth;$i++)
	 {    
			 $pos=strpos($haystack,$needle,$pos+$len);
			 if ($i==0) $len=strlen($needle);
		 }
	 return $pos;
}

echo gOptSymbol("BHQ+WX") ;
?>