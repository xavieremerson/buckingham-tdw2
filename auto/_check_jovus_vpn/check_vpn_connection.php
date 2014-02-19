<?
error_reporting(E_ALL);
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// BEGIN JOVUS SECTION
// SQL Server Connection Information
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

$link = mssql_connect("192.168.1.78","buckinghamtwo_db","9eFah9fe");

if(!$link)
{
	shell_exec("c:\pstools\pskill vpngui.exe");
	echo "VPN Process killed"."\n";
	sleep(5);
	exec("schtasks /run /tn ____start_VPN");
	echo "VPN Process started"."\n";
} else {
  echo "VPN Connection to Hosted Jovus is working fine.\n"; 
}
?>