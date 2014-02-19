//-------------This page contains code that is Copyright Ferant.com 2003-----------------------
var _912	= '';
var _911 = '';
var _910 = 1;
var _909 = false;
var _908 = false;
var _907 = false;
var _906 = new Object();
var _905	= new Object();
var _904	= new Object();
var _903	= new Object();
var _902	= new Object();

var _901 = (navigator.appName=="Netscape")?'_900':'_899';

	_898	= 0;
	_897	= 0;
	_896 = 0;
	_895 = 0;
	_894 = 0;
	_893 = 0;	

function _892(){	
	
	for(x in _891)
		eval('this.' + _891[x] + ' = this.' + _890[x]);
}
	
function FerantDHTMLWindow(params){

	this._889 = 'FerantDHTMLWindow1'; 
	
	this._888 = 10;
	this._887 = 10;	
	this._886 = 11;
	this._885 = 'verdana,arial';
	this._884 = 'Bold';
	this._883 = 'white';
	this._882 = 'Normal'; 
	this._881 = 10;
	this._880 = 'verdana,arial';;
	this._879 = 'Normal';
	this._878 = 'black';
	this._877 = 'Normal'; 
	this._876 = 10;
	this._875 = 'verdana,arial';
	this._874 = 'Normal';
	this._873 = 'black';
	this._872 = 'Normal'; 
	this._871  = 'White';
	this._870  = 'Transparent';
	this._869  = 3;
	this._868  = 16;
	this._867  = 'img/Closebox.gif';
	this._866  = 16;
	this._865  = 'white';
	this._864  = 'Transparent';
	this._863  = 'Navy';
	this._862  = 0;
	this._861  = '';	
	this._860  = 'Navy';
	this._859  = 0;
	this._858  = 5;
	this._857  = 'Navy';
	this._856  = 0;
	this._855  = 'Navy';
	this._854  = 0;
	this._853  = 'Both';
	this._852  = 200;
	this._851  = 'Gray';
	this._850  = 'solid';
	this._849  = 1;
	this._848  = -1;
	this._847  = 0;
	this._846  = 0;
	this._845  = 0;
	this._844  = 0;
	this._843  = 'Gray';
	this._842  = 'Transparent';
	this._841  = 'Solid';
	this._840  = 1;
	this._839  = 'Both';
	this._838  = 15;
	this._837  = 'img/resize_blue.gif';
	this._836  = 15;
	this._835  = 0;
	this._834  = 20;
	this._833  = '';
	this._832  = 'Transparent';
	this._831  = '';
	this._830  = 5;
	this._829  = 'Center';
	this._828  = 'Silver';
	this._827  = 20;
	this._826  = '';
	this._825  = '';
	this._824  = 'Normal';
	this._823  = 'Center';
	this._822  = 'Navy';
	this._821  = 'Transparent';
	this._820  = -1;
	this._819  = 300;	
	this._892 = _892;	

	this._892();

	for(x in params)
		this[_891[x]] = params[x];	
	
	this._818	= parseInt(this._817 - this._816)/2;
	
	this._815  = true;	
	if(this._814 == 0 || this._813 == 0 || this._814 == '' || this._812 == 0) this._815  = false;

	this._811  = true;	
	if(this._816 == 0 || this._810 == 0 || this._816 == '' || this._817 == 0) this._811  = false;
	
	if(typeof(_906[this._809])=='string'){
 		alert("Error! \n\nTwo Ferant DHTM windows found with the same id: '" + this._809 + "'. Each window must have a unique id.") 
 		return
 	}
 	
 	_906[this._809] = this._809;  	

	this._808 = document.createElement("div");
	this._808.setAttribute("id","_807" + this._809); 
	this._808.style.position = "absolute";  
	this._808.style.display = "none";
	this._808.style.overflow = "hidden";
	this._808.style.borderStyle = this._806;
	this._808.style.borderWidth = this._805 + 'px';
	this._808.style.borderColor = this._804;
	this._808.style.backgroundColor = this._803;

	this._802 = document.createElement("span");
	this._802.setAttribute("id","_801f_Title_"); 
	this._802.style.position = "absolute";  
	this._802.style.left = this._800 +  "px"; 
	this._802.style.top = this._800 + "px"; 
	this._802.style.display = "none"; 
	this._802.style.backgroundColor=this._799;
	this._802.style.cursor='default';
	this._802.style.borderColor = this._798;
	this._802.style.borderStyle='solid';
	this._802.style.borderWidth=this._797  + 'px';
	this._802.style.overflow="hidden";
	
	this._802.style.fontSize = this._796  + 'px';
	this._802.style.fontFamily = this._795;
	this._802.style.fontWeight = this._794;
	this._802.style.color = this._793;
	this._802.style.fontStyle = this._792; 
	if(_901=='_899')
		this._802.style.height = this._817   + 2 * this._797  + 'px';
	else
		this._802.style.height = this._817  + 'px';
	this._802.innerHTML = this._791.replace(/</g,'&lt;').replace(/>/g,'&gt;');
	this._802.style.textAlign  = this._790;
	this._802.style.whiteSpace = 'nowrap';
	this._802.style.paddingTop = Math.max(0,parseInt((this._817 - this._796 )/2) - 1) + 'px';


	this._789 = document.createElement("span");
	this._789.style.position = "absolute";  
	this._789.style.left = "0px";
	this._789.style.display = "none";  
	this._789.style.top = "0px";
	this._789.style.height = this._816 + "px";
	this._789.style.width = this._810 + "px";
	this._789.style.cursor = "hand";
	this._789.style.overflow = "hidden";
	this._789.innerHTML = "<img src='" + this._788 + "' height = " + 
							this._816 + " width = " + this._810 + " id='_787'>";

	this._786 = document.createElement("div");
	this._786.setAttribute("id","_785" + this._809); 
	this._786.style.backgroundColor = this._784; 
	this._786.style.position = "absolute"; 
	this._786.style.display = "none"; 
	this._786.innerHTML = this._783;
	this._786.style.left = this._800  + 'px';
	this._786.style.top = parseInt(this._800) + parseInt(this._817) + parseInt(this._797)  + 'px';
	this._786.style.borderColor = this._798; 	
	this._786.style.borderStyle = this._782;
	this._786.style.borderWidth = this._797  + 'px';
	this._786.style.whiteSpace = this._781; 
	this._786.style.overflow='auto';
	this._786.style.padding = this._780 + 'px ' + this._780 + 'px ' +
										this._780 + 'px ' + this._780 + 'px';
										
	if(this._779 != 0){ 
		this._786.style.borderTopColor = this._778;
		this._786.style.borderTopWidth = this._779  + 'px';
	}
		
	if(this._777 != 0){
		this._786.style.borderRightColor = this._776;
		this._786.style.borderRightWidth = this._777  + 'px';
	}
		
	if(this._775 != 0){
		this._786.style.borderBottomColor = this._774;
		this._786.style.borderBottomWidth = this._775  + 'px';
	}

		
	if(this._773 != 0){
		this._786.style.borderLeftColor = this._772;
		this._786.style.borderLeftWidth = this._773  + 'px';
	}
										
	this._786.style.fontSize = this._771  + 'px';
	this._786.style.fontFamily = this._770;
	this._786.style.fontWeight = this._769;
	this._786.style.color = this._768;
	this._786.style.fontStyle = this._767; 

	this._766 = document.createElement("div"); 
	this._766.style.display = "none"; 
	this._766.style.position = "absolute"; 
	this._766.style.overflow = "hidden"; 
	this._766.style.left=this._800  + 'px';
	this._766.style.backgroundColor=this._765; 
	if (this._812 >0) this._766.style.paddingTop = Math.max(0,parseInt((this._812 - this._764 )/2) - 1)  + 'px';

	this._766.style.borderColor = this._798; 	
	this._766.style.borderStyle='solid';
	this._766.style.borderWidth=this._797  + 'px';
	
	this._766.style.fontSize = this._764 + 'px';
	this._766.style.fontFamily = this._763;
	this._766.style.fontWeight = this._762;
	this._766.style.color = this._761;
	this._766.style.fontStyle = this._760; 
	this._766.innerHTML = this._759.replace(/</g,'&lt;').replace(/>/g,'&gt;');
	this._766.style.textAlign  = this._758;
	this._766.style.whiteSpace = 'nowrap';
	
	if(this._757 != '') 
		this._802.innerHTML = this._757; 
	else 
		this._802.innerHTML = this._791.replace(/</g,'&lt;').replace(/>/g,'&gt;');
		
	if(this._756 != '') 
		this._766.innerHTML = this._756; 
	else 
		this._766.innerHTML = this._759.replace(/</g,'&lt;').replace(/>/g,'&gt;');	

	this._755 = document.createElement("span");
	this._755.style.position = "absolute";  
	this._755.style.left = "0px"; 
	this._755.style.height = this._814 + "px";
	this._755.style.width = this._813 + "px";
	this._755.style.cursor = "nw-resize";
	this._755.style.display = "none";
	this._755.innerHTML = "<img src='" + this._754 + "' height = " + this._814 + 
									" width=" + this._813 + " id='_753'>";

	this._752 = document.createElement("div"); 
	this._752.style.position = "absolute";  
	this._752.style.display = "none";
	this._752.style.overflow = "hidden";
	this._752.style.backgroundColor='gray';
	if(_901=='_899')
		this._752.style.filter="alpha(opacity=50)";
	else	
		this._752.style.MozOpacity=.5;

	this._751 = document.createElement("div");
	this._751.style.backgroundColor = "transparent";  
	this._751.style.position = "absolute"; 
	this._751.style.cursor = "nw-resize"; 
	this._751.style.width = "11px"; 
	this._751.style.height = "11px"; 
	this._751.style.overflow = "hidden";  
	this._751.innerHTML = "&nbsp;";
	this._751.style.display = "none";

	this._808.appendChild(this._802);
	this._808.appendChild(this._786);
	document.body.appendChild(this._755);
	this._808.appendChild(this._766);

	document.body.appendChild(this._752);
	document.body.appendChild(this._751);
	document.body.appendChild(this._808);
	document.body.appendChild(this._789);

	this._750 = _750;
	this._749 = _749;
	this._748 = _748;
	this._747 = _747;
	this._746 = _746;
	this._745 = _745;
	this._744 = _744;
	
	this.UpdateTitleBarText = UpdateTitleBarText;
	this.UpdateTitleBarHTML = UpdateTitleBarHTML;
	this.UpdateContentHTML = UpdateContentHTML;
	this.UpdateStatusBarText = UpdateStatusBarText;
	this.UpdateStatusBarHTML = UpdateStatusBarHTML;
	this.OpenWindow = OpenWindow; 
	this.CloseWindow = CloseWindow;
	
	this._743 = false;
}

var _891 = new Object();

	_891.Id = '_809';
	_891.StatusLeftMargin = '_742';
	_891.TitleLeftMargin = '_741';
	_891.BorderColor  = '_803';
	_891.BorderInactiveColor  = '_740' ;
	_891.BorderWidth  = '_800';
	_891.CloseBoxHeight  = '_816' ;
	_891.CloseBoxSrc  = '_788' ;
	_891.CloseBoxWidth  = '_810';
	_891.ContentColor  = '_784';
	_891.ContentInactiveColor  = '_739' ;
	_891.ContentBottomBorderColor  = '_774' ;
	_891.ContentBottomBorderWidth  = '_775' ;
	_891.ContentHTML  =  '_783';
	_891.ContentLeftBorderColor  = '_772';
	_891.ContentLeftBorderWidth  = '_773' ;
	_891.ContentPadding  = '_780';
	_891.ContentRightBorderColor  = '_776' ;
	_891.ContentRightBorderWidth  = '_777' ;
	_891.ContentTopBorderColor  = '_778' ;
	_891.ContentTopBorderWidth  = '_779' ;
	_891.Dragable  = '_738';
	_891.Height  = '_737' ;
	_891.InnerBorderColor  = '_798' ;
	_891.InnerBorderStyle  = '_782' ;
	_891.InnerBorderWidth  = '_797' ;
	_891.Left  = '_736' ;
	_891.MaxHeight  = '_735' ;
	_891.MaxWidth  = '_734' ;
	_891.MinHeight  = '_733' ;
	_891.MinWidth  = '_732' ;
	_891.OuterBorderColor  = '_804' ;
	_891.OuterBorderInactiveColor  = '_731' ;
	_891.OuterBorderStyle  = '_806' ;
	_891.OuterBorderWidth  = '_805' ;
	_891.Resizable  = '_730' ;
	_891.ResizeBoxHeight  = '_814' ;
	_891.ResizeBoxSrc = '_754' ;
	_891.ResizeBoxWidth  = '_813' ;
	_891.Shadow  = '_729' ;
	_891.StatusBarHeight  = '_812' ;
	_891.StatusBarHTML  = '_756' ;
	_891.StatusBarInactiveColor  = '_728' ;
	_891.StatusBarText  = '_759' ;
	_891.StatusBarTextMargin  = '_727' ;
	_891.StatusBarAlign  = '_758' ;
	_891.StatusColor  = '_765' ;
	_891.TitleBarHeight  = '_817' ;
	_891.TitleBarHTML  = '_757' ;
	_891.TitleBarText  = '_791' ;
	_891.ContentNoWrap  = '_781' ;
	_891.TitleBarAlign  = '_790' ;
	_891.TitleColor  = '_799' ;
	_891.TitleBarInactiveColor  = '_726' ;
	_891.Top  = '_725' ;
	_891.Width  = '_724' ;	
	_891.TitleFontSize  = '_796' ;
	_891.TitleFontFamily  = '_795' ;
	_891.TitleFontWeight  = '_794' ;
	_891.TitleFontColor  = '_793' ;
	_891.TitleFontStyle   = '_792' ;
	_891.ContentFontSize  = '_771' ;
	_891.ContentFontFamily  = '_770' ;
	_891.ContentFontWeight  = '_769' ;
	_891.ContentFontColor  = '_768' ;
	_891.ContentFontStyle   = '_767' ;
	_891.StatusFontSize  = '_764' ;
	_891.StatusFontFamily  = '_763' ;
	_891.StatusFontWeight  = '_762' ;
	_891.StatusFontColor  = '_761' ;
	_891.StatusFontStyle   = '_760' ;
	
	var _890 = new Object();

	_890.Id = '_889';
	_890.StatusLeftMargin = '_888';
	_890.TitleLeftMargin = '_887';
	_890.BorderColor  = '_871';
	_890.BorderInactiveColor  = '_870' ;
	_890.BorderWidth  = '_869';
	_890.CloseBoxHeight  = '_868' ;
	_890.CloseBoxSrc  = '_867' ;
	_890.CloseBoxWidth  = '_866';
	_890.ContentColor  = '_865';
	_890.ContentInactiveColor  = '_864' ;
	_890.ContentBottomBorderColor  = '_863' ;
	_890.ContentBottomBorderWidth  = '_862' ;
	_890.ContentHTML  =  '_861';
	_890.ContentLeftBorderColor  = '_860';
	_890.ContentLeftBorderWidth  = '_859' ;
	_890.ContentPadding  = '_858';
	_890.ContentRightBorderColor  = '_857' ;
	_890.ContentRightBorderWidth  = '_856' ;
	_890.ContentTopBorderColor  = '_855' ;
	_890.ContentTopBorderWidth  = '_854' ;
	_890.Dragable  = '_853';
	_890.Height  = '_852' ;
	_890.InnerBorderColor  = '_851' ;
	_890.InnerBorderStyle  = '_850' ;
	_890.InnerBorderWidth  = '_849' ;
	_890.Left  = '_848' ;
	_890.MaxHeight  = '_847' ;
	_890.MaxWidth  = '_846' ;
	_890.MinHeight  = '_845' ;
	_890.MinWidth  = '_844' ;
	_890.OuterBorderColor  = '_843' ;
	_890.OuterBorderInactiveColor  = '_842' ;
	_890.OuterBorderStyle  = '_841' ;
	_890.OuterBorderWidth  = '_840' ;
	_890.Resizable  = '_839' ;
	_890.ResizeBoxHeight  = '_838' ;
	_890.ResizeBoxSrc = '_837' ;
	_890.ResizeBoxWidth  = '_836' ;
	_890.Shadow  = '_835' ;
	_890.StatusBarHeight  = '_834' ;
	_890.StatusBarHTML  = '_833' ;
	_890.StatusBarInactiveColor  = '_832' ;
	_890.StatusBarText  = '_831' ;
	_890.StatusBarTextMargin  = '_830' ;
	_890.StatusBarAlign  = '_829' ;
	_890.StatusColor  = '_828' ;
	_890.TitleBarHeight  = '_827' ;
	_890.TitleBarHTML  = '_826' ;
	_890.TitleBarText  = '_825' ;
	_890.ContentNoWrap  = '_824' ;
	_890.TitleBarAlign  = '_823' ;
	_890.TitleColor  = '_822' ;
	_890.TitleBarInactiveColor  = '_821' ;
	_890.Top  = '_820' ;
	_890.Width  = '_819' ;	
	_890.TitleFontSize  = '_886' ;
	_890.TitleFontFamily  = '_885' ;
	_890.TitleFontWeight  = '_884' ;
	_890.TitleFontColor  = '_883' ;
	_890.TitleFontStyle   = '_882' ;
	_890.ContentFontSize  = '_881' ;
	_890.ContentFontFamily  = '_880' ;
	_890.ContentFontWeight  = '_879' ;
	_890.ContentFontColor  = '_878' ;
	_890.ContentFontStyle   = '_877' ;
	_890.StatusFontSize  = '_876' ;
	_890.StatusFontFamily  = '_875' ;
	_890.StatusFontWeight  = '_874' ;
	_890.StatusFontColor  = '_873' ;
	_890.StatusFontStyle   = '_872' ;
	
	var _723 = new Object();

	_723.StatusLeftMargin = false;
	_723.TitleLeftMargin = false;
	_723.BorderColor  = true;
	_723.BorderInactiveColor  = true ;
	_723.BorderWidth  = false;
	_723.CloseBoxHeight  = false ;
	_723.CloseBoxSrc  = true ;
	_723.CloseBoxWidth  = false;
	_723.ContentColor  = true;
	_723.ContentInactiveColor  = true ;
	_723.ContentBottomBorderColor  = true ;
	_723.ContentBottomBorderWidth  = false ;
	_723.ContentHTML  =  true;
	_723.ContentLeftBorderColor  = true;
	_723.ContentLeftBorderWidth  = false ;
	_723.ContentPadding  = false;
	_723.ContentRightBorderColor  = true ;
	_723.ContentRightBorderWidth  = false ;
	_723.ContentTopBorderColor  = true ;
	_723.ContentTopBorderWidth  = false ;
	_723.Dragable  = true;
	_723.Height  = false ;
	_723.InnerBorderColor  = true ;
	_723.InnerBorderStyle  = true ;
	_723.InnerBorderWidth  = false ;
	_723.Left  = false ;
	_723.MaxHeight  = false ;
	_723.MaxWidth  = false ;
	_723.MinHeight  = false ;
	_723.MinWidth  = false ;
	_723.OuterBorderColor  = true ;
	_723.OuterBorderInactiveColor  = true ;
	_723.OuterBorderStyle  = true ;
	_723.OuterBorderWidth  = false ;
	_723.Resizable  = true ;
	_723.ResizeBoxHeight  = false ;
	_723.ResizeBoxSrc  = true ;
	_723.ResizeBoxWidth  = false ;
	_723.Shadow  = false ;
	_723.StatusBarHeight  = false ;
	_723.StatusBarHTML  = true ;
	_723.StatusBarInactiveColor  = true ;
	_723.StatusBarText  = true ;
	_723.StatusBarTextMargin  = false ;
	_723.StatusBarAlign  = true ;
	_723.StatusColor  = true ;
	_723.TitleBarHeight  = false ;
	_723.TitleBarHTML  = true ;
	_723.TitleBarText  = true ;
	_723.ContentNoWrap  = true ;
	_723.TitleBarAlign  = true ;
	_723.TitleColor  = true ;
	_723.TitleBarInactiveColor  = true ;
	_723.Top  = false ;
	_723.Width  = false ;	
	_723.TitleFontSize  = false ;
	_723.TitleFontFamily  = true ;
	_723.TitleFontWeight  = true ;
	_723.TitleFontColor  = true ;
	_723.TitleFontStyle   = true ;
	_723.ContentFontSize  = false ;
	_723.ContentFontFamily  = true ;
	_723.ContentFontWeight  = true ;
	_723.ContentFontColor  = true ;
	_723.ContentFontStyle   = true ;
	_723.StatusFontSize  = false ;
	_723.StatusFontFamily  = true ;
	_723.StatusFontWeight  = true ;
	_723.StatusFontColor  = true ;
	_723.StatusFontStyle   = true ;	

function OpenWindow(){ 

	_909 = false;
	_908 = false;

	if ( this._809 != _912){
		if(_912 != '') {
			_911 = _912; 
			eval(_911 + '._744();')
		}
		_912 = this._809;
	}

	if(!this._743) this._748();
	
	if(this._814 == 0 || this._813 == 0 || this._754 == '' || 
									this._812 == 0 || this._730 == 'None') 
		this._815  = false;
	else
		this._815  = true;
				
  if(this._816 == 0 || this._810 == 0 || this._788 == '' || this._817 == 0) 
		this._811  = false;
  else
		this._811  = true;		

	var x=0; 
	var y=0; 
	var h=this._737; 
	var w=this._724;	
	
	if( this._725 == -1){	
		if(_901=='_899')
			y = document.body.offsetHeight/2 - this._737/2 + document.body.scrollTop;
		else
			y = window.innerHeight/2 - this._737/2 + window.pageYOffset;
	}else{	
		if(_901=='_899')
			y = this._725 + document.body.scrollTop;
		else
			y = this._725 + window.pageYOffset;;
	}	
	
	if( this._736 == -1){
		if(_901=='_899')
			x = document.body.offsetWidth/2 - this._724/2 + document.body.scrollLeft;
		else
			x = window.innerWidth/2 - this._724/2 + window.pageXOffset;
	}else{	
		if(_901=='_899')
			x = this._736 + document.body.scrollLeft;
		else
			x = this._736 + window.pageXOffset;
	}	
	
  	this._786.style.display = 'block'; 
  	this._802.style.display = 'block';
  	if(this._811){	
			this._789.style.display='block';
	} else { 
		this._789.style.display='none';
	}
	
	if (this._730  != 'none' && this._815)	
		this._755.style.display='block';
	else
		this._755.style.display='none';
	
	this._751.style.display = (this._730.toLowerCase() == 'none' || this._815 )? "none" : "block";
		
	if(this._812 > 0) this._766.style.display = 'block';	

	this._808.style.display = 'block'; 	 

 	if(this._729) 
 		this._752.style.display='block';
 	else
 		this._752.style.display='none'; 	
 	
 	this._752.style.zIndex=++_910;
	this._808.style.zIndex=++_910;
	this._755.style.zIndex=++_910;
	this._751.style.zIndex=++_910;
	this._789.style.zIndex=++_910;
	this._755.style.zIndex=++_910;		
	
	var paddingTitleTop = Math.max(0,parseInt((this._817 - this._796 - 1)/2));		
	if(this._757 !=''){
		this._802.style.padding = '0px 0px 0px 0px';
		paddingTitleTop = 0;
	}else	
		this._802.style.padding = paddingTitleTop + 'px 0px 0px ' + this._741 + 'px';
	
	if(_901=='_899')	
		this._802.style.height=this._817 + 2*this._797;
	else
		this._802.style.height=this._817 - paddingTitleTop;

	if (this._812 >0){
		var paddingStatusTop = Math.max(0,parseInt((this._812 - this._764 - 1)/2));
	
		if(this._756 !=''){
			this._766.style.padding = '0px 0px 0px 0px';
			paddingStatusTop = 0;
		}else
			this._766.style.padding = paddingStatusTop + 'px 0px 0px ' + this._742 + 'px';
		
		if(_901=='_899')	
			this._766.style.height=this._812 + 2*this._797;
		else
			this._766.style.height=this._812 - paddingStatusTop;
	}		
	
	if(!_907){		 
		_905[this._809] = x;
		_904[this._809] = y;
		_903[this._809] = h;
		_902[this._809] = w;		
			
		this._750(w,h,true);
		this._749(x,y,true);
	}
	else{
	
		this._750(_902[this._809],_903[this._809],true);
		this._749(_905[this._809] ,_904[this._809],true);		
	}		
		this._745();	
		
		_156(this._809);
	
} 

function _748(){ 
	this._743 = true;	
 	
 	_722 = this._809;
  	
	this._802.onmousedown = new Function("_721", "if(_912 != '" + _722 + "' && _911 != '') {" +
	"_911 = _912; _912 = '" + _722 + "'; eval(_912 + '._745();'); eval(_911 + '._744();')} else" +
	"{ _912 = '" + _722 + "';} _720(_721); return false;");

		
	if(this._811) this._789.onmousedown = new Function("if(_912 != '" + _722 + "' && _912 != '') " +
		"_911 = _912; _912 = '" + _722 + "'; " +
		"  if(_911!='') eval(_911 + '._745();'); " +
		"eval('" + _722 + "' + '.CloseWindow();'); if(_911 != '') _912 = _911; _911 = '';");

	this._808.onmousedown = new Function(" if(_912 != '" + _722 + "' && _911 != '') {" +
		"_911 = _912; _912 = '" + _722 + "'; eval(_912 + '._745();'); eval(_911 + '._744();')} else" +
		"{ _912 = '" + _722 + "';} _719() ");
	
	if (this._730  != 'none')	this._755.onmousedown = new Function("_721","if(_912 != '" + _722 + 
		"' && _911 != '') {" + "_911 = _912; _912 = '" + _722 + 
		"'; eval(_912 + '._745();'); eval(_911 + '._744();')} else" +
		"{ _912 = '" + _722 + "';} _718(_721); return false;");
		
	if (this._730  != 'none')	this._751.onmousedown = new Function("_721","if(_912 != '" + 
		_722 + "' && _911 != '') {" + "_911 = _912; _912 = '" + _722 + 
		"'; eval(_912 + '._745();');_719(); eval(_911 + '._744();')} else" +
		"{ _912 = '" + _722 + "';} _718(_721); return false;");
		
 }
 
document.onmousemove = _717;
document.onmouseup = new Function("if (_909 || _908) eval(_912 + '._746();');  _908 = false; _909 = false;");
			
function _717(_721){ if(_908 == false && _909 == false) return true; 
	if(_912!='')
	{
		if(_901=='_899'){
			if(event.button !=1 )
			{
				_908 = false; 
				_909 = false; 
				return true;
			}
		}
		
		_716 = (_901=='_899')?event.clientX + document.body.scrollLeft:_721.pageX;
		_715= (_901=='_899')?event.clientY + document.body.scrollTop:_721.pageY;	
		window.setTimeout("_714()",10);

		if(_908 || _909 )
		{
			if(_901=='_899') event.returnValue = false; 
			return false;
		} 
	}
	
	return false;
}

function UpdateTitleBarText(TitleBarText){
	this._802.innerHTML = TitleBarText.replace(/</g,'&lt;').replace(/>/g,'&gt;');	
}
 
 function UpdateTitleBarHTML(TitleHTML){
	this._802.innerHTML = TitleHTML;	
 }
 
 function UpdateContentHTML(ContentHTML){
	this._786.innerHTML = ContentHTML;	
}
 
 function UpdateStatusBarText(StatusBarText){
	this._766.innerHTML = StatusBarText.replace(/</g,'&lt;').replace(/>/g,'&gt;');	
}
 
 function UpdateStatusBarHTML(StatusBarHTML){
	this._766.innerHTML = StatusBarHTML;
}
 
 
 function _747(){ 	
	if(!this._729)  return;
	
	this._808.style.left = parseInt(this._808.style.left) + 2  + 'px';
	this._808.style.top = parseInt(this._808.style.top) + 2 + 'px';
	this._752.style.left = parseInt(this._752.style.left) + 2 +'px';
	this._752.style.top =  parseInt(this._752.style.top) + 2 + 'px';
	this._752.style.display='none';
	
	if(this._815 ) this._755.style.left = parseInt(this._755.style.left) + 2  + 'px';
	this._789.style.left = parseInt(this._789.style.left) + 2  + 'px';
	if(this._815 ) this._755.style.top = parseInt(this._755.style.top) + 2  + 'px';
	this._789.style.top = parseInt(this._789.style.top) + 2  + 'px';
	
	_905[this._809] += 2;
	_904[this._809] += 2;
	
 }
 	
 function _746(){ 
 
    _908 = false;
    _909 = false;
    
     
	if(!this._729)  return;
	if(this._808.style.display !='block')  return;
	
	this._808.style.left = parseInt(this._808.style.left) - 2  + 'px';
	this._808.style.top = parseInt(this._808.style.top) - 2 + 'px';
	this._752.style.left = parseInt(this._752.style.left) - 2 +'px';
	this._752.style.top = parseInt(this._752.style.top) - 2 + 'px';
	this._752.style.display='block';
	
	if(this._815 ) this._755.style.left = parseInt(this._755.style.left) - 2  + 'px';
	this._789.style.left = parseInt(this._789.style.left) - 2  + 'px';
	if(this._815 ) this._755.style.top = parseInt(this._755.style.top) - 2  + 'px';
	this._789.style.top = parseInt(this._789.style.top) - 2  + 'px';
	
	_905[this._809] -= 2;
	_904[this._809] -= 2;
} 	
 	
function _745(){ 		
	this._802.style.backgroundColor = this._799;
	this._808.style.borderColor= this._804;
	this._808.style.backgroundColor = this._803;

	this._766.style.backgroundColor = this._765; 
	this._786.style.backgroundColor = this._784;
 } 
 
 function _744(){ 	
 
 	if(this._726 != 'Transparent')
		this._802.style.backgroundColor = this._726;		
 
	if(this._731 != 'Transparent') 
		this._808.style.borderColor = this._731;
		
	if(this._800 > 0 && this._740 !='Transparent') 
		this._808.style.backgroundColor = this._740;		

	
		
	if(this._728 != 'Transparent')	
		this._766.style.backgroundColor = this._728;
			
		
	if(this._739 != 'Transparent')
		 this._786.style.backgroundColor = this._739;
			
	_156(this._809);					
 } 
 
 function CloseWindow() { 	
	this._808.style.display='none';
  	this._786.style.display='none';
  	this._802.style.display='none';
    this._755.style.display='none';
    this._751.style.display='none';
	this._752.style.display='none';
	this._766.style.display='none';
	this._789.style.display='none';
 	
  	_156(this._809);
 } 

 function _749(x,y,_713){ 
	x=parseInt(x);
	y=parseInt(y); 
 	
 	var _712 = _713;
 	if(this._738 == 'VerticalOnly' || this._738 == 'Both') _712 = true;
 	
 	var _711 = _713;
 	if(this._738 == 'HorizontalOnly' || this._738 == 'Both') _711 = true; 	
 	
	if(_711){ this._808.style.left	= x + 'px'; _905[this._809] = x;}
	if(_712){ this._808.style.top	= y + 'px'; _904[this._809] = y;}

	if(typeof(x)!='number') x = parseInt(x);
	if(typeof(y)!='number')  y = parseInt(y);	

	if(this._729  && _711) this._752.style.left	= (x + 3) +'px';
	if(this._729  && _712) this._752.style.top	= (y + 3) + 'px';

	if(_711) this._751.style.left	= (x - 9 + parseInt(this._808.style.width)) + 'px';
	if(_712) this._751.style.top	= (y - 9 + parseInt(this._808.style.height)) + 'px';

	if(_711 && this._811)	this._789.style.left = x + _902[this._809]-2*this._805 -2*this._800 -2*this._797  - this._810 - 
				this._818 + this._800 + this._805 +this._797;
	if(_712 && this._811)	this._789.style.top = y + this._818 + this._800 + this._805 +this._797;
	
	if(_711 && this._815) this._755.style.left = _905[this._809] + _902[this._809] - 
			this._805 - this._800 - this._797 - this._814;
	
	if(_712 && this._815) this._755.style.top = _904[this._809] + _903[this._809] - 
		this._805 - this._800 - this._797 - this._813;		
		
	window.setTimeout('_156("' + this._809 +'")',50);	
 }
 	
function _750(w,h,_713){	
	w = Math.max(w , 2*this._805 + 2*this._800 + 2*this._797 + this._818 + this._810);
	
	h = Math.max(h ,this._812 + 2*this._805 +2*this._800 + 3*this._797 + parseInt(this._817));

	if(this._735 > 0) h = Math.min(h , this._735);
	if(this._733 > 0) h = Math.max(h , this._733);
	if(this._734 > 0) w = Math.min(w , this._734);
	if(this._732 > 0) w = Math.max(w , this._732);
 	_710 = _713;
	if(this._730  == 'Both' || this._730  == 'VerticalOnly')	_710 = true;
 	var _709 = _713;
	if(this._730  == 'Both' || this._730  == 'HorizontalOnly')	_709 = true;	
	
	if(_901=='_899'){
		if(_709)	this._808.style.width=w;
		if(_710)	this._808.style.height=h;
	}else{	
		if(_709) this._808.style.width=w-2*this._805; 
		if(_710) this._808.style.height=h-2*this._805;
	}     

	if(_709) _902[this._809] = w;
	if(_710) _903[this._809] = h;
	  
	if(_709 && this._811)	this._789.style.left = _905[this._809] + w-2*this._805 -2*this._800 -
				2*this._797  - this._810 - 
				this._818 + this._800 + this._805 +this._797;
	if(_710 && this._811)	this._789.style.top= _904[this._809] + this._818 + 
					this._800 + this._805 +this._797;
												
	if(_709 && _901=='_899')	this._802.style.width=w -2*this._805-2*this._800;
	if(_709 && _901!='_899')	this._802.style.width=w -2*this._805 
											-2*this._800 -2*this._797 -
											parseInt((this._757 == '')?this._741:0);

	if(this._729 && _709)	this._752.style.width = w +'px';
	if(this._729 && _710)	this._752.style.height = h +'px';
													
	if(_901=='_899'){
		if(_709)	this._786.style.width = w -2*this._805 - 2*this._800;
	}else{
		if(_709)	this._786.style.width = w - this._777  - this._773 -
				2*this._780  -2*this._805 -2*this._800 -2*this._797;
	}
	
	if(_710){
		if(this._812 > 0){
			if(_901=='_899')
				this._786.style.height = h - this._812 - this._817 - 2*this._800 -
					2*this._805 - 2*this._797;
			else
				this._786.style.height = h - this._779 - this._775 - 
					this._812 - 2*this._780 - this._817 - 2*this._800 - 
					2*this._805 - 4*this._797;
		}else{
 			if(_901=='_899')
 				this._786.style.height = h - this._812 - this._817 - 2*this._800 -2 *this._805 - this._797;
 			else
 				this._786.style.height = h - this._779 - this._775 - 
 					this._812 - 2*this._780 - this._817 - 2*this._800 - 
 					2*this._805 - 3*this._797;
		}
	}
	
	if(_709 && this._812 > 0 && _901=='_899')	 this._766.style.width = w - 2*this._805 - 2*this._800 ;
	if(_709 && this._812 > 0 && _901!='_899')	 this._766.style.width = w - 
		2*this._805 - 2*this._800 - 2*this._797 - 
			parseInt((this._756 =='')?this._742:0);
																					
	if(_710 && this._812 > 0)	this._766.style.top	= h  - 2*this._805 - this._800 - 2*this._797 - this._812 ;

	if(_709 && this._815) this._755.style.left = _905[this._809] + _902[this._809] - 
			this._805 - this._800 - this._797 - this._814;
	
	if(_710 && this._815) this._755.style.top = _904[this._809] + _903[this._809] - 
		this._805 - this._800 - this._797 - this._813;		
																			  
	if(_709 && !_713 && !this._815 && this._730  != 'none') this._751.style.left	= (parseInt(this._808.style.left) - 9 + w) +'px';
	if(_710 && !_713 && !this._815 && this._730  != 'none') this._751.style.top	= (parseInt(this._808.style.top)  - 9 + h) + 'px';
 
	window.setTimeout('_156("' + this._809 +'")',50);
 }
  
 function _714(){   

 	if(_909){		
 		var x= _716 + _896;
 		var y = _715+ _895;			
 		eval(_912 + "._749('" + x + "','" + y + "',false);");		
 	}	
 		
 	if(_908){	 			
 		var _708=_716 + _894;
 		var _707=_715 + _893;
 		eval(_912 + "._750('" + _708 + "','" + _707 + "',false);");
 	}
 	     
	 _156();
 	 
 	 if(_908 || _909  ) return false;
 }
 
 function _718(_721){	

 	if(_901=='_899'){
 		_894 = parseInt(eval(_912 + "._808.style.width")) - event.clientX - document.body.scrollLeft;
 		_893 = parseInt(eval(_912 + "._808.style.height")) - event.clientY - document.body.scrollTop; 
	}else{ 	
 		_894 = parseInt(eval(_912 + "._808.style.width")) - _721.pageX;
 		_893 = parseInt(eval(_912 + "._808.style.height")) - _721.pageY; 
 	}
 	if(eval(_912 + "._729")  && !_908) eval(_912 + "._747();");

	_719();

 	_908 = true; 	
 	

 }
 
 function _720(_721){  
	
	if(eval(_912 + "._729")  && !_909) eval(_912 + "._747();"); 
	
	if(_901=='_899'){	
		_896 = parseInt(eval(_912 + "._808.style.left;")) - event.clientX - document.body.scrollLeft;
		_895 = parseInt(eval(_912 + "._808.style.top;")) - event.clientY - document.body.scrollTop; 
	}else{
		_896 = parseInt(eval(_912 + "._808.style.left;")) - _721.pageX;
		_895 = parseInt(eval(_912 + "._808.style.top;")) - _721.pageY; 
	}
		
 	_909 = true;

 }
 
 function _719(){     

 	eval(_912 + "._752.style.zIndex=++_910;");
 	eval(_912 + "._808.style.zIndex=++_910;");
 	eval(_912 + "._751.style.zIndex=++_910;");
 	eval(_912 + "._789.style.zIndex=++_910;");
 	eval(_912 + "._755.style.zIndex=++_910;"); 		 		
 	
 	_156(this._809);
}

  function _139(_138){ 
	var _137 = "_785";  
	if(_138.indexOf(_137)!=0) return "";
	return _138.substring(_137.length);  
 }
  	
  	
 function _136(_135,_134,_133,_132, _131){ 
 for (_130 in _906)  //"_807" + this._809
 	{  

 	if( document.getElementById('_807' + _906[_130]).style.display!='none' && 
 	((_135 <= _905[_130] && _905[_130] <= _135 + _133) || ( _905[_130] <= _135 && _135 <= _905[_130] + _902[_130])) && 
 	((_134 <= _904[_130] && _904[_130] <= _134 + _132) || ( _904[_130] <= _134 && _134 <= _904[_130] + _903[_130]))) 
 	{
 	if (_131 == '') return true;  
 	
 	if (document.getElementById('_807' + _131).style.zIndex < document.getElementById('_807' + _906[_130]).style.zIndex) return true;
 	}
 	}
 	return false;
 }
 	
 function _129(_128){
	var _127 = 0;
	if (_128.offsetParent){
		while (_128.offsetParent){
			_127 += _128.offsetLeft;
			_128 = _128.offsetParent;
		}
	}else if(_128.x){ 
		_127 += _128.x;
	}
	
	return _127;
 }
 
 function _126(_128){
	var _125  = 0;
	if (_128.offsetParent){
		while (_128.offsetParent){
			_125 += _128.offsetTop;
			_128 = _128.offsetParent;
		}
	}else if (_128.y){ 
		_125 += _128.y;
	}
	
	return _125;
 }
  
 function _156(id) {
	if(_901 != '_899') return;
	_124 = document.getElementsByTagName('s' + 'e' + 'l' + 'e' + 'c' + 't'); 
	var _131;
	var _123 = false;
	
	for(_122=0; _122 < _124.length; _122++){
		_128 = _124[_122];
		_123 = false;
		_131 = '';

		while (_128.parentElement && _123!=true && _131 == ''){
			_128 = _128.parentElement; 
			if(_128.id == "_785" + id){  
 				_123 = true;
				_124[_122].style.visibility='visible'; 
			}
			_131 = _139(_128.id); 
		}
 
		if(_123!=true){
			_121 = _129(_124[_122]);
			_120 = _126(_124[_122]);    	
			_119 = _124[_122].offsetWidth;
			_118 = _124[_122].offsetHeight; 
	
			if(_136(_121, _120 ,_119, _118, _131))  
				_124[_122].style.visibility='hidden'; 
			else
				_124[_122].style.visibility='visible'; 
		}
		
		}
 } 