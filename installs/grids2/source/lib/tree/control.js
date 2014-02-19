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

AW.Tree.Control = AW.UI.List.subclass();

AW.Tree.Control.create = function(){

	var obj = this.prototype;

	obj.defineTemplate("group", new AW.Tree.Group);

	obj.setItemTemplate(new AW.Tree.Item);
	obj.setScrollTemplate(function(){return this.getGroupTemplate(0)});

	obj.getContentTemplate().mapTemplate("item", function(i){
		return this.$owner.getGroupTemplate(i);
	});

	obj.defineViewProperty("expanded", false);

	obj.onTreeSignClicked = function(src, i){
		if (this.getViewIndices(i)) {
			this.setViewExpanded(!this.getViewExpanded(i), i);
		}
	};

	obj.onViewExpandedChanged = function(e, i){
		this.getGroupTemplate(i).refresh();
	};
};

AW.UI.Tree = AW.Tree.Control;