/*****************************************************************

	ActiveWidgets 2.0.b3 (beta3)
	Copyright (C) 2003-2005 ActiveWidgets Ltd. All Rights Reserved. 
	http://www.activewidgets.com/

	WARNING: This software program is protected by copyright law 
	and international treaties. Unauthorized reproduction or
	distribution of this program, or any portion of it, may result
	in severe civil and criminal penalties, and will be prosecuted
	to the maximum extent possible under the law.

	FOR TESTING AND EVALUATION ONLY:
	This software is not free and is licensed to you for testing 
	and evaluation only. You are not allowed to distribute 
	or use any parts of this software for any other purposes.

*****************************************************************/

(function(){

	AW.browser = "";

	if (window.external) {AW.browser = "ie"}
	else if (window.__defineGetter__) {AW.browser = "gecko"}
	else if (window.opera){AW.browser = "opera"}
	else if (navigator.userAgent.match("Safari")){AW.browser = "safari"}

	if (AW.browser){AW[AW.browser] = true}

	AW.os = "";

	if (!navigator.userAgent.match("Windows")){AW.unix = true}
	if (navigator.userAgent.match("Mac OS")){AW.os = "mac"}
	if (navigator.userAgent.match("Linux")){AW.os = "linux"}

	AW.strict = (document.compatMode && document.compatMode.match("CSS")) || AW.browser == "safari";

	var htmlc = "";

	if (AW.strict) {htmlc += " aw-strict"}
	if (AW.browser){htmlc += " aw-" + AW.browser}
	if (AW.unix) {htmlc += " aw-unix"}
	if (AW.os) {htmlc += " aw-" + AW.os}

	document.getElementsByTagName("html")[0].className += htmlc;

	if (AW.strict) {
		AW.dx = 8;
		AW.dy = 4;
	}
	else {
		AW.dx = 0;
		AW.dy = 0;
	}

})();
