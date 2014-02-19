<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>jQuery Autocomplete</title>
	<script type="text/javascript" src="includes/jquery/jquery.js"></script>
	<script type='text/javascript' src='includes/jquery/jquery.autocomplete.js'></script>
	<link rel="stylesheet" type="text/css" href="includes/jquery/jquery.autocomplete.css" />
</head>

<body>

<h1>
	jQuery Autocomplete Mod
</h1>

<p>
	This is modification of <a href="http://www.dyve.net/jquery/?autocomplete">Dylan Verheul's jQuery Autcomplete plug-in</a>.
	I customized his library adding the features I needed and fixing issues
	I considered bugs.
</p>

<h2>
	Enhancements

</h2>
<ul>
	<li>
		Supports local data array (can now use w/out AJAX).
	</li>
	<li>
		Limit dropdown to XX number of results (good for limiting
		the results to users)
	</li>
	<li>
		Autofill pre-populates text box as you type
	</li>

	<li>
		New findValue() method can be used to programmatically
		determine if the value in the box is a valid option.
		(Useful for verifying the text entered is an existing
		value option.)
	</li>
	<li>
		Dropdown options now correctly re-position themselves on
		each display (which means they adjust for changing to the
		DOM)
	</li>
	<li>
		Dropdown box defaults to the width of the input field its
		attached to (you can manually specify a larger width as well)
	</li>
	<li>

		Better emulates Windows autocomplete boxes (for example: hitting delete
		and retyping the same box will now bring back the dropdown
		menu)
	</li>
	<li>
		Miscellaneous bug fixes
	</li>
</ul>

<form action="" onsubmit="return false;">

	<p>
		Local City Autocomplete:
		<input type="text" id="CityLocal" value="" />

		<input type="button" value="Get Value" onclick="lookupLocal();" />
		(Shows a max of 10 entries)
	</p>

</form>

<script type="text/javascript">
function findValue(li) {
	if( li == null ) return alert("No match!");

	// if coming from an AJAX call, let's use the CityId as the value
	if( !!li.extra ) var sValue = li.extra[0];

	// otherwise, let's just display the value in the text box
	else var sValue = li.selectValue;

	alert("The value you selected was: " + sValue);
}

function selectItem(li) {
	findValue(li);
}

function formatItem(row) {
	return row[0] + " (id: " + row[1] + ")";
}

function lookupLocal(){
	var oSuggest = $("#CityLocal")[0].autocompleter;

	oSuggest.findValue();

	return false;
}

$(document).ready(function() {
	$("#CityAjax").autocomplete(
		"autocomplete_ajax.cfm",
		{
			delay:10,
			minChars:2,
			matchSubset:1,
			matchContains:1,
			cacheLength:10,
			onItemSelect:selectItem,
			onFindValue:findValue,
			formatItem:formatItem,
			autoFill:true
		}
	);

	$("#CityLocal").autocompleteArray(
		[
			"Aberdeen", "Ada", "Adamsville", "Addyston", "Adelphi", "Adena", "Adrian", "Akron",
			"Albany", "Alexandria", "Alger", "Alledonia", "Alliance", "Alpha", "Alvada",
			"Alvordton", "Amanda", "Amelia", "Amesville", "Amherst", "Amlin", "Amsden",
			"Amsterdam", "Andover", "Anna", "Ansonia", "Antwerp", "Apple Creek", "Arcadia",
			"Arcanum", "Archbold", "Arlington", "Ashland", "Ashley", "Ashtabula", "Ashville",
			"Athens", "Attica", "Atwater", "Augusta", "Aurora", "Austinburg", "Ava", "Avon",
			"Avon Lake", "Bainbridge", "Bakersville", "Baltic", "Baltimore", "Bannock",
			"Barberton", "Barlow", "Barnesville", "Bartlett", "Barton", "Bascom", "Batavia",
			"Bath", "Bay Village", "Beach City", "Beachwood", "Beallsville", "Beaver",
			"Beaverdam", "Bedford", "Bellaire", "Bellbrook", "Belle Center", "Belle Valley",
			"Bellefontaine", "Bellevue", "Bellville", "Belmont", "Belmore", "Beloit", "Belpre",
			"Benton Ridge", "Bentonville", "Berea", "Bergholz", "Berkey", "Berlin",
			"Berlin Center", "Berlin Heights", "Bethel", "Bethesda", "Bettsville", "Beverly",
			"Yorkshire", "Yorkville", "Youngstown", "Zaleski", "Zanesfield", "Zanesville",
			"Zoar"
		],
		{
			delay:2,
			minChars:1,
			matchSubset:1,
			onItemSelect:selectItem,
			onFindValue:findValue,
			autoFill:false,
			maxItemsToShow:10
		}
	);
});
</script>

</body>
</html>
