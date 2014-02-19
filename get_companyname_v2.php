<?

// Turn off all error reporting
error_reporting(0);


include('includes/functions.php');


function getsinglecompanyname($ticker) {

	if (!fopen ("http://quote.yahoo.com/d?f=snl1d1t1c1p2va2bapomwerr1dyj1&s=".strtoupper($ticker), "r")) {
		return 'PROBABLE ERROR';
	} else {

			$file = fopen ("http://quote.yahoo.com/d?f=snl1d1t1c1p2va2bapomwerr1dyj1&s=".strtoupper($ticker), "r");
			
			while ($data = fgetcsv($file,4096, ",")) {
				$strCompany = $data[1] ; 
			}
						
			return $strCompany;

	}
}


$companyname = getsinglecompanyname(strtoupper($symbol));
if ($companyname == strtoupper($symbol) or $companyname == '') {
echo 'PROBABLE ERROR';
} else {
echo $companyname;
}
?>
