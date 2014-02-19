<?
$aCountries = array( 'United States', 'Afghanistan', 'Albania', 'Algeria', 'American Samoa', 'Andorra', 'Angola', 'Antigua', 'Argentina', 'Armenia', 'Aruba', 'Australia', 'Austria', 'Azerbaijan', 'Azores', 'Bahamas', 'Bahrain', 'Bangladesh', 'Barbados', 'Barbuda', 'Belarus', 'Belgium', 'Belize', 'Benin', 'Bermuda [U.K.]', 'Bhutan', 'Bolivia', 'Bosnia/Herzegovina', 'Botswana', 'Brazil', 'Brunei Darussalam', 'Bulgaria', 'Burkina Faso', 'Burundi', 'Caicos', 'Cambodia', 'Cameroon', 'Canada', 'Canary Islands', 'Cape Verde islands', 'Cayman Islands', 'Central Africa', 'Chad', 'Chile', 'China', 'Christmas Island', 'Colombia', 'Comoros', 'Congo', 'Congo [Zaire]', 'Cook Island', 'Costa Rica', 'Cote d’Ivoire (Ivory Coast)', 'Croatia', 'Cuba', 'Cyprus', 'Czech Republic', 'Denmark', 'Djibouti', 'Dominica', 'Dominican Republic', 'East Timor', 'Ecuador', 'Egypt', 'El Salvador', 'England', 'Equatorial Guinea', 'Eritrea', 'Estonia', 'Ethiopia', 'Falkland Islands', 'Faroe Island', 'Fiji', 'Finland', 'France', 'French Guiana', 'Gabon', 'Gambia', 'Georgia', 'Germany', 'Ghana', 'Gibraltar', 'Greece', 'Greenland', 'Grenada', 'Grenadines', 'Guadeloupe', 'Guam', 'Guatemala', 'Guinea', 'Guinea-Bissau', 'Guyana', 'Haiti', 'Holland', 'Honduras', 'Hong Kong', 'Hungary', 'Iceland', 'India', 'Indonesia', 'Iran', 'Iraq', 'Ireland', 'Israel', 'Italy', 'Jamaica', 'Japan', 'Jordan', 'Kazakhstan', 'Kenya', 'Kiribati', 'Korea [North]', 'Korea [South]', 'Kuwait', 'Kyrgyzstan', 'Lao [Laos]', 'Latvia', 'Lebanon', 'Lesotho', 'Liberia', 'Liechtenstein', 'Lithuania', 'Luxembourg', 'Lybia', 'Macao', 'Macendonia', 'Madagascar', 'Madeira', 'Malawi', 'Malaysia', 'Maldives', 'Mali', 'Malta', 'Mariana Islands', 'Marshall Islands', 'Martinique [France]', 'Mauritania', 'Mauritius', 'Mayotte', 'Mexico', 'Micronesia', 'Moldova', 'Monaco', 'Mongolia', 'Montserrat [U.K.]', 'Morocco', 'Mozambique', 'Myanmar [Burma],', 'Namibia', 'Nauru', 'Nepal', 'Netherlands', 'Netherlands Antilles', 'Nevis', 'New Caledonia', 'New Hebrides', 'New Zealand', 'Nicaragua', 'Niger', 'Nigeria', 'Niue', 'North Ireland', 'Norway', 'Oman', 'Pakistan', 'Palau', 'Panama', 'Papua New Guinea', 'Paraguay', 'Peru', 'Philippines', 'Pitcairn', 'Poland', 'Portugal', 'Principe', 'Puerto Rico [U.S.]', 'Qatar', 'Reunion Island', 'Romania', 'Russia', 'Rwanda', 'Ryukyu Islands', 'Samoa', 'San Marino', 'Sa~o Tome', 'Saudi Arabia', 'Scotland', 'Senegal', 'Serbia/Montenegro', 'Seychelles', 'Sierra Leone', 'Singapore', 'Slovak Republic', 'Slovenia', 'Solomon Islands', 'Somalia', 'South Africa', 'Spain', 'Sri Lanka', 'St. Helena', 'St. Kitts', 'St. Lucia', 'St. Vincent', 'Sudan', 'Suriname', 'Swaziland', 'Sweden', 'Switzerland', 'Syrian Arab', 'Tahiti', 'Taiwan', 'Tajikistan', 'Tanzania', 'Thailand', 'Tobago', 'Togo', 'Tokelau', 'Tonga', 'Trinidad', 'Tunisia', 'Turkey', 'Turkmenistan', 'Tuvalu', 'Uganda', 'Ukraine', 'United Arab Emirates', 'United States', 'Uruguay', 'Uzbekistan', 'Vanuatu', 'Venezuela', 'Vietnam', 'Virgin Islands', 'Wake Island', 'Wales', 'Wallis', 'Yemen', 'Yugoslavia', 'Zaire', 'Zambia', 'Zimbabwe' );
$sSearch = $_GET[ "s" ];
$sResult = "";

$hF = fopen( "countries.txt", "r" );
if( $hF )
{
	$sF = fread( $hF, filesize( "countries.txt" ) );
	fclose( $hF );
	$aCountries = explode( "\n", $sF );
}

$nLimit = $_GET[ "l" ];
if( empty( $nLimit ) )
{
	$nLimit = 5;
}

$nCount = 0;

for( $nI = 0; $nI < count( $aCountries ); $nI++ )
{
	if( eregi( ",".$sSearch, $aCountries[ $nI ] ) )
	{
		$sResult .= "|".$aCountries[ $nI ];
		//$sResult .= "|".substr( $aCountries[ $nI ], 0, 2 ).",".$aCountries[ $nI ];
		//$sResult .= "|".$aCountries[ $nI ];
		$nCount++;
	}
	if( $nCount >= $nLimit )
	{
		break;
	}
}

if( $sResult[ 0 ] == "|" )
{
	$sResult = substr( $sResult, 1, strlen( $sResult ) - 1 );
}
echo $sResult;
?>
