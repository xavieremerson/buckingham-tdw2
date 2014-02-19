<?
			######################################
			
			#Coded By Jijo Last Update Date[Jan/19/06]
			
			#####################################
			##########################################################			
			###################### Class readattachment ###############

class readattachment
{
	
		function getdecodevalue($message,$coding)
		{
		if ($coding == 0) 
		{ 
		   $message = imap_8bit($message); 
		} 
		elseif ($coding == 1) 
		{ 
		  $message = imap_8bit($message); 
		} 
		elseif ($coding == 2) 
		{ 
		   $message = imap_binary($message); 
		} 
		elseif ($coding == 3) 
		{ 
	   $message=imap_base64($message); 
	   } 
		elseif ($coding == 4) 
		{ 
		   $message = imap_qprint($message); 
		} 
		elseif ($coding == 5) 
		{ 
		 $message = imap_base64($message); 
		} 
		return $message;
		}

			function getdata($host,$login,$password,$savedirpath)
			{
			$mbox = imap_open ($host,  $login, $password) or die("can't connect: " . imap_last_error());
			$message = array();
			$message["attachment"]["type"][0] = "text";
			$message["attachment"]["type"][1] = "multipart";
			$message["attachment"]["type"][2] = "message";
			$message["attachment"]["type"][3] = "application";
			$message["attachment"]["type"][4] = "audio";
			$message["attachment"]["type"][5] = "image";
			$message["attachment"]["type"][6] = "video";
			$message["attachment"]["type"][7] = "other";
			
			
			echo "Starting Email Processing...\n";
			for ($jk = 1; $jk <= imap_num_msg($mbox); $jk++)
			{
			echo "Processing Email #".$jk."\n";
			$structure = imap_fetchstructure($mbox, $jk , FT_UID);    
			$parts = $structure->parts;
			
			//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			$val_header = imap_header($mbox, $jk);
			
			$from = $val_header->from;
			foreach ($from as $id => $object) {
				$fromname = $object->personal;
				$fromaddress = $object->mailbox . "@" . $object->host;
			}

			$email_log = '
						<table width="100%" border="0" cellspacing="0" cellpadding="10">
							<tr> 
								<td valign="top">
									<p><a class="bodytext12"><strong>'.$fromname.':</strong></a></p>			
									<p class="bodytext12">Your email has been received and processed.</p>
									<p>&nbsp;</p>
									<p>&nbsp;</p>
									<p><a class="bodytext12"><strong>TDW Administrator<br>tdw@buckresearch.com</strong></a></p></td>
							</tr>
						</table>
							';
			//create mail to send
			$html_body = "";
			$html_body .= TDWMailHeader("");
			$html_body .= $email_log;
			$html_body .= TDWMailFooter ();
			
			$subject = 'Email Received from '.$fromname.' ['.date('m/d/Y h:i:sa')."]";
			$text_body = $subject;
			
			
			
			TDWMailer($val_header->reply_toaddress, "", $subject, $html_body, $text_body, "") ;
			//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

			$fpos=2;
					for($i = 1; $i < count($parts); $i++)
					   {
						$message["pid"][$i] = ($i);
						$part = $parts[$i];

						if($part->disposition == "ATTACHMENT") 
							{
							
							$message["type"][$i] = $message["attachment"]["type"][$part->type] . "/" . strtolower($part->subtype);
							$message["subtype"][$i] = strtolower($part->subtype);
							$ext=$part->subtype;
							$params = $part->dparameters;
							$filename=$part->dparameters[0]->value;
														
									$mege="";
									$data="";
								  	$mege = imap_fetchbody($mbox,$jk,$fpos);  
									$filename="$filename";
									$fp=fopen($savedirpath.$filename,w);
									$data=$this->getdecodevalue($mege,$part->type);	
									fputs($fp,$data);
									fclose($fp);
									$fpos+=1;
							}			
					}
      //imap_delete tags a message for deletion
			imap_delete($mbox,$jk);
			}
      // imap_expunge deletes all tagged messages
			imap_expunge($mbox);
			imap_close($mbox);
			}
}


?>
