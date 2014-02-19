<?
//************************************************************************************************
//************************************************************************************************
/*
THIS FILE IS TO BE RUN AS A JOB AT 6:00am IN THE MORNING SO USING THE PREVIOUS BUSINESS DAY FUNCTION
IT WILL ONLY PROCESS FILES THAT ARE CREATED IN THE PREVIOUS BUSINESS DAY. WILL CREATE SEPARATE EMAILS
FOR EACH RESEARCH DOCUMENT.

THIS FILE MUST BE RUN AS SHELL CMD IN BAT
*/
//************************************************************************************************
//************************************************************************************************
include('anr.jovus.config.inc.php');
include('anr.functions.php');

include('../../includes/functions.php');


		 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		 //Create and send emails:
				$email_log_txt = 'ATTACHMENT FILENAME : '. get_pdfname($docid)."\n".
												 'HEADLINE : '.$headline."\n".
												 'PUBLISHED : '.$publish_time."\n";

				$email_log_html = 'ATTACHMENT FILENAME : '. get_pdfname($docid)."<br>".
													'HEADLINE : '.$headline."<br>".
													'PUBLISHED : '.$publish_time."<br>";

				//create mail to send
				
				$subject = "[TEST Bloomberg ANR] ".get_pdfname($docid);
				$text_body = $email_log_txt;
				$html_body = $email_log_html;
				
				$arr_attachpdf = array(get_pdfname($docid)=>$pdflocation_bucknotes_tdw . get_pdfname($docid));
				
				zTextMailer('prasad_pravin@yahoo.com', "", $subject, $html_body, $text_body, $arr_attachpdf) ;
				zTextMailer('pprasad@centersys.com', "", $subject, $html_body, $text_body, $arr_attachpdf) ;
				zTextMailer('pprasad@centersys.com', "", $subject, $html_body, $text_body, $arr_attachpdf) ;
		 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
exit;
?>