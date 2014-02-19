<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script type="text/javascript" src="../common/meta/js/dbGridFunctions.js"></script>
<link href="../common/meta/styles/datagrid.css" rel="stylesheet" type="text/css" />
<title>dbGrid Extreme - Example Page</title>
</head>
<body>
<?php
## Set up the Data Connection
	$hostname = "localhost";
	$username = "newadmin";
	$password = "newpassword";

	$Conn 		= mysql_pconnect($hostname, $username, $password) or trigger_error(mysql_error(),E_USER_ERROR); 
	
	$ActionCol 					= true;						// Defaults to true		- If true, will show a column with a link to EDIT/VIEW/DELETE current record =based on Primary Key)
	$ActionView 				= true;						// Defaults to true		- If ActionCol is True, then will allow the 'VIEW' Link
	$ActionSeeChildren 	= true;						// Defaults to true		- If ActionCol is True, then will allow the 'See <Children Data>' Link

	$ActionEdit 				= false;					// Defaults to true		- If ActionCol is True, then will allow the 'EDIT' Link
	$ActionDelete 			= false;					// Defaults to true		- If ActionCol is True, then will allow the 'DELETE' Link
	$ActionInsert 			= false;					// Defaults to true		- If ActionCol is True, then will allow the 'INSERT' Link


## Require the dbGrid Class File
	require('../common/classes/dbGrid.class.php');
	$Userlevel = 1;
	
	SetUserlevel($Userlevel);
	
	function SetUserlevel($Userlevel) {
		global $ActionEdit;							
		global $ActionDelete;	
		global $ActionInsert;	

		switch ($Userlevel) {
		case 1:
			$ActionEdit 				= true;
			$ActionDelete 			= true;
			$ActionInsert 			= true;
			break;
		case 2:
			$ActionEdit 				= true;
			$ActionDelete 			= false;
			$ActionInsert 			= false;
			break;
		case 3:
			$ActionEdit 				= false;
			$ActionDelete 			= false;
			$ActionInsert 			= false;
			break;
		}	
	}
 
## If we want another grid, we should instatiate another object - so that previous settings do not intefere with this one

## 'EXTENDED' Settings for DBGrid
	$GridName 			= 'GridClient';
	$database 			= "warehouse";
	$GridSQL 				= 'SELECT clnt_auto_id, clnt_code, clnt_name, clnt_isactive from int_clnt_clients where clnt_isactive = 1';
	$MaindbTable 		= 'int_clnt_clients';
	$Title 					= 'Clients';
	$FieldNameList 	= 'Client Code, Client Name';
	$ChildList			= 'id=mry_nfs_nadd.nadd_advisor';		//<Field used in Child Table>=<ChildTable>.<Child Field>
	$ActionSeeChildrenText = 'Accounts';
	$ParentList			= '';
	$Defaults				= '';
	$RowsPerPage		= 10;
	$BlobNumWords 	= 12;
	$DateFormat 		= 'd-m-Y H:i';
	
	$Validation="'short_name','#q','0',Field \'short_name\' is required'|'long_name','#q','0','Field \'long_name\' is required.'";
	
	//$objGrid1 = new dbGrid($GridName,$Conn,$database,$GridSQL,false); // Instatiate Object
	$objGrid1 = new dbGrid();																// Instatiate Object
	
	##Minimum paratmeters:
	$objGrid1->setMyName($GridName);												// Set the Name
	$objGrid1->setConn($Conn);																// Pass the connection
	$objGrid1->setDb($database);															// Pass the database name
	$objGrid1->setSQLMain($GridSQL);													// Pass the SQL Statement

	## Optional Parameters
	$objGrid1->setActionCol($ActionCol);												// Defaults to true		- If true, will show a column with a link to EDIT/VIEW/DELETE current record (based on Primary Key)
	$objGrid1->setActionView($ActionView );										// Defaults to true		- If ActionCol is True, then will allow the 'VIEW' Link	
	$objGrid1->setActionSeeChildren($ActionSeeChildren); 					// Defaults to true		- Will allow the 'See 'field's' Link
	$objGrid1->setActionSeeChildrenText($ActionSeeChildrenText);	// Defaults to 'Display <child_tablename>' - this is the text display in the action column for viewing 'children'. If not set the text will be 'Display <child_tablename>'
	$objGrid1->setActionEdit($ActionEdit);											// Defaults to true		- If ActionCol is True, then will allow the 'EDIT' Link
	$objGrid1->setActionDelete($ActionDelete);									// Defaults to true		- If ActionCol is True, then will allow the 'DELETE' Link
	$objGrid1->setActionInsert($ActionInsert);										// Defaults to true		- Will allow the 'INSERT' Link
	$objGrid1->setValidation($Validation);												// the Validation for update/insert See Documentation on validation

	$objGrid1->setMainDbTable($MaindbTable);									// Main Table to be used for Insert /Edit - DEFAULTS to first table in SQL Statement
	$objGrid1->setFieldNameList($FieldNameList);									// Defaults to none	 - Column Headings
	$objGrid1->setTitle($Title);																// Defaults to MainDbTable	-Title - Above the GridSQL	

	$objGrid1->setChildren($ChildList);													// Defaults to none	 	- For childGrids
	$objGrid1->setParents($ParentList);												// Defaults to none	 	- Tables used for Dropdowns in Insert / Edit

	$objGrid1->setRowsPerPage($RowsPerPage);									// Defaults to 20			- How many Rows per page (invalid if HasPaging is false)
	$objGrid1->setBlobNumWords($BlobNumWords); 							// Defaults to 25			- How many words to display whern a clumn is 'Blob' Data type	
	$objGrid1->setstrDateFormat($DateFormat);									// Defaults to 'Y-m-d'	- this affects the display of datatime  or Date data - but insert & update forms still show the YYY-MM-DD format required by mysql
	

	##The Following are just for this demonstration - they are the same as the default settings */
/*
	$objGrid1->setShowChildrenNoSelection(false);				// Defaults to false  	- When there is a child table, Should I show this table unfiltered if a selection on the parent (me) has not been made
	$objGrid1->setShowsFilter(false);										// Defaults to false  	- Shows the Where Statement as a H3 Title
	$objGrid1->setDefaultValues($Defaults);							// Defaults to none	 	- Values used in 'Insert from' as defaults....
	
	$objGrid1->setDateDefaultsToNow(true);						// Defaults to true		- Specify if the datetime column (if any ) defaults to 'Now()' when inserting a record
	$objGrid1->setbDebug(false);											// Defaults to false  	- Set Debug Mode on/Off

	$objGrid1->setColumnSort(true); 										// Defaults to true  	- Turns columns headings into sort links
	$objGrid1->setShowSortBox(true); 									// Defaults to true  	- Allows a uuser to input his own order By clause
	$objGrid1->setHasPaging(true);										// Defaults to true		- Has data set split into pages
	$objGrid1->setShowNavigation(true);								// Defaults to true	
	$objGrid1->setShowRecordInfo(true);								// Defaults to true	
	
	$objGrid1->setShowPageNums(true); 								// Defaults to true		- Shows a box with Page Number Links
	$objGrid1->setShowGoToPageBox(true); 						// Defaults to true 		- Shows an input box to put a page number in
	$objGrid1->setAlternateRowColors(true); 						// Defaults to true 		- Alternate Row colouring

	$objGrid1->setPrimaryKey('ID');										// Defaults to none		- If blank, the grid will try to identify the PK
	$objGrid1->setShowPrimaryKey(false);								// Defaults to false		- Show the Primary Key Column?
	$objGrid1->setShowRowCounter(true); 							// Defaults to true 		- Will Show a column with the row number	

	$objGrid1->setheaderwrap(false);									// Defaults to false		- Allow wrapping of Header?
	$objGrid1->setdatawrap(false);										// Defaults to false		- Allow wrapping of non blob data?
	$objGrid1->setDefaultStyleClass(true); 							// Defaults to true 		- If false, must be set after ->MyName

	$objGrid1->setEditPage(basename($_SERVER['PHP_SELF'])); // Defaults to basename($_SERVER['PHP_SELF']) - Set the page to which any EDIT/DELETE/VIEW/INSERT Commands should be sent
	$objGrid1->setDeletePage(basename($_SERVER['PHP_SELF'])); // Defaults to basename($_SERVER['PHP_SELF']) - Set the page to which any EDIT/DELETE/VIEW/INSERT Commands should be sent
	$objGrid1->setViewPage(basename($_SERVER['PHP_SELF'])); // Defaults to basename($_SERVER['PHP_SELF']) - Set the page to which any EDIT/DELETE/VIEW/INSERT Commands should be sent
	$objGrid1->setInsertPage(basename($_SERVER['PHP_SELF'])); // Defaults to basename($_SERVER['PHP_SELF']) - Set the page to which any EDIT/DELETE/VIEW/INSERT Commands should be sent
*/

	$objGrid1->execute();
	
	##NOW LET's DO THE CHILD GRID:
	$ParentGrid			= $GridName;
	$GridName 			= 'GridAccounts';
	$Title 					= 'Accounts';
	$GridSQL 				= 'SELECT mry_nfs_nadd.nadd_branch, mry_nfs_nadd.nadd_account_number, mry_nfs_nadd.nadd_full_account_number, 
	                          mry_nfs_nadd.nadd_advisor,mry_nfs_nadd.nadd_short_name,mry_nfs_nadd.nadd_rr_owning_rep
										 FROM mry_nfs_nadd
										 LEFT OUTER JOIN int_clnt_clients on mry_nfs_nadd.nadd_advisor=int_clnt_clients.clnt_code';
	$MaindbTable 		= 'mry_nfs_nadd'; 
	$FieldNameList 	= 'Branch, Account Number, Full Account Number, Advisor, Short Name, Registered Rep.';
	$ParentList			= 'int_clnt_clients=int_clnt_clients.clnt_code'; 	// MUST be <field(from this table)>=<table>.<DisplayField>
	$ParentLink			= 'int_clnt_clients.clnt_code=mry_nfs_nadd.nadd_advisor';															// MUST be <table>.<key>=<shownfield>
	$ChildList				= '';
	
	$Validation="'title','#q','0','Field \'title\' is required.'|'body','10','1','Field \'body\' is required and must contain at east 10 words.'|'status','#q','0','Field \'status\' is required.'|'posted','#q','0','Field \'posted\' is required.'";
	
	//$objGrid2 = new dbGrid($GridName,$Conn,$database,$GridSQL,false);								// Instatiate Object
	$objGrid2 = new dbGrid();								// Instatiate Object
	$objGrid2->setMyName($GridName);								// Set the Name
	$objGrid2->setConn($Conn);												// Pass the connection
	$objGrid2->setDb($database);											// Pass the database name
	$objGrid2->setSQLMain($GridSQL);		

	## Optional Parameters
	$objGrid2->setActionCol($ActionCol);											// Defaults to true		- If true, will show a column with a link to EDIT/VIEW/DELETE current record (based on Primary Key)
	$objGrid2->setActionView($ActionView );									// Defaults to true		- If ActionCol is True, then will allow the 'VIEW' Link	
	$objGrid2->setActionSeeChildren($ActionSeeChildren );				// Defaults to true		- Will allow the 'See 'field's' Link
	$objGrid2->setActionEdit($ActionEdit);										// Defaults to true		- If ActionCol is True, then will allow the 'EDIT' Link
	$objGrid2->setActionDelete($ActionDelete);								// Defaults to true		- If ActionCol is True, then will allow the 'DELETE' Link
	$objGrid2->setActionInsert($ActionInsert);									// Defaults to true		- Will allow the 'INSERT' Link

	$objGrid2->setValidation($Validation);	

	$objGrid2->setMainDbTable($MaindbTable);					// Main Table to be used for Insert /Edit - DEFAULTS to first table in SQL Statement
	$objGrid2->setFieldNameList($FieldNameList);					// Defaults to none	 - Column Headings
	$objGrid2->setTitle($Title);												// Defaults to MainDbTable	-Title - Above the GridSQL	

	$objGrid2->setChildren($ChildList);									// Defaults to none	 	- For childGrids
	$objGrid2->setParents($ParentList);								// Defaults to none	 	- Tables used for Dropdowns in Insert / Edit

	$objGrid2->setRowsPerPage($RowsPerPage);										// Defaults to 20			- How many Rows per page (invalid if HasPaging is false)
	$objGrid2->setBlobNumWords($BlobNumWords); 									// Defaults to 25			- How many words to display whern a clumn is 'Blob' Data type	
	$objGrid2->setstrDateFormat($DateFormat);						// Defaults to 'Y-m-d'	- this affects the display of datatime  or Date data - but insert & update forms still show the YYY-MM-DD format required by mysql

	$objGrid2->setParentGrid($ParentGrid);	
	$objGrid2->setParentLink($ParentLink);								// Defaults to none	 	- Tables used for Dropdowns in Insert / Edit
	$objGrid2->setActionSeeChildren(false);							// Defaults to false		- Will allow the 'See 'field's' Link
	$objGrid2->setParents($ParentList);								// Defaults to none	 	- Tables used for Dropdowns in Insert / Edit
	$objGrid2->setShowsFilter(true);										// Defaults to false  	- Shows the Where Statement as a H3 Title
	if ($objGrid1->ShowChildren) {
		$objGrid2->execute();
	} 
/*
	## 'QUICK' or 'BASIC' Settings for DBGrid
	$GridName 			= 'MyGrid2';
	$GridSQL 				= 'SELECT newsitems.id, category.name,author.short_name,title, body, status, posted';
	$GridSQL 				.= ' FROM newsitems';
	$GridSQL 				.= ' LEFT OUTER JOIN Category on newsitems.Category = Category.id';
	$GridSQL 				.= ' LEFT OUTER JOIN Author on newsitems.Author=Author.id';
	$GridSQL 				.= ' WHERE newsitems.status = 1 ';
	if (isset($_GET['MyGrid1SeeChild'])) {$GridSQL 				.= ' AND author='.$_GET['MyGrid1SeeChild'];}
	
	$GridSQL 				.= ' ORDER BY Author, posted DESC ';

	$bDebug 				= false;
	
	## Create an Instance, and pass the Name of the Grid, a connection object, and the SQL statement - 'QUICK MODE'
	## If the Connection and SQL are VALID, this will run all the way to writeGrid()......
	$objGrid2 = new dbGrid($GridName, $Conn, $database, $GridSQL, $bDebug); //NAME, CONNECTION, SQL, DEBUG
*/

?>
</body>
</html>