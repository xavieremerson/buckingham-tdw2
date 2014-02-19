<?

function tsp_b_px($width, $title) {
//align="center" 
echo '<table width="'.$width.'" cellpadding="0" cellspacing="0" bgcolor="#F7F7F7" style="BORDER-RIGHT: #000000 1px solid; 
        BORDER-TOP: #000000 1px solid; BORDER-LEFT: #000000 1px solid; BORDER-BOTTOM: #000000 1px solid;">
				<tr>
					<td>
						<table border="0" CELLPADDING="0" CELLSPACING="0">
							<tr>
								<td align="left" valign="top" nowrap style="FONT-WEIGHT: bold;	BORDER-TOP-STYLE: none;	
								PADDING-TOP: 0px; PADDING-BOTTOM: 2px; FONT-FAMILY: sans-serif;	
  							BORDER-RIGHT-STYLE: none;	BORDER-LEFT-STYLE: none;	background-image: url(images/tables5/base.png);	
								background-repeat: repeat-x;	color: #FFFFFF;	font-size: 10px;	letter-spacing: 2px;">&nbsp;&nbsp; '.$title.' &nbsp;&nbsp;</td>
								<td nowrap valign=top ><img src="images/tables5/r_angle.png"></td>
								<td nowrap width="100%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
				<td valign="top">
					<table width="100%" border="0" cellpadding="4" cellspacing="0">
						<tr>
							<td valign="top">';
}

function tep_b_px() {
echo '			  </td>
						</tr>
					</table>
				</td>
			</tr>
		</table>';
}

	tsp_b_px(100, "Email Recipients Maintenance"); 

  echo "test";
	
	tep_b_px(); 

?>