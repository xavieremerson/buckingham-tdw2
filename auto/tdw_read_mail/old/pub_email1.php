<?
   //$ServerName = "{localhost/imap:143}INBOX"; // For a IMAP connection    (PORT 143)
   $ServerName = "{192.168.20.38/pop3:110}INBOX"; // For a POP3 connection    (PORT 110)
   
   $UserName = "tdw";
   $PassWord = "Trade!23";
   
   $mbox = imap_open($ServerName, $UserName,$PassWord) or die("Could not open Mailbox - try again later!");
   
   if ($hdr = imap_check($mbox)) {
	   echo "Num Messages " . $hdr->Nmsgs ."\n\n<br><br>";
   	$msgCount = $hdr->Nmsgs;
   } else {
   	echo "failed";
   }
   $MN=$msgCount;
   $overview=imap_fetch_overview($mbox,"1:$MN",0);
   $size=sizeof($overview);
   
   echo "<table border=\"0\" cellspacing=\"0\" width=\"582\">";
   
   for($i=$size-1;$i>=0;$i--){
   	$val=$overview[$i];
		$msg=$val->msgno;
   	$from=$val->from;
  		$date=$val->date;
		$subj=$val->subject;
   	$seen=$val->seen;
   
	   $from = ereg_replace("\"","",$from);
   
	   // MAKE DANISH DATE DISPLAY
   	list($dayName,$day,$month,$year,$time) = split(" ",$date); 
		$time = substr($time,0,5);
   	$date = $day ." ". $month ." ". $year . " ". $time;
   
   	if ($bgColor == "#F0F0F0") {
   		$bgColor = "#FFFFFF";
   	} else {
			$bgColor = "#F0F0F0";
		}
   
		if (strlen($subj) > 60) {
   		$subj = substr($subj,0,59) ."...";
		}
   
   	echo "<tr bgcolor=\"$bgColor\"><td colspan=\"2\">$from</td><td colspan=\"2\">$subj</td>
   		 <td class=\"tblContent\" colspan=\"2\">$date</td></tr>\n";
   }
	echo "</table>";
   imap_close($mbox);
?>

<?
   function get_mime_type(&$structure) {
   $primary_mime_type = array("TEXT", "MULTIPART","MESSAGE", "APPLICATION", "AUDIO","IMAGE", "VIDEO", "OTHER");
   if($structure->subtype) {
   	return $primary_mime_type[(int) $structure->type] . '/' .$structure->subtype;
   }
   	return "TEXT/PLAIN";
   }
   function get_part($stream, $msg_number, $mime_type, $structure = false,$part_number    = false) {
   
   	if(!$structure) {
   		$structure = imap_fetchstructure($stream, $msg_number);
   	}
   	if($structure) {
   		if($mime_type == get_mime_type($structure)) {
   			if(!$part_number) {
   				$part_number = "1";
   			}
   			$text = imap_fetchbody($stream, $msg_number, $part_number);
   			if($structure->encoding == 3) {
   				return imap_base64($text);
   			} else if($structure->encoding == 4) {
   				return imap_qprint($text);
   			} else {
   			return $text;
   		}
   	}
   
		if($structure->type == 1) /* multipart */ {
   		while(list($index, $sub_structure) = each($structure->parts)) {
   			if($part_number) {
   				$prefix = $part_number . '.';
   			}
   			$data = get_part($stream, $msg_number, $mime_type, $sub_structure,$prefix .    ($index + 1));
   			if($data) {
   				return $data;
   			}
   		} // END OF WHILE
   		} // END OF MULTIPART
   	} // END OF STRUTURE
   	return false;
   } // END OF FUNCTION
   
?> 
