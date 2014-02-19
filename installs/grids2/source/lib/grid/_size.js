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

AW.Grid.Controllers.Size = (function(){

	function init(){

		this._rowHeight = this.getContent("sample").element().offsetHeight;

		if (!this._columnWidth1) {
			this._columnWidth1 = [];
		}

		var i, ii, n = this.getColumnCount(), a = this.getColumnIndices();

		for (i=0; i<n; i++){
			ii = a ? a[i] : i;
			this._columnWidth1[ii] = this.getHeaderTemplate(ii).element().offsetWidth;
		}

	//	---

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

	}

	function footer(){
		var i, h = 0, count = this.$extended ? this.getFooterCount() : 1, a = this.getFooterIndices();
		if (this.getFooterVisible()){
			for (i=0; i<count; i++){
				h += this.getFooterHeight(a ? a[i] : i);
			}
		}
		this.setContentHeight(h, 2);
	}

	function header(){
		var i, h = 0, count = this.$extended ? this.getHeaderCount() : 1, a = this.getHeaderIndices();
		if (this.getHeaderVisible()){
			for (i=0; i<count; i++){
				h += this.getHeaderHeight(a ? a[i] : i);
			}
		}
		this.setContentHeight(h, 0);
	}

	return {

		onHeaderVisibleChanged: header,
		onHeaderHeightChanged: header,
		onHeaderCountChanged: header,

		onFooterVisibleChanged: footer,
		onFooterHeightChanged: footer,
		onFooterCountChanged: footer,

		paint: init };

})();

