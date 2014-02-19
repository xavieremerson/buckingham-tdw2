<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>autocomplete</title>
<style>
	@import url( css/page.css );
	@import url( css/tabsexamples.css );
	@import url( css/SyntaxHighlighter.css );
	@import url( css/dropdown.css );
</style>
<script src="js/modomevent3.js"></script>
<script src="js/modomt.js"></script>
<script src="js/modomext.js"></script>
<script src="js/tabs2.js"></script>
<script src="js/getobject2.js"></script>
<script src="js/xmlextras.js"></script>
<script src="js/acdropdown.js"></script>
<!-- syntax highlight -->
<script language="javascript" src="js/shCore.js" ></script >
<script language="javascript" src="js/shBrushXML.js" ></script >
<!-- syntax highlight -->
<script language="javascript">
var aCountries = new Array( 'United States', 'Afghanistan', 'Albania', 'Algeria', 'American Samoa', 'Andorra', 'Angola', 'Antigua', 'Argentina', 'Armenia', 'Aruba', 'Australia', 'Austria', 'Azerbaijan', 'Azores', 'Bahamas', 'Bahrain', 'Bangladesh', 'Barbados', 'Barbuda', 'Belarus', 'Belgium', 'Belize', 'Benin', 'Bermuda [U.K.]', 'Bhutan', 'Bolivia', 'Bosnia/Herzegovina', 'Botswana', 'Brazil', 'Brunei Darussalam', 'Bulgaria', 'Burkina Faso', 'Burundi', 'Caicos', 'Cambodia', 'Cameroon', 'Canada', 'Canary Islands', 'Cape Verde islands', 'Cayman Islands', 'Central Africa', 'Chad', 'Chile', 'China', 'Christmas Island', 'Colombia', 'Comoros', 'Congo', 'Congo [Zaire]', 'Cook Island', 'Costa Rica', 'Cote dIvoire (Ivory Coast)', 'Croatia', 'Cuba', 'Cyprus', 'Czech Republic', 'Denmark', 'Djibouti', 'Dominica', 'Dominican Republic', 'East Timor', 'Ecuador', 'Egypt', 'El Salvador', 'England', 'Equatorial Guinea', 'Eritrea', 'Estonia', 'Ethiopia', 'Falkland Islands', 'Faroe Island', 'Fiji', 'Finland', 'France', 'French Guiana', 'Gabon', 'Gambia', 'Georgia', 'Germany', 'Ghana', 'Gibraltar', 'Greece', 'Greenland', 'Grenada', 'Grenadines', 'Guadeloupe', 'Guam', 'Guatemala', 'Guinea', 'Guinea-Bissau', 'Guyana', 'Haiti', 'Holland', 'Honduras', 'Hong Kong', 'Hungary', 'Iceland', 'India', 'Indonesia', 'Iran', 'Iraq', 'Ireland', 'Israel', 'Italy', 'Jamaica', 'Japan', 'Jordan', 'Kazakhstan', 'Kenya', 'Kiribati', 'Korea [North]', 'Korea [South]', 'Kuwait', 'Kyrgyzstan', 'Lao [Laos]', 'Latvia', 'Lebanon', 'Lesotho', 'Liberia', 'Liechtenstein', 'Lithuania', 'Luxembourg', 'Lybia', 'Macao', 'Macendonia', 'Madagascar', 'Madeira', 'Malawi', 'Malaysia', 'Maldives', 'Mali', 'Malta', 'Mariana Islands', 'Marshall Islands', 'Martinique [France]', 'Mauritania', 'Mauritius', 'Mayotte', 'Mexico', 'Micronesia', 'Moldova', 'Monaco', 'Mongolia', 'Montserrat [U.K.]', 'Morocco', 'Mozambique', 'Myanmar [Burma],', 'Namibia', 'Nauru', 'Nepal', 'Netherlands', 'Netherlands Antilles', 'Nevis', 'New Caledonia', 'New Hebrides', 'New Zealand', 'Nicaragua', 'Niger', 'Nigeria', 'Niue', 'North Ireland', 'Norway', 'Oman', 'Pakistan', 'Palau', 'Panama', 'Papua New Guinea', 'Paraguay', 'Peru', 'Philippines', 'Pitcairn', 'Poland', 'Portugal', 'Principe', 'Puerto Rico [U.S.]', 'Qatar', 'Reunion Island', 'Romania', 'Russia', 'Rwanda', 'Ryukyu Islands', 'Samoa', 'San Marino', 'Sa~o Tome', 'Saudi Arabia', 'Scotland', 'Senegal', 'Serbia/Montenegro', 'Seychelles', 'Sierra Leone', 'Singapore', 'Slovak Republic', 'Slovenia', 'Solomon Islands', 'Somalia', 'South Africa', 'Spain', 'Sri Lanka', 'St. Helena', 'St. Kitts', 'St. Lucia', 'St. Vincent', 'Sudan', 'Suriname', 'Swaziland', 'Sweden', 'Switzerland', 'Syrian Arab', 'Tahiti', 'Taiwan', 'Tajikistan', 'Tanzania', 'Thailand', 'Tobago', 'Togo', 'Tokelau', 'Tonga', 'Trinidad', 'Tunisia', 'Turkey', 'Turkmenistan', 'Tuvalu', 'Uganda', 'Ukraine', 'United Arab Emirates', 'United States', 'Uruguay', 'Uzbekistan', 'Vanuatu', 'Venezuela', 'Vietnam', 'Virgin Islands', 'Wake Island', 'Wales', 'Wallis', 'Yemen', 'Yugoslavia', 'Zaire', 'Zambia', 'Zimbabwe' )
function formatCountries( sText )
{
return sText.substr( 0, sText.toLowerCase().indexOf( this.sActiveValue.toLowerCase() ) ) + sText.substr( sText.toLowerCase().indexOf( this.sActiveValue.toLowerCase() ), this.sActiveValue.length ).bold().fontcolor( '#ff0000' ) + sText.substr( sText.toLowerCase().indexOf( this.sActiveValue.toLowerCase() ) + this.sActiveValue.length )
}
function alertSelected()
{
document.getElementById( 'selectedCountry' ).innerHTML = this.sActiveValue
}
/* TOGGLE CODE */
function toggleCode( hEvent )
{
cDomEvent.init( hEvent )
var hLink = cDomEvent.target
var hREX = new RegExp( 'codeblock:(.*)', 'ig' )
hLink.getAttribute( 'target' ).match( hREX )
var sBlockId = RegExp.$1
var hCodeBlock = document.getElementById( sBlockId )
hCodeBlock.style.display = hCodeBlock.style.display == 'block' ? 'none' : 'block'
hLink.blur()
return false
}
function attachToggleCode( hLink )
{
hLink.onclick = toggleCode
hLink.className = 'codeToggle'
}
cDomExtensionManager.register( new cDomExtension( document, [ 'a[target*=codeblock]' ], attachToggleCode ) )
function onLoadInit()
{
dp.SyntaxHighlighter.HighlightAll('code')
}
//cDomEvent.addEvent( window, 'load', onLoadInit )
</script>
</head>
<body>
      <br/>
      ...or you can turn a regular select with countries list to an autocompleted one: <br/>
      <br/>
      <select acdropdown=true autocomplete_complete="true" name=country>
        <option value="af" > Afghanistan </option>
        <option value="al" > Albania </option>
        <option value="dz" > Algeria </option>
        <option value="as" > American Samoa </option>
        <option value="ad" > Andorra </option>
        <option value="ao" > Angola </option>
        <option value="ai" > Anguilla </option>
        <option value="aq" > Antarctica </option>
        <option value="ag" > Antigua And Barbuda </option>
        <option value="ar" > Argentina </option>
        <option value="am" > Armenia </option>
        <option value="aw" > Aruba </option>
        <option value="au" > Australia </option>
        <option value="at" > Austria </option>
        <option value="az" > Azerbaijan </option>
        <option value="bs" > Bahamas </option>
        <option value="bh" > Bahrain </option>
        <option value="bd" > Bangladesh </option>
        <option value="bb" > Barbados </option>
        <option value="by" > Belarus </option>
        <option value="be" > Belgium </option>
        <option value="bz" > Belize </option>
        <option value="bj" > Benin </option>
        <option value="bm" > Bermuda </option>
        <option value="bt" > Bhutan </option>
        <option value="bo" > Bolivia </option>
        <option value="ba" > Bosnia And Herzegovina </option>
        <option value="bw" > Botswana </option>
        <option value="bv" > Bouvet Island </option>
        <option value="br" > Brazil </option>
        <option value="io" > British Indian Ocean Territory </option>
        <option value="bn" > Brunei Darussalam </option>
        <option value="bg" > Bulgaria </option>
        <option value="bf" > Burkina Faso </option>
        <option value="bi" > Burundi </option>
        <option value="kh" > Cambodia </option>
        <option value="cm" > Cameroon </option>
        <option value="ca" > Canada </option>
        <option value="cv" > Cape Verde </option>
        <option value="ky" > Cayman Islands </option>
        <option value="cf" > Central African Republic </option>
        <option value="td" > Chad </option>
        <option value="cl" > Chile </option>
        <option value="cn" > China </option>
        <option value="cx" > Christmas Island </option>
        <option value="cc" > Cocos (Keeling) Islands </option>
        <option value="co" > Colombia </option>
        <option value="km" > Comoros </option>
        <option value="cg" > Congo </option>
        <option value="cd" > Congo, The Democratic Republic Of The </option>
        <option value="ck" > Cook Islands </option>
        <option value="cr" > Costa Rica </option>
        <option value="ci" > Co^te D'Ivoire </option>
        <option value="hr" > Croatia </option>
        <option value="cu" > Cuba </option>
        <option value="cy" > Cyprus </option>
        <option value="cz" > Czech Republic </option>
        <option value="dk" > Denmark </option>
        <option value="dj" > Djibouti </option>
        <option value="dm" > Dominica </option>
        <option value="do" > Dominican Republic </option>
        <option value="tp" > East Timor </option>
        <option value="ec" > Ecuador </option>
        <option value="eg" > Egypt </option>
        <option value="gq" > Equatorial Guinea </option>
        <option value="er" > Eritrea </option>
        <option value="ee" > Estonia </option>
        <option value="et" > Ethiopia </option>
        <option value="fk" > Falkland Islands (Malvinas) </option>
        <option value="fo" > Faroe Islands </option>
        <option value="fj" > Fiji </option>
        <option value="fi" > Finland </option>
        <option value="fr" selected> France </option>
        <option value="gf" > French Guiana </option>
        <option value="pf" > French Polynesia </option>
        <option value="tf" > French Southern Territories </option>
        <option value="ga" > Gabon </option>
        <option value="gm" > Gambia </option>
        <option value="ge" > Georgia </option>
        <option value="de" > Germany </option>
        <option value="gh" > Ghana </option>
        <option value="gi" > Gibraltar </option>
        <option value="gr" > Greece </option>
        <option value="gl" > Greenland </option>
        <option value="gd" > Grenada </option>
        <option value="gp" > Guadeloupe </option>
        <option value="gu" > Guam </option>
        <option value="gt" > Guatemala </option>
        <option value="gn" > Guinea </option>
        <option value="gw" > Guinea-Bissau </option>
        <option value="gy" > Guyana </option>
        <option value="ht" > Haiti </option>
        <option value="hm" > Heard Island And McDonald Islands </option>
        <option value="hn" > Honduras </option>
        <option value="hk" > Hong Kong </option>
        <option value="hu" > Hungary </option>
        <option value="is" > Iceland </option>
        <option value="in" > India </option>
        <option value="id" > Indonesia </option>
        <option value="ir" > Iran, Islamic Republic Of </option>
        <option value="iq" > Iraq </option>
        <option value="ie" > Ireland </option>
        <option value="il" > Israel </option>
        <option value="it" > Italy </option>
        <option value="jm" > Jamaica </option>
        <option value="jp" > Japan </option>
        <option value="jo" > Jordan </option>
        <option value="kz" > Kazakstan </option>
        <option value="ke" > Kenya </option>
        <option value="ki" > Kiribati </option>
        <option value="kp" > Korea, Democratic People's Republic Of </option>
        <option value="kr" > Korea, Republic Of </option>
        <option value="kw" > Kuwait </option>
        <option value="kg" > Kyrgyzstan </option>
        <option value="la" > Lao People's' Democratic Republic </option>
        <option value="lv" > Latvia </option>
        <option value="lb" > Lebanon </option>
        <option value="ls" > Lesotho </option>
        <option value="lr" > Liberia </option>
        <option value="ly" > Libyan Arab Jamahiriya </option>
        <option value="li" > Liechtenstein </option>
        <option value="lt" > Lithuania </option>
        <option value="lu" > Luxembourg </option>
        <option value="mo" > Macau </option>
        <option value="mk" > Macedonia, The Former Yugoslav Republic Of </option>
        <option value="mg" > Madagascar </option>
        <option value="mw" > Malawi </option>
        <option value="my" > Malaysia </option>
        <option value="mv" > Maldives </option>
        <option value="ml" > Mali </option>
        <option value="mt" > Malta </option>
        <option value="mh" > Marshall Islands </option>
        <option value="mq" > Martinique </option>
        <option value="mr" > Mauritania </option>
        <option value="mu" > Mauritius </option>
        <option value="yt" > Mayotte </option>
        <option value="mx" > Mexico </option>
        <option value="fm" > Micronesia, Federated States Of </option>
        <option value="md" > Moldova, Republic Of </option>
        <option value="mc" > Monaco </option>
        <option value="mn" > Mongolia </option>
        <option value="ms" > Montserrat </option>
        <option value="ma" > Morocco </option>
        <option value="mz" > Mozambique </option>
        <option value="mm" > Myanmar </option>
        <option value="na" > Namibia </option>
        <option value="nr" > Nauru </option>
        <option value="np" > Nepal </option>
        <option value="nl" > Netherlands </option>
        <option value="an" > Netherlands Antilles </option>
        <option value="nc" > New Caledonia </option>
        <option value="nz" > New Zealand </option>
        <option value="ni" > Nicaragua </option>
        <option value="ne" > Niger </option>
        <option value="ng" > Nigeria </option>
        <option value="nu" > Niue </option>
        <option value="nf" > Norfolk Island </option>
        <option value="mp" > Northern Mariana Islands </option>
        <option value="no" > Norway </option>
        <option value="om" > Oman </option>
        <option value="pk" > Pakistan </option>
        <option value="pw" > Palau </option>
        <option value="ps" > Palestinian Territory, Occupied </option>
        <option value="pa" > Panama </option>
        <option value="pg" > Papua New Guinea </option>
        <option value="py" > Paraguay </option>
        <option value="pe" > Peru </option>
        <option value="ph" > Philippines </option>
        <option value="pn" > Pitcairn </option>
        <option value="pl" > Poland </option>
        <option value="pt" > Portugal </option>
        <option value="pr" > Puerto Rico </option>
        <option value="qa" > Qatar </option>
        <option value="re" > Re'union </option>
        <option value="ro" > Romania </option>
        <option value="ru" > Russian Federation </option>
        <option value="rw" > Rwanda </option>
        <option value="sh" > Saint Helena </option>
        <option value="kn" > Saint Kitts And Nevis </option>
        <option value="lc" > Saint Lucia </option>
        <option value="pm" > Saint Pierre And Miquelon </option>
        <option value="vc" > Saint Vincent And The Grenadines </option>
        <option value="sv" > Salvador </option>
        <option value="ws" > Samoa </option>
        <option value="sm" > San Marino </option>
        <option value="st" > Sao Tome And Principe </option>
        <option value="sa" > Saudi Arabia </option>
        <option value="sn" > Senegal </option>
        <option value="sc" > Seychelles </option>
        <option value="sl" > Sierra Leone </option>
        <option value="sg" > Singapore </option>
        <option value="sk" > Slovakia </option>
        <option value="si" > Slovenia </option>
        <option value="sb" > Solomon Islands </option>
        <option value="so" > Somalia </option>
        <option value="za" > South Africa </option>
        <option value="gs" > South Georgia And The South Sandwich Islands </option>
        <option value="es" > Spain </option>
        <option value="lk" > Sri Lanka </option>
        <option value="sd" > Sudan </option>
        <option value="sr" > Suriname </option>
        <option value="sj" > Svalbard And Jan Mayen </option>
        <option value="sz" > Swaziland </option>
        <option value="se" > Sweden </option>
        <option value="ch" > Switzerland </option>
        <option value="sy" > Syrian Arab Republic </option>
        <option value="tw" > Taiwan </option>
        <option value="tj" > Tajikistan </option>
        <option value="tz" > Tanzania, United Republic Of </option>
        <option value="th" > Thailand </option>
        <option value="tg" > Togo </option>
        <option value="tk" > Tokelau </option>
        <option value="to" > Tonga </option>
        <option value="tt" > Trinidad And Tobago </option>
        <option value="tn" > Tunisia </option>
        <option value="tr" > Turkey </option>
        <option value="tm" > Turkmenistan </option>
        <option value="tc" > Turks And Caicos Islands </option>
        <option value="tv" > Tuvalu </option>
        <option value="ug" > Uganda </option>
        <option value="ua" > Ukraine </option>
        <option value="ae" > United Arab Emirates </option>
        <option value="gb" > United Kingdom </option>
        <option value="us" > United States </option>
        <option value="um" > United States Minor Outlying Islands </option>
        <option value="uy" > Uruguay </option>
        <option value="uz" > Uzbekistan </option>
        <option value="vu" > Vanuatu </option>
        <option value="va" > Vatican City State (Holy See) </option>
        <option value="ve" > Venezuela </option>
        <option value="vn" > Viet Nam </option>
        <option value="vg" > Virgin Islands, British </option>
        <option value="vi" > Virgin Islands, U.S. </option>
        <option value="wf" > Wallis And Futuna </option>
        <option value="eh" > Western Sahara </option>
        <option value="ye" > Yemen </option>
        <option value="yu" > Yugoslavia </option>
        <option value="zm" > Zambia </option>
        <option value="zw" > Zimbabwe </option>
      </select>
      <br/>
      <br/>
</body>
</html>
