<?php

include ("../../includes/global.php");
include ("../../includes/dbconnect.php");
include ("../../includes/functions.php");
/*
Trade Data Download from RBC Dain Rauscher
Location: https://dataworks.dainrauscher.com//output/rr6lbme/TRADE_FILE/<filename>
filename: TF123103.EXE  => This is the format
          TFMMDDYY.EXE     It is a zip file which has to be unzipped on download
*/

// THIS FILE IS SUPPOSED TO BE RUN AS A CRON JOB ON WEEKDAYS ONLY. A WEEKEND RUN HOWEVER
// WILL NOT CAUSE ISSUES, JUST WON'T DO ANYTHING.

$todayname = date("l");

echo $todayname . "<BR>";

if ($todayname == "Wednesday" or $todayname == "Thursday" or $todayname == "Friday") {


	//Check if today is a holiday, if it is, then exit.
	
	if ( check_holiday(date("Y-m-d")) == 1 ) {
	exit;
	}
	
	//Check if yesterday was a holiday, if it was, get day prior to that
	
	 $previous = time() - (60*60*24);
   $previousday = date("Y-m-d", $previous);
	 
	 if ( check_holiday($previousday) == 1 ) {
	 
	 	$previous = time() - (60*60*24*2);	
    $previousday = date("Y-m-d", $previous);
		
		}
		
  //Gotten the previous day, prepare filename
	
	$filetodownload = "TF".date("m", $previous).date("d", $previous).date("y", $previous).".EXE";
	echo $filetodownload. "<BR>";
	
	//Check prior download attempts from status table
	
			if (check_rbc_download ($filetodownload) == 0 ) {
			
			//Download Data
		  //-k option : turn off curl's verification of the certificate
			$exec_string = "/usr/bin/curl -k -O ".$_download_protocol."://".$_rbc_user.":".$_rbc_password."@".$_download_location.$_rbc_user."/TRADE_FILE/".$filetodownload;
			echo $exec_string;
			shell_exec($exec_string);
			
			//Unzip file
			shell_exec("unzip ".$filetodownload);
		
			$filetoupload = str_replace (".EXE", ".TXT", $filetodownload);	
			include ('upload_trades_to_db.php');
			
			//Write to status table and email tech support if failure
			
			$writestatus = mysql_query("insert into Status_downloads(sdow_filename, sdow_datetime, sdow_status) values('$filetodownload', now(), 1)") or die (mysql_error());
			
			} else {
			
			echo "The Trade File has already been downloaded.<BR>";
			write_status (1, "The file ".$filetodownload." has been downloaded successfully in a prior attempt.");
			
			}


	}
	
elseif ($todayname == "Monday") {

	//Check if today is a holiday, if it is then exit.
	
	if ( check_holiday(date("Y-m-d")) == 1 ) {
	exit;
	}
	
	//Check if prior Friday was a holiday, if it was, get Thursday prior to that

	 $previous = time() - (60*60*24*3);
   $previousday = date("Y-m-d", $previous);
	 
	 if ( check_holiday($previousday) == 1 ) {
	 
	 	$previous = time() - (60*60*24*4);	
    $previousday = date("Y-m-d", $previous);
		
		}

  //Gotten the previous day, prepare filename
	
	$filetodownload = "TF".date("m", $previous).date("d", $previous).date("y", $previous).".EXE";
	echo $filetodownload. "<BR>";
	
		//Check prior download attempts from status table
	
			if (check_rbc_download ($filetodownload) == 0 ) {
			
			//Download Data
		
			$exec_string = "/usr/bin/curl -k -O ".$_download_protocol."://".$_rbc_user.":".$_rbc_password."@".$_download_location.$_rbc_user."/TRADE_FILE/".$filetodownload;
			echo $exec_string;
			shell_exec($exec_string);
			
			//Unzip file
			shell_exec("unzip ".$filetodownload);
		
			$filetoupload = str_replace (".EXE", ".TXT", $filetodownload);	
			include ('upload_trades_to_db.php');
			
			//Write to status table and email tech support if failure
			
			$writestatus = mysql_query("insert into Status_downloads(sdow_filename, sdow_datetime, sdow_status) values('$filetodownload', now(), 1)") or die (mysql_error());
			
			} else {
			
			echo "The Trade File has already been downloaded.<BR>";
			write_status (1, "The file ".$filetodownload." has been downloaded successfully in a prior attempt.");
			
			}

	}
	
elseif ($todayname == "Tuesday") {

	//Check if today is a holiday, if it is then exit.

	if ( check_holiday(date("Y-m-d")) == 1 ) {
	exit;
	}
	
	//Check if yesterday (Monday) was a holiday, if it was, get Friday prior to that

	 $previous = time() - (60*60*24*1);
   $previousday = date("Y-m-d", $previous);
	 
	 if ( check_holiday($previousday) == 1 ) {
	 
	 	$previous = time() - (60*60*24*4);	
    $previousday = date("Y-m-d", $previous);
		
		}

  //Gotten the previous day, prepare filename
	
	$filetodownload = "TF".date("m", $previous).date("d", $previous).date("y", $previous).".EXE";
	echo $filetodownload. "<BR>";
	
	//Check prior download attempts from status table
	
			if (check_rbc_download ($filetodownload) == 0 ) {
			
			//Download Data
		
			$exec_string = "/usr/bin/curl -k -O ".$_download_protocol."://".$_rbc_user.":".$_rbc_password."@".$_download_location.$_rbc_user."/TRADE_FILE/".$filetodownload;
			echo $exec_string;
			shell_exec($exec_string);
			
			//Unzip file
			shell_exec("unzip ".$filetodownload);
		
			$filetoupload = str_replace (".EXE", ".TXT", $filetodownload);	
			include ('upload_trades_to_db.php');
			
			//Write to status table and email tech support if failure
			
			$writestatus = mysql_query("insert into Status_downloads(sdow_filename, sdow_datetime, sdow_status) values('$filetodownload', now(), 1)") or die (mysql_error());
			
			} else {
			
			echo "The Trade File has already been downloaded.<BR>";
			write_status (1, "The file ".$filetodownload." has been downloaded successfully in a prior attempt.");
			
			}

}

else {

echo "Today is a weekend!";

}



?> 