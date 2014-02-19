<?
function displayErrMsg($message)
{
	printf("<BLOCKQUOTE><BLOCKQUOTE><BLOCKQUOTE><H3><FONT COLOR=\'cc0000\'>			%s</FONT></H3></BLOCKQUOTE></BLOCKQUOTE></BLOCKQUOTE>\n", $message);
}

// In www.microsoft.com you can search for Office Web Components where you will find all the information to improve this class and adapt it to your needs
// 
// but is the easiest way to create a excel sheet and a lot of different charts

/** \class EasyExcel
 *  \brief This class generate excel sheets and charts using Office Web Components.
 * Aqui iría la detallada....	
 *  \author Rafael de Pablo
 *  \version 1.0
 *  \date    01-2005
 *  \warning The html pages generated using this class, only will work with Internet Explorer 
 *  \warning and the client need Office 2003 to can see the data.
 */

class EasyExcel
{
	/**
	* Color for  the index fields
	*/
	var $m_strColorField ="";
	/**
	* Font for  the index fields
	*/
	var $m_strFontField ="";	
	/**
	* Font Size for  the index fields
	*/
	var $m_strFontSizeField ="";		

	/**
	* Color for  the data fields
	*/
	var $m_strColorData ="";
	/**
	* Font for  the data fields
	*/
	var $m_strFontData ="";	
	/**
	* Font Size for  the data fields
	*/
	var $m_strFontSizeData ="";		
	
	/**
	* Name of the vbscript object
	*/
	var $m_strExcelSheet ="";
	/**
	* Name of the main worksheet
	*/
	var $m_strExcelLabel ="";
	
	/**
	* Number of registers executing the query
	*/
	var $m_nRegisters =0;
	
	/**
	* Auxiliar variable to build the details
	*/
	var $m_nPosDetail =0;
	/**
	* Auxiliar variable to build the details
	*/
	var $m_nSeries =0;
	
	
	/**
	* Fields used to combinate and generate new sheets and charts
	*/
	var $m_arrFields = array();
	/**
	* The different values for every field in $m_arrFields
	*/
	var $m_arrFieldValues = array();
	/*
	* The different combinations for the different fields in $m_arrFields
	*/
	var $m_arrAllCombinations =array();
	/**
	* Fields showed in the main excel sheet, all the data
	*/
	var $m_arrFieldsShow = array();	
	
	/**
	* Set the name of vbscript object
	*/
	function SetExcelSheet($strExcelSheet)
	{
		$this->m_strExcelSheet =$strExcelSheet;
	}

	/**
	* Get the name of vbscript object
	*/
	function GetExcelSheet()
	{
		return $this->m_strExcelSheet;
	}
	
	/**
	* Set the label of main sheet
	*/
	function SetExcelLabel($strExcelLabel)
	{
		$this->m_strExcelLabel =$strExcelLabel;
	}

	/**
	* Get the label of main sheet
	*/
	function GetExcelLabel()
	{
		return $this->m_strExcelLabel;
	}
	
	/**
	* Set the color for data fields
	*/
	function SetColorData($strColorData)
	{
		$this->m_strColorData =$strColorData;
	}

	/**
	* Get the color for data fields
	*/
	function GetColorData()
	{
		return $this->m_strColorData;
	}
	
	/**
	* Set the color for index fields
	*/
	function SetColorField($strColorField)
	{
		$this->m_strColorField =$strColorField;
	}

	/**
	*  Get the color for index fields
	*/
	function GetColorField()
	{
		return $this->m_strColorField;
	}
	
	/**
	* Set the font for data fields
	*/
	function SetFontData($strFontData)
	{
		$this->m_strFontData =$strFontData;
	}

	/**
	* Get the font for data fields
	*/
	function GetFontData()
	{
		return $this->m_strFontData;
	}
	
	/**
	* Set the font for index fields
	*/
	function SetFontField($strFontField)
	{
		$this->m_strFontField =$strFontField;
	}

	/**
	* Get the font for index fields
	*/
	function GetFontField()
	{
		return $this->m_strFontField;
	}

	/**
	* Set the font size for data fields
	*/
	function SetFontSizeData($strFontSizeData)
	{
		$this->m_strFontSizeData =$strFontSizeData;
	}

	/**
	* Get the font size for data fields
	*/
	function GetFontSizeData()
	{
		return $this->m_strFontSizeData;
	}
	
	/**
	* Set the font size for index fields
	*/
	function SetFontSizeField($strFontSizeField)
	{
		$this->m_strFontSizeField =$strFontSizeField;
	}

	/**
	* Get the font size for index fields
	*/
	function GetFontSizeField()
	{
		return $this->m_strFontSizeField;
	}	
	

	/**
	* Create a new connection, 
	* \warning you will need change this function to connect your database
	*/
	function dbconnect()
	{
		$db=mysql_pconnect('localhost','root','root');
		if (mysql_select_db("test",$db))
		{
			return $db;
		}
		else
		{
			displayErrMsg(sprintf("internal error %d:%s\n",mysql_errno(),mysql_error()));
			die;
		}
	}

	/**
	* Execute a query
	*/
	function doQuery($sql) 
	{
		if ($result=mysql_query($sql)){
			return $result;
		}
		else
		{
			echo mysql_error();
			die;
		}
	}
	
	/**
	* Create a new detail of the main data
	* \param $arrFields The fields used to group the data
	* \param $strLabelNew The label for the new worksheet
	* \param $arrFieldsShow The fields we want to show in the new worksheet
	* \param $strChartSpace The name of the chart vbscript object
	* \param $strChartLegend The legend for the chart
	* \param $strSerieColumn The column where the series are, normally the first one 'A'
	* \param $strCategoryColumn The column where the categories are, normally the corresponding to the last $arrFields
	* \param $arrValueColumn The different values used to generate the charts
	* \param $arrType The type of every chart
	* \param $arrLegends The legend for every chart
	*/
	function GenCountArray($arrFields,$strLabelNew,$arrFieldsShow,
			       $strChartSpace,$strChartLegend,$strSerieColumn,$strCategoryColumn,$arrValueColumn,$arrType,$arrLegends)
	{
		$this->m_nPosDetail =2;
		$this->m_nSeries =0;	
		
		$strSheetNew =$arrFields[0];
		$strExcelSheet =$this->GetExcelSheet();
		
		if ($strChartSpace != "")
		{
			echo "$strChartSpace.Clear\n";
			echo "$strChartSpace.DataSource = $strExcelSheet\n";
			echo "Set chConstants$strChartSpace = $strChartSpace.Constants\n";

			for ($i=0;$i<count($arrValueColumn);$i++)
			{								
				echo "$strChartSpace.Charts.Add\n";
				if ($arrType[$i] != "")
					echo "$strChartSpace.Charts($i).Type = chConstants$strChartSpace." . $arrType[$i] ."\n";
			}
		}
		
		
		echo "\t$strExcelSheet" . ".Worksheets.Add , $strExcelSheet.Worksheets($this->m_nExcelSheets),1\n";
		$this->m_nExcelSheets++;
		
		echo "\t$strExcelSheet.Worksheets($this->m_nExcelSheets).Select\n";
		echo "\t$strExcelSheet.ActiveSheet.Name =\"$strLabelNew\"\n";

		$nColumns =count($arrFields);
		$nCount =1;
		for ($i=0;$i<$nColumns;$i++)
		{
			echo "\t$strExcelSheet.ActiveSheet.Cells(" . $nCount . ",". ($i + 1) .").Value =\"";
			echo $arrFields[$i] ."\"\n";
			echo "\t$strExcelSheet.ActiveSheet.Cells(" . $nCount . ",". ($i + 1) .").Font.Color =\"";
			echo $this->GetColorField() ."\"\n";
			echo "\t$strExcelSheet.ActiveSheet.Cells(" . $nCount . ",". ($i + 1) .").Font.Name =\"";
			echo $this->GetFontField() ."\"\n";
			echo "\t$strExcelSheet.ActiveSheet.Cells(" . $nCount . ",". ($i + 1) .").Font.Size =";
			echo $this->GetFontSizeField() ."\n";
		}
		// show the total column
		echo "\t$strExcelSheet.ActiveSheet.Cells(" . $nCount . ",". ($i + 1) .").Value =\"";
		echo "Total\"\n";
		echo "\t$strExcelSheet.ActiveSheet.Cells(" . $nCount . ",". ($i + 1) .").Font.Color =\"";
		echo $this->GetColorField() ."\"\n";
		echo "\t$strExcelSheet.ActiveSheet.Cells(" . $nCount . ",". ($i + 1) .").Font.Name =\"";
		echo $this->GetFontField() ."\"\n";
		echo "\t$strExcelSheet.ActiveSheet.Cells(" . $nCount . ",". ($i + 1) .").Font.Size =";
		echo $this->GetFontSizeField() ."\n";
		$j =$i + 1;
		$nColumns =count($arrFieldsShow);
		for ($i=0;$i<$nColumns;$i++)
		{
			echo "\t$strExcelSheet.ActiveSheet.Cells(" . $nCount . ",". ($j + $i + 1) .").Value =\"";
			echo $arrFieldsShow[$i] ."\"\n";
			echo "\t$strExcelSheet.ActiveSheet.Cells(" . $nCount . ",". ($j + $i + 1) .").Font.Color =\"";
			echo $this->GetColorField() ."\"\n";
			echo "\t$strExcelSheet.ActiveSheet.Cells(" . $nCount . ",". ($j + $i + 1) .").Font.Name =\"";
			echo $this->GetFontField() ."\"\n";
			echo "\t$strExcelSheet.ActiveSheet.Cells(" . $nCount . ",". ($j + $i + 1) .").Font.Size =";
			echo $this->GetFontSizeField() ."\n";
		}

		$arrCombination = array();
		
		$this->GenCombination($arrCombination,$arrFields,0,$arrFieldsShow,
		                      $strChartSpace,$strSerieColumn,$strCategoryColumn,$arrValueColumn);
		
		$strColumn =chr(ord('A') + count($arrFields) + count($arrFieldsShow));
		echo "\t$strExcelSheet" . ".ActiveSheet.Range(\"A2:$strColumn" . "$this->m_nPosDetail\").Font.Color=\"";
		echo $this->GetColorData() ."\"\n";
		echo "\t$strExcelSheet" . ".ActiveSheet.Range(\"A2:$strColumn" . "$this->m_nPosDetail\").Font.Name=\"";
		echo $this->GetFontData() ."\"\n";
		echo "\t$strExcelSheet" . ".ActiveSheet.Range(\"A2:$strColumn" . "$this->m_nPosDetail\").Font.Size=\"";
		echo $this->GetFontSizeData() ."\"\n";
		echo "\t$strExcelSheet" . ".ActiveSheet.Range(\"A1:$strColumn" . "$this->m_nPosDetail\").Columns.AutoFit\n";
		
		if ($strChartSpace != "")
		{
			if ($strSerieColumn != "")
			{
				for ($i=0;$i<count($arrValueColumn);$i++)
				{				
					echo "\t$strChartSpace.Charts($i).HasLegend = True\n";
				}
			}
			for ($i=0;$i<count($arrLegends);$i++)
			{				
				echo "\t$strChartSpace.Charts($i).HasTitle = True\n";
				echo "\t$strChartSpace.Charts($i).Title.Caption = \"" . $arrLegends[$i] . "\"\n";
			}
			if ($strChartLegend != "")
			{
				echo "$strChartSpace.HasChartSpaceTitle = True\n";
				echo "$strChartSpace.ChartSpaceTitle.Caption = \"$strChartLegend\"\n";
				
			}
		}
	}

	/**
	* Private
	*/
	function GenCombination($arrCombination,$arrFields,$nColumnAct,$arrFieldsShow,
	                        $strChartSpace,$strSerieColumn,$strCategoryColumn,$arrValueColumn)
	{
		$strExcelSheet =$this->GetExcelSheet();
		
		reset($arrFields);
		$arrFieldsTemp =$arrFields;
		$nCount =count($arrFieldsTemp);
		if ($nColumnAct < $nCount)
		{
			for ($i=0;$i<$nColumnAct;$i++)
				next($arrFieldsTemp);
			
			$strFieldAct =current($arrFieldsTemp);
			
			//echo "alert(\"Campo: $strFieldAct\")\n";
			$arrFieldValuesTemp =$this->m_arrFieldValues;
			reset($arrFieldValuesTemp[$strFieldAct]);
			$nPosDetailStart =$this->m_nPosDetail;
			while (list($key, $strValueField) = each($arrFieldValuesTemp[$strFieldAct]))
			{
				$arrCombinationTemp =$arrCombination;
				$arrCombinationTemp[] =$key;
				$this->GenCombination($arrCombinationTemp,$arrFields,$nColumnAct + 1,$arrFieldsShow,
				                      $strChartSpace,$strSerieColumn,$strCategoryColumn,$arrValueColumn);
			}
			if (($nColumnAct + 1) == $nCount)
			{
				if ($strChartSpace != "")
				{
					$nPosDetailEnd =$this->m_nPosDetail;
					for ($i=0;$i<count($arrValueColumn);$i++)
					{
						$strValueColumn =$arrValueColumn[$i];
						echo "$strChartSpace.Charts($i).SeriesCollection.Add\n";
						if ($strSerieColumn != "")
							echo "$strChartSpace.Charts($i).SeriesCollection($this->m_nSeries).SetData chConstants.chDimSeriesNames, chConstants.chDataBound, \"$strSerieColumn$nPosDetailStart\"\n";
						if ($strCategoryColumn != "")
							echo "$strChartSpace.Charts($i).SeriesCollection($this->m_nSeries).SetData chConstants.chDimCategories, chConstants.chDataBound, \"$strCategoryColumn$nPosDetailStart:$strCategoryColumn$nPosDetailEnd\"\n";
						if ($strValueColumn != "")
							echo "$strChartSpace.Charts($i).SeriesCollection($this->m_nSeries).SetData chConstants.chDimValues, chConstants.chDataBound, \"$strValueColumn$nPosDetailStart:$strValueColumn$nPosDetailEnd\"\n";
					}
					$this->m_nSeries++;
				}
			}
		}
		else
		{
			$strCombinationTemp ="";
			
			$arrFieldsNum = array();
			reset($arrFields);
			while (list($key, $strField) = each($arrFields))
			{
				$arrFieldsNum[]=$strField;
			}
			
			reset($arrCombination);
			$arrFieldCombination = array();
			while (list($key, $strCombinationTemp) = each($arrCombination))
			{
				$arrFieldCombination[] =$arrFieldsNum[$key] .":" .$strCombinationTemp .";";
			}
			
			$bFound =False;
			for ($i=0; ($i< count($this->m_arrAllCombinations)) && ($bFound == False);$i++)
			{
				$strAllFieldCombination =$this->m_arrAllCombinations[$i];
				reset($arrFieldCombination);
				$bFound =True;
				while (list($key, $strFieldCombination) = each($arrFieldCombination))
				{
					if (!strstr($strAllFieldCombination,$strFieldCombination))
						$bFound =False;
				}
			}
			
			if ($bFound == true)
			{
				reset($arrCombination);
				$nCount =1;
				while (list($key, $strCombination) = each($arrCombination))
				{
					echo "\t$strExcelSheet.ActiveSheet.Cells($this->m_nPosDetail,$nCount).Value =\"$strCombination\"\n";
					$nCount++;
				}
				echo "\t$strExcelSheet.ActiveSheet.Cells($this->m_nPosDetail,$nCount).FormulaArray =\"=sum(";
				$i=0;
				reset($arrCombination);
				while (list($key, $strValue) = each($arrCombination))
				{
					for ($nField=0;$nField<count($this->m_arrFieldsShow);$nField++)
					{
						if ($this->m_arrFieldsShow[$nField] == $arrFieldsNum[$i])
							$strColumn =chr(ord('A') + $nField);
					}
					
					echo "($strExcelSheet!$strColumn" . "2:$strColumn$this->m_nRegisters=\" & chr(34) & \"$strValue\" & chr(34) & \")";
					$i++;
					if ($i < count($arrCombination))
						echo "*";
				}		
				echo "*1)\"\n";
				reset($arrFieldsShow);				
				while (list($key, $strActionFieldShow) = each($arrFieldsShow))
				{
					// $strFieldShow
					// field -> count
					// field.sum -> sum of the fields
					// field.average.fieldB -> sum of the fields divide column fieldB
					$arrActionTemp =explode(".", $strActionFieldShow);
					
					$strFieldShow =$arrActionTemp[0];
					
					//echo "alert (\"Posicion: " . $arrActionFieldShow. "\")\n";
					
					$strOperationEnd ="";
					$strColumnOperationEnd ="";
					if (count($arrActionTemp) >= 2)
					{
						if ($arrActionTemp[1] =="sum")
						{
							$strOperationEnd ="sum";
						}
						else
							if ($arrActionTemp[1] == "avg")
							{
								$strOperationEnd ="avg";
								$strColumnOperationEnd =$arrActionTemp[2];
							}
					}
					
					$nCount++;
					echo "\t$strExcelSheet.ActiveSheet.Cells($this->m_nPosDetail,$nCount).FormulaArray =\"=sum(";				
					$i=0;
					reset($arrCombination);
					while (list($key, $strValue) = each($arrCombination))
					{
						for ($nField=0;$nField<count($this->m_arrFieldsShow);$nField++)
						{	
							if ($this->m_arrFieldsShow[$nField] == $arrFieldsNum[$i])
								$strColumn =chr(ord('A') + $nField);
						}
						echo "($strExcelSheet!$strColumn" . "2:$strColumn$this->m_nRegisters=\" & chr(34) & \"$strValue\" & chr(34) & \")";
						$i++;
						if ($i < count($arrCombination))
							echo "*";
					}		
					for ($nField=0;$nField<count($this->m_arrFieldsShow);$nField++)
					{	
						if ($this->m_arrFieldsShow[$nField] == $strFieldShow)
							$strColumn =chr(ord('A') + $nField);
					}
					if ($strOperationEnd == "")
						echo "*1)\"\n";
					if ($strOperationEnd == "sum")
						echo "*($strExcelSheet!$strColumn" . "2:$strColumn$this->m_nRegisters))\"\n";
					if ($strOperationEnd == "avg")
					{
						echo "*($strExcelSheet!$strColumn" . "2:$strColumn$this->m_nRegisters)";
						echo "/($strColumnOperationEnd" . "$this->m_nPosDetail:$strColumnOperationEnd$this->m_nPosDetail))\"\n";
					}
				}
				// recorrer el resto de campos mostrando la información
				
				$this->m_nPosDetail++;
				//echo "alert(\"HOLA\")\n";
			}
			
		}
	}
	
	/**
	* Not used (previous version)
	*/
	function GenCount($strSheetNew,$strLabelNew,$bNumber)
	{
		$strExcelSheet =$this->GetExcelSheet();
		echo "\t$strExcelSheet" . ".Worksheets.Add , $strExcelSheet.Worksheets($this->m_nExcelSheets),1\n";
		
		$this->m_nExcelSheets++;
		echo "\t$strExcelSheet.Worksheets($this->m_nExcelSheets).Select\n";
		echo "\t$strExcelSheet.ActiveSheet.Name =\"$strLabelNew\"\n";
		
		$nColumns =0;
		reset($this->m_arrFields);
		while (list($key, $strValueField) = each($this->m_arrFields))
		{
			if ($strValueField == $strSheetNew) 
			{ 
				$nOrdColumn =ord('A') + $nColumns;
				$strColumn =chr($nOrdColumn);
				//continue;
			} 	
			$nColumns =$nColumns +1;
		}
				
		$nCount =2;
		reset($this->m_arrFieldValues[$strSheetNew]);
		while (list($key, $strValueField) = each($this->m_arrFieldValues[$strSheetNew]))
		{
			echo "\t$strExcelSheet.ActiveSheet.Cells($nCount,1).Value =\"";
			echo $key ."\"\n";
			
			echo "\t$strExcelSheet.ActiveSheet.Cells($nCount,2).FormulaArray =\"=";
			if ($bNumber == TRUE)
				echo "sum(($strExcelSheet!$strColumn" . "2:$strColumn$this->=\" & \"$key\" & \")*1)\"\n";
			else
				echo "sum(($strExcelSheet!$strColumn" . "2:$strColumn$this->=\" & chr(34) & \"$key\" & chr(34) & \")*1)\"\n";
			
			$nCount++;
		}
	}
	
	/**
	* Generate a new excel sheet with the data obtained executing the sentence $sql
	* \param $sql Sql sentence to get the data
	*/
	function GenExcel($sql)
	{
		$strExcelSheet =$this->GetExcelSheet();
		$strExcelLabel =$this->GetExcelLabel();
		$this->m_nExcelSheets =1;
		
		echo "\t$strExcelSheet" . ".Worksheets(2).Delete\n";
		echo "\t$strExcelSheet" . ".Worksheets(2).Delete\n";
	
		echo "\t$strExcelSheet" . ".Worksheets(1).Select\n";
		echo "\t$strExcelSheet" . ".ActiveSheet.Name =\"$strExcelLabel\"\n";
		
		$this->dbconnect();
		$result=$this->doQuery($sql);
		$nCount =1;
		$nColumns =count($this->m_arrFieldsShow);

		for ($i=0;$i<$nColumns;$i++)
		{
			echo "\t$strExcelSheet.ActiveSheet.Cells(" . $nCount . ",". ($i + 1) .").Value =\"";
			echo $this->m_arrFieldsShow[$i] ."\"\n";
			echo "\t$strExcelSheet.ActiveSheet.Cells(" . $nCount . ",". ($i + 1) .").Font.Color =\"";
			echo $this->GetColorField() ."\"\n";
			echo "\t$strExcelSheet.ActiveSheet.Cells(" . $nCount . ",". ($i + 1) .").Font.Name =\"";
			echo $this->GetFontField() ."\"\n";
			echo "\t$strExcelSheet.ActiveSheet.Cells(" . $nCount . ",". ($i + 1) .").Font.Size =";
			echo $this->GetFontSizeField() ."\n";
			if (array_search($this->m_arrFieldsShow[$i],$this->m_arrFields) !== false)
				$this->m_arrFieldValues[$this->m_arrFieldsShow[$i]] = array();
		}
		$nCount =2;
		
		$this->m_nRegisters = mysql_num_rows($result) + 1; 
		while ($r=mysql_fetch_array($result))
		{
			$strDescripcion =$r['strDescripcion'];
			$strDescripcion =str_replace("\n","",$strDescripcion);
			$strDescripcion =str_replace("\r","",$strDescripcion);
						
			$strCombination ="";
			for ($i=0;$i<$nColumns;$i++)
			{
				echo "\t$strExcelSheet.ActiveSheet.Cells(" . $nCount . ",". ($i + 1) .").Value =\"";
				echo $r[$this->m_arrFieldsShow[$i]] ."\"\n";
				
				if (array_search($this->m_arrFieldsShow[$i],$this->m_arrFields) !== false)
				{
					$strCombination .=$this->m_arrFieldsShow[$i] . ":" . $r[$this->m_arrFieldsShow[$i]] .";";
				
					if (!array_key_exists($r[$this->m_arrFieldsShow[$i]], $this->m_arrFieldValues[$this->m_arrFieldsShow[$i]])) 
					{ 
						$this->m_arrFieldValues[$this->m_arrFieldsShow[$i]][$r[$this->m_arrFieldsShow[$i]]] =1;
					} 	
				}
			}
			if (($strCombination != "") && (!in_array($strCombination,$this->m_arrAllCombinations)))
			{
				//echo "alert(\"$strCombination\")\n";
				$this->m_arrAllCombinations[] =$strCombination;
			}
			$nCount++;
		}
		$nOrdColumn =ord('A') + $nColumns - 1;
		$strColumn =chr($nOrdColumn);
		echo "\t$strExcelSheet" . ".ActiveSheet.Range(\"A2:$strColumn" . "$nCount\").Font.Color=\"";
		echo $this->GetColorData() ."\"\n";
		echo "\t$strExcelSheet" . ".ActiveSheet.Range(\"A2:$strColumn" . "$nCount\").Font.Name=\"";
		echo $this->GetFontData() ."\"\n";
		echo "\t$strExcelSheet" . ".ActiveSheet.Range(\"A2:$strColumn" . "$nCount\").Font.Size=\"";
		echo $this->GetFontSizeData() ."\"\n";
		echo "\t$strExcelSheet" . ".ActiveSheet.Range(\"A1:$strColumn" . "$nCount\").Columns.AutoFit\n";
		echo "\t$strExcelSheet" . ".ActiveSheet.Range(\"A1:$strColumn" . "$nCount\").NumberFormat =\"General\"\n";
	}
	
	/**
	* Include a new excel sheet vbscript object in the page
	* \param nWidth Width of the excel sheet in %
	* \param nHeight Height of the excel sheet in ???
	*/
	function AddExcel($nWidth, $nHeight)
	{
		$strExcel =$this->GetExcelSheet();
		echo "<object id=$strExcel classid=CLSID:0002E559-0000-0000-C000-000000000046 style=\"width:$nWidth%;height:$nHeight\"></object>\n";
	}
	
	/**
	* Include a new excel chart vbscript object in the page
	* \param nWidth Width of the excel chart in %
	* \param nHeight Height of the excel chart in ???
	*/
	function AddChart($strChart, $nWidth, $nHeight)
	{
		echo "<object id=$strChart classid=CLSID:0002E55D-0000-0000-C000-000000000046 style=\"width:$nWidth%;height:$nHeight\"></object>\n";
	}
	
	/**
	* Constructor of the class
	* \param strExcelSheet name of the excel vbscript object
	* \param strExcelLabel Label of the excel sheet
	*/
	function EasyExcel($strExcelSheet,$strExcelLabel)
	{
		$this->m_strExcelSheet =$strExcelSheet;
		$this->m_strExcelLabel =$strExcelLabel;
		$this->m_strColorField ="red";
		$this->m_strFontField ="Courier New";	
		$this->m_strFontSizeField ="11";		
		$this->m_strColorData ="blue";
		$this->m_strFontData ="Courier New";	
		$this->	m_strFontSizeData ="10";
	}
	
	/**
	* Generate the declaration of the method to load the data in the page
	*/
	function Begin_OnLoad()
	{
		echo "<script language=vbscript>\n";
		echo "Sub Window_OnLoad()\n";
		echo "Set chConstants = " .$this->GetExcelSheet() . ".Constants\n";
		echo $this->GetExcelSheet() . ".ActiveSheet.Cells.Clear\n";	
	}
	
	/**
	* Close the method
	*/
	function Close_OnLoad()
	{
		echo "End Sub\n";
		echo "</script>\n";		
	}
}



?>