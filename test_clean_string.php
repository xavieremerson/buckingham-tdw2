<?
function tdw_clean_string($str) {
	return ereg_replace("[^&A-Za-z0-9 _]", "", $str);
}

echo tdw_clean_string('&SEGAW1F04');

?>