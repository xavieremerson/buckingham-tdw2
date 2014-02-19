<?
   include 'includes/global.php';
   include 'includes/dbconnect.php';
   include 'includes/functions.php';
   
$datefrom = format_date_mdy_to_ymd($iddatefrom);
$dateto = format_date_mdy_to_ymd($iddateto);


$output_filename = "compliance_notes.xls";
$fp = fopen($exportlocation.$output_filename, "w");

$str = '<html xmlns="http://www.w3.org/1999/xhtml">
				<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head> 
				<body>';
//fputs ($fp, $str);

			//create an array of report id's between the selected dates.
			$arr_rid = array();
			$qry_rid = "select auto_id, msrv_trade_date 
									from mgmt_reports_creation 
									where msrv_trade_date between '".$datefrom."' and '".$dateto."'
									and msrv_rep_id = 'DCARV2'"; 
									//between '2011-01-27' and '2011-01-31'
									//between '2009-08-01' and '2012-08-31'
			$res_rid = mysql_query($qry_rid) or die(tdw_mysql_error($qry_rid));
			while ($row = mysql_fetch_array($res_rid)) {
					$arr_rid[$row["msrv_trade_date"]] = $row["auto_id"];
			}
			
$str .= '<br>POTENTIAL AGENCY CROSS<br><table width="100%" border="1">
					 <tr>
           		<td>Trade Date</td>
           		<td>Symbol</td>
           		<td>Is PAC</td>
           		<td>Date</td>
           		<td>Person</td>
           		<td>Is Open</td>              
           		<td>Comment</td>
            </tr>';


			foreach ($arr_rid as $tdate=>$report_id) {

					 //First get Potential Agency Cross data if any.
					 $val_pac_exists = db_single_val("select count(*) as single_val from crep_agency_cross where pac_rep_id = '".$report_id."'");

					 if ($val_pac_exists > 0)   {

							$str_sql = "SELECT * 
																 FROM crep_agency_cross 
																 WHERE pac_rep_id = '".$report_id."' 
																 AND pac_isactive = 1
																 ORDER BY pac_entered_on DESC";
																 //xdebug("rep_auto_id",$rep_auto_id);
							$result = mysql_query($str_sql) or die(tdw_mysql_error($str_sql));
							while ( $row = mysql_fetch_array($result) ) {
							$commentor = db_single_val("select Fullname as single_val from users where ID = '".$row['pac_entered_by']."'");  
							$str .= '<tr>
              	<td>'.format_date_ymd_to_mdy($tdate).'</td>
              	<td>'.$row['pac_symbol'].'</td>
								<td>';
							
							if ($row['pac_yes_no'] == 1) { $str .= 'Yes'; } else {  $str .= 'No'; }
							
							$str .= '</td>
                <td>'.date('m/d',strtotime($row['pac_entered_on'])).'</td>
           			<td>'.$commentor.'</td>';
								
           		$str .= '<td>';
							
							if ($row['pac_isopen'] == 1) { $str .= 'Yes'; }
							
							$str .= '</td>              
                <td>'.$row['pac_comment'].'</td>
							<tr>';

							}
					 }
				} 

			 $str .= '</table>';

			 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

$str .= '
<br />EMPLOYEE TRADES<br />
<table width="100%" border="1">
	 <tr>
			<td>Trade Date</td>
			<td>Symbol</td>
			<td>Date</td>
			<td>Person</td>
			<td>Is Open</td>              
			<td>Employee</td>
			<td>Approver</td>
			<td>Trade Type</td>
			<td>Comments</td>
		</tr>';


foreach ($arr_rid as $tdate=>$report_id) {
		 $val_emp_exists = db_single_val("select count(*) as single_val from crep_emp_trades where emp_rep_id = '".$report_id."'");

		 if ($val_emp_exists > 0)   {

    $str_sql = "SELECT * FROM crep_emp_trades 
                       WHERE emp_rep_id = '".$report_id."' 
                       AND emp_isactive = 1
                       ORDER BY emp_entered_on DESC";
    $result = mysql_query($str_sql) or die(tdw_mysql_error($str_sql));
    while ( $row = mysql_fetch_array($result) ) {
    
		$str .= '<tr>
      <td>'.format_date_ymd_to_mdy($tdate).'</td>
      <td>'.$row['emp_symbol'].'</td>
			<td>'.date('m/d',strtotime($row['emp_entered_on'])).'</td>
			<td>'.get_user_by_id($row['emp_entered_by']).'</td>
			<td>';
			
		if ($row['emp_isopen'] == 1) { $str .= 'Yes'; } 
		
		$str .= '</td>              
			<td>'.get_user_by_id($row['emp_emp_id']).'</td>
			<td>'.get_user_by_id($row['emp_approver']).'</td>
      <td>';

      if ($row['emp_trade_type'] == 2) { 
        $str .= 'Vs. Restricted List';          
      } 
      
      if ($row['emp_trade_type'] == 1){
        $client_val = db_single_val("select clnt_name as single_val from int_clnt_clients where clnt_code = '".$row['emp_client_id']."'");
        $str .= 'Vs. Client => '.$client_val;        
      }
      if ($row['emp_trade_type'] == 3){
        $str .= 'Trade Approval Exception';         
      }

      $str .= '</td>
				<td>'.$row['emp_comment'].'</td>
		</tr>';
    }
	}
}

    $str .= '</table>';

			 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

$str .= '
<br />MRI<br />
<table width="100%" border="1">
	 <tr>
			<td>Trade Date</td>
			<td>Symbol</td>
			<td>Date</td>
			<td>Person</td>
			<td>Is Open</td>              
			<td>Employee</td>
			<td>Rating</td>
			<td>Target</td>
			<td>Analyst</td>
			<td>Port. Mgr.</td>
			<td>T-0</td>
			<td>T-1</td>
			<td>T-2</td>
			<td>T-3</td>
			<td>T-4</td>
			<td>MRI Reqd.</td>
			<td>Is Open</td>
			<td>Comments</td>
		</tr>';

foreach ($arr_rid as $tdate=>$report_id) {

	 $val_mri_exists = db_single_val("select count(*) as single_val from crep_mri_trades where mri_rep_id = '".$report_id."'");

	 if ($val_mri_exists > 0)   {

			$str_sql = "SELECT * FROM crep_mri_trades 
												 WHERE mri_rep_id = '".$report_id."' 
												 AND mri_isactive = 1
												 ORDER BY mri_entered_on DESC";
			$result = mysql_query($str_sql) or die(tdw_mysql_error($str_sql));
			while ( $row = mysql_fetch_array($result) ) {

      $str .= '<tr>
        <td>'.format_date_ymd_to_mdy($tdate).'</td>
        <td>'.$row['mri_symbol'].'</td>
        <td>'.date('m/d',strtotime($row['mri_entered_on'])).'</td>
        <td>'.get_user_by_id($row['mri_entered_by']).'</td>
        <td>';
				
			if ($row['mri_isopen'] == 1) { $str .= 'Yes'; } 
			
			$str .= '</td>              
        <td>'.get_user_by_id($row['mri_emp_mri']).'</td>
        <td>'.$row['mri_rating'].'</td>
        <td>'.$row['mri_target'].'</td>
        <td>'.get_user_by_id($row['mri_analyst']).'</td>
        <td>'.get_user_by_id($row['mri_portfol_mgr']).'</td>
        <td>'.$row['mri_t-0'].'</td>
        <td>'.$row['mri_t-1'].'</td>
        <td>'.$row['mri_t-2'].'</td>
        <td>'.$row['mri_t-3'].'</td>
        <td>'.$row['mri_t-4'].'</td>
        <td>';
				
			if ($row['mri_required'] == 1) { $str .= 'Yes'; } else { $str .= 'No'; } 
			
			$str .= '</td>
        <td>';
				
			if ($row['mri_isopen'] == 1) { $str .= 'Yes'; }
			
			$str .= '</td>
				<td>'.$row['mri_comment'].'</td>
      </tr>';

    }
	}
}

    $str .= '</table>';


			 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

$str .= '<br />SALES & RESEARCH APPROVAL<br />
<table width="100%" border="1">
	 <tr>
			<td>Trade Date</td>
			<td>Date</td>
			<td>Person</td>
			<td>Is Open</td>              
			<td>Comments</td>
		</tr>';

foreach ($arr_rid as $tdate=>$report_id) {

			//First get SRA data if any.
			$val_sra_exists = db_single_val("select count(*) as single_val from crep_sra_approval where sra_rep_id = '".$report_id."'");

			if ($val_sra_exists > 0)   {
			
				$str_sql = "SELECT * 
													 FROM crep_sra_approval 
													 WHERE sra_rep_id = '".$report_id."' 
													 AND sra_isactive = 1
													 ORDER BY sra_entered_on DESC";
													 //xdebug("rep_auto_id",$rep_auto_id);
				$result = mysql_query($str_sql) or die(tdw_mysql_error($str_sql));
				while ( $row = mysql_fetch_array($result) ) {
				$commentor = db_single_val("select Fullname as single_val from users where ID = '".$row['sra_entered_by']."'");  

        $str .= '<tr>
          <td>'.format_date_ymd_to_mdy($tdate).'</td>
          <td>'.date('m/d',strtotime($row['sra_entered_on'])).'</td>
          <td>'.$commentor.'</td>
          <td>';
					
				if ($row['sra_isopen'] == 1) { $str .= 'Yes'; } 
				
				$str .= '</td>
				<td>'.$row['sra_comment'].'</td>
        </tr>';

    }
	}
}

    $str .= '</table>';

			 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

$str .= '<br />OTHERS<br />
<table width="100%" border="1">
	 <tr>
			<td>Trade Date</td>
			<td>Date</td>
			<td>Person</td>
			<td>Is Open</td>              
			<td>Comments</td>
		</tr>';

foreach ($arr_rid as $tdate=>$report_id) {

		$val_oth_exists = db_single_val("select count(*) as single_val from crep_other_notes where oth_rep_id = '".$report_id."'");
		
		if ($val_oth_exists > 0)   {
		
			$str_sql = "SELECT * 
												 FROM crep_other_notes 
												 WHERE oth_rep_id = '".$report_id."' 
												 AND oth_isactive = 1
												 ORDER BY oth_entered_on DESC";
												 //xdebug("rep_auto_id",$rep_auto_id);
			$result = mysql_query($str_sql) or die(tdw_mysql_error($str_sql));
			while ( $row = mysql_fetch_array($result) ) {
			$commentor = db_single_val("select Fullname as single_val from users where ID = '".$row['oth_entered_by']."'");  

			$str .= '<tr>
				<td>'.format_date_ymd_to_mdy($tdate).'</td>
				<td>'.date('m/d',strtotime($row['oth_entered_on'])).'</td>
				<td>'.$commentor.'</td>
				<td>';
				
			if ($row['oth_isopen'] == 1) { $str .= 'Yes'; } 
			
			$str .= '</td>              
				<td>'.$row['oth_comment'].'</td>
			<tr>';

    }
	}
}

    $str .= '</table>';

			 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			
$str .= '</body>
</html>';

fputs ($fp, $str);
fclose($fp);

Header("Location: http://192.168.20.63/tdw/fileserve_xls.php?l=data/exports/&f=".$output_filename);
?>