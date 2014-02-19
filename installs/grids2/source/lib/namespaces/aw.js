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

if (!window.AW)	{
	var AW = function(a, b){
		return AW.dispatch(a, b);
	};

	AW.toString = function(){
		return "ActiveWidgets 2.0";
	}
}

if (!AW.System)	  	{AW.System		= {}}
if (!AW.HTML)	  	{AW.HTML		= {}}
if (!AW.Templates)	{AW.Templates 	= {}}
if (!AW.Scroll)		{AW.Scroll 		= {}}
if (!AW.Panels)		{AW.Panels 		= {}}
if (!AW.Formats)	{AW.Formats 	= {}}
if (!AW.HTTP)		{AW.HTTP	 	= {}}
if (!AW.CSV)		{AW.CSV			= {}}
if (!AW.XML)		{AW.XML		 	= {}}
if (!AW.UI) 		{AW.UI			= {}}
if (!AW.Grid)	 	{AW.Grid		= {}}
if (!AW.Tree)	 	{AW.Tree		= {}}

if (!AW.Grid.Controllers) {AW.Grid.Controllers = {}}

