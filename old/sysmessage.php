<center>
  <table width="400" border="2" cellpadding="0" cellspacing="0">
    <tr> 
      <td bgcolor="#0000FF">
			<font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif"><B><?=$headingval?></B></font></td>
    </tr>
    <tr> 
      
      <td align="right" valign="top" bgcolor="#000000">
			<center>
				<BR>
				<?
				if ($msgval == '_tm_futurerelease'){ $msgval = $_tm_futurerelease;}
				if ($msgval == '_tm_underconstruction'){ $msgval = $_tm_underconstruction;}
				if ($msgval == '_primarycontact'){ $msgval = $_primarycontact;}
				?>
				<font color="#FFFFFF" size="2" face="Verdana"><B><?=$msgval?></B></font><BR>
        <BR>

        </center>
				</td>
    </tr>
		<tr>
			<td>
   		<center>
        <BR>
        <input name="submit" type="button" onClick="JavaScript:history.back()" value="   OK   ">
				<BR>
				&nbsp;
			</center>
			</td>
		</tr>
  </table>
</center>
