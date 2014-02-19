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

AW.Grid.Controllers.Scroll = {

	onScrollLeftChanged: function(x){
		var e1 = this.getRowsTemplate().element();
		var e2 = this.getHeadersTemplate().element();
		var e3 = this.getFootersTemplate().element();
		if (e1) {e1.parentNode.scrollLeft = x}
		if (e2) {e2.parentNode.scrollLeft = x}
		if (e3) {e3.parentNode.scrollLeft = x}
	},

	onScrollTopChanged: function(y){
		var e = this.getRowsTemplate().element();
		if (e) {e.parentNode.scrollTop = y}
	},

	adjustScrollWidth: function(){

		var a = this.getColumnIndices();
		var c = this.getColumnCount();
		var w = this.getSelectorVisible() ? this.getSelectorWidth() : 0;

		for(var i=0; i<c; i++){
			w += this.getColumnWidth(a ? a[i] : i);
		}

		this.setScrollWidth(w+3); 
	},

	adjustScrollHeight: function(){
		var h = this.getRowCount() * this.getRowHeight();
		h += this.getContentHeight(0);
		h += this.getContentHeight(2);
		this.setScrollHeight(h+3); 
	}
};
