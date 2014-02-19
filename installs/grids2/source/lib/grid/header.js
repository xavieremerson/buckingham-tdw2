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

AW.Grid.Header = AW.Templates.ImageText.subclass();

AW.Grid.Header.create = function(){

	var obj = this.prototype;
	var _super = this.superclass.prototype;

	function _direction(){
		return this.getSortProperty("direction") || "none";
	}

	obj.setClass("sort", _direction);

	var sort = new AW.HTML.SPAN;
	sort.setClass("grid", "sort");

	obj.setContent("box/text/sort", sort);

	obj.element = function(){
		if (typeof(this.$1) == "undefined" && this.$owner.$extended){
			return _super.element.call(this.$owner.getHeaderTemplate(this.$0, 0));
		}
		else {
			return _super.element.call(this);
		}
	};
};

