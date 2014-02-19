<?
////
// Returns the pdf name for a given docid
function get_pdfname ($docid) {
	include('anr.jovus.config.inc.php');
	$pdfname = $docid . '.pdf';
	$pdfwithlocation = $pdflocation_jovus . $pdfname;
	if (file_exists($pdfwithlocation)) {
		return $pdfname;
	} else {
		$filename = split('-',$docid);
		$pdfname = $filename[0].'.pdf';
  	$pdfwithlocation = $pdflocation_jovus . $pdfname;
		if (file_exists($pdfwithlocation)) {
			return $pdfname;
		} else {
		  return $docid. ".pdf";
		}
	}
}

////
// {{{ lpad($input, $padLength, $padString) 

function lpad($input, $padLength, $padString) { 
		return str_pad($input, $padLength, $padString, STR_PAD_LEFT); 
	} 

////
// {{{ rpad($input, $padlength, $padString) 

function rpad($input, $padlength, $padString) { 
		return str_pad($input, $padLength, $padString, STR_PAD_RIGHT); 
	} 
	
	////
// Give a meaningful error output
function csys_mysql_error($qry) {
return "<b>A fatal Database (MySQL) error occured</b>.\n<br>Query: " . $qry . "<br>\nError: (" . mysql_errno() . ") " . mysql_error();
}

?>