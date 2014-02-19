<?php

  include('top.php');
	 
?>
<tr>
<td valign="top">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td valign="top" class="appmytext">
		<!-- ADMIN CONTENT BEGIN -->



<?php
function usedtime() {
	list($msec,$sec)=explode(' ',microtime());
	return $msec+$sec;
}
$tstart=usedtime();
// GET VALUES
$host = "localhost";
$user = "root";
$db = "demo_compliance";
$pass = "password";
$sql = $_REQUEST["sql"];
$allsql = $_REQUEST["allsql"];
$max = $_REQUEST["max"];;
if (!$max) $max="0";
$sql = str_replace("\\","",$sql);
$allsql = str_replace("\\","",$allsql);
?>

<script>
function extractSql(){
	var sql = document.selection.createRange();
    document.eingabe.sql.value=sql.text;
    document.eingabe.submit();
}
</script>


<center>
<form name="eingabe" method="post" action="<?=$php_self?>">
<input type="hidden" name ="sql">
<table border=0 class="appmytext" cellspacing=1 width="100%">

<tr bgcolor="#FFA851"><td><b>HOST</b></td><td><input type="text" name ="host" value="<?=$host?>"></td>
<td rowspan=4 align="center" class="appmytext">
<b>SQL Query:<br></b>
<textarea cols = 60 rows=5 name="allsql"><?=$allsql?></textarea><br>
<input type="button" value="  execute  " name="do" onClick="javascript:extractSql();">
</td>
<td rowspan=4 align="center" class="appmytext">max. datalength in result <br>(0=no limit)<br>
<input type="text" name = "max" value="<?=$max?>" size = 5> <br><br>
<a href="javascript:document.eingabe.action='<?=$php_self?>?help=true';document.eingabe.submit();">show help</a>
</td>
</tr>
<tr bgcolor="#FFA851"><td><b>DATABASE</b></td><td><input type="text" name ="db" value="<?=$db?>"></td></tr>
<tr bgcolor="#FFA851"><td><b>USER</b></td><td><input type="text" name ="user" value="<?=$user?>"></td></tr>
<tr bgcolor="#FFA851"><td><b>PASS</b></td><td><input type="password" name ="pass" value="<?=$pass?>"></td></tr>
</form>
</table>
</center>
<hr color=black size=1>
<?php
if(!$_REQUEST["help"]){
	if ($host & $pass & $user & $db){
	    //connect to host
	    $con = @mysql_connect($host, $user, $pass) or die("<font color=red><b>Connection Error</b></font><br>");
	    @mysql_select_db($db) or die("<font color=red><b>Can't open Database $db</b></font>");

	    if($sql){
	        $done = "false";

	        //TABLEINFO
	        if (strpos($sql,"[TABLEINFO]")>-1){
	            $tmp = substr($sql,11,strlen($sql));
	            $abfrage = mysql_query("select * from $tmp limit 1") or die("<font color=red><b>SQL Error</b><br>".mysql_error()."</font>");
	            $rs = mysql_fetch_object($abfrage);
	            echo('<table class="appmytext" cellspacing= 1 cellpadding = 1 align=center>');
	            for ($i=0;$i<mysql_num_fields($abfrage); $i++){
	                $feld = mysql_field_name($abfrage,$i);
	                $len = mysql_field_len($abfrage,$i);
	                $type = mysql_field_type($abfrage,$i);
	                $flag = mysql_field_flags($abfrage,$i);
	                echo("<tr><td bgcolor=\"#FFA851\"><b>$feld</b></td><td bgcolor=\"#DBDBDB\">$type ($len)</td><td bgcolor=\"#DBDBDB\">$flag</td></tr>\n");

	            }
	            echo("</table>");
	            mysql_free_result($abfrage);
	            $done = "true";
	        }

            //LIST OF TABLES
	        if (strpos($sql,"[TABLELIST]")>-1 & $done=="false"){
                //mysql_select_db($datenbank,$conn_id)) or die("<font color=red><b>SQL Error</b><br>".mysql_error()."</font>");
                $abfrage = mysql_list_tables($db,$con);
                echo('<table class="appmytext" cellspacing= 1 cellpadding = 1 align=center>');
                echo "<tr><td bgcolor=\"#FFA851\"><b>List of all Tables</b></td></tr>\n";
                for($i=0; $i < mysql_num_rows($abfrage); $i++) {
					$tabelle = mysql_tablename($abfrage,$i);
					echo "<tr><td bgcolor=\"#DBDBDB\">$tabelle</td></tr>";
				}
                echo("</table>");
	            mysql_free_result($abfrage);
	            $done = "true";
	        }

	        // SELECT STMT
	        if ((strpos($sql,"select")>-1 | strpos($sql,"SELECT")>-1)&&($done=="false")){
	                echo("<b>Query:</b> $sql<br>");
	                echo('<table border=0 cellspacing= 1 cellpadding = 1 class="appmytext" width="100%" align=center>');
	                $abfrage = mysql_query($sql) or die("<font color=red><b>SQL Error</b><br>".mysql_error()."</font>");
	                $rs = mysql_fetch_array($abfrage);
	                echo("<tr>\n");
	                echo("<td bgcolor=\"#FFA851\"><b>#</b></td>\n");
	                for ($i=0;$i<mysql_num_fields($abfrage); $i++){
	                    $feld = mysql_field_name($abfrage,$i);
	                    echo("<td bgcolor=\"#FFA851\"><b>$feld</b></td>\n");
	                }
	                echo("</tr>\n");
	                mysql_free_result($abfrage);
                    $abfrage = mysql_query($sql) or die("<font color=red><b>SQL Error</b><br>".mysql_error()."</font>");
	                while($rs = mysql_fetch_array($abfrage)) {
	                $rn++;
	                    echo("<tr><td bgcolor=\"#E1E1E1\">$rn</td>\n");
	                    for($j=0;$j<$i;$j++){
	                    $back="#DBDBDB";
											//is_int($rn/2)
	                    //if (bcmod($rn,2)==0) 
											if (is_int($rn/2)) $back="#EAEAEA";
	                        $value = $rs[$j];
	                        if ($max){
	                            if(strlen($value)>$max) $value=substr($value,0,$max);
	                        }
	                        if (isset($value)) {
	                            echo("<td bgcolor=\"$back\">$value</td>");
	                        }else{
	                            echo("<td bgcolor=\"$back\">NULL</td>");
	                        }
	                    }
	                    echo("</tr>\n");
	                }
	                echo("</table>\n");
                    mysql_free_result($abfrage);
                    $done = "true";
	            }

                // OTHER STMT
	            if($done=="false"){
                    echo("<b>QUERY:</b> $sql<br>");
	                mysql_query($sql) or die("<font color=red><b>SQL Error</b><br>".mysql_error()."</font>");
	                echo("Row(s) affected: ".mysql_affected_rows());
                    $done = "true";
	            }
	    } else {
	        echo("<b>Note: </b>Please select your statement, only the selected text will be executed.");
	    }
	    mysql_close();
	} else {
	        echo("<b>Note:</b> I need a host, database, user and password to connect to a MySQL database.");
	}
}else{
?>
<table width="95%" border=0 align = center class="appmytext">
<tr><td class="appmytext">
<li><b>Executing SQL</b><bR>
Write a sql query into the textfield and selet the query with your mouse. Then press 'execute'.<br>
Why is it required to select the statement?<br>Thats not a bug, it's a feature. You can write more than one statement
and only execute one.
<br><br><li><b>Show all tables</b><bR>
To get a list of all tables just write <k>[TABLELIST]</k> into the textbox, mark it and press 'execute'.
<br><br><li><b>Describing a table</b><bR>
To get more infos about a table just write <k>[TABLEINFO] tablename</k> into the textbox, mark it and press 'execute'.
<br><br><li><b>SQL</b><bR>
Here are some examples of SQL Queries to copy and paste (modify):<br><br>
<ul>
<li>ADD FIELD: ALTER TABLE tablename ADD fieldname INT
<br>Other types than int: varchar(255), text, char(1), bigint, date
<br><li>DELETE FIELD: ALTER TABLE tablename DROP fieldname
<br><li>CREATE NEW DATABASE: CREATE DATABASE dbname
<br><li>DELETE DATABASE: DROP DATABASE dbname
<br><li>CREATE TABLE (with id as autoincrement key): <br>
CREATE TABLE tablename (<br>
id INT UNSIGNED NOT NULL AUTO_INCREMENT,<br>
person VARCHAR (255) DEFAULT '0',<br>
PRIMARY KEY(id), UNIQUE(id), INDEX(id)<br>
)
<br><li>DELETE TABLE: DROP TABLE tablename

</ul>
<br><li><b>Updates, bugreport or wanted features</b><bR>
Please visit my website <a href="http://www.powerweb99.at" target="_blank">www.powerweb99.at</a>
</td>
</table>
<?php
}
$tend=usedtime();
echo("<center>".round($tend-$tstart,2)." sec.</center>");
?>

		<!-- ADMIN CONTENT END -->
		</td>
  </tr>
</table>
</td>
</tr>

<?php

  include('bottom.php');
	 
?>

