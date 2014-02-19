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

AW.Tree.View = AW.System.Template.subclass();

AW.Tree.View.create = function(){

	var obj = this.prototype;

	obj.setTag("span");

	obj.setClass("tree", "view");

	obj.setContent("start", function(){
		return this.$0 ? this.getItemTemplate() : "";
	});

	obj.setContent("items", function(){

		if (this.$0 && !this.getViewProperty("expanded")){
			return "";
		}

		var i, ii, a = [];
		var count = this.getViewProperty("count");
		var offset = this.getViewProperty("offset");
		var indices = this.getViewProperty("indices");

		for(i=0; i<count; i++){
			ii = indices ? indices[i+offset] : i+offset;
			a[i] = this.getContentTemplate(ii).toString();
		}

//		var i, index, a = [];
//		var offset = this.getViewProperty("offset");
//		var count = this.getViewProperty("count");

//		for(i=0; i<count; i++){
//			index = this.getViewProperty("index", i + offset);
//			a[i] = this.getViewTemplate(index).toString();
//		}

		return a.join("");
	});

	obj.setContent("end", "");

};

