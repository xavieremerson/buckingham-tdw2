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

AW.Formats.String = AW.System.Format.subclass();

AW.Formats.String.create = function(){

/****************************************************************

	String data format.

*****************************************************************/

	var obj = this.prototype;

	obj.valueToText = function(data){
		return data ? String(data).replace(AW.textPattern, AW.textReplace) : "";
	};

	obj.textToValue = function(text){
		return text ? String(text).replace(AW.htmlPattern, AW.htmlReplace) : "";
	};

	obj.dataToText = obj.valueToText;
	obj.textToData = obj.textToValue;

	if ("".localeCompare){
		obj.comparator = function(values, greater, less, equal, error){
			return function(i, j){
				try {
					return greater * ("" + values[i]).localeCompare(values[j]) || equal(i, j);
				}
				catch(e){
					return error(i, j, e);
				}
			}
		};
	}

};

