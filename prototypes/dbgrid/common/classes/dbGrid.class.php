<?php
// ============================================================================================================
//	Author: Redfern Reid-Pitcher
//	Web: 	
//	Name: 	dbGrid Extreme
// Desc: 	This is a DataGrid Class, supporting Cloumn sorting, Paging, etc. with many user setable features
// ============================================================================================================
class dbGrid {
	## ##### VARIABLE DECLARATION ##############################################
	/* ************************* Administrator Email Address ************************* */
	var $Author						= 'redfern@avatarweb.net';				// Please Change this if you distribute to others!!!

	/* ************************* Must be set either by the instantiation function or by the calling page ************************* */
	var $MyName;						// String				SET | GET
	var $Conn;							// Resource 		SET
	var $Db;								// String 				SET
	var $SQLMain;					// String				SET
	var $bDebug ;						// Boolean 			SET

	/* ************************* Defaults ************************* */
	## ####### Paging ############################################################
	var $HasPaging						= true;	  		// Boolean 	SET					| Default Setting
	var $RowsPerPage 					= 20; 			// Integer 	SET 					| Default Setting 
	var $ShowPageNums 				= true;			// Boolean 	SET					| Default Setting
	var $ShowNavigation 			= true;			// Boolean 	SET					| Default Setting
	var $ShowRecordInfo 			= true;			// Boolean 	SET					| Default Setting
	var $ShowGoToPageBox			= true;			// Boolean 	SET					| Default Setting
	
	var $PrimaryKey; 										// String		SET
	var $ShowPrimaryKey 			= false; 		// Boolean 	SET					| Default Setting
	var $ShowRowCounter			= true;			// Boolean 	SET					| Default Setting 

	var $ColumnSort  					= true; 		// Boolean 	SET					| Default Setting
	var $ShowSortBox 					= true;			// Boolean 	SET					| Default Setting
	var $AlternateRowColors 		= true;			// Boolean 	SET					| Default Setting
	var $headerwrap 					= false;		// Boolean 	SET					| Default Setting
	var $datawrap 						= false;		// Boolean 	SET					| Default Setting
	var $DefaultStyleClass 			= true;			// Boolean 	SET					| Default Setting
	
	var $ActionCol 						= true;			// Boolean 	SET					| Default Setting - Determines if there is an 'Action' column, containing edit /delete / view
	var $ActionView 					= true;			// Boolean 	SET					} available if I can
	var $ActionDelete 					= true;			// Boolean 	SET					} These only
	var $ActionEdit 						= true;			// Boolean 	SET					} glean the Primary
	var $ActionInsert 					= true;			// Boolean 	SET					} Key from the table
	var $ActionSeeChildren 			= false	;		// Boolean 	SET					} Key from the table
	var $ActionSeeChildrenText;
	
	var $Children;												// String 		SET 	
	var $ShowChildrenNoSelection=false;		// Boolean 	SET					| Default Setting

	var $EditPage;											// String		SET
	var $DeletePage;										// String		SET
	var $ViewPage;											// String		SET
	var $InsertPage;											// String		SET

	var $ParentLink;											// String		SET (By setParentLink)
	var $ParentLinkName;								// String		SET (By setParentLink)
	var $ParentGrid;
	var $ShowsFilter						=	false;

	var $MainDbTable;										// String		SET
	var $FieldNameList;									// String		SET
	var $Title;													// String 		SET	
	
	var $ActionHandling				= true;			// Boolean 	SET					| Default Setting - Should any Actions be handled by the Page?
	var $Validation;
		
	var $BlobNumWords  				= 10; 			// integer 	SET					| Default Setting
	var $strDateFormat				=	'Y-m-d'; 	// String 		SET					| Default Setting
	var $DateDefaultsToNow 		= true;			// Boolean 	SET					| Default Setting
	
## ############## USED ONLY WITHIN THE CLASS #####################################
	var $Parents							= array();	// Only within Class 			| Default Setting
	var $CurrentPage 					= 1;				// Only within Class 			| Default Setting	
	var $Offset 							= 0;				// Only within Class 			| Default Setting	
	var $MessageList 					= '';				// Only within Class 			| Default Setting
	var $ActionChangeTotalRows= 0;				// Only within Class 			| Default Setting
	var $basic								=	false;		// Only within Class 			| Default Setting

	var $GoStateReady				= false;		// Only within Class 			| Default Setting
	var $Mode 								= 'Grid';		// Only within Class 			| Default Setting
	var $ShowChildren 					= true;			// Only within Class 			| Default Setting
	var $AmChild 							= false; 		// Only within Class 			| Default Setting
	var $VARS 								= array(); 	// Only within Class 			| Default Setting
	var $OrderFields 					= array(); 	// Only within Class 			| Default Setting
	var $SQLFieldInfo 					= array(); 	// Only within Class 			| Default Setting THIS IS THE FIELD INFO FOR THE MAIN SQL STATEMENT
	var $TBLSQLFieldInfo 			= array(); 	// Only within Class 			| Default Setting THIS IS THE FIELD INFO FOR THE MAIN TABLE
	var $SQLFieldFlags					= array(); 	// Only within Class 			| Default Setting THIS IS THE FIELD FLAGS FOR THE MAIN SQL STATEMENT
	var $TBLFieldFlags	 				= array(); 	// Only within Class 			| Default Setting THIS IS THE FIELD FLAGS FOR THE MAIN TABLE
	
	var $SQLFieldArray 				= array();	// Only within Class 			| Default Setting
	
	var $DbTableList;										// Only within Class 
	var $FieldList;												// Only within Class 
	var $WhereSql;											// Only within Class 
	var $OrderBySql;										// Only within Class 

	var $SELF;													// Only within Class 

	var $rsMain;												// Only within Class 
	var $SQLColumns;										// Number of Columns SQL
	
	var $FieldTitles;											// Array
	var $FieldNames;										// Array
	var $DefaultsArr;										// Array
	var $SQLPKIndex;										// The index of Primary Key in the Main SQL Query
	var $TBLPKIndex ;										// The index of Primary Key in the Main Table
	var $TotalRows;					
	var $TotalPages;				
	var $RowsTag;
	var $PageTag;
	var $OrderTag;
	var $ViewTag;
	var $DeleteTag;
	var $EditTag;
	var $InsertTag;
	var $SeeChildrenTag;
	var $ParentFilterTag;
	var $DeleteTagDone;
	var $EditTagDone;
	var $InsertTagDone;
	var $CancelActionTag;
	var $ActionPK;
	var $sortActionBoxText;
	var $PageAction;
	var $sortBoxAction;
	var $sortLinksAction;
	var $EditAction;
	var $DeleteAction;
	var $ViewAction;
	var $InsertAction;
	var $SeeChildrenAction;
	var $UnSeeChildrenAction;
	var $RefreshAction;
	var $DivClass;								// String
	var $TableClass;							// String
	var $ActionClass;						// String
	var $SortClass;							// String
	var $SortBoxClass;						// String
	var $PageClass;							// String
	var $PageBoxClass;					// String
	var $NavigationClass;					// String
	var $MessageListClass;				// String

	## BEGIN INSTATIATION FUNCTION ################################################################################
	function dbGrid ($MyName='GridDF', $conn='', $database = '', $sql='', $bDebug=false ) {
		## Passed By the Calling function.... or Defaults
		$this->MyName 		= $MyName;
		$this->Conn 			= $conn;
		$this->Db 				= $database;
		$this->SQLMain 		= $sql;
		$this->bDebug 		= $bDebug;
		
		## Defaults!! Here let's set up the Grid with the ability to take no arguments except the query:
		$this->SELF					=	basename($_SERVER['PHP_SELF']);
		$this->EditPage				=	$this->SELF;
		$this->DeletePage			=	$this->SELF;
		$this->ViewPage				=	$this->SELF;
		$this->InsertPage			=	$this->SELF;

		$this->OpenDebug();

		$this->setDefaultStyleClass($this->DefaultStyleClass); //Sets up the Div and Table classnames

		$this->setVars(); 							// Puts all POST & GET Functions into our $VARS Array
		
		if  ((!$conn=='') && (!$sql=='') && (!$database=='')) {	// Check for  existence of a connection and query to display a basic grid
			$this->basic = true;
			$this->debugecho ("Connection Database, and SQL passed via Object Initialisation:<br />\n ::::>$this->SQLMain\n :: Now attempting to Parse and Run:" );
			$this->execute();
		} 
	}
	## END INSTATIATION FUNCTION  #################################################################################

	## BEGIN EXECUTE FUNCTION  ####################################################################################
	function execute (){
		if (isset($this->Db) && ($this->Db<>'')) {
			if(!mysql_select_db($this->Db, $this->Conn)) {
				$this->MYECHO("There was an error while trying to connect to database <strong>'$this->Db'</strong>. Please contact the administrator of this site.");
			}
		} else{
				$this->MYECHO("No database was selected  for connection. Please contact the administrator of this site.");
		}

		$this->SetTagNames();							// Set's the tags used in querystrings
		if (($this->AmChild) && (isset($this->ParentGrid)) && ($this->ParentGrid<>'')) {
			$this->SetParentTags();
		}


		$this->parseSQL();								// Parse the SQL statement given into it's constituent components
		$this->buildSQL();									// Re-build the SQL statement

		if (!isset($this->MainDbTable)) {
			$Tables 							= array();
			$Tables 							=	split('[, ]',$this->DbTableList);
			$this->MainDbTable 		= $Tables[0];
		}
		$this->MainDbTableStr = strtolower($this->MainDbTable);

		if (!isset($this->Title)) {$this->Title 		= $this->MainDbTable;}

		$this->SetSQLFieldInfo('Underlying');				// I want this in all sorts of places.. so let's set the underlying table info now...:)

		$this->SQLFieldArray 	= explode(',',strtolower($this->FieldList)); // Can strtolower because I just use it for searching

		$this->setPK();
		if(isset($this->PrimaryKey)) {
			$this->PrimaryKeyStr = strtolower($this->PrimaryKey);
		} else { //We need to make sure that action clumns and action handling is off
			unset($this->PrimaryKeyStr);
			$this->ActionHandling 	= false;
			$this->ActionCol 			= false;
			
		}
		## Let's quickly check that if HasPaging is true, then at least Navigation OR PageNums is on too - if not let's give them both!!
		if (($this->HasPaging) &((!$this->ShowPageNums) && (!$this->ShowNavigation))) {
				$this->ShowPageNums = true;
				$this->ShowNavigation = true;
		}

		##Let's Suppose that totalRows is =0... I still want to process all this shit, so that any VARS are passed to an insert page etc...
		if (count($this->VARS)>0) {
			$this->OrderBySql = ((isset($this->VARS[$this->OrderTag])) && ($this->VARS[$this->OrderTag]<>'') ? $this->VARS[$this->OrderTag] : $this->OrderBySql );
			$this->CurrentPage = (isset($this->VARS[$this->PageTag]) && $this->VARS[$this->PageTag]!=''  ? $this->VARS[$this->PageTag] : 1 );
		}

		$this->Mode = $this->CheckAction();

		if (($this->Mode!='Grid') && ($this->ActionHandling)) {
			$this->ActionHandler();
		}

		$this->TotalRows = $this->GetTotalRows(); // May be Null - if Paging is not needed......

		$this->debugecho ('$this->OrderTag='.((isset($this->VARS[$this->OrderTag])) && ($this->VARS[$this->OrderTag]<>'') ? $this->VARS[$this->OrderTag] : 'NULL' ));
		$this->debugecho ('$this->OrderBySql='.((isset($this->VARS[$this->OrderTag])) && ($this->VARS[$this->OrderTag]<>'') ? $this->VARS[$this->OrderTag] : $this->OrderBySql ));

		if (($this->Mode!='Insert') && ($this->Mode!='Update') && ($this->Mode!='Delete') && ($this->Mode!='View')) {
			$this->SQLMain 	.= ($this->OrderBySql<>'' ? ' ORDER BY '.$this->OrderBySql : '');
		}

		$this->debugEcho ('$this->SQLMain='.$this->SQLMain);
		$Run_query = false;

		if (!isset($this->TotalRows)) {
			if ($this->Mode!='Insert'){
				$Run_query = true;
				$this->CurrentPage = 1;
			} else {
				$this->GoStateReady = true;
			}
		} else {
			switch ($this->TotalRows) {
				case -2 :
					$this->debugecho ('There was an unknown error while trying to determine the Number rows in the Recordset');
					$this->Offset 		= $this->CalcOffset($this->CurrentPage);
					$this->SQLMain 	.= ' LIMIT '.$this->Offset.','.$this->RowsPerPage;
					$Run_query 		= true;
					 break;
				case -1 :
					 break;
				case 0 :
					if ($this->Mode!='Insert'){
						$this->MessageListAdd('The Recordset was empty - No Data to Be displayed');
						$this->CurrentPage = 1;
					} else {
						$this->GoStateReady = true;
					}
					 break;
				default: // IE NULL!!
					if($this->CurrentPage>$this->CalcTotalPages()) {
						$this->CurrentPage= $this->CalcTotalPages();
						$this->VARS[$this->PageTag] = $this->CurrentPage;
					}
					$this->Offset 		= $this->CalcOffset($this->CurrentPage);
					if(	$this->Offset >=$this->TotalRows) {
						$this->CurrentPage= $this->CalcTotalPages();
						$this->VARS[$this->PageTag] = $this->CurrentPage;
						$this->Offset 		= $this->CalcOffset($this->CurrentPage);
					}
					if ($this->Mode=='Grid') {
						$this->SQLMain 	.= ' LIMIT '.$this->Offset.','.$this->RowsPerPage;
						$Run_query 		= true;
					} else if ($this->Mode=='Insert') {
						$Run_query 		= false;
						$this->GoStateReady 		= true;
					} else {
						$Run_query 		= true;
					}
			}
		}
		if ($Run_query) {
			$this->rsMain 				= @mysql_query($this->SQLMain, $this->Conn) ;
			if (!mysql_errno()==0) {
				$this->MessageListAdd('There was an error while attempting to load the data - mysql error #:'.mysql_errno());
				$this->MessageListAdd('Error Message:'.mysql_error());
				$this->MessageListAdd('<br />Complete Query:'.$this->SQLMain);
				$this->TotalRows 			= -1;
			} else {

				if (!isset($this->TotalRows)) $this->TotalRows 		= mysql_num_rows($this->rsMain);
				if (!isset($this->TotalRows)) $this->RowsPerPage	= $this->TotalRows;
				$this->SQLColumns 	= mysql_num_fields($this->rsMain);
				$this->SetSQLFieldInfo();
				//if ($this->TotalRows>0) {
					$this->GoStateReady 		= true;
				//}
			}
		}
		$this->VARS[$this->RowsTag] = $this->TotalRows;
		$this-> setActionStrings();
		
		if ($this->GoStateReady) {
			## Set the Field Types
			$this->debugEcho ('Primary Key SET: '.$this->PrimaryKey);
			## Set Up the Order Statement for the Column heading Sort Tags
			if ($this->ColumnSort ) {$this->setOrderFields();}
		}
		
		$this->CloseDebug();
		
		if (isset($this->ParentLink)) {
			$tmp = explode('.', $this->ParentLink);
			$ParentTable = $tmp[0];
			$tmpLinkName = $tmp[0].'.'.$this->ParentLinkName;
		}
			
		if ((isset($this->FieldNameList)) && (!is_null($this->FieldNameList)) && ($this->FieldNameList!='')){
				$this->FieldTitles = array();
				$this->FieldTitles = explode(',', str_replace(' ','',$this->FieldNameList));
		} else {
			unset($this->FieldTitles);
		}

		$this->MYECHO('<h2>'.$this->Title.'</h2>');
	
			if ($this->ShowsFilter) {
			if ((isset($this->FieldNameList)) && (isset($this->ParentLinkName))	) {
				$x = in_array($this->ParentLinkName, $this->SQLFieldArray);
				$y = in_array($tmpLinkName, $this->SQLFieldArray);
				if ($x) {$tmpKey = array_search($this->ParentLinkName, $this->SQLFieldArray); }
				if ($y) {$tmpKey = array_search($tmpLinkName, $this->SQLFieldArray);}
				if (isset($tmpKey)) {
					$String=$this->FieldTitles[$tmpKey];
					$tmpString = $this->SQLFieldArray[$tmpKey];
					if (	(isset($this->ParentFilterTag)) && (isset($this->VARS[$this->ParentFilterTag])) && ($this->VARS[$this->ParentFilterTag]<>'') && ($this->Mode=='Grid')) {
						$TempSQL = "SELECT $tmpString FROM $ParentTable WHERE $this->ParentLink=".$this->VARS[$this->ParentFilterTag];
						$TempRS = @mysql_query($TempSQL, $this->Conn) ;
						if (!mysql_errno()==0) {
							$this->MessageListAdd('There was an error while attempting to fetch the Link Title - mysql error #:'.mysql_errno());
							$this->MessageListAdd('Error Message:'.mysql_error());
							$this->MessageListAdd('<br />Complete Query:'.$this->SQLMain);
						} else {
							$TempRow=mysql_fetch_array($TempRS);
							$String .= ' is '.$TempRow[0];
						}
					}
				}
			}
			$TitleString = str_replace('WHERE ', '', $this->WhereSql);
			if (isset($this->ParentLinkName) && (isset($this->ParentFilterTag)) && (isset($this->VARS[$this->ParentFilterTag])) && ($this->VARS[$this->ParentFilterTag]<>'') && ($this->Mode=='Grid')) {
				$TitleString = str_replace($this->ParentLink.'='.$this->VARS[$this->ParentFilterTag],$String,$this->WhereSql);
			}
			if ($this->Mode=='Grid') {
				$this->MYECHO(((isset($this->WhereSql)) &&  ($this->WhereSql<>'') ? '<h3>Where '.$TitleString.'</h3>': ''));
			}
		}
		$this->MYECHO($this->Mode=='Insert' ? '<h3>Insert Record</h3>': '');
		$this->MYECHO($this->Mode=='Update' ? '<h3>Update Record</h3>': '');
		$this->MYECHO($this->Mode=='Delete' ? '<h3>Delete Record</h3>': '');
		
		if (!$this->GoStateReady) {
				$this->mode='Empty';
				$this->doActionBox();
		}
		
				$this->DisplayMessageList();
				
		if ($this->GoStateReady) {
			if ((isset($this->FieldNameList)) && (!is_null($this->FieldNameList)) && ($this->FieldNameList!='')){
				$this->FieldTitles = array();
				$this->FieldTitles = explode(',', $this->FieldNameList);
				$TmpFieldTitles=Array();
				$i = 0;
				while ($i < $this->SQLColumns) {$TmpFieldTitles[$i]=mysql_field_name($this->rsMain,$i); $i++;	}
				if(!(count($this->FieldTitles)==count($TmpFieldTitles))) {
					unset($this->FieldTitles); // There aren't enough field names in the given list!!
					$this->debugEcho('There aren\'t enough field names in the given fieldname Listlist => I will revert to fieldnames from the query..:)') ;
				}
				unset($TmpFieldTitles);
			} 
			
			if ($this->Mode=='Grid') {
				$this->MYECHO('<div class="'.$this->GridClass.'">');

				if ($this->ShowSortBox) {$this->doSortBox();}
				if($this->HasPaging) {
					if (!isset($this->TotalPages)) 	{$this->TotalPages=$this->CalcTotalPages();}
					if ($this->ShowPageNums) 		{$this->doPageNums();}
				}
				$this->doActionBox();
				$this->doNavigation();
				$this->doGrid();
				$this->doNavigation();

				$this->MYECHO('</div>');
			} elseif (($this->Mode=='View') or ($this->Mode=='Delete')) {
				$this->MYECHO('<div class="'.$this->GridClass.'">');
				$this->doActionBox();
				$this->doViewDeleteForm();
				
			} elseif (($this->Mode=='Insert') or ($this->Mode=='Update')) {
				$this->doActionForm();
			}
		}
		
		if ((!$this->ShowChildrenNoSelection) && ($this->ActionSeeChildren) && (!isset($this->VARS[$this->SeeChildrenTag]))) {
				$this->ShowChildren=false;
		}
	} ## END EXECUTE FUNCTION  ####################################################################################
 
## ############################################################################################################
## SQL Setup Functions (parseSQL,buildSQL, buildActionSQL )  ###################################################
## ############################################################################################################
	function parseSQL() {
		$this->FieldList = '';
		$this->DbTableList='';
		$this->WhereSql='';
		$this->OrderBySql='';
		
		$UpperSQL = strtoupper($this->SQLMain);
		
		$Uppersqlcomponents = explode(' ', $UpperSQL);
		$sqlcomponents = explode(' ', $this->SQLMain);
		$i=0;
		$this->debugEcho ('Parse SQL Started:');
		while ($i < count($sqlcomponents)) {
			if ($Uppersqlcomponents[$i]=='SELECT') {$i++;continue;}
			if ($Uppersqlcomponents[$i]=='FROM') {$i++;continue;}
			if ($Uppersqlcomponents[$i]=='WHERE') {$i++;continue;}
			if ($Uppersqlcomponents[$i]=='ORDER') {$i++;continue;}				
			if ($Uppersqlcomponents[$i]=='BY') {$i++;continue;}		
			if ($i <array_search('FROM', $Uppersqlcomponents)) {
				$this->FieldList.=$sqlcomponents[$i];
			} else {
				if (!array_search('WHERE', $Uppersqlcomponents)) {
					if (!array_search('BY', $Uppersqlcomponents)) {
						$this->DbTableList.=$sqlcomponents[$i].' '; //No WHERE and No BY
					} else {
						if ($i <array_search('BY', $Uppersqlcomponents)) {
							$this->DbTableList.=$sqlcomponents[$i].' '; //No WHERE BUT is BY and is less than BY
						} else {	
							$this->OrderBySql.=' '.$sqlcomponents[$i]; //No WHERE BUT is BY and is more than BY
						}
					}
				} else {
					if (!array_search('BY', $Uppersqlcomponents)) { 
						if ($i <array_search('WHERE', $Uppersqlcomponents)) { 
							$this->DbTableList.=$sqlcomponents[$i].' ';// A WHERE but no BY and is less that WHERE
						} else {
							$this->WhereSql.=' '.$sqlcomponents[$i];// A WHERE but no BY and is more that WHERE
						}
					} else {
						//Where and BY both
						if ($i <array_search('WHERE', $Uppersqlcomponents)) { 
							$this->DbTableList.=$sqlcomponents[$i].' '; //WHERE and BY both it's less than WHERE
						} else {
							if ($i <array_search('BY', $Uppersqlcomponents)) {
								$this->WhereSql.=' '.$sqlcomponents[$i]; //WHERE and BY both it's more than WHERE and Less than BY
							} else {	
								$this->OrderBySql.=' '.$sqlcomponents[$i]; //No WHERE BUT is BY and is more than BY
							}
						}
					}
				}
			}
			$i++;
		}
		$this->FieldList = str_replace(',',',',$this->FieldList);
		$this->DbTableList = str_replace(',',',',$this->DbTableList);
		$this->WhereSql = str_replace(',',',',$this->WhereSql);
		$this->OrderBySql = str_replace(',',',',$this->OrderBySql);
		$this->debugEcho('Field List:: '.$this->FieldList);
		$this->debugEcho('Table List:: '.$this->DbTableList);
		$this->debugEcho('Where List:: '.$this->WhereSql);
		$this->debugEcho('Order List:: '.$this->OrderBySql);
		
	}
	
 	function buildSQL () {
		$this->SQLMain 		= '';
		$this->SQLMain 		.= 'SELECT '.((isset($this->FieldList) && ($this->FieldList<>'')) ? $this->FieldList : '*');
		$this->SQLMain 		.= ' FROM '.$this->DbTableList;
		$this->SQLMain 		.= ((isset($this->WhereSql) && ($this->WhereSql<>'')) ? ' WHERE '.$this->WhereSql : '');

		if (($this->AmChild) && (isset($this->ParentGrid)) && ($this->ParentGrid<>'')) {
			if (	(isset($this->ParentFilterTag)) && (isset($this->VARS[$this->ParentFilterTag])) && ($this->VARS[$this->ParentFilterTag]<>'')	) {
				$AddWhere = $this->ParentLink.'='.$this->VARS[$this->ParentFilterTag];
				$this->WhereSql		.= ((isset($this->WhereSql) && ($this->WhereSql<>'')) ? ' AND '.$AddWhere : 'WHERE '.$AddWhere);
				$this->SQLMain 		.= ((isset($this->WhereSql) && ($this->WhereSql<>'')) ? ' AND '.$AddWhere : 'WHERE '.$AddWhere);
			
			}
		}
 	}
 
 	function buildActionSQL($type) { // For Insert and Update Operations: $type can be either 'Insert' or 'Update'
		$SQLFieldStr 		= '';
		$SQLValStr 			= '';

		while (list ($key, $val) = each ($this->VARS)) {	
			$this->debugEcho("$key => $val\n");
			$pos = strpos($key, $this->MyName.'update_');
			if($pos===0) {
				if ($SQLValStr !='') {$SQLValStr=$SQLValStr.',';}
				if ($SQLFieldStr !='') {$SQLFieldStr=$SQLFieldStr.',';}

				$FieldName 	= str_replace($this->MyName.'update_','',$key);
				$numeric 	= $this->TBLSQLFieldInfo[$FieldName]->numeric;
				$tmpStr 		= ($numeric==1 ? "$val " :"'$val' ");
				
				if ($type=='Insert') {
					$SQLFieldStr 	.=$FieldName.' ';
					$SQLValStr 		.= $tmpStr;
				} elseif ($type=='Update')  {
					$SQLFieldStr 	.= $FieldName.'='.$tmpStr;
				}
			}
		}
		$SQL = ($type=='Insert' ? "INSERT INTO $this->MainDbTable ($SQLFieldStr) VALUES ($SQLValStr)" : "UPDATE $this->MainDbTable SET $SQLFieldStr WHERE $this->PrimaryKey=".$this->VARS[$this->EditTag]);
		return $SQL;
	}

	function SetSQLFieldInfo($type='Main') {
		if ($type=='Main') {
			for ($i = 0; $i < $this->SQLColumns; $i++) {
				$this->SQLFieldInfo[$i] = mysql_fetch_field($this->rsMain,$i);
				$this->SQLFieldFlags[$i] = mysql_field_flags($this->rsMain,$i);
			}
		} elseif ($type=='Underlying') { // Then it's the Underlying Recordset
			$fields 			= mysql_list_fields($this->Db, $this->MainDbTable ,$this->Conn );
			$columns 	= mysql_num_fields($fields);
			for ($i = 0; $i < $columns; $i++) {
				$field 															= mysql_fetch_field($fields, $i);
				$flag 															= mysql_field_flags ($fields, $i);
				$this->TBLSQLFieldInfo[$field->name] 	= $field;
				$this->TBLFieldFlags[$field->name] 			= $flag;
			}
		}
	}

	function setPK() {
		if((!isset($this->PrimaryKey)) or ($this->PrimaryKey=='')) {
			$i=0;
			while (list ($key, $val) = each ($this->TBLSQLFieldInfo)){
				if ($val->primary_key == 1) {
					$this->PrimaryKey 		= $val->name;
					$this->TBLPKIndex 	=$i;
					break;
				}
				$i++;
			}
			reset($this->TBLSQLFieldInfo);
			if(isset($this->PrimaryKey)) {$this->PrimaryKeyStr = strtolower($this->PrimaryKey);}
		} else {
			$this->PrimaryKeyStr 			= strtolower($this->PrimaryKey);
			$i=0;
			while (list ($key, $val) = each ($this->TBLSQLFieldInfo)){
				if ($val->name==$this->PrimaryKeyStr) {
					$this->TBLPKIndex 	=$i;
					break;
				}
				$i++;
			}
			reset($this->TBLSQLFieldInfo);
			if(!isset($this->TBLPKIndex)) { // It's Possible it's not in the underlying table... in which case the PK is invalid:
				unset($this->PrimaryKey);
				unset($this->PrimaryKeyStr);
				$i=0;
				while (list ($key, $val) = each ($this->TBLSQLFieldInfo)){
					if ($val->primary_key == 1) {
						$this->PrimaryKey 		= $val->name;
						$this->TBLPKIndex 	=$i;
						break;
					}
					$i++;
				}
				reset($this->TBLSQLFieldInfo);
				if(isset($this->PrimaryKey)) {$this->PrimaryKeyStr = strtolower($this->PrimaryKey);}
			}
		}
		// Now we need to know if & where it is in the Main SQL table
		
		if(isset($this->PrimaryKey)) {
			$this->SQLPKIndex = array_search($this->PrimaryKeyStr,$this->SQLFieldArray);			
			if ($this->SQLPKIndex>=0) { } else {
				$this->SQLPKIndex = array_search($this->MainDbTableStr.'.'.$this->PrimaryKeyStr,$this->SQLFieldArray);
				if ($this->SQLPKIndex>=0) { } else {
					unset($this->SQLPKIndex);
				}
			}
		}
	}

## #############################################################################################################
## Functions for Setting Paging (TotalRows, TotalPages, Offset)  ################################################
## #############################################################################################################
	function GetTotalRows() {
		//if (isset($this->VARS[$this->RowsTag])) {
		//	$TotalRows = $this->VARS[$this->RowsTag]+ $this->ActionChangeTotalRows;
		//}
		//if ((!$this->HasPaging) or ($this->RowsPerPage==0)) {
		//	$this->debugecho ('HasPaging is '.($this->HasPaging ? 'true BUT' : 'false').', '.(($this->RowsPerPage==0) && (!$this->HasPaging) ? 'AND': '').' RowsPerPage = '.$this->RowsPerPage.' so NO NEED to calculate the Total # rows in the recordset');
		//	return NULL;
		//} 
		
		if (((!isset($TotalRows)) or ($TotalRows==0))) {
			## 1 - That $TotalRows IS NOT Set (TotalRows was not passed)					-	Need to Run Query
			## 2 - That $TotalRows IS Set (TotalRows was Passed) and is =0 					-  Need to Run Query (Want to check!!)
			$this->debugecho ('HasPaging is true, RowsPerPage is not zero, and TotalRows is either not set or =0 - We Need to Run Query');

			$sqlCountStmt		= 'SELECT Count(*) FROM '.$this->DbTableList.((isset($this->WhereSql) && ($this->WhereSql<>'')) ? ' WHERE '.$this->WhereSql : '');
			$sqlCountResult 		= @mysql_query($sqlCountStmt, $this->Conn);

			if (!mysql_errno()==0) {
				
				$this->MessageListAdd('There was an error while attempting to load the data - mysql error #:'.mysql_errno());
				$this->MessageListAdd('Error Message:'.mysql_error());
				$this->MessageListAdd('<br />Complete Query:'.$this->SQLMain);
				$TotalRows 	= -1;
			} else {
				$row_Count 	= mysql_fetch_array($sqlCountResult);
				$TotalRows		=$row_Count[0];
				$this->debugecho('TotalRows: '.$TotalRows);
			}
		//} else {
		//	## 3 - That $TotalRows IS Set (TotalRows was Passed) and is >0 (Great!)	-	NO Need to Run query
		}
		return (isset($TotalRows) ? $TotalRows : -2);
	}

	function FindPK($Table) {
		$SQLTmp 				= "SELECT * FROM $Table LIMIT 0,1";
		$ResultTmp 			= @mysql_query($SQLTmp, $this->Conn) ;
		$NumFieldsTmp 	= mysql_num_fields($ResultTmp);
		$i = 0;
		while ($i< $NumFieldsTmp)  {
			$FieldTmp = mysql_fetch_field($ResultTmp,$i) ;
			if($FieldTmp->primary_key==1) {
				$PK=$FieldTmp->name;
				if($Table==$this->MainDbTable) {
					$this->PKIndex=$i;
				}
			}
			$i++;
		}
		return $PK;
	}

	function CalcTotalPages() {
		if($this->RowsPerPage==0) {
			$this->RowsPerPage= $this->TotalRows;
			return 1;
		} else {
			return ceil($this->Totalpages=($this->TotalRows/$this->RowsPerPage));
		}
	}

	function CalcOffset($page) {
		if($page>$this->CalcTotalPages()) {
			$page=$this->CalcTotalPages();
		}
		if($page=="") {
			$this->page=1;
			$page=1;
		}	else {
			$this->page=$page;
		}
		
		if($page=="1") {
			$this->Offset=0;
			return $this->Offset;
		}	else {
			for($i=2;$i<=$page;$i++) {
				$this->Offset=$this->Offset+$this->RowsPerPage;
			}
			return $this->Offset;
		}
	}

## ##################################################################################################################
## Action HANDLING Functions : (CheckAction, ActionHandler)  #######################################################
## ##################################################################################################################
	function CheckAction() {
		if ((isset($this->VARS[$this->ViewTag])) && (intval($this->VARS[$this->ViewTag]))<>0) {
			$thisis='View';
		} elseif ((isset($this->VARS[$this->DeleteTag])) && (intval($this->VARS[$this->DeleteTag])<>0)) {
			if ((isset($this->VARS[$this->DeleteTagDone])) && ($this->VARS[$this->DeleteTagDone])=='Delete') {
				$thisis='Delete-Done';
			} elseif (isset($this->VARS[$this->CancelActionTag])) {
				$thisis='Grid';
			} else {
				$thisis='Delete';
			}
		} elseif ((isset($this->VARS[$this->InsertTag])) && ($this->VARS[$this->InsertTag]==1)) {
			if ((isset($this->VARS[$this->InsertTagDone])) && ($this->VARS[$this->InsertTagDone])=='Insert') {
				$thisis='Insert-Done';
			}elseif (isset($this->VARS[$this->CancelActionTag])) {
				$thisis='Grid';
			} else {
				$thisis='Insert';
			}
		} elseif ((isset($this->VARS[$this->EditTag])) && (intval($this->VARS[$this->EditTag]))<>0) {
			if ((isset($this->VARS[$this->EditTagDone])) && ($this->VARS[$this->EditTagDone])=='Update') {
				$thisis='Update-Done';
			} elseif (isset($this->VARS[$this->CancelActionTag])) {
				$thisis='Grid';
			} else {
				$thisis='Update';
			}
		} else {
			$thisis='Grid';
		}
		return $thisis;
	}

	function ActionHandler() {
		## NOW LET's HANDLE ANY DELETE /UPDATE / INSERT STATEMENTS:
		## DELETE	############################################################################

		if (($this->Mode=='View') or ($this->Mode=='Delete')) {
			$this->SQLMain 		= '';
			$this->SQLMain 		.= "SELECT $this->FieldList";
			$this->SQLMain 		.= " FROM $this->DbTableList";
		}
		
		if ($this->Mode=='Update') {
			$this->SQLMain 		= '';
			$this->SQLMain 		.= "SELECT *";
			$this->SQLMain 		.= " FROM $this->MainDbTable";
		}

		if ($this->Mode=='Insert') {
			$this->SQLMain 		= '';
			//$this->SQLMain 		.= "SELECT *";
			//$this->SQLMain 		.= " FROM $this->MainDbTable";
		}

		switch ($this->Mode) {
			case 'Delete-Done' :
				$this->ShowChildren 						= true;
				$this->Mode 									= 'Grid';
				$tempSQL 										= "DELETE from $this->MainDbTable WHERE $this->PrimaryKey='".$this->VARS[$this->DeleteTag]."'";
				mysql_query($tempSQL, $this->Conn);
				$strMessage 									= (mysql_affected_rows()>0 ? mysql_affected_rows()." Records deleted\n": "There were no records deleted\n");
				$this->MessageListAdd($strMessage); 
				$this->ActionChangeTotalRows 	= -mysql_affected_rows();	
				break;
			case 'Insert-Done' :
				$this->ShowChildren 						= true;
				$this->Mode 									= 'Grid';
				$tempSQL 										= $this->buildActionSQL('Insert');
				mysql_query($tempSQL, $this->Conn);
				$strMessage 									= (mysql_affected_rows()>0 ? mysql_affected_rows()." Records inserted\n": "There were no records inserted\n");
				//$this->inserted_id 							= mysql_insert_id();
				$this->MessageListAdd($strMessage); 
				$this->ActionChangeTotalRows 	= mysql_affected_rows();	
				$this->CurrentPage = 1;
				break;
			case 'Update-Done' :
				$this->ShowChildren 						= true;
				$this->Mode 									= 'Grid';
				$tempSQL 										= $this->buildActionSQL('Update');
				mysql_query($tempSQL, $this->Conn);
				$strMessage 									= (mysql_affected_rows()>0 ? mysql_affected_rows()." Records updated\n": "There were no records updated\n");
				$this->MessageListAdd($strMessage); 
				break;
			case 'View' :
				$this->ShowChildren 						= false;
				$this->ActionPK 								= $this->VARS[$this->ViewTag];
				$this->SQLMain 								.= " WHERE $this->MainDbTable.$this->PrimaryKey=".$this->ActionPK;
				break;			
			case 'Delete' :
				$this->ShowChildren 						= false;
				$this->ActionPK 								= $this->VARS[$this->DeleteTag];
				$this->SQLMain								.= " WHERE $this->MainDbTable.$this->PrimaryKey=".$this->ActionPK;
				break;	
			case 'Insert' :
				$this->ShowChildren 						= false;
				//$this->SQLMain 								.= ' LIMIT 0,1';
				break;	
			case 'Update' :
				$this->ShowChildren 						= false;
				$this->ActionPK 								= $this->VARS[$this->EditTag];
				$this->SQLMain								.= " WHERE $this->MainDbTable.$this->PrimaryKey=".$this->ActionPK;
				break;									
			default:
			$this->MessageListAdd('In Actionhandler function, the Mode seems not to have been set?! Please report to the creator of this website');
		}
	}

	#######################################333
	function doViewDeleteForm () {
		$this->MYECHO('<table class="DFView">');
				
				$this->row_rsMain=mysql_fetch_array($this->rsMain);
				$i = 0;	
				while ($i < $this->SQLColumns)  { 
					$heading=ucwords(str_replace('_', ' ',mysql_field_name($this->rsMain,$i)));
					$colText=(isset($this->FieldTitles)  ? trim($this->FieldTitles[$i]) : $heading);
					$rowdata=$this->row_rsMain[$i];
					switch ($this->SQLFieldInfo[$i]->type) {
						case 'datetime':
							$rowdata=date($this->strDateFormat,strtotime($rowdata));               
							if ($this->datawrap==0) {$rowdata = str_replace(' ', '&nbsp;',$rowdata);}
						break;
						case 'blob':
							$rowdata=nl2br(htmlentities($rowdata));
						break;
					}
					?>
<tr>
	<th><?php $this->MYECHO($colText); ?>&nbsp;:</th>
	<td><?php $this->MYECHO($rowdata); ?></td>
</tr>
<?php $i++;} ?>
</table>
<table>
	<tr>
		<td><?php
			if ($this->Mode=='Delete') {
				$ActionFormStr 	="\n";
				$ActionFormStr = '<form id="'.$this->MyName.'Deleteform" action="'.$this->DeleteAction.$this->ActionPK.'" method="post" class="dsfsdf">'."\n";
				$ActionFormStr .= '<input type="submit" name="'.$this->DeleteTagDone.'" value="Delete" class="sdfsdf" />'."\n";
				$ActionFormStr .= '<input type="submit" name="'.$this->CancelActionTag.'" value="Cancel" class="sdfsdf" />'."\n";
				$ActionFormStr .= '</form>'."\n";
				$this->MYECHO($ActionFormStr);
			}
			?>
		</td>
	</tr>
</table>
<?php
	}
	
 	function SetParentTags() {
 		$this->ParentFilterTag				= $this->ParentGrid.'SeeChild';
 	}
 	
 	function SetTagNames() {
		$this->RowsTag				= $this->MyName.'TotalRows';
		$this->PageTag				= $this->MyName.'Page';
		$this->OrderTag				= $this->MyName.'Sort';
		$this->ViewTag				= $this->MyName.'View';
		$this->DeleteTag			= $this->MyName.'Delete';
		$this->EditTag				= $this->MyName.'Edit';
		$this->InsertTag			= $this->MyName.'Insert';	
		$this->SeeChildrenTag	= $this->MyName.'SeeChild';	
		$this->DeleteTagDone	= $this->MyName.'DeleteDone';		
		$this->EditTagDone		= $this->MyName.'EditDone';
		$this->InsertTagDone	= $this->MyName.'InsertDone';		
		$this->CancelActionTag= $this->MyName.'CancelAction';
	}
	
	function doActionForm() {
		$i = 0;	
		if ((isset($this->Validation)) && ($this->Validation<>'') && (!is_null($this->Validation))) {
			$validationArray=explode('|',$this->Validation);
			if(count($validationArray)>0) {
				$newValue='';
				foreach ($validationArray as $value) {
				
					if ($newValue!='') {$newValue.=',';}
    				$newValue.=substr_replace($value, "'".$this->MyName.'update_', 0,1);
				}
			}
			$ValidationString="YY_checkform('update',".$newValue.');return document.YY_returnValue';
		}
				
		$this->MYECHO('<form method="post" id ="update" action= "'.($this->Mode=='Update' ? $this->EditAction.$this->VARS[$this->EditTag] : $this->InsertAction.$this->VARS[$this->InsertTag]).'">');
		$this->MYECHO('<table class="DFView">');
		if (count($this->Parents)>0) {
			$DDtables = array();
			$DDtables = explode(',',$this->FieldList);
		}
		
		if ($this->Mode=='Update') {
			$this->row_rsMain=mysql_fetch_array($this->rsMain);
		}

		while (list ($key, $val) = each ($this->TBLSQLFieldInfo))  { 
			$FieldInfo = $this->TBLSQLFieldInfo[$key];
			$Flags = explode(' ',$this->TBLFieldFlags[$key]);
			$heading=ucwords(str_replace('_', ' ',$key));
			$colText=(isset($this->FieldTitles)  ? trim($this->FieldTitles[$i]) : $heading);

			if ($this->Mode=='Update') {
				$rowdata=$this->row_rsMain[$i];
				switch ($val->type) {
					case 'datetime':
						$rowdata=date('Y-m-d H:i',strtotime($rowdata));               
					break;
					case 'blob':
						$rowdata=htmlentities($rowdata);
					break;
				}
			} else {
				$rowdata=NULL;
				if ($val->type=='datetime') {
					$rowdata = date('Y-m-d H:i');
				}
			}
			
			if (in_array('auto_increment',$Flags )) {
				$rowdata='<input type="text" name="'.$this->MyName.'update_'.$key.'" value="'.$rowdata.'" size="'.$FieldInfo->max_length.'" disabled="disabled"/> (Auto Increment)';
			} elseif(in_array('primary_key',$Flags ))  {
				$rowdata='<input type="text" name="'.$this->MyName.'update_'.$key.'" value="'.$rowdata.'" size="'.$FieldInfo->max_length.'"/> Must be Unique';
			} elseif ((count($this->Parents)>0) &&  (isset($this->Parents[$key]))) {
				$tmpTable 	= $this->Parents[$key]['table'];
				$PKSub 		= $this->FindPK($this->Parents[$key]['table']);
				$tmpField 	= $this->Parents[$key]['field'];
				if ($PKSub<>$tmpField ) {
					$tmpSQL 	= " SELECT $PKSub,$tmpField FROM $tmpTable";
				} else {
					$tmpSQL 	= " SELECT $PKSub FROM $tmpTable";
				}

				$rsSub 				= @mysql_query($tmpSQL , $this->Conn) ;
				if (!mysql_errno()==0) {
					$rowdata='			<input type="text" name="'.$this->MyName.'update_'.$key.'" value="'.$rowdata.'" />';
				} else {
					$tmpData = '			<select name="'.$this->MyName.'update_'.$key.'">'."\n";
					while($tmpRow=mysql_fetch_array($rsSub )) {
							$tmpData .= '				<option value="'.$tmpRow[0].'"';
							$tmpData .=(($this->Mode=='Update') && ($this->row_rsMain[$i]==$tmpRow[0]) ? ' selected="selected"': '');

							if (($this->AmChild) && (isset($this->ParentGrid)) && ($this->ParentGrid<>'')) {
								if (	(isset($this->ParentFilterTag)) && (isset($this->VARS[$this->ParentFilterTag])) && ($this->VARS[$this->ParentFilterTag]<>'')	) {
									$tmpData .=(($this->Mode=='Insert') && ($this->VARS[$this->ParentFilterTag]==$tmpRow[0]) ? ' selected="selected"': '');								
								}
							}
							
						if ($PKSub<>$tmpField ) {
							$tmpData .='>'.$tmpRow[1]."</option>\n";
						} else {
							$tmpData .= '				<option value="'.$tmpRow[0].'">'.$tmpRow[0]."</option>\n";
						}
					}
					$tmpData .= '			</select>';
					$rowdata = $tmpData;
				}
			} else {
				if ($FieldInfo->type=='blob') {
					$rowdata='<textarea name="'.$this->MyName.'update_'.$key.'" rows="10" cols="76">'.$rowdata.'</textarea>' ;
				} elseif($FieldInfo->type=='datetime'){
					if($this->DateDefaultsToNow) {
						$rowdata='<input type="text" name="'.$this->MyName.'update_'.$key.'" size="'.$FieldInfo->max_length.'" value="'.$rowdata.'"/>';
					} else {
						$rowdata='<input type="text" name="'.$this->MyName.'update_'.$key.'" size="'.$FieldInfo->max_length.'" value="'.$rowdata.'" />';
					}
					$rowdata .= ' must be in the following format : \'Y-m-d H:i \'';
				} else {
					$rowdata='<input type="text" name="'.$this->MyName.'update_'.$key.'" size="'.$FieldInfo->max_length.'" value="'.$rowdata.'" />';
				}
				
			}
			if (($FieldInfo->type=='datetime') && ($this->DateDefaultsToNow) && ($this->Mode=='Insert')) {
				
			} elseif (in_array('auto_increment',$Flags ))  {
				
			} elseif (in_array('not_null',$Flags))  {
				$rowdata .= ' (Required)';
			}
			if (($FieldInfo->type=='datetime') && ($this->DateDefaultsToNow) && ($this->Mode=='Insert')) {
				$this->MYECHO('	<tr>');
				$this->MYECHO('		<th>'.$colText.'&nbsp;:</th>');
				$this->MYECHO('		<td>'."\n			".$rowdata.'&nbsp;(Will default to Now()'."\n".'	</td> ');
				$this->MYECHO('	</tr>');
			} else {
				$this->MYECHO('	<tr>');
				$this->MYECHO('		<th>'.$colText.'&nbsp;:</th>');
				$this->MYECHO('		<td>'."\n			".$rowdata.'&nbsp;'."\n".'		</td>');
				$this->MYECHO('	</tr>');
			}
			$i++;
		} 
		$this->MYECHO('	<tr>');
		$this->MYECHO('		<td></td>');
		$this->MYECHO('		<td>');
		$ActionFormStr 	="\n";
		if ($this->Mode=='Update') {
			$ActionFormStr .= '			<input type="submit" name="'.$this->EditTagDone.'" value="Update" class="DFActionButton" ';
			if ((isset($ValidationString)) && ($ValidationString<>'') && (!is_null($ValidationString))) {
				$ActionFormStr .= 'onclick="'.$ValidationString.'"';
			}
			$ActionFormStr .= '>'."\n";
		} elseif ($this->Mode=='Insert') {
			$ActionFormStr .= '			<input type="submit" name="'.$this->InsertTagDone.'" value="Insert" class="DFActionButton" ';
			if ((isset($ValidationString)) && ($ValidationString<>'') && (!is_null($ValidationString))) {
				$ActionFormStr .= 'onclick="'.$ValidationString.'"';
			}
			$ActionFormStr .= '>'."\n";
		}
		$ActionFormStr .= '			<input type="submit" name="'.$this->CancelActionTag.'" value="Cancel" class="DFActionButton" />'."\n";
		$ActionFormStr .= '			<input type="reset" name="reset" value="Reset form" class="DFActionButton" />'."\n";
		$this->MYECHO($ActionFormStr);
		$this->MYECHO('		</td>');
		$this->MYECHO('	</tr>');
		$this->MYECHO('</table>');
		$this->MYECHO('</form>');
	}
	
	function doActionBox() {
		if (($this->ActionInsert) or (($this->Mode=='Empty') or ($this->Mode=='Grid') or ($this->Mode=='View'))){
			$this->MYECHO('<div class="'.$this->ActionClass.'">');
			if (($this->Mode=='Empty') or ($this->Mode=='Grid')) {
				$this->MYECHO('	<a class="'.$this->ActionClass.'" href="'.$this->RefreshAction.'">Refresh Page</a>');
				if ($this->ActionInsert){
					$this->MYECHO('	<a class="'.$this->ActionClass.'" href="'.$this->InsertAction.'1">Insert Record</a>');
				}
			} elseif ($this->Mode=='View'){
				$this->MYECHO('	<a class="'.$this->ActionClass.'" href="'.$this->CancelAction.'">GO BACK</a> ');
			}
			$this->MYECHO('</div>'."\n");
		}
	}
## #############################################################################################################
## Main Page Drawing Function: (doGrid)  ###################################################################################
## #############################################################################################################
	function doGrid() { 
		$this->RowCount = $this->Offset; 
		$this->MYECHO('<table class="'.$this->TableClass.'" cellspacing="0">');
		$this->MYECHO('	<tr class="'.$this->TableClass.'Heading">');

		if($this->ShowRowCounter) {$this->MYECHO('		<th>#</th>');	}
		if (($this->ActionCol) && (isset($this->PrimaryKey))) {$this->MYECHO('		<th>Action</th>');	}
		
		$i = 0;		
		while ($i < $this->SQLColumns)  {
			$show = (((!$this->ShowPrimaryKey) && (mysql_field_name($this->rsMain,$i)==$this->PrimaryKeyStr)) ? false :true );
			if ($show) { 
				$heading=ucwords(str_replace('_', ' ',mysql_field_name($this->rsMain,$i)));
				$colText=(isset($this->FieldTitles)  ? trim($this->FieldTitles[$i]) : $heading);
				$ActionString = $this->sortLinksAction.mysql_field_name($this->rsMain,$i);
				if ($this->ColumnSort) {
					if (in_array(strtolower(mysql_field_name($this->rsMain,$i)), $this->OrderFields)) {
						$ActionString .= ' DESC';
						$th_cell = '<a class="'.$this->SortClass.'" href="'.$ActionString.'" title="Sort By '.$colText.' Descending">'.($this->headerwrap==0 ? str_replace(' ','&nbsp;',$colText) : $colText).'</a>&nbsp;<img class="sortimg" src="../common/images/asc.gif" alt="(asc)" />';
					} elseif (in_array(strtolower(mysql_field_name($this->rsMain,$i)).' desc' , $this->OrderFields)) {
						$th_cell = '<a class="'.$this->SortClass.'" href="'.$ActionString.'" title="Sort By '.$colText.'">'.($this->headerwrap==0 ? str_replace(' ','&nbsp;',$colText) : $colText).'</a>&nbsp;<img class="sortimg" src="../common/images/desc.gif" alt="(desc)" />';
					} else {
						$th_cell = '<a class="'.$this->SortClass.'" href="'.$ActionString.'" title="Sort By '.$colText.'">'.($this->headerwrap==0 ? str_replace(' ','&nbsp;',$colText) : $colText).'</a>';
					} 
				} else {
					$th_cell =$colText;
				}
				$this->MYECHO('		<th>'.$th_cell.'</th>');
			}
		$i++;
		}
		
		$this->MYECHO('	</tr>');
		
		$EvenOdd = 0;
		while($this->row_rsMain=mysql_fetch_array($this->rsMain)) {
			$EvenOdd++;
			$this->RowCount++; 
			
			if ((isset($this->Children[0])) && (count($this->Children)>0)) {
				$ChildColumn=$this->Children[0]['column'];
				$ChildTable = ucwords($this->Children[0]['table']);
				$ChildValue=$this->row_rsMain[$ChildColumn];

				if (($this->ActionCol)  && ($this->ActionSeeChildren) && (isset($this->VARS[$this->SeeChildrenTag])) && ($this->VARS[$this->SeeChildrenTag]==$ChildValue)) {
					$this->MYECHO('	<tr class="SELECTED">');	
				} else {
					$this->MYECHO('	<tr class="'.(($EvenOdd & 1)==0 ? 'even' : 'odd').'">');
				}
			} else {
				$this->MYECHO('	<tr class="'.(($EvenOdd & 1)==0 ? 'even' : 'odd').'">');
			}
			
			if($this->ShowRowCounter) {$this->MYECHO('		<th>'.$this->RowCount.'</th>');}
			if (($this->ActionCol) && (isset($this->PrimaryKey))) {
				$PKValue=$this->row_rsMain[$this->SQLPKIndex];
				if ((isset($this->ActionSeeChildrenText) ) && ($this->ActionSeeChildrenText<>'')  && (!is_null($this->ActionSeeChildrenText<>''))) {
					$ChildrenActionText=$this->ActionSeeChildrenText;
				} else {
					$ChildrenActionText='Display ';
					if ((isset($ChildTable) ) && ($ChildTable<>'')  && (!is_null($ChildTable<>''))) {
						$ChildrenActionText.=$ChildTable;
					} else{
						$ChildrenActionText.='Children';
					}
				}
				$tmpString		= '';
				$tmpStringlen = 0;

				$tmpString 		= ($this->ActionEdit ? '			<a class="'.$this->ActionClass.'" href="'.$this->EditAction.$PKValue.'">Edit</a>'."\n" : '');
				$tmpStringlenNew =strlen($tmpString);

				if (($tmpStringlenNew<>$tmpStringlen) && (($this->ActionDelete) or ($this->ActionView) or ($this->ActionSeeChildren))) {$tmpString .='|';}
				$tmpStringlen=$tmpStringlenNew;
				$tmpString 		.= ($this->ActionDelete ? '			<a class="'.$this->ActionClass.'" href="'.$this->DeleteAction.$PKValue.'">Delete</a>'."\n" : '');
				$tmpStringlenNew=strlen($tmpString);

				if (($tmpStringlenNew<>$tmpStringlen) && (($this->ActionView) or ($this->ActionSeeChildren))) {$tmpString .='|';}
				$tmpStringlen=$tmpStringlenNew;
				$tmpString 		.= ($this->ActionView ? '			<a class="'.$this->ActionClass.'" href="'.$this->ViewAction.$PKValue.'">View</a>'."\n" : '');
				$tmpStringlenNew=strlen($tmpString);
				
				if (($tmpStringlenNew<>$tmpStringlen) && ($this->ActionSeeChildren) && (isset($this->Children[0])) && (count($this->Children)>0)) {$tmpString .='|';}
				$tmpStringlen=$tmpStringlenNew;
				if (($this->ActionSeeChildren) && (isset($this->Children[0])) && (count($this->Children)>0)) {
					if (isset($this->VARS[$this->SeeChildrenTag]) && ($this->VARS[$this->SeeChildrenTag]==$ChildValue)) {
						$tmpString 		.='			<a class="'.$this->ActionClass.'" href="'.$this->UnSeeChildrenAction.'">'.$ChildrenActionText.'</a>'."\n";
					} else {
						$tmpString 		.='			<a class="'.$this->ActionClass.'" href="'.$this->SeeChildrenAction.$ChildValue.'">'.$ChildrenActionText.'</a>'."\n";
					}
				}

				$this->MYECHO('		<th>&nbsp;');
				$this->MYECHO($tmpString);
				$this->MYECHO('		&nbsp;</th>');
			}
			$i = 0;
			while ($i < $this->SQLColumns) { 
				$show = (((!$this->ShowPrimaryKey) && (mysql_field_name($this->rsMain,$i)==$this->PrimaryKeyStr)) ? false :true );
				if ($show) {
					if (($this->datawrap==0) && ($this->SQLFieldInfo[$i]->type!='blob') && ($this->SQLFieldInfo[$i]->type!='datetime')) {
						$rowdata=str_replace(' ', '&nbsp;',$this->row_rsMain[$i]);
					} else {
						$rowdata=$this->row_rsMain[$i];
					}
					//if (($this->ActionCol)  && ($this->ActionSeeChildren) && (isset($this->VARS[$this->SeeChildrenTag])) && ($this->VARS[$this->SeeChildrenTag]==$ChildValue)) {
					//		$rowdata 	.= mysql_field_name($this->rsMain,$i).$this->Children[0]['column'].'<em>Selected - See below</em>';
					//}
					
					switch ($this->SQLFieldInfo[$i]->type) {
						case 'blob':
							if (str_word_count($rowdata) > 1) {
								$rowdata = $this->trim_text($rowdata, $this->BlobNumWords).($this->ActionView ? '.......<a href="'.$this->ViewAction.$PKValue.'">more</a>' : '');
							}
							break;
						case 'datetime':
							$rowdata=date($this->strDateFormat,strtotime($rowdata));               
							if ($this->datawrap==0) {$rowdata = str_replace(' ', '&nbsp;',$rowdata);}
							break;
					}
					$this->MYECHO('		<td>'.$rowdata.'</td>');
				}
				$i++;
			} 
			$this->MYECHO('	</tr>');
		}
		$this->MYECHO('</table>');
		mysql_free_result($this->rsMain);
	} ## END MAIN DRAWING FUNCTION  #####################################################################################


## #############################################################################################################
## Utility Functions: (trim_text)  ################################################################################
## #############################################################################################################
	function trim_text ($string, $truncation=25) {
		if (str_word_count($string) > $truncation) {
			$string = preg_split("/\s+/",$string,($truncation+1));
			unset($string[(sizeof($string)-1)]);
			return implode(' ',$string);
		} else {
			return $string;
		}
	}
	
## #############################################################################################################
## Other Page Drawing Functions: (doGoToPageBox, doPageNums, doSortBox, doNavigation, doRecordInfo, DisplayMessageList, MessageListAdd)  ##############
## #############################################################################################################
	function MessageListAdd($strMessage) {
		$this->MessageList .= $strMessage."\n<br/>";
	}
	
	function  DisplayMessageList() {
		if((isset($this->MessageList)) && ($this->MessageList<>'')){
			$this->MYECHO('<div class="'.$this->MessageListClass.'">');
			$this->MYECHO($this->MessageList);
			$this->MYECHO('</div>');
		}
	}
	
	function doRecordInfo (){ 
		if ($this->ShowRecordInfo) {
			$startRow = $this->Offset;
			$this->MYECHO(sprintf('	Records %d to %d of %d',($startRow)+1,min($startRow + $this->RowsPerPage, $this->TotalRows),$this->TotalRows ));
		}
	}

	function MYECHO($string) {
		echo $string."\n";
	}
	
	function doNavigation () { 
		if (($this->ShowNavigation) or ($this->ShowRecordInfo)){$this->MYECHO('<div class="'.$this->NavigationClass.'">');}
		if ($this->ShowNavigation) {
			$NavString = '	&nbsp;<a href="'."%1\$s%2\$d".'"><img src="../common/images/'."%3\$s.gif".'" title="'."%3\$s".' alt="'."%3\$s".'" /></a>&nbsp;';
			if ($this->CurrentPage > 1) { 
				$this->MYECHO(sprintf($NavString,$this->PageAction,1,'First' ));
				$this->MYECHO(sprintf($NavString,$this->PageAction,$this->CurrentPage-1,'Previous'));
			} else {
				$this->MYECHO(	'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
			}
		}
		
		if ($this->ShowRecordInfo)  { $this->doRecordInfo();}
		
		if ($this->ShowNavigation)  {
			if ($this->CurrentPage < $this->TotalPages) { 
				$this->MYECHO(sprintf($NavString,$this->PageAction,$this->CurrentPage+1,'Next' ));
				$this->MYECHO(sprintf($NavString,$this->PageAction,$this->TotalPages,'Last'));
			 } else { 
			 	$this->MYECHO('	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
			 } 
		}
		if (($this->ShowNavigation) or ($this->ShowRecordInfo)){$this->MYECHO ('</div>'."\n");}
	}

	function doSortBox() {
		$this->MYECHO('	<form id="'.$this->MyName.'SortBox" class="'.$this->SortBoxClass.'" method="post" action= "'.$this->sortBoxAction.'">');
		$this->MYECHO('	<table class="'.$this->SortBoxClass.'">');
		$this->MYECHO('		<tr>');
		$this->MYECHO('			<td>Sort By: </td>');
		$this->MYECHO('			<td><input name= "'.$this->OrderTag.'" class ="'.$this->SortBoxClass.'" type="text" value="'.$this->sortActionBoxText.'" /></td>');
		$this->MYECHO('			<td><input name="submit" class="'.$this->SortBoxClass.'Button" type="submit" value="Go!" /></td>');
		$this->MYECHO('		</tr>');
		$this->MYECHO('	</table>');
		$this->MYECHO('	</form>');
	}

	function doPageNums() {
		if($this->TotalPages>1) {
			$PageNumStr='';
			$PageNumStr .='<div class="'.$this->PageClass.'">'."\n";
			if($this->ShowGoToPageBox) {$PageNumStr .=$this->doGoToPageBox()."\n";}
			$PageNumStr .='	Pages: '."\n";

			for($i=1;$i<=$this->TotalPages;$i++) {
				if($i==$this->CurrentPage) {
					if ($i==1) {} else {$PageNumStr .='	| ';}
					$PageNumStr .='	<span class="active">'.$i.'</span>'."\n";
				} else {
					if ($i==1) {} else {$PageNumStr .='	| ';}
					$PageNumStr .='	<a href="'.$this->PageAction.$i.'" >'.$i.'</a>'."\n";
				}
			}
			$PageNumStr .='</div>'."\n\n";
			echo $PageNumStr;
			}
	}

	function doGoToPageBox() {
		if($this->TotalPages>1) {
			
			$PageBoxStr='';
			$PageBoxStr .= '	<div class="'.$this->PageBoxClass.'">'."\n";
			$PageBoxStr .= '		<form id="'.$this->MyName.'frmPage" action="'.$this->PageAction.'" method="get" class="'.$this->PageBoxClass.'" onsubmit ="return VerifyPageBox(\''.$this->MyName.'frmPage\', \''.$this->PageTag.'\','.$this->TotalPages.')">'."\n";
			$PageBoxStr .= '		Go To Page&nbsp;<input type="text" name="'.$this->PageTag.'" size="3" value="'.$this->CurrentPage.'" />&nbsp;'."\n";
			$PageBoxStr .= '		<input type="submit" name="submit" value="Go!" class="'.$this->PageBoxClass.'Button" />'."\n";
			$PageBoxStr .= '		</form>'."\n";
			$PageBoxStr .= '	</div>'."\n\n";
			return $PageBoxStr;			
			}
		}

## #############################################################################################################
## Action String Functions : (setOrderFields, setActionStrings, buildAction)  ##################################
## #############################################################################################################
	function setOrderFields() {
		if ((isset($this->VARS[$this->OrderTag])) && ($this->VARS[$this->OrderTag]<>'')) {
			$this->OrderFields = explode(",",strtolower($this->VARS[$this->OrderTag]));
			while (list ($key, $val) = each ($this->OrderFields)) {
				$this->OrderFields[$key] = strtolower(trim($val));
			}
		} else {
			//there's no order tag in the querystring... but maybe a sort has already been set?
			if($this->OrderBySql<>'') {
				$this->OrderFields = explode(",",$this->OrderBySql);
				while (list ($key, $val) = each ($this->OrderFields)) {
					$this->OrderFields[$key] = strtolower(trim($val));
				}
				reset ($this->OrderFields);
			}
		}
	}

	function setActionStrings () {
	## SET UP THE 'QUERYSTRING's
		## Arrays containing lists of QueryString Variables that we are  NOT interested in for a particular Action
		## QueryString Variables we NEVER want: i.e. submit buttons, Insert/Edit/Delete/View Actions......
		
		$restrictedAlways 				= array('submit',$this->CancelActionTag,$this->ViewTag,$this->DeleteTag,$this->EditTag,$this->InsertTag,$this->DeleteTagDone,$this->EditTagDone,$this->InsertTagDone);
		$passedAlways 					= array($this->RowsTag);

		## Action type specifiic QueryString Variables
		$restrictedPageNums 			= $restrictedAlways;
		$restrictedSortAction 			= $restrictedAlways;
		$restrictedActionColumns 		= $restrictedAlways;
		$restrictedActionCancel 		= $restrictedAlways;
		$restrictedActionRefresh 		= $restrictedAlways;
		
		$restrictedSpecial=$this->FillRestrictedArrayActions();
		$restrictedActionCancel = array_merge($restrictedActionCancel, $restrictedSpecial);
		array_push($restrictedSortAction, $this->PageTag, $this->SeeChildrenTag);		// Of course if we change the sort, We Don't want to put Page Numbers in
		array_push($restrictedActionRefresh, $this->RowsTag);
		$restrictedActionRefresh= array_merge($restrictedActionRefresh, $restrictedSpecial);
		array_push($restrictedPageNums, $this->SeeChildrenTag)	;					// At present we want all'
	##Lets do the 'Sort' Query String: For this we need everything execpt ACTIONS & PAGENUMS (but we'll set page to 1)
		$this->sortActionBoxText = trim(($this->OrderBySql<>'' ? $this->OrderBySql : NULL)); // Just fill the Action Box with the Current 'Order By' Statement. 
		$Action=$this->buildAction ($this->OrderTag, $restrictedSortAction);
		if ($Action=='') {
			$Action='?';	$this->debugEcho ('Sort Action is empty');
		} else {
			$Action = substr_replace($Action,'?',0,5).'&amp;'; $this->debugEcho ('Sort Action is NOT Empty');
			$this->sortBoxAction = $Action;
		}
		$this->sortLinksAction 		= $this->SELF.$Action.$this->OrderTag.'=';
					$this->debugEcho ('sortLinksAction $Action='.$Action);
		$this->sortBoxAction =$this->SELF.$this->sortBoxAction ;

	##Lets do the 'Action' Query String: For this we need everything (We want to keep the page number for the return leg) execpt ACTIONS
		$Action=$this->buildAction ($this->EditTag, $restrictedActionColumns);	
		if ($Action=='') {
			$Action='?'; $this->debugEcho ('Action Action is empty');
		} else {
			$Action = substr_replace($Action,'?',0,5).'&amp;'; $this->debugEcho ('Action Action is NOT Empty');
		}

		$this->EditAction 				= $this->EditPage.$Action.$this->EditTag.'=';
		$this->DeleteAction 			= $this->DeletePage.$Action.$this->DeleteTag.'=';
		$this->ViewAction 				= $this->ViewPage.$Action.$this->ViewTag.'=';
		$this->InsertAction 			= $this->InsertPage.$Action.$this->InsertTag.'=';
		
		$this->debugEcho ('Action $Action='.$Action);
		

		##Lets do the 'SeeChildren Action' Query String
		$Action=$this->buildAction ($this->SeeChildrenTag, $restrictedActionColumns);	
		if ($Action=='') {
			$Action='?'; $this->debugEcho ('Action Action is empty');
		} else {
			$Action = substr_replace($Action,'?',0,5).'&amp;'; $this->debugEcho ('Action Action is NOT Empty');
		}
		$this->SeeChildrenAction 	= $this->InsertPage.$Action.$this->SeeChildrenTag.'=';
		$this->UnSeeChildrenAction 	= $this->InsertPage.$Action;
		$this->debugEcho ('SeeChildrenAction $Action='.$this->SeeChildrenAction);
	
	
	##Lets do the 'PageNumbers' Query String: For this we need everything execpt ACTIONS
		$Action=$this->buildAction ($this->PageTag, $restrictedPageNums);	
		if ($Action=='') {
			$Action='?';	$this->debugEcho ('Page Action is empty');
		} else {
			$Action = substr_replace($Action,'?',0,5).'&amp;';$this->debugEcho ('Page Action is NOT Empty');
		}
		
		$this->PageAction=$this->SELF.$Action.$this->PageTag.'=';
		$this->debugEcho ('Page $Action='.$Action);
	
	## Cancel Actions string:
		$Action=$this->buildAction ('', $restrictedActionCancel);	
		if ($Action=='') {
			$this->debugEcho ('ActionCancel Action is empty');
		} else {
			$Action = substr_replace($Action,'?',0,5).'&amp';$this->debugEcho ('ActionCancel Action is NOT Empty');
		}
		$this->CancelAction=$this->SELF.$Action;

	
	## Refresh Actions string:
		$Action=$this->buildAction ('', $restrictedActionRefresh);	
		if ($Action=='') {
			$this->debugEcho ('ActionRefresh Action is empty');
		} else {
			$Action = substr_replace($Action,'?',0,5); $this->debugEcho ('ActionRefresh Action is NOT Empty');
		}
		$this->RefreshAction=$this->SELF.$Action;
	}

	function FillRestrictedArrayActions() {
		$TempArray = array();
		$tmpLen = strlen($this->MyName);
		while (list ($key, $val) = each ($this->VARS)) {
			if (substr($key,0,$tmpLen +7)==$this->MyName.'insert_') {array_push ($TempArray,$key);}
			if (substr($key,0,$tmpLen +7)==$this->MyName.'update_') {array_push ($TempArray, $key);}

			if (strpos($key,'update_' )) {array_push ($TempArray,$key);}
			if (strpos($key,'update_' )) {array_push ($TempArray,$key);}
			if (strpos($key,'Insert' )) {array_push ($TempArray,$key);}
			if (strpos($key,'InsertDone' )) {array_push ($TempArray,$key);}
		}
		reset($this->VARS);
		return $TempArray;
		
		
	}

	function buildAction ($Tag, &$restricted) {
		$NewAction = '';
		$tmpLen = strlen($this->MyName);
		if (count($this->VARS)>0) {
			array_push($restricted,$Tag);
			while (list ($key, $val) = each ($this->VARS)) {
				if (!in_array($key, $restricted, true) && (substr($key,0,$tmpLen +7)!=$this->MyName.'update_') && (substr($key,0,$tmpLen +7)!=$this->MyName.'insert_')) {
            		$NewAction.='&amp;'.$key.'='.$val;
        		}
			}
			reset ($this->VARS);
			if($NewAction !='') {
				$NewAction = substr_replace($NewAction,'?',0,1);
				return $NewAction;
			} else {
				return;	 // Nothing - So Return NULL
			}
		} else {
			return;	 // No Vars - So Return NULL
		}
	}

## #############################################################################################################
## Debug Output Functions  #####################################################################################
## #############################################################################################################
	function debugEcho($str) {
		if ($this->bDebug) {
			echo '<p>:: '.$str.'</p>'."\n";
		}
	}

	function OpenDebug() {
		if ($this->bDebug) {
			$this->MYECHO('<div class="gridDebug">'."\n".'<strong>Datagrid Debug Info:</strong>'."\n".'<br />'."\n");
		}
	}

	function CloseDebug() {
		if ($this->bDebug) {
			$this->MYECHO('</div>'."\n");
		}
	}

## DefaultStyleClass
	function setDefaultStyleClass($bool) {
		$this->DefaultStyleClass=$bool;
		if ($this->DefaultStyleClass) {
			$this->GridClass='DFGridDiv';		
			$this->TableClass='DFGridTable';		
			$this->ActionClass='DFAction';
			$this->SortClass='DFSort';	
			$this->SortBoxClass='DFSortBox';	
			$this->PageClass='DFPageDiv';	
			$this->PageBoxClass='DFPageBox';
			$this->NavigationClass = 'DFNavigation';
			$this->MessageListClass = 'DFMessageList';	
		} else {
			if ((isset($this->MyName)) && (!$this->MyName=='')) {
				$tmpName=str_replace('Grid', '',$this->MyName);
				$this->GridClass=$tmpName.'GridDiv';		
				$this->TableClass=$tmpName.'GridTable';	
				$this->ActionClass=$tmpName.'Action';
				$this->SortClass=$tmpName.'Sort';
				$this->SortBoxClass=$tmpName.'SortBox';	
				$this->PageClass=$tmpName.'PageDiv';	
				$this->PageBoxClass=$tmpName.'PageBox';	
				$this->NavigationClass = $tmpName.'Navigation';		
				$this->MessageListClass = $tmpName.'MessageList';		
			} else {
				$this->GridClass='DFGridDiv';	
				$this->TableClass='DFGridTable';
				$this->ActionClass='DFAction';	
				$this->SortClass='DFSort';
				$this->SortBoxClass='DFSortBox';	
				$this->PageClass='DFPageDiv';	
				$this->PageBoxClass='DFPageBox';
				$this->NavigationClass = 'DFNavigation';
				$this->MessageListClass = 'DFMessageList';	
			}
		}
	}

## #############################################################################################################
## Functions for Setting up the $VARS Array  ################################################################################
## #############################################################################################################
	function setVars() { // Get all the $VARS from http GET & POST and put them into my internal VARS array:
		global $SYS; 
		$SYS = array();
		$SYS['DB_magic_quotes_gpc'] = get_magic_quotes_gpc();													
		$SYS['DB_magic_quotes_sybase'] = 0;
		$shift = 0;
		$sybase_is_used = 0;
		$this->VARS = $this->getpost_vars($SYS['DB_magic_quotes_gpc'],$SYS['DB_magic_quotes_sybase'],$sybase_is_used);
		$shift = "0";

		reset ($this->VARS);
		$this->debugEcho('****************** VARS: ******************');
		if (count($this->VARS)==0) {
			$this->debugEcho('$VARS is empty (There were no POST or GET Variables)');
		} else {
			while (list ($key, $val) = each ($this->VARS)) {	$this->debugEcho($key.'=>'.$val);}
		}
		$this->debugEcho('*************** END VARS: ***************');
		reset ($this->VARS);
	}

	## getpost_vars FUNCTION ##########################################################################################
	function getpost_vars($mq_gpc,$mq_sybase,$sybase) {
		$VARS = array();
		$this->debugEcho('COUNT VARS: '.count($VARS));
		
		# read in variables
		if(gettype($_POST) == 'array') {
			while(list($key,$value) = each($_POST)) {$VARS[$key] = $value;}
		}
		
		if(gettype($_GET) == 'array') {
			while(list($key,$value) = each($_GET)) { $VARS[$key] = $value;}
		}
		
		## MAGIC_QUOTES_GPC && MAGIC_QUOTES_SYBASE ##
		## start 8 condition test for all situations based on 3 true/false variables for each condition
		
		if($mq_gpc && (! $mq_sybase) && (! $sybase)) {
		# true; false; false; => do not need to do anything
		# while(list($key,$value) = each($VARS)) { $VARS[$key] = $value; }
		}
		
		elseif($mq_gpc && (! $mq_sybase) && $sybase) {
		# true; false; true; => need to replace \' with ''
		while(list($key,$value) = each($VARS)) { $VARS[$key] = str_replace("\'","''",$value); }
		}
		
		elseif($mq_gpc && $mq_sybase && (! $sybase)) {
		# true; true; false; => need to remove extra single quotes
		while(list($key,$value) = each($VARS)) { $VARS[$key] = str_replace("''","'",$value); }
		}
		
		elseif($mq_gpc && $mq_sybase && $sybase) {
		# true; true; true; => do not need to do anything
		# while(list($key,$value) = each($VARS)) { $VARS[$key] = $value; }
		}
		
		elseif((! $mq_gpc) && (! $sybase)) {
		# false; true/false; false; => need to add slashes
		while(list($key,$value) = each($VARS)) { $VARS[$key] = addslashes($value); }
		}
		
		else  #  elseif((! $mq_gpc) && $sybase)
		{ # false; true/false; true; => need to add slashes and then replace \' with ''
		while(list($key,$value) = each($VARS)) { $VARS[$key] = str_replace("\'","''",addslashes($value)); }
		}
		
		## end 8 condition test... last 2 conditions count as 4 in true/false logic
		#############################################
		$this->NumVars=(count($VARS));
		reset($_POST);
		reset($_GET);

		$this->debugEcho('COUNT VARS: '.count($VARS));
		return($VARS);
	}

## #############################################################################################################
## SET & GET Functions for external use:  ###################################################################################
## ##############################################################################################################

## MyName
	function setMyName($str) {
		$this->MyName=$str;
	}
	function getMyName() {
		return $this->MyName;
	}
## Conn
	function setConn($Conn) {
		$this->Conn=$Conn;
	}
## Database Name
	function setDb($str) {
		$this->Db=$str;
	}
## SQL Statement
	function setSQLMain($str) {
		$this->SQLMain=$str;
	}
## bDebug
	function setbDebug($bool) {
		$this->bDebug=$bool;
	}

## ParentGrid
	function setParentGrid($str) {
		if ($str<>'') {
			$this->ParentGrid 	= $str;
			$this->AmChild 		= true;
		}
	}

## ParentLink
	function setParentLink($str) {
		$str = str_replace(' ','',$str);
		if ($str<>'') {
			$tmpStr = explode('=',$str);
			$this->ParentLink 	= $tmpStr[0];
			$this->ParentLinkName 	= $tmpStr[1];
		}
	}

## Parents
	function setParents($str) {
		$str = str_replace(' ','',$str);
		if ($str<>'') {
			$tmpStr 	= explode(',',$str);
			foreach ($tmpStr as $i => $value) {
				$tmpfld 	= split('[=.]',$tmpStr[$i]);
  				$this->Parents[$tmpfld[0]]['table'] = $tmpfld[1];
				$this->Parents[$tmpfld[0]]['field'] = $tmpfld[2];
			}
		}
	}

## Children
	function setChildren($str) {
		$str = str_replace(' ','',$str);
		if ($str<>'') {
			$tmpStr 	= explode(',',$str);
			foreach ($tmpStr as $i => $value) {
  				$tmpfld 	= split('[=.]',$tmpStr[$i]);
  		 		$this->Children[$i]['column'] = $tmpfld[0];
  				$this->Children[$i]['table'] = $tmpfld[1];
				$this->Children[$i]['field'] = $tmpfld[2];
			}	
		} else {
			$this->ActionSeeChildren = false;
		}
	}
	
##Title
	function setTitle($str) {
		$this->Title=$str;
	}
## AmChild	
	function setAmChild($bool) {
		$this->AmChild=$bool;
	}
	function getAmChild() {
		return $this->AmChild;
	}
## DateDefaultsToNow
	function setDateDefaultsToNow($bool) {
		$this->DateDefaultsToNow=$bool;
	}
## MainDbTable
	function setMainDbTable($str) {
		$this->MainDbTable=$str;
	}
## FieldNameList	
	function setFieldNameList($str) {
		$this->FieldNameList=$str;
	}
## PrimaryKey		
	function setPrimaryKey($str) {
		$this->PrimaryKey=$str;
	}
## ShowPrimaryKey		
	function setShowPrimaryKey($bool) {
		$this->ShowPrimaryKey=$bool;
	}
## ShowRowCounter		
	function setShowRowCounter($bool) {
		$this->ShowRowCounter=$bool;
	}
## Action Columns ?
	function setActionCol($bool) {
		$this->ActionCol=$bool;
	}
## Actions
	function setActionEdit($bool) {
		$this->ActionEdit=$bool;
	}
	function setActionDelete($bool) {
		$this->ActionDelete=$bool;
	}
	function setActionView($bool) {
		$this->ActionView=$bool;
	}
	function setActionInsert($bool) {
		$this->ActionInsert=$bool;
	}
	function setActionSeeChildren($bool) {
		$this->ActionSeeChildren=$bool;
	}
## Action Pages
	function setEditPage($str) {
		$this->EditPage=$str;
	}
	function setDeletePage($str) {
		$this->DeletePage=$str;
	}
	function setViewPage($str) {
		$this->ViewPage=$str;
	}
	function setInsertPage($str) {
		$this->InsertPage=$str;
	}
## Action Handling
	function setActionHandling($bool) {
		$this->ActionHandling=$bool;
	}
## ShowSortBox	
	function setShowSortBox($bool) {
		$this->ShowSortBox=$bool;
	}
## HasPaging
	function setHasPaging($bool) {
		$this->HasPaging=$bool;
	}
## ShowPageNums
	function setShowPageNums($bool) {
		$this->ShowPageNums=$bool;
	}
## ShowNavigation
	function setShowNavigation($bool) {
		$this->ShowNavigation=$bool;
	}
## ShowGoToPageBox
	function setShowGoToPageBox($bool) {
		$this->ShowGoToPageBox=$bool;
	}
## ShowRecordInfo
	function setShowRecordInfo($bool) {
		$this->ShowRecordInfo=$bool;
	}
## RowsPerPage	
	function setRowsPerPage($int) {
		$this->RowsPerPage=$int;
	}
## ShowGoToPageBox
	function setheaderwrap($bool) {
		$this->headerwrap=$bool;
	}
## ShowGoToPageBox
	function setdatawrap($bool) {
		$this->datawrap=$bool;
	}
## AlternateRowColors
	function setAlternateRowColors($bool) {
		$this->AlternateRowColors=$bool;
	}
## BlobNumWords
	function setBlobNumWords($int) {
		$this->BlobNumWords=$int;
	}
## ColumnSort
	function setColumnSort($bool) {
		$this->ColumnSort=$bool;
	}
## strDateFormat
	function setstrDateFormat($str) {
		$this->strDateFormat=$str;
	}
## OrderBySql		
	function setDefaultValues($str) {
		$this->DefaultValues=$str;
	}

## ActionSeeChildrenText		
	function setActionSeeChildrenText($str) {
		$this->ActionSeeChildrenText=$str;
	}

## ShowsFilter		
	function setShowsFilter($bool) {
		$this->ShowsFilter=$bool;
	}

## Validation
	function setValidation($str) {
		$this->Validation=$str;
	}

## ShowChildrenNoSelection		
	function setShowChildrenNoSelection($bool) {
		$this->ShowChildrenNoSelection=$bool;
	}



}
?>
