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

AW.Grid.Controllers.Grid = {

	onRowsTemplateChanged : function(rows){
		rows.mapTemplate("item", "row");
		rows.mapModel("view", "row");
	},

	onRowTemplateChanged : function(row){
		row.setAttribute("aw", "row");
		row.setClass("grid", "row");
		row.setClass("row", function(){return this.$0});
		row.setClass("rows", function(){return this.getRowProperty("state") || "normal"});
		row.setClass("alternate", function(){return this.getRowProperty("position") % 2 ? "odd" : "even"});
		row.mapTemplate("item", function(i){return this.$owner.getCellTemplate(i, this.$0)});
		row.mapTemplate("selector", function(){return this.$owner.getSelectorTemplate(this.$0)});
		row.mapModel("view", "column");
	},

	onCellTemplateChanged : function(cell){
		cell.setAttribute("aw", "cell");
		cell.setAttribute("title", "");
		cell.setClass("grid", "cell");
		cell.setClass("column", function(){return this.$0});
		cell.setClass("cells", function(){return this.getControlProperty("state") || "normal"});
		cell.mapModel("control", "cell");

		cell.getStateProperty = function(p){return this.$owner.getRowProperty(p, this.$1)};
		cell.setStateProperty = function(p, v){this.$owner.setRowProperty(p, v, this.$1)};
	},

	onEditorTemplateChanged : function(editor){
		editor.setClass("grid", "cell");
		editor.setClass("column", function(){return this.$0});
		editor.mapModel("control", "cell");
	},

	onHeadersTemplateChanged : function(headers){
		headers.setClass("grid", "headers");
		headers.setClass("header", function(){return this.$0 || "0"});
		headers.setStyle("height", function(){return this.getHeaderProperty("height") - AW.dy + "px"});
		headers.getContent("end").setClass("grid", "header");
		headers.mapTemplate("item", function(i){
			return this.$owner.getHeaderTemplate(i, this.$0) +
				   this.$owner.getSeparatorTemplate(i, this.$0);
		});

		headers.mapTemplate("selector", function(){
			return this.$owner.getTopSelectorTemplate(this.$0) +
				   (this.$owner.getSelectorResizable() && !this.$0 ? this.$owner.getSeparatorTemplate() : "");
		});

		headers.mapModel("view", "column");
	},

	onFootersTemplateChanged : function(footers){
		footers.setClass("grid", "footers");
		footers.setClass("footer", function(){return this.$0 || "0"});
		footers.setStyle("height", function(){return this.getFooterProperty("height") - AW.dy + "px"});
		footers.mapTemplate("item", function(i){return this.$owner.getFooterTemplate(i, this.$0)});
		footers.mapTemplate("selector", "bottomSelector");
		footers.mapModel("view", "column");
	},

	onHeaderTemplateChanged : function(header){
		header.setAttribute("aw", "header");
		header.setClass("grid", "header");
		header.setClass("column", function(){return this.$0});
		header.mapModel("control", "header");

		header.getStateProperty = function(p){return this.$owner.getColumnProperty(p, this.$0)};
		header.setStateProperty = function(p, v){this.$owner.setColumnProperty(p, v, this.$0)};

	},

	onFooterTemplateChanged : function(footer){
		footer.setAttribute("aw", "footer");
		footer.setClass("grid", "footer");
		footer.setClass("column", function(){return this.$0});
		footer.mapModel("control", "footer");
	},

	onSelectorTemplateChanged : function(selector){
		selector.setAttribute("aw", "selector");
		selector.setClass("row", "selector");
		selector.mapModel("control", "selector");
		selector.mapModel("state", "row");
	},

	onTopSelectorTemplateChanged : function(selector){
		selector.setAttribute("aw", "topSelector");
		selector.setClass("grid", "header");
		selector.setClass("row", "selector");
		selector.mapModel("control", "top");
	},

	onBottomSelectorTemplateChanged : function(selector){
		selector.setAttribute("aw", "bottomSelector");
		selector.setClass("row", "selector");
		selector.mapModel("control", "bottom");
	},

	onContentTemplateChanged : function(content){
		content.mapModel("panel", "content");
		content.mapTemplate("panel", function(i){
			switch(i){
				case 0: return this.getHeadersTemplate();
				case 1: return this.getRowsTemplate();
				case 2: return this.getFootersTemplate();
			}
		});
	}
};
