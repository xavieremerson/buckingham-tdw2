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

AW.Formats.Date = AW.System.Format.subclass();

AW.Formats.Date.create = function(){

/****************************************************************

	Date formatting class.

*****************************************************************/

	var obj = this.prototype;

	obj.date = new Date();

	obj.digits = [];
	obj.shortMonths = obj.shortMonths ? obj.shortMonths : ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
	obj.longMonths = obj.longMonths ? obj.longMonths : ["January","February","March","April","May","June","July","August","September","October","November","December"];
	obj.shortWeekdays = obj.shortWeekdays ? obj.shortWeekdays : ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"];
	obj.longWeekdays = obj.longWeekdays ? obj.longWeekdays : ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];

	for(var i=0; i<100; i++){obj.digits[i] = i<10 ? "0" + i : "" + i}

	var tokens = {
		"hh"	: "this.digits[this.date.getUTCHours()]",
		":mm"	: "':'+this.digits[this.date.getUTCMinutes()]",
		"mm:"	: "this.digits[this.date.getUTCMinutes()]+':'",
		"ss"	: "this.digits[this.date.getUTCSeconds()]",
		"dddd"	: "this.longWeekdays[this.date.getUTCDay()]",
		"ddd"	: "this.shortWeekdays[this.date.getUTCDay()]",
		"dd"	: "this.digits[this.date.getUTCDate()]",
		"d"		: "this.date.getUTCDate()",
		"mmmm"	: "this.longMonths[this.date.getUTCMonth()]",
		"mmm"	: "this.shortMonths[this.date.getUTCMonth()]",
		"mm"	: "this.digits[this.date.getUTCMonth()+1]",
		"m"		: "(this.date.getUTCMonth()+1)",
		"yyyy"	: "this.date.getUTCFullYear()",
		"yy"    : "this.digits[this.date.getUTCFullYear()%100]" };

	var match = "";
	for(i in tokens){
		if (typeof(i) == "string"){
			match += "|" + i;
		}
	}
	var re = new RegExp(match.replace("|", "(")+")", "gi");

/****************************************************************

	Allows to specify the format for the text output.

	@param	format	(String) Format pattern.

*****************************************************************/

	obj.setTextFormat = function(format){
		format = format.replace(re, function(i){return "'+" + tokens[i.toLowerCase()] + "+'"});
		format = "if (isNaN(value) || (value === this._valueError)) return this._textError;" +
				 "this.date.setTime(value + this._textTimezoneOffset);" +
				("return '" + format + "'").replace(/(''\+|\+'')/g, "");
		this.valueToText = new Function("value", format);
	};

	var xmlExpr = /^(....).(..).(..).(..).(..).(..)........(...).(..)/;
	var xmlOut = "$1/$2/$3 $4:$5:$6 GMT$7$8";

	var auto = function(data){
		var value = Date.parse(data + this._dataTimezoneCode);
		return isNaN(value) ? this._valueError : value;
	};

	var RFC822 = function(data){
		var value = Date.parse(data);
		return isNaN(value) ? this._valueError : value;
	};

	var ISO8601 = function(data){
		var value = Date.parse(data.replace(xmlExpr, xmlOut));
		return isNaN(value) ? this._valueError : value;
	};

/****************************************************************

	Allows to specify the wire format for data input.

	@param	format	(String) Format pattern.

*****************************************************************/

	obj.setDataFormat = function(format){
		if (format == "RFC822") {
			this.dataToValue = RFC822;
		}
		else if (format == "ISO8601" || format == "ISO8061") {
			this.dataToValue = ISO8601;
		}
		else {
			this.dataToValue = auto;
		}
	};

/****************************************************************

	Allows to specify the timezone used for the text output.

	@param	value	(Number) Timezone offset.

*****************************************************************/

	obj.setTextTimezone = function(value){
		this._textTimezoneOffset = value;
	};

/****************************************************************

	Allows to specify the timezone used for the data input.

	@param	value	(Number) Timezone offset.

*****************************************************************/

	obj.setDataTimezone = function(value){
		if (!value) {
			this._dataTimezoneCode = " GMT";
		}
		else {
			this._dataTimezoneCode = " GMT" +
				(value>0 ? "+" : "-") +
				this.digits[Math.floor(Math.abs(value/3600000))] +
				this.digits[Math.abs(value/60000)%60];
		}
	};

	var localTimezone = - obj.date.getTimezoneOffset() * 60000;

	obj.setTextTimezone(localTimezone);
	obj.setDataTimezone(localTimezone);

	obj.setTextFormat("d mmm yy");
	obj.setDataFormat("default");

	obj.textToValue = RFC822;
};


