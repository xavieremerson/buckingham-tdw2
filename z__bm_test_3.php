<?
include('includes/dbconnect.php');
include('includes/functions.php');
include('includes/global.php'); 

//https://buckresearch.bluematrix.com/sellside/ChangeFeed.action?firmId=67811&login=aMAi1s98&password=a02Akws&mode=coverage&param=full

//Create array of people with email to update TDW.
$getXMLString = file_get_contents('https://buckresearch.bluematrix.com/sellside/ChangeFeed.action?firmId=67811&login=aMAi1s98&password=a02Akws&mode=People&param=full');
$bm_data = simplexml_load_string($getXMLString);

foreach ($bm_data->People as $People) {

/*
<id>2</id><firstName>Joseph</firstName><lastName>Amaturo</lastName><initial>C.</initial><title>Director of Research</title><phone>212-922-1815</phone><email>jamaturo@buckresearch.com</email>
*/

		if ($People->firstName != "") {
    echo $People->clientCode."<br>";
    echo $People->id."<br>";
    echo $People->firstName."<br>";
    echo $People->lastName."<br>";
    echo $People->initial."<br>";
		}
		//foreach($People->Security as $Security) {
//    	echo ">>".$Security["pricingSymbol"].">>". $Security["companyName"]."<br>";
//		}
		
//    echo "<p>>>> >>> >>></p>"."<br>";
//    echo $People->pricingSymbol."y<br>";
//    echo $People->creator."z<br>";

}
exit;




foreach ($bm_data->People as $People) {

    echo $People["clientCode"]."<br>";

		foreach($People->Security as $Security) {
    	echo ">>".$Security["pricingSymbol"].">>". $Security["companyName"]."<br>";
		}
		
//    echo "<p>>>> >>> >>></p>"."<br>";
//    echo $People->pricingSymbol."y<br>";
//    echo $People->creator."z<br>";

}



$getXMLString = file_get_contents('https://buckresearch.bluematrix.com/sellside/ChangeFeed.action?firmId=67811&login=aMAi1s98&password=a02Akws&mode=coverage&param=full');
$bm_data = simplexml_load_string($getXMLString);

//print_r($bm_data);
foreach ($bm_data->People as $People) {

    echo $People["clientCode"]."<br>";

		foreach($People->Security as $Security) {
    	echo ">>".$Security["pricingSymbol"].">>". $Security["companyName"]."<br>";
		}
		
//    echo "<p>>>> >>> >>></p>"."<br>";
//    echo $People->pricingSymbol."y<br>";
//    echo $People->creator."z<br>";

}




//foreach ($bm_data as $k=>$v) {
//	echo $v."<br>";
//}


//$url = 'https://buckresearch.bluematrix.com/sellside/ChangeFeed.action?firmId=67811&login=aMAi1s98&password=a02Akws&mode=coverage&param=full';
//$arr_coverage = xml2array($url, $get_attributes = 1, $priority = 'tag');
//
////show_array($arr_coverage); 
//print_r($arr_coverage);

exit;

?>

<?php
function xml2array($url, $get_attributes = 1, $priority = 'tag')
{
    $contents = "";
    if (!function_exists('xml_parser_create'))
    {
        return array ();
    }
    $parser = xml_parser_create('');
    if (!($fp = @ fopen($url, 'rb')))
    {
        return array ();
    }
    while (!feof($fp))
    {
        $contents .= fread($fp, 8192);
    }
    fclose($fp);
    xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
    xml_parse_into_struct($parser, trim($contents), $xml_values);
    xml_parser_free($parser);
    if (!$xml_values)
        return; //Hmm...
    $xml_array = array ();
    $parents = array ();
    $opened_tags = array ();
    $arr = array ();
    $current = & $xml_array;
    $repeated_tag_index = array ();
    foreach ($xml_values as $data)
    {
        unset ($attributes, $value);
        extract($data);
        $result = array ();
        $attributes_data = array ();
        if (isset ($value))
        {
            if ($priority == 'tag')
                $result = $value;
            else
                $result['value'] = $value;
        }
        if (isset ($attributes) and $get_attributes)
        {
            foreach ($attributes as $attr => $val)
            {
                if ($priority == 'tag')
                    $attributes_data[$attr] = $val;
                else
                    $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
            }
        }
        if ($type == "open")
        {
            $parent[$level -1] = & $current;
            if (!is_array($current) or (!in_array($tag, array_keys($current))))
            {
                $current[$tag] = $result;
                if ($attributes_data)
                    $current[$tag . '_attr'] = $attributes_data;
                $repeated_tag_index[$tag . '_' . $level] = 1;
                $current = & $current[$tag];
            }
            else
            {
                if (isset ($current[$tag][0]))
                {
                    $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
                    $repeated_tag_index[$tag . '_' . $level]++;
                }
                else
                {
                    $current[$tag] = array (
                        $current[$tag],
                        $result
                    );
                    $repeated_tag_index[$tag . '_' . $level] = 2;
                    if (isset ($current[$tag . '_attr']))
                    {
                        $current[$tag]['0_attr'] = $current[$tag . '_attr'];
                        unset ($current[$tag . '_attr']);
                    }
                }
                $last_item_index = $repeated_tag_index[$tag . '_' . $level] - 1;
                $current = & $current[$tag][$last_item_index];
            }
        }
        elseif ($type == "complete")
        {
            if (!isset ($current[$tag]))
            {
                $current[$tag] = $result;
                $repeated_tag_index[$tag . '_' . $level] = 1;
                if ($priority == 'tag' and $attributes_data)
                    $current[$tag . '_attr'] = $attributes_data;
            }
            else
            {
                if (isset ($current[$tag][0]) and is_array($current[$tag]))
                {
                    $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
                    if ($priority == 'tag' and $get_attributes and $attributes_data)
                    {
                        $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
                    }
                    $repeated_tag_index[$tag . '_' . $level]++;
                }
                else
                {
                    $current[$tag] = array (
                        $current[$tag],
                        $result
                    );
                    $repeated_tag_index[$tag . '_' . $level] = 1;
                    if ($priority == 'tag' and $get_attributes)
                    {
                        if (isset ($current[$tag . '_attr']))
                        {
                            $current[$tag]['0_attr'] = $current[$tag . '_attr'];
                            unset ($current[$tag . '_attr']);
                        }
                        if ($attributes_data)
                        {
                            $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
                        }
                    }
                    $repeated_tag_index[$tag . '_' . $level]++; //0 and 1 index is already taken
                }
            }
        }
        elseif ($type == 'close')
        {
            $current = & $parent[$level -1];
        }
    }
    return ($xml_array);
}

/*
ChangeFeed Modes and Parameters

There are certain parameters you need to provide to get to the service. Any parameter that is not registered will take you to the help screen

HELP

//$bm_page_help = file_get_contents('https://buckresearch.bluematrix.com/sellside/ChangeFeed.action?firmId=67811&login=aMAi1s98&password=a02Akws&mode=help');
//echo '<pre>'.$bm_page_help.'</pre>';
$url = 'https://buckresearch.bluematrix.com/sellside/ChangeFeed.action?firmId=67811&login=aMAi1s98&password=a02Akws&mode=help';
//$arr_help = xml2array($url, $get_attributes = 1, $priority = 'tag');
//show_array($arr_help);

SYSTEM 
https://buckresearch.bluematrix.com/sellside/ChangeFeed.action?firmId=67811&login=aMAi1s98&password=a02Akws&mode=system
The fromEventId param allows you to specify the starting change event.
The timeframe param allows you to specify a different timeframe.

CONTENT 
$url = 'https://buckresearch.bluematrix.com/sellside/ChangeFeed.action?firmId=67811&login=aMAi1s98&password=a02Akws&mode=Content&param=full';
$arr_content = xml2array($url, $get_attributes = 1, $priority = 'tag');
show_array($arr_content);

This gives you information related to published documents (including status, versions, and urls)
With param=full you get all the documents for a given period of time (30 days by default), but you can specify other intervals:
https://buckresearch.bluematrix.com/sellside/ChangeFeed.action?handler=LoginHandler&firmId=67811&login=aMAi1s98&password=a02Akws&mode=content&param=full&timeframe=2012-01-01T05:53:02.00
param can be a doc id:
https://buckresearch.bluematrix.com/sellside/ChangeFeed.action?firmId=67811&login=aMAi1s98&password=a02Akws&mode=Content&param=3813
If "&renderXml=true" is added to the URL string, document XML will be re-rendered
If ""&renderXml=true&renderAll=true" is added to the URL string, the XML will re-rendered and all the MIME TYPES are refreshed also.

TAGGING_TYPE 
https://buckresearch.bluematrix.com/sellside/ChangeFeed.action?firmId=67811&login=aMAi1s98&password=a02Akws&mode=taggingType

TAGGING 
https://buckresearch.bluematrix.com/sellside/ChangeFeed.action?firmId=67811&login=aMAi1s98&password=a02Akws&mode=tagging&param=1
param is required and needs to specify a valid tagging type (the types can be found in the tagging feed above).

CONTENT_META 
https://buckresearch.bluematrix.com/sellside/ChangeFeed.action?firmId=67811&login=aMAi1s98&password=a02Akws&mode=contentMeta&param=full
Document type information: param=full will return all document types, or you can specify a doctype id.

STATIC_META 
https://buckresearch.bluematrix.com/sellside/ChangeFeed.action?firmId=67811&login=aMAi1s98&password=a02Akws&mode=staticMeta&type=Currencies.xml
Information about static data XML files used by Sellside; the type parameter can be taken from the HELP feed, type section.

COMPLIANCE COMPANY XML https://buckresearch.bluematrix.com/sellside/ChangeFeed.action?firmId=67811&login=aMAi1s98&password=a02Akws&mode=complianceCompanyXml
This is a compliance-company info feed.

PRODUCT CATEGORY 
https://buckresearch.bluematrix.com/sellside/ChangeFeed.action?firmId=67811&login=aMAi1s98&password=a02Akws&mode=productCategory
Information about the available focus types for a document and where more information about these can be found.

INDUSTRY 
https://buckresearch.bluematrix.com/sellside/ChangeFeed.action?firmId=67811&login=aMAi1s98&password=a02Akws&mode=Industry&param=full
Industry information: param=full or an industry id is required 

ISSUER 
https://buckresearch.bluematrix.com/sellside/ChangeFeed.action?firmId=67811&login=aMAi1s98&password=a02Akws&mode=Issuer&param=full
Issuer information: param=full or an issuer id is required

SECURITY 
https://buckresearch.bluematrix.com/sellside/ChangeFeed.action?firmId=67811&login=aMAi1s98&password=a02Akws&mode=Security&param=full
Security information: param=full or a security id is required

PEOPLE
https://buckresearch.bluematrix.com/sellside/ChangeFeed.action?firmId=67811&login=aMAi1s98&password=a02Akws&mode=People&param=full
User related information: param=full or an issuer id is required

ROLE 
https://buckresearch.bluematrix.com/sellside/ChangeFeed.action?firmId=67811&login=aMAi1s98&password=a02Akws&mode=role&param=full
Role and user-role mapping information: param=full or a role id is required

CURRENCY PAIR 
https://buckresearch.bluematrix.com/sellside/ChangeFeed.action?firmId=67811&login=aMAi1s98&password=a02Akws&mode=CurrencyPair&param=full
Currency pair related information: param=full or a currency pair id is required

COMMODITY 
https://buckresearch.bluematrix.com/sellside/ChangeFeed.action?firmId=67811&login=aMAi1s98&password=a02Akws&mode=Commodity&param=full
Commodity related information: param=full or a commodity id is required

SERIES 
https://buckresearch.bluematrix.com/sellside/ChangeFeed.action?firmId=67811&login=aMAi1s98&password=a02Akws&mode=Series&param=full
Series related information: param=full or a series id is required

EMAIL LOGS 
https://buckresearch.bluematrix.com/sellside/ChangeFeed.action?firmId=67811&login=aMAi1s98&password=a02Akws&mode=emailLog&param=1
Email logs for a specific document: param is required and should be a valid integer and a valid docId



SEARCH 
https://buckresearch.bluematrix.com/sellside/ChangeFeed.action?firmId=67811&login=aMAi1s98&password=a02Akws&mode=search&searchText="Blu”

People (name) and Company (ticker/name/symbol) Search: 
searchText is required and should be a non-empty String
this method searches people (based on the first sequence of the names) and companies (based on tickers, names and symbols). 
For companies, anything less than 3 letters will be a ticker search. 

WORKFLOW 
https://buckresearch.bluematrix.com/sellside/ChangeFeed.action?firmId=67811&login=aMAi1s98&password=a02Akws&mode=workflow&param=full
Workflow information: param=full or a workflow id is required

ELEMENTS 
https://buckresearch.bluematrix.com/sellside/ChangeFeed.action?firmId=67811&login=aMAi1s98&password=a02Akws&mode=elements&param=full
Element-related information: param=full or an element id is required

For the content feed:

Status Type Attribute Possible values:

“Not Published” – anything that is not published (author, approval queue, recalled, deleted)
“Published” – a document that is currently published 

Extended Status Attribute Possible values:

“Not Published” – a draft document (not yet published)
“Published” – a published document that was never recalled
“Recalled” – a recalled document
“Republished” – a published document previously recalled
“Deleted” – a document that was deleted (the published documents cannot be deleted, they will have to be recalled first)*/

?>