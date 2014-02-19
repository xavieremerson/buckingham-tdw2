<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>jQuery Autocomplete</title>
	<script type="text/javascript" src="includes/jquery/jquery.js"></script>
	<script type='text/javascript' src='includes/jquery/jquery.autocomplete.js'></script>
	<link rel="stylesheet" type="text/css" href="includes/jquery/jquery.autocomplete.css" />
</head>

<body>
<form action="" onsubmit="return false;">

	<p>
		Local City Autocomplete:
		<input type="text" id="CityLocal" value="" />

		(Shows a max of 10 entries)
	</p>

</form>

<script type="text/javascript">
$(document).ready(function() {
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
			autoFill:false,
			maxItemsToShow:10
		}
	);
});
</script>

</body>
</html>
