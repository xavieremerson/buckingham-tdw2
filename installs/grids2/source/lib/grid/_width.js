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

AW.Grid.Controllers.Width = {

	onColumnWidthChanged : function(width, column){
		try{

			if (this.element()){
				this.setTimeout(function(){
					this.raiseEvent("adjustScrollWidth");
					this.raiseEvent("adjustScrollBars");
				});
			}

			

			var w = (width - AW.dx) + "px", c = column;

			var i, ss = document.styleSheets[document.styleSheets.length-1];
			var selector = "#" + this.getId() + " .aw-column-" + c;
			var rules = AW.getRules(ss);

			for(i=0; i<rules.length;i++){
				if(rules[i].selectorText == selector){
					rules[i].style.width = w;
					return;
				}
			}
			AW.addRule(ss, selector, "width:" + w);

		}
		catch(r){
		}
	},

	onSelectorWidthChanged : function(width){
		try{

			if (this.element()){
				this.setTimeout(function(){
					this.raiseEvent("adjustScrollWidth");
					this.raiseEvent("adjustScrollBars");
				});
			}

			var w = (width - AW.dx) + "px";

			var i, ss = document.styleSheets[document.styleSheets.length-1];
			var selector = "#" + this.getId() + " .aw-row-selector";
			var rules = AW.getRules(ss);

			for(i=0; i<rules.length;i++){
				if(rules[i].selectorText == selector){
					rules[i].style.width = w;
					return;
				}
			}
			AW.addRule(ss, selector, "width:" + w);

		}
		catch(r){
		}
	},

	onRowHeightChanged : function(height){
		try{

			if (this.element()){
				this.setTimeout(function(){
					this.raiseEvent("adjustScrollHeight");
					this.raiseEvent("adjustScrollBars");
				});
			}

			var h = (height - AW.dy) + "px";

			var i, ss = document.styleSheets[document.styleSheets.length-1];
			var selector = "#" + this.getId() + " .aw-grid-row";
			var rules = AW.getRules(ss);

			for(i=0; i<rules.length;i++){
				if(rules[i].selectorText == selector){
					rules[i].style.height = h;
					return;
				}
			}
			AW.addRule(ss, selector, "height:" + h);

		}
		catch(r){
		}
	}

 };
