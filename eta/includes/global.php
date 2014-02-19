<?php
//BRG

  //Client Specific Entries (Mostly for purposes of Branding)
	$_company_name = "The Buckingham Research Group, Inc."; 
		
	$_version                    = "2.02";
	$_app_name                   = "TDW";
	$_app_fullname               = "Trade Data Warehouse";
	$_app_last_update            = "08/16/2006";
	$_app_title                  = "TDW v 1.8b";
	$_app_administrator          = "pprasad@centersys.com";
	$_site_url                   = "http://192.168.20.63/tdw/";		//Trailing slash required
	$_comp_off_id                = '79';
	$_system_email_sender        = 'TDW Buckingham<buck@donotreply.com>';
	$_client_logo_url            = 'http://192.168.20.63/tdw/images/logo.gif';
	$_client_name                = "The Buckingham Research Group, Inc.";
	
	$_email_SMTP_Server          = "192.18.20.55";
	$_email_system_sender_name   = "TDW Buckingham";
	$_email_system_sender_email  = "buck@donotreply.com";	
	
	//$email_err_subject_prefix    = "TDW Error Alert: (".date('m/d/Y h:i a').") : ";
	
	$tdw_local_location          = "D:\\tdw\\tdw\\";   /* Trailing slash must exist */
	$download_location           = "D:\\nfs_data\\";   /* Trailing slash must exist */
	$exportlocation              = "D:\\tdw\\tdw\\data\\exports\\";   /* Trailing slash must exist */
	$exportchartlocation         = "D:\\tdw\\tdw\\data\\charts\\";   /* Trailing slash must exist */
	$export_compliance           = "D:\\tdw\\tdw\\data\\compliance\\";   /* Trailing slash must exist */
	$src_location           		 = "D:\\tdw\\tdw\\";   /* Trailing slash must exist */

	//Email links
	$_email_tech_support         = "mailto:support@centersys.com?Subject=Technical Support Request [".$_client_name." : ".$_app_title." : ID:".md5(rand(1,99999999))."]&Body=Problem Description:%0D--------------------%0D%0D%0D%0DSeverity:%0D---------%0D%0D%0D%0DMy Contact Information:%0D-----------------------";
	

	//Menus and Messages (Temporary and Permanent)
	$_tm_underconstruction       = "This feature is currently under development.<BR>Sorry about the inconvenience caused.<BR><BR>You will be notified of updates to <BR>the $_app_name <BR> via email. <BR><BR>Thank you.";
	$_tm_futurerelease           = "This feature is scheduled to be included in the next release.";
	$_headinginfo                = "Info";
	$_primarycontact             = "Carol M. Lew<BR>Ph: (917) 111-1111<BR>Email: support@csysg.com";
	$_primarycontactheading      = "Primary Contact Information";
	
	//APPLICATION LEVEL MESSAGE (usually to update all users with a notice)
	//$_global_header_message      = "This application is still in beta and as such please alert IT with  any problems encountered.";
	$_global_header_message      = "";
	
?>