<?
ini_set('max_execution_time', 3600);
?>
<?
include('../../includes/functions.php');
?>


<?
$log_data = "";
//Backup the entire database (warehouse to the filesystem for restoration if required.
//Done on a daily basis at 3:00AM in the morning (all seven days), files overwritten

//ALL VARIABLES USED HERE.
$backup_folder_temp 	= "d:\\tdw\\tdw\\auto\\_backup_manual\\";  //(Backup Location)trailing slash required
$backup_folder_perm 	= "e:\\backup\\";  //(Backup Location)trailing slash required
$archive_location_perm = "\\\\bucksnapNY\\SHARE1\\TDWbackup\\";
$db_hostname		= "localhost";
$db_user 				= "newadmin";
$db_pw 					= "newpassword";
$db_database		= "warehouse_archive";
$loc_mysqldump	= "D:\\Program Files\\MySQL\\MySQL Server 5.0\\bin\\"; //trailing slash required
$temp_dump_file	= "file_warehouse_archive.dump";

$zip_name = date('l')."_warehouse_archive.zip";

$command = "\"".$loc_mysqldump."mysqldump"."\""." -v -c -h ".$db_hostname." -u".$db_user." -p".$db_pw." -r ".$backup_folder_perm.$temp_dump_file." --databases ".$db_database;

//echo $command;
//exit;

//$log_data .= $command."<br>";
$log_data .= date('m-d-Y h:i:s a')."<br>";
$time_start=getmicrotime(); 
$log_data .= "Dumping data to file...<br>";
shell_exec($command);
$log_data .= "<b>Time taken to process = </b>". sprintf("%01.7f",((getmicrotime()-$time_start)/1000))." s."."<br><br>"; 						


$log_data .= date('m-d-Y h:i:s a')."<br>";
$time_start=getmicrotime(); 
$log_data .= "Creating ZIP file...<br>";
shell_exec("\"C:\\Program Files\\WinZip\\wzzip\" " .$backup_folder_perm . $zip_name ." ". $backup_folder_perm . $temp_dump_file);
$log_data .= "<b>Time taken to process = </b>". sprintf("%01.7f",((getmicrotime()-$time_start)/1000))." s."."<br><br>"; 						

$log_data .= date('m-d-Y h:i:s a')."<br>";
$time_start=getmicrotime(); 
$log_data .= "Copying ZIP file...<br>";
shell_exec("copy ".$backup_folder_perm.$zip_name." ".$backup_folder_perm.$zip_name);
xdebug("copy ".$backup_folder_perm.$zip_name." ".$backup_folder_perm.$zip_name,'');
$log_data .= "<b>Time taken to process = </b>". sprintf("%01.7f",((getmicrotime()-$time_start)/1000))." s."."<br><br>"; 						

//Write log data to file
$log_data .= date('m-d-Y h:i:s a')."<br>";
$time_start=getmicrotime(); 
$log_data .= "Writing log file...<br>";
write_to_file($backup_folder_perm,"log_".date('l').".html",$log_data);
$log_data .= "<b>Time taken to process = </b>". sprintf("%01.7f",((getmicrotime()-$time_start)/1000))." s."."<br><br>"; 						

//copy zip file to permanent location for archival
$log_data .= date('m-d-Y h:i:s a')."<br>";
$time_start=getmicrotime(); 
$log_data .= "Writing log file...<br>";
shell_exec("copy ".$backup_folder_perm.$zip_name." ".$archive_location_perm.$zip_name);
write_to_file($backup_folder_perm,"log_".date('l').".html",$log_data);
$log_data .= "<b>Time taken to process = </b>". sprintf("%01.7f",((getmicrotime()-$time_start)/1000))." s."."<br><br>"; 						

//show the log data as well
echo $log_data;


?>