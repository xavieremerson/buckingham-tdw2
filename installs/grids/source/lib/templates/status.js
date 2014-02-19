/*****************************************************************

	ActiveWidgets Grid 1.0.1 (GPL).
	Copyright (C) 2003-2005 ActiveWidgets Ltd. All Rights Reserved. 
	http://www.activewidgets.com/

	WARNING: This copy is made available to you under the terms of 
	the GNU General Public License and is not suitable for inclusion
	into commercial or internal applications.
	
	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.

*****************************************************************/

Active.Templates.Status = Active.System.Template.subclass();

Active.Templates.Status.create = function(){

/****************************************************************

	Displays status text.

*****************************************************************/

	var obj = this.prototype;

	obj.setClass("templates", "status");

	var image = new Active.HTML.SPAN;

	image.setClass("box", "image");
	image.setClass("image", function(){return this.getStatusProperty("image")});

	obj.setContent("image", image);
	obj.setContent("text", function(){return this.getStatusProperty("text")});
};

Active.Templates.Status.create();