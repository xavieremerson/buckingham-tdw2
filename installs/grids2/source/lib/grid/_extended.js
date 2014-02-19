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

AW.Grid.Controllers.Extended = {

	onContentTemplateChanged : function(content){
		content.mapModel("panel", "content");
		content.mapTemplate("panel", function(i, j){
			switch(i){
				case 0: return this.$owner.getTopTemplate(j);
				case 1: return this.$owner.getRowsTemplate(j);
				case 2: return this.$owner.getBottomTemplate(j);
			}
		});
	},

	onTopTemplateChanged : function(top){
		top.mapTemplate("item", "headers");
		top.mapModel("view", "header");
	},

	onBottomTemplateChanged : function(bottom){
		bottom.mapTemplate("item", "footers");
		bottom.mapModel("view", "footer");
	}
};
