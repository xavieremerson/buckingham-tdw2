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

AW.Formats.Number = AW.System.Format.subclass();

AW.Formats.Number.create = function(){

/****************************************************************

	Number formatting class.

*****************************************************************/

	var obj = this.prototype;


	obj.dataToValue = function(v){
		return Number(("" + v).replace(numPattern, ""));
	};

	obj.textToValue = function(v){
		return Number(("" + v).replace(numPattern, ""));
	};

	var numPattern = /[^0-9.\-+]+/gm;

	var noFormat = function(value){
		return "" + value;
	};

	var doFormat = function(value){
		var multiplier = this._multiplier;
		var abs = (value<0) ? -value : value;
		var delta = (value<0) ? -0.5 : +0.5;
		var rounded = (Math.round(value * multiplier) + delta)/multiplier + "";
		if (abs<1000) {return rounded.replace(this.p1, this.r1)}
		if (abs<1000000) {return rounded.replace(this.p2, this.r2)}
		if (abs<1000000000) {return rounded.replace(this.p3, this.r3)}
		return rounded.replace(this.p4, this.r4);
	};


	obj.setTextFormat = function(format){
		var pattern = /^([^0#]*)([0#]*)([ .,]?)([0#]|[0#]{3})([.,])([0#]*)([^0#]*)$/;
		var f = format.match(pattern);

		if (!f) {
			this.valueToText = function(value){return "" + value};
			this.dataToText = function(value){return "" + value};
			return;
		}

		this.valueToText = doFormat;
		this.dataToText = function(v){return doFormat.call(this, Number(("" + v).replace(numPattern, "")))};

		var rs = f[1]; // result start
		var rg = f[3]; // result group separator;
		var rd = f[5]; // result decimal separator;
		var re = f[7]; // result end

		var decimals = f[6].length;

		this._multiplier = Math.pow(10, decimals);

		var ps = "^(-?\\d+)", pm = "(\\d{3})", pe = "\\.(\\d{" + decimals + "})\\d$";

		this.p1 = new RegExp(ps + pe);
		this.p2 = new RegExp(ps + pm + pe);
		this.p3 = new RegExp(ps + pm + pm + pe);
		this.p4 = new RegExp(ps + pm + pm + pm + pe);

		this.r1 = rs + "$1" + rd + "$2" + re;
		this.r2 = rs + "$1" + rg + "$2" + rd + "$3" + re;
		this.r3 = rs + "$1" + rg + "$2" + rg + "$3" + rd + "$4" + re;
		this.r4 = rs + "$1" + rg + "$2" + rg + "$3" + rg + "$4" + rd + "$5" + re;

	};

	obj.setTextFormat("");
};


