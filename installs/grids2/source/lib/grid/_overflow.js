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

AW.Grid.Controllers.Overflow = {

	onScrollLeftChanged: function(x){
		var e = this.getScrollTemplate().element();
		if (e) {e.firstChild.scrollLeft = x}
	},

	onScrollTopChanged: function(y){
		var e = this.getScrollTemplate().element();
		if (e) {e.firstChild.scrollTop = y}
	},

	onScrollWidthChanged: function(w){
		var e = this.getScrollTemplate().element();
		if (e) {e.firstChild.firstChild.style.width = w + "px"}
	},

	onScrollHeightChanged: function(h){
		var e = this.getScrollTemplate().element();
		if (e) {e.firstChild.firstChild.style.height = h + "px"}
	},

	onScrollBarsChanged: function(x){
		this.getScrollTemplate().refreshClasses();
	},

	adjustScrollBars: function(){

		var e = this.getScrollTemplate().element();
		if (!e) {return}

		var s, x, y;

		var l = this.getScrollLeft();
		var t = this.getScrollTop();
		var w = this.getScrollWidth();
		var h = this.getScrollHeight();

		var ww = e.offsetWidth;
		var hh = e.offsetHeight;

		if (w < ww && h < hh){
			s = "none";
			x = 0;
			y = 0;
		}
		else if (w < ww - 16){
			s = "vertical";
			x = 20;
			y = 0;
		}
		else if (h < hh - 16){
			s = "horizontal";
			x = 0;
			y = 20;
		}
		else {
			s = "both";
			x = 20;
			y = 20;
		}

		if (this.getScrollBars() != s) {
			this.setScrollBars(s);
		}

		if (w - l < ww - x){
			var ll = Math.max(0, w - ww + x);
			if (ll != l) {
				this.setScrollLeft(ll);
			}
		}

		if (h - t < hh - y){
			var tt = Math.max(0, h - hh + y);
			if (tt != t ) {
				this.setScrollTop(tt);
			}
		}

//		var h = this.getContentTemplate().element().offsetHeight;
		this.setContentHeight(hh - y - this.getContentHeight(0) - this.getContentHeight(2), 1);
		this.setContentWidth(ww - x - this.getContentWidth(0) - this.getContentWidth(2), 1);
	},

	onColumnWidthChanged: function(){

			var i, a = this.getColumnIndices();

			var lw = 0, lc = this.$extended ? this.getFixedLeft() : 0;
			var mw = 0, c = this.getColumnCount();
			var rw = 0, rc = this.$extended ? this.getFixedRight() : 0;

			lw = this.getSelectorVisible() ? this.getSelectorWidth() : lw;

			if (!this.$extended){
				mw = lw;
				lw = 0;
			}

			for(i=0; i<lc; i++){
				lw += this.getColumnWidth(a ? a[i] : i);
			}

			for(i=c-rc; i<c; i++){
				rw += this.getColumnWidth(a ? a[i] : i);
			}

			this.setContentWidth(lw, 0);
			this.setContentWidth(rw, 2);
	},

	paint: function(){

		var x = this.getScrollLeft();
		var y = this.getScrollTop();

		this.raiseEvent("adjustScrollWidth");
		this.raiseEvent("adjustScrollHeight");
		if (x) {this.raiseEvent("onScrollLeftChanged", x)}
		if (y) {this.raiseEvent("onScrollTopChanged", y)}
		this.raiseEvent("adjustScrollBars");

		if (x) {this.setTimeout(function(){this.raiseEvent("onScrollLeftChanged", x)})} 
		if (y) {this.setTimeout(function(){this.raiseEvent("onScrollTopChanged", y)})}
	}

};
