<?php
//BRG

  //Client Specific Entries (Mostly for purposes of Branding)
	$_company_name = "The Buckingham Research Group, Inc."; 
		
	$_version = "0.1";
	$_app_name = "TDW";
	$_app_fullname = "Trade Data Warehouse";
	$_app_last_update = "01/18/2006";
	$_app_title = "TDW v 0.1";
	$_app_administrator = "pprasad@centersys.com";
	$_site_url = "http://192.168.20.63/tdw/";				//Trailing slash required
	$_comp_off_id = '79';
	$_system_email_sender = 'TDW Buckingham<buck@donotreply.com>';
	$_client_logo_url = 'http://192.168.20.63/tdw/images/logo.gif';
	$_client_name = "The Buckingham Research Group, Inc.";
	
	$download_location = "D:\\nfs_data\\";   /* Trailing slash must exist */;;

	$exportlocation = "D:\\tdw\\tdw\\data\\exports\\";   /* Trailing slash must exist */;

	//$_app_location = "/compliance/";      //Trailing slash required
	
	//Exporting Accounts information to csv file
	
	//$scriptlocation = "/var/www/html/compliance/data/exports/";      /* Trailing slash must exist */;
	$exportchartlocation = "D:\\tdw\\tdw\\data\\charts\\";   /* Trailing slash must exist */;
	//Email links
	$_email_tech_support = "mailto:support@centersysgroup.com?Subject=Technical Support Request (".$_client_name." : ".$_app_title.")&Body=Problem Description:%0D--------------------%0D%0D%0D%0DSeverity:%0D---------%0D%0D%0D%0DMy Contact Information:%0D-----------------------";
	

	//Menus and Messages (Temporary and Permanent)
	$_tm_underconstruction = "This feature is currently under development.<BR>Sorry about the inconvenience caused.<BR><BR>You will be notified of updates to <BR>the $_app_name <BR> via email. <BR><BR>Thank you.";
	$_tm_futurerelease = "This feature is scheduled to be included in the next release.";
	$_headinginfo = "Info";
	$_primarycontact = "Jason Briggs<BR>Ph: (917) 111-1111<BR>Email: support@csysg.com";
	$_primarycontactheading = "Primary Contact Information";
	
//APPLICATION LEVEL MESSAGE (usually to update all users with a notice)
//	$_global_header_message = "For testing and demonstration purposes, the <i>Trade Date</i> is fixed at <font color='red'>2/20/04.</font>";
	$_global_header_message = "Version currently under development. Please DO NOT use this version for purposes other than design/development!";
	
	
//Rounded corner tables used across the application

$table_start = '<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td width="9" height="9" background="images/tables2/lt.gif"></td>
    <td background="images/tables2/ts.jpg"></td>
    <td width="9" height="9" background="images/tables2/rt.jpg"></td>
  </tr>
  <tr> 
    <td width="9" background="images/tables2/ls.jpg"></td>
    <td valign="top">';
$table_end = '</td>
    <td width="9" background="images/tables2/rs.jpg"></td>
  </tr>
  <tr> 
    <td width="9" height="9" background="images/tables2/lb.jpg"></td>
    <td background="images/tables2/bs.jpg"></td>
    <td width="9" height="9" background="images/tables2/rb.gif"></td>
  </tr>
</table>';

/*
$table_start = '<table width="100%" border="0" cellspacing="0" cellpadding="0"> 
  <tr> 
    <td width="7" height="7" background="images/tables1/lt.gif"></td>
    <td background="images/tables1/ts.gif"></td>
    <td width="7" height="7" background="images/tables1/rt.gif"></td>
  </tr>
  <tr> 
    <td width="7" background="images/tables1/ls.gif"></td>
    <td valign="top">';
$table_end = '</td>
    <td width="7" background="images/tables1/rs.gif"></td>
  </tr>
  <tr> 
    <td width="7" height="7" background="images/tables1/lb.gif"></td>
    <td background="images/tables1/bs.gif"></td>
    <td width="7" height="7" background="images/tables1/rb.gif"></td>
  </tr>
</table>';
*/

/*
$table_start = '<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td width="7" height="7" background="images/tables1/lt.jpg"></td>
    <td background="images/tables1/ts.jpg"></td>
    <td width="7" height="7" background="images/tables1/rt.jpg"></td>
  </tr>
  <tr> 
    <td width="7" background="images/tables1/ls.jpg"></td>
    <td valign="top">';
$table_end = '</td>
    <td width="7" background="images/tables1/rs.jpg"></td>
  </tr>
  <tr> 
    <td width="7" height="7" background="images/tables1/lb.jpg"></td>
    <td background="images/tables1/bs.jpg"></td>
    <td width="7" height="7" background="images/tables1/rb.jpg"></td>
  </tr>
</table>';

*/
//Rounded corner tables used across the application
$table_start_h100 = '<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td width="7" height="7" background="images/tables1/lt.jpg"></td>
    <td background="images/tables1/ts.jpg"></td>
    <td width="7" height="7" background="images/tables1/rt.jpg"></td>
  </tr>
  <tr> 
    <td width="7" background="images/tables1/ls.jpg"></td>
    <td valign="top">';
$table_end_h100 = '</td>
    <td width="7" background="images/tables1/rs.jpg"></td>
  </tr>
  <tr> 
    <td width="7" height="7" background="images/tables1/lb.jpg"></td>
    <td background="images/tables1/bs.jpg"></td>
    <td width="7" height="7" background="images/tables1/rb.jpg"></td>
  </tr>
</table>';		
	
	
?>
