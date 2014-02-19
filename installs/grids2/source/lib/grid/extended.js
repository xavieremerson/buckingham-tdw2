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

AW.Grid.Extended = AW.Grid.Control.subclass();

AW.Grid.Extended.create = function(){

	var obj = this.prototype;

	obj.$extended = true;

	obj.setController("extended", AW.Grid.Controllers.Extended);

	obj.setContentTemplate(new AW.Panels.Grid);

	obj.defineTemplate("top", new AW.Templates.List);
	obj.defineTemplate("bottom", new AW.Templates.List);


//	---

	var splitColumns = function(p, j){
		var left = this.$owner._fixedLeft, right = this.$owner._fixedRight;
		var i = this.$1;
		switch(p) {
			case "count":
				if (i === 0) {return left}
				if (i == 1) {return this.$owner.getColumnProperty("count") - left - right}
				if (i == 2) {return right}
				return 0;
			case "offset":
				if (i === 0) {return 0}
				if (i == 1) {return left}
				if (i == 2) {return this.$owner.getColumnProperty("count") - right}
				return 0;
			default:
				return this.$owner.getColumnProperty(p, j);
		}
	};

	obj.getHeadersTemplate().mapModel("view", splitColumns);
	obj.getFootersTemplate().mapModel("view", splitColumns);
	obj.getRowTemplate().mapModel("view", splitColumns);


	var scrollController = {

		onScrollLeftChanged: function(x){
			var e1 = this.getRowsTemplate(1).element();
			var e2 = this.getTopTemplate(1).element();
			var e3 = this.getBottomTemplate(1).element();
			if (e1) {e1.parentNode.scrollLeft = x}
			if (e2) {e2.parentNode.scrollLeft = x}
			if (e3) {e3.parentNode.scrollLeft = x}
		},

		onScrollTopChanged: function(y){
			var e1 = this.getRowsTemplate(1).element();
			var e2 = this.getRowsTemplate(0).element();
			var e3 = this.getRowsTemplate(2).element();
			if (e1) {e1.parentNode.scrollTop = y}
			if (e2) {e2.parentNode.scrollTop = y}
			if (e3) {e3.parentNode.scrollTop = y}
		},

		adjustScrollWidth: function(){

			var i, a = this.getColumnIndices();

			var lw = 0, lc = this.getFixedLeft();
			var mw = 0, c = this.getColumnCount();
			var rw = 0, rc = this.getFixedRight();

			lw = this.getSelectorVisible() ? this.getSelectorWidth() : lw;

			for(i=0; i<lc; i++){
				lw += this.getColumnWidth(a ? a[i] : i);
			}

			for(i=lc; i<c-rc; i++){
				mw += this.getColumnWidth(a ? a[i] : i);
			}

			for(i=c-rc; i<c; i++){
				rw += this.getColumnWidth(a ? a[i] : i);
			}

			this.setScrollWidth(lw + mw + rw + 3); 

			lw = lw + "px";
			rw = rw + "px";

			var e1 = this.getRowsTemplate(0).element();
			var e2 = this.getTopTemplate(0).element();
			var e3 = this.getBottomTemplate(0).element();

			if (e1) {e1.parentNode.style.width = lw;}
			if (e2) {e2.parentNode.style.width = lw;}
			if (e3) {e3.parentNode.style.width = lw;}

			if (AW.ie){
				if (e1) {e1.parentNode.parentNode.style.paddingLeft = lw;}
				if (e2) {e2.parentNode.parentNode.style.paddingLeft = lw;}
				if (e3) {e3.parentNode.parentNode.style.paddingLeft = lw;}
			}

			if (!AW.ie) {
				if (e1) {
					e1.parentNode.nextSibling.style.left = lw;
					e1.parentNode.nextSibling.style.right = rw;
				}
				if (e2) {
					e2.parentNode.nextSibling.style.left = lw;
					e2.parentNode.nextSibling.style.right = rw;
				}
				if (e3) {
					e3.parentNode.nextSibling.style.left = lw;
					e3.parentNode.nextSibling.style.right = rw;
				}
			}

			e1 = this.getRowsTemplate(2).element();
			e2 = this.getTopTemplate(2).element();
			e3 = this.getBottomTemplate(2).element();

			if (e1) {e1.parentNode.style.width = rw;}
			if (e2) {e2.parentNode.style.width = rw;}
			if (e3) {e3.parentNode.style.width = rw;}

			if (AW.ie){
				if (e1) {e1.parentNode.parentNode.style.paddingRight = rw;}
				if (e2) {e2.parentNode.parentNode.style.paddingRight = rw;}
				if (e3) {e3.parentNode.parentNode.style.paddingRight = rw;}
			}
		},

		adjustScrollHeight: function(){
			var h = this.getRowCount() * this.getRowHeight();
			h += this.getContentHeight(0);
			h += this.getContentHeight(2);
			this.setScrollHeight(h+3); 
		}
	};

	obj.setController("scroll", scrollController);


};

