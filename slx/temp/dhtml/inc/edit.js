//-------------This page contains code that is Copyright Ferant.com 2003-----------------------
function _520(){ 
	
	var temp = document.getElementById('InputTemplate').value;
	eval(temp);

	if(typeof(template)!='object'){
		alert('Invalid template');
		return;
	}
	
	if(template.Id == '' || template.Id == null){
		alert('Invalid template');
		return;
	}
	
	if(_703!=template.Id){
			var msg = "Name Conflict!\n\n" +
						"The template could not be loaded, because \n\n\t in step 1 you set id = '" + _703 + 
						"', \n\n\t but the template has id = '" + template.Id + "'. " +
						"\n\nTo solve the problem go to step 1 and change id to '" + template.Id + "', then load the template again. " +
						"Do you want to go to Step 1 now?"

			alert(msg);	
			return;		
	}

	FerantDHTMLWindow1._892();

	for(x in template){
		if(x!='Id') FerantDHTMLWindow1[_891[x]] = template[x];		
			FerantDHTMLWindow1[x] = template[x];	
	}
	
	_513();		
	FerantDHTMLWindow1.OpenWindow();
} 

function _699(){ 
	
	FerantDHTMLWindow1._783 = document.getElementById('InputContentHTML').value;	
	FerantDHTMLWindow1._786.innerHTML = FerantDHTMLWindow1._783;
	_509();
}

function _565(){ 
	document.getElementById('InputTitleColor').value = _507(document.getElementById('InputTitleColor').value);
	if(_506(document.getElementById('InputTitleColor').value)) return;	
	FerantDHTMLWindow1._799 = document.getElementById('InputTitleColor').value;	
	_509();
}
function _579(){ 
	document.getElementById('InputStatusColor').value = _507(document.getElementById('InputStatusColor').value);
	if(_506(document.getElementById('InputStatusColor').value)) return;
	FerantDHTMLWindow1._765 = document.getElementById('InputStatusColor').value;
	_509();
}

function _583(){ 
	if(!_501(document.getElementById('InputTitleLeftMargin').value)){
		alert('Title Left Margin must be numeric.');
		return;
	}
	
	if(parseInt(document.getElementById('InputTitleLeftMargin').value)<0){
		alert('Title Left Margin must not be negative.');
		return;
	}
	
	FerantDHTMLWindow1._741 = parseInt(document.getElementById('InputTitleLeftMargin').value);
	_509();
}

function _586(){ 
	if(!_501(document.getElementById('InputStatusLeftMargin').value)){
		alert('Status Left Margin must be numeric.');
		return;
	}
	
	if(parseInt(document.getElementById('InputStatusLeftMargin').value)<0){
		alert('Status Left Margin must not be negative.');
		return;
	}	

	FerantDHTMLWindow1._742 = document.getElementById('InputStatusLeftMargin').value;
	_509();
}

function _696(){ 

	document.getElementById('InputBorderColor').value = _507(document.getElementById('InputBorderColor').value);
	if(_506(document.getElementById('InputBorderColor').value)) return;	
	FerantDHTMLWindow1._803 = document.getElementById('InputBorderColor').value;
	FerantDHTMLWindow1._808.style.backgroundColor = FerantDHTMLWindow1._803; 
	_509();
} 

function _641(){ 

	document.getElementById('InputInnerBorderColor').value = _507(document.getElementById('InputInnerBorderColor').value);
	if(_506(document.getElementById('InputInnerBorderColor').value)) return;
	FerantDHTMLWindow1._798 = document.getElementById('InputInnerBorderColor').value;
	FerantDHTMLWindow1._766.style.borderColor = FerantDHTMLWindow1._798; 
	FerantDHTMLWindow1._786.style.borderColor = FerantDHTMLWindow1._798; 
	FerantDHTMLWindow1._802.style.borderColor = FerantDHTMLWindow1._798; 
	_509();
} 

function _619(){ 

	document.getElementById('InputOuterBorderColor').value = _507(document.getElementById('InputOuterBorderColor').value);
	if(_506(document.getElementById('InputOuterBorderColor').value)) return;	
	FerantDHTMLWindow1._804 = document.getElementById('InputOuterBorderColor').value;
	FerantDHTMLWindow1._808.style.borderColor = FerantDHTMLWindow1._804; 
	_509();
} 

function _678(){ 

	document.getElementById('InputContentColor').value = _507(document.getElementById('InputContentColor').value);
	if(_506(document.getElementById('InputContentColor').value)) return;
	FerantDHTMLWindow1._784 = document.getElementById('InputContentColor').value;
	FerantDHTMLWindow1._786.style.backgroundColor = FerantDHTMLWindow1._784; 
	_509();
	
} 

function _675(){ 

	document.getElementById('InputContentInactiveColor').value = _507(document.getElementById('InputContentInactiveColor').value);
	if(_506(document.getElementById('InputContentInactiveColor').value)) return;						
	FerantDHTMLWindow1._739 = document.getElementById('InputContentInactiveColor').value;
	_509();
	
}
function _616(){ 

	document.getElementById('InputOuterBorderInactiveColor').value = _507(document.getElementById('InputOuterBorderInactiveColor').value);
	if(_506(document.getElementById('InputOuterBorderInactiveColor').value)) return;
	FerantDHTMLWindow1._731 = document.getElementById('InputOuterBorderInactiveColor').value; 
	_509();
}

function _482(){ 

	document.getElementById('InputInnerBorderInactiveColor').value = _507(document.getElementById('InputInnerBorderInactiveColor').value);
	if(_506(document.getElementById('InputInnerBorderInactiveColor').value)) return;
	FerantDHTMLWindow1._481 = document.getElementById('InputInnerBorderInactiveColor').value; 
	_509();
}
function _693(){ 

	document.getElementById('InputBorderInactiveColor').value = _507(document.getElementById('InputBorderInactiveColor').value);
	if(_506(document.getElementById('InputBorderInactiveColor').value)) return;
	FerantDHTMLWindow1._740 = document.getElementById('InputBorderInactiveColor').value; 
	_509();
}

function _612(){ 

	if(!_501(document.getElementById('InputOuterBorderWidth').value)){
		alert('Outer Border Width must be numeric.');
		return;
	}
	
	if(parseInt(document.getElementById('InputOuterBorderWidth').value)<0){
		alert('Outer Border Width must not be negative.');
		return;
	}
   
  FerantDHTMLWindow1._805 = parseInt(document.getElementById('InputOuterBorderWidth').value);
  FerantDHTMLWindow1._808.style.borderWidth = FerantDHTMLWindow1._805;
  _509();
} 

function _637(){ 
   
   if(!_501(document.getElementById('InputInnerBorderWidth').value)){
		alert('Inner Border Width must be numeric.');
		return;
	}
	
	if(parseInt(document.getElementById('InputInnerBorderWidth').value)<0){
		alert('Inner Border Width must not be negative.');
		return;
	}
	   
  if(FerantDHTMLWindow1._797==0 &&
	parseInt(document.getElementById('InputInnerBorderWidth').value) > 0 && (
	FerantDHTMLWindow1._777>0 ||
	FerantDHTMLWindow1._775>0 || 
	FerantDHTMLWindow1._773>0 ||
	FerantDHTMLWindow1._779>0 ))
		alert('You need first to reset ContentRightBorderWidth, ContentBottomBorderWidth, ContentLeftBorderWidth, and ContentTopBorderWidth to zero.');
	
  FerantDHTMLWindow1._797 = parseInt(document.getElementById('InputInnerBorderWidth').value);
  
  FerantDHTMLWindow1._802.style.borderWidth = FerantDHTMLWindow1._797; 
												
  if(_901=='_899')
		FerantDHTMLWindow1._802.style.height = FerantDHTMLWindow1._817   + 
														2 * FerantDHTMLWindow1._797;
  else
		FerantDHTMLWindow1._802.style.height = FerantDHTMLWindow1._817;
												
  FerantDHTMLWindow1._786.style.top = parseInt(FerantDHTMLWindow1._800) + 
												parseInt(FerantDHTMLWindow1._817) + 
												FerantDHTMLWindow1._797; 
												
  FerantDHTMLWindow1._786.style.borderWidth = FerantDHTMLWindow1._797;  
  
  FerantDHTMLWindow1._766.style.borderWidth = FerantDHTMLWindow1._797; 

  if(_901=='_899')
		FerantDHTMLWindow1._766.style.height = FerantDHTMLWindow1._812 + 
														2*FerantDHTMLWindow1._797;
  else
		FerantDHTMLWindow1._766.style.height=FerantDHTMLWindow1._812;
												
  _509(); 
} 

function _576(){ 

	if(!_501(document.getElementById('InputTitleBarHeight').value)){
		alert('Title Bar Height must be numeric.');
		return;
	}
	
	if(parseInt(document.getElementById('InputTitleBarHeight').value)<=0){
		alert('Title Bar Height must be > 0.');
		return;
	}
	
	FerantDHTMLWindow1._817 = parseInt(document.getElementById('InputTitleBarHeight').value);

	if(parseInt(document.getElementById('InputTitleBarHeight').value) + 1 < FerantDHTMLWindow1._796 &&
		FerantDHTMLWindow1._796 > 0){
		alert('This action resets Title Font Size to ' + (FerantDHTMLWindow1._817  - 2) + '.');
		FerantDHTMLWindow1._796 = FerantDHTMLWindow1._817  - 2;
		FerantDHTMLWindow1._802.style.fontSize = FerantDHTMLWindow1._796;
	}    
  
   if(_901=='_899')
		FerantDHTMLWindow1._802.style.height = FerantDHTMLWindow1._817   + 
														2 * FerantDHTMLWindow1._797;
   else
		FerantDHTMLWindow1._802.style.height = FerantDHTMLWindow1._817;
		
   FerantDHTMLWindow1._818 = parseInt(FerantDHTMLWindow1._817 - FerantDHTMLWindow1._816)/2;
  
   FerantDHTMLWindow1._786.style.top = parseInt(FerantDHTMLWindow1._800) + 
												parseInt(FerantDHTMLWindow1._817) + 
												FerantDHTMLWindow1._797;   
  
	if(FerantDHTMLWindow1._757 != '') 
		FerantDHTMLWindow1._802.innerHTML = FerantDHTMLWindow1._757; 
	else 
		FerantDHTMLWindow1._802.innerHTML = FerantDHTMLWindow1._791.replace(/</g,'&lt;').replace(/>/g,'&gt;');
		
  _509();
  
} 

function _598(){  

	if(!_501(document.getElementById('InputStatusBarHeight').value)){
		alert('Status Bar Height must be numeric.');
		return;
	}
	
	if(parseInt(document.getElementById('InputStatusBarHeight').value)<0){
		alert('Status Bar Height must not be negative.');
		return;
	}        

   
  FerantDHTMLWindow1._812 = parseInt(document.getElementById('InputStatusBarHeight').value);
  
  if(parseInt(document.getElementById('InputStatusBarHeight').value) + 1 < FerantDHTMLWindow1._764 &&
		FerantDHTMLWindow1._764 > 0 && FerantDHTMLWindow1._812 > 1){
		alert('This action resets Status Font Size to ' + (FerantDHTMLWindow1._812  - 2) + '.');
		FerantDHTMLWindow1._764 = FerantDHTMLWindow1._812  - 2;
		FerantDHTMLWindow1._766.style.fontSize = FerantDHTMLWindow1._764;
  }
  
  if(FerantDHTMLWindow1._812 > 0)
		FerantDHTMLWindow1._766.style.display = "block";
  else
		FerantDHTMLWindow1._766.style.display= "none";

    if(_901=='_899')
		FerantDHTMLWindow1._766.style.height = FerantDHTMLWindow1._812 + 
														2*FerantDHTMLWindow1._797;
    else
		FerantDHTMLWindow1._766.style.height=FerantDHTMLWindow1._812;
										                         
	if(FerantDHTMLWindow1._756 != '') 
		FerantDHTMLWindow1._766.innerHTML = FerantDHTMLWindow1._756; 
	else 
		FerantDHTMLWindow1._766.innerHTML = FerantDHTMLWindow1._759.replace(/</g,'&lt;').replace(/>/g,'&gt;');

  _509();
  
} 

function _687(){ 

	if(!_501(document.getElementById('InputCloseBoxHeight').value)){
		alert('Close Box Height must be numeric.');
		return;
	}
	
	if(parseInt(document.getElementById('InputCloseBoxHeight').value)<0){
		alert('Close Box Height must not be negative.');
		return;
	}
   
  FerantDHTMLWindow1._816 = parseInt(document.getElementById('InputCloseBoxHeight').value);
  FerantDHTMLWindow1._818 = parseInt((FerantDHTMLWindow1._817 - FerantDHTMLWindow1._816 )/2);  
  
  
  FerantDHTMLWindow1._789.innerHTML = "<img src='" + FerantDHTMLWindow1._788 + "' height = " + 
							FerantDHTMLWindow1._816 + " width = " + FerantDHTMLWindow1._810 + " id='_787'>";
  
		
  FerantDHTMLWindow1._789.style.height = FerantDHTMLWindow1._816 + "px";
  _509();  
  
}

function _681(){ 

   if(!_501(document.getElementById('InputCloseBoxWidth').value)){
		alert('Close Box Width must be numeric.');
		return;
	}
	
	if(parseInt(document.getElementById('InputCloseBoxWidth').value)<0){
		alert('Close Box Width must not be negative.');
		return;
	}
   
  FerantDHTMLWindow1._810 = parseInt(document.getElementById('InputCloseBoxWidth').value);

  FerantDHTMLWindow1._789.innerHTML = "<img src='" + FerantDHTMLWindow1._788 + "' height = " + 
							FerantDHTMLWindow1._816 + " width = " + FerantDHTMLWindow1._810 + " id='_787'>";

		
  FerantDHTMLWindow1._789.style.width = FerantDHTMLWindow1._810 + "px";
  
  _509(); 
}

function _690(){ 
  
    if(!_501(document.getElementById('InputBorderWidth').value)){
		alert('Border Width must be numeric.');
		return;
	}
	
	if(parseInt(document.getElementById('InputBorderWidth').value)<0){
		alert('Border Width must not be negative.');
		return;
	}
   
  FerantDHTMLWindow1._800 = parseInt(document.getElementById('InputBorderWidth').value);
  FerantDHTMLWindow1._802.style.left = FerantDHTMLWindow1._800;
  FerantDHTMLWindow1._802.style.top = FerantDHTMLWindow1._800;
  
  FerantDHTMLWindow1._786.style.left = FerantDHTMLWindow1._800;
  FerantDHTMLWindow1._786.style.top = parseInt(FerantDHTMLWindow1._800) + 
												parseInt(FerantDHTMLWindow1._817) + 
												parseInt(FerantDHTMLWindow1._797);
												
  FerantDHTMLWindow1._766.style.left = FerantDHTMLWindow1._800;

  _509();
  
} 


function _634(){  

   if(!_501(document.getElementById('InputLeft').value)){
		alert('Left must be numeric.');
		return;
	}
	
	if(parseInt(document.getElementById('InputLeft').value)<-1){
		alert('Left must be -1, 0, or > 0.');
		return;
	}
	  
  FerantDHTMLWindow1._736 = parseInt(document.getElementById('InputLeft').value);
  FerantDHTMLWindow1.OpenWindow();
  
} 

function _559(){ 

	if(!_501(document.getElementById('InputTop').value)){
		alert('Top must be numeric.');
		return;
	}
	
	if(parseInt(document.getElementById('InputTop').value)<-1){
		alert('Top must be -1, 0, or > 0.');
		return;
	}
	
	  FerantDHTMLWindow1._725 = parseInt(document.getElementById('InputTop').value);
	  FerantDHTMLWindow1.OpenWindow(); 
	  
} 

function _644(){ 

	if(!_501(document.getElementById('InputHeight').value)){
		alert('Height must be numeric.');
		return;
	}
	
	if(parseInt(document.getElementById('InputHeight').value)<0){
		alert('Height must not be negative.');
		return;
	}
	
	  FerantDHTMLWindow1._737 = parseInt(document.getElementById('InputHeight').value);
	  FerantDHTMLWindow1.OpenWindow(); 
} 

function _556(){ 

	  if(!_501(document.getElementById('InputWidth').value)){
		alert('Width must be numeric.');
		return;
	}
	
	if(parseInt(document.getElementById('InputWidth').value)<0){
		alert('Width must not be negative.');
		return;
	}
	
	  FerantDHTMLWindow1._724 = parseInt(document.getElementById('InputWidth').value);
	  FerantDHTMLWindow1.OpenWindow(); 
} 

function _628(){ 

	 if(!_501(document.getElementById('InputMaxWidth').value)){
		alert('Max Width must be numeric.');
		return;
	}
	
	if(parseInt(document.getElementById('InputMaxWidth').value)<0){
		alert('Max Width must not be negative.');
		return;
	}
	
	  FerantDHTMLWindow1._734 = parseInt(document.getElementById('InputMaxWidth').value);
	  _509();
	  
}

function _622(){ 

	if(!_501(document.getElementById('InputMinWidth').value)){
		alert('Min Width must be numeric.');
		return;
	}
	
	if(parseInt(document.getElementById('InputMinWidth').value)<0){
		alert('Min Width must not be negative.');
		return;
	}
	
	  FerantDHTMLWindow1._732 = parseInt(document.getElementById('InputMinWidth').value);
	  _509(); 
}

function _631(){ 

	if(!_501(document.getElementById('InputMaxHeight').value)){
		alert('Max Height must be numeric.');
		return;
	}
	
	if(parseInt(document.getElementById('InputMaxHeight').value)<0){
		alert('Max Height must not be negative.');
		return;
	}
	
	  FerantDHTMLWindow1._735 = parseInt(document.getElementById('InputMaxHeight').value);
	  _509(); 
}

function _625(){ 

	if(!_501(document.getElementById('InputMinHeight').value)){
		alert('Min Height must be numeric.');
		return;
	}
	
	if(parseInt(document.getElementById('InputMinHeight').value)<0){
		alert('Min Height must not be negative.');
		return;
	}
	
	  FerantDHTMLWindow1._733 = parseInt(document.getElementById('InputMinHeight').value);
	  _509(); 
}

function _599(){ 

	if(document.form1.InputShadow[1].checked)
		FerantDHTMLWindow1._729 = true;
    else
		FerantDHTMLWindow1._729 = false;

	
	if(FerantDHTMLWindow1._729)
	{		
		FerantDHTMLWindow1._752.style.visibility = "visible"; 
	}
	else
	{
		FerantDHTMLWindow1._752.style.visibility = "hidden";
	}
	
	_509();
	
}

function _609(){ 

	if(document.form1.InputResizable[0].checked)
		FerantDHTMLWindow1._730 = 'Both';
		
	if(document.form1.InputResizable[1].checked)
		FerantDHTMLWindow1._730 = 'VerticalOnly';
		
	if(document.form1.InputResizable[2].checked)
		FerantDHTMLWindow1._730 = 'HorizontalOnly';
		
	if(document.form1.InputResizable[3].checked)
		FerantDHTMLWindow1._730 = 'None';


	
	if(FerantDHTMLWindow1._730 == 'None')
		FerantDHTMLWindow1._755.style.display = "none"; 
	else
		FerantDHTMLWindow1._755.style.display = "block";

	_509();
} 

function _684(){ 

	  FerantDHTMLWindow1._788 = document.getElementById('InputCloseBoxSrc').value;
	  FerantDHTMLWindow1._789.innerHTML = "<img src='" + FerantDHTMLWindow1._788 + "' height = " + 
							FerantDHTMLWindow1._816 + " width = " + FerantDHTMLWindow1._810 + " id='_787'>";
  
	  _509(); 
}

function _605(){ 

	  FerantDHTMLWindow1._754 = document.getElementById('InputResizeBoxSrc').value;
	  if(FerantDHTMLWindow1._814 ==0 || FerantDHTMLWindow1._813 ==0 || 
				FerantDHTMLWindow1._754 == '') FerantDHTMLWindow1.f_ShowResizeBox  = false;
				
	  FerantDHTMLWindow1._755.innerHTML = "<img src='" + FerantDHTMLWindow1._754 + "' height = " + 
									FerantDHTMLWindow1._814 + 
									" width=" + FerantDHTMLWindow1._813 + " id='_753'>";
	  
	  _509(); 
}


function _608(){ 

	if(!_501(document.getElementById('InputResizeBoxHeight').value)){
		alert('Resize Box Height must be numeric.');
		return;
	}
	
	if(parseInt(document.getElementById('InputResizeBoxHeight').value)<0){
		alert('Resize Box Height must not be negative.');
		return;
	}
	
	 FerantDHTMLWindow1._814 = parseInt(document.getElementById('InputResizeBoxHeight').value);
	 if(FerantDHTMLWindow1._814 ==0 || FerantDHTMLWindow1._813 ==0 || 
				FerantDHTMLWindow1._754 == '') FerantDHTMLWindow1.f_ShowResizeBox  = false;
				
	  FerantDHTMLWindow1._755.innerHTML = "<img src='" + FerantDHTMLWindow1._754 + "' height = " + 
									FerantDHTMLWindow1._814 + 
									" width=" + FerantDHTMLWindow1._813 + " id='_753'>";
									
	  FerantDHTMLWindow1._755.style.top = parseInt(FerantDHTMLWindow1._812) - parseInt(FerantDHTMLWindow1._814) + "px";
	  FerantDHTMLWindow1._755.style.height = FerantDHTMLWindow1._814 + "px";
	  FerantDHTMLWindow1._755.style.width = FerantDHTMLWindow1._813 + "px";
		
	  _509(); 
}

function _602(){  

	  if(!_501(document.getElementById('InputResizeBoxWidth').value)){
		alert('Resize Box Width must be numeric.');
		return;
	}
	
	if(parseInt(document.getElementById('InputResizeBoxWidth').value)<0){
		alert('Resize Box Width must not be negative.');
		return;
	}
	 
	  FerantDHTMLWindow1._813 = parseInt(document.getElementById('InputResizeBoxWidth').value);
	  if(FerantDHTMLWindow1._814 ==0 || FerantDHTMLWindow1._813 ==0 || 
				FerantDHTMLWindow1._754 == '') FerantDHTMLWindow1.f_ShowResizeBox  = false;
		
	  FerantDHTMLWindow1._755.style.height = FerantDHTMLWindow1._814 + "px";
	  FerantDHTMLWindow1._755.style.width = FerantDHTMLWindow1._813 + "px";
			
	  FerantDHTMLWindow1._755.innerHTML = "<img src='" + FerantDHTMLWindow1._754 + "' height = " + 
									FerantDHTMLWindow1._814 + 
									" width=" + FerantDHTMLWindow1._813 + " id='_753'>";
	  
	  _509();  
}

function _589(){  
   
	FerantDHTMLWindow1._759 = document.getElementById('InputStatusBarText').value;
		
	if(FerantDHTMLWindow1._756 != '') {
		FerantDHTMLWindow1._766.innerHTML = FerantDHTMLWindow1._756;
		alert('Status Bar HTML overwrites Status Bar Text.'); 
	}else 
		FerantDHTMLWindow1._766.innerHTML = FerantDHTMLWindow1._759.replace(/</g,'&lt;').replace(/>/g,'&gt;');

  _509(); 
}

function _580(){ 
 
  if(document.form1.InputStatusBarAlign[0].checked)
		FerantDHTMLWindow1._758 = 'center';
  else
		FerantDHTMLWindow1._758 = 'left'; 
  
  FerantDHTMLWindow1._766.style.textAlign  = FerantDHTMLWindow1._758;
                   
  _509();   
}

function _570(){ 
   
    FerantDHTMLWindow1._791 = document.getElementById('InputTitleBarText').value;

	if(FerantDHTMLWindow1._757 != '') {
		FerantDHTMLWindow1._802.innerHTML = FerantDHTMLWindow1._757; 
		alert('Title Bar HTML overwrites Title Bar Text.');
	}else 
		FerantDHTMLWindow1._802.innerHTML = FerantDHTMLWindow1._791.replace(/</g,'&lt;').replace(/>/g,'&gt;');
		  
  _509(); 
} 

function _566(){ 
   
  if(document.form1.InputTitleBarAlign[0].checked)
		FerantDHTMLWindow1._790 = 'center';
  else
		FerantDHTMLWindow1._790 = 'left';

  FerantDHTMLWindow1._802.style.textAlign  = FerantDHTMLWindow1._790;
  
  _509(); 
} 

function _613(){ 
  
  if(document.form1.InputOuterBorderStyle[0].checked)
		FerantDHTMLWindow1._806 = 'solid';
  else
		FerantDHTMLWindow1._806 = 'dashed';

  FerantDHTMLWindow1._808.style.borderStyle = FerantDHTMLWindow1._806;
  _509();
} 

function _638(){ 
   
  if(document.form1.InputInnerBorderStyle[0].checked)
		FerantDHTMLWindow1._782 = 'solid';
	else
		FerantDHTMLWindow1._782 = 'dashed';

  FerantDHTMLWindow1._802.style.borderStyle = FerantDHTMLWindow1._782;
  FerantDHTMLWindow1._786.style.borderStyle = FerantDHTMLWindow1._782;
  FerantDHTMLWindow1._766.style.borderStyle = FerantDHTMLWindow1._782;
  
  _509();
}

function _592(){ 

	document.getElementById('InputStatusBarInactiveColor').value = _507(document.getElementById('InputStatusBarInactiveColor').value);
	if(_506(document.getElementById('InputStatusBarInactiveColor').value)) return;
						
	FerantDHTMLWindow1._728 = document.getElementById('InputStatusBarInactiveColor').value;
	_509();	
}

function _562(){ 

	document.getElementById('InputTitleBarInactiveColor').value = _507(document.getElementById('InputTitleBarInactiveColor').value);
	if(_506(document.getElementById('InputTitleBarInactiveColor').value)) return;
						
	FerantDHTMLWindow1._726 = document.getElementById('InputTitleBarInactiveColor').value;
	_509();	
}


function _645(){ 
	
	if(document.form1.InputDragable[0].checked)
		FerantDHTMLWindow1._738 = 'Both';
		
	if(document.form1.InputDragable[1].checked)
		FerantDHTMLWindow1._738 = 'VerticalOnly';
		
	if(document.form1.InputDragable[2].checked)
		FerantDHTMLWindow1._738 = 'HorizontalOnly';
		
	if(document.form1.InputDragable[3].checked)
		FerantDHTMLWindow1._738 = 'None';

	_509();
} 

function _573(){ 

	FerantDHTMLWindow1._757  = document.getElementById('InputTitleBarHTML').value;
	
	if(FerantDHTMLWindow1._757 != '') 
		FerantDHTMLWindow1._802.innerHTML = FerantDHTMLWindow1._757; 
	else 
		FerantDHTMLWindow1._802.innerHTML = FerantDHTMLWindow1._791.replace(/</g,'&lt;').replace(/>/g,'&gt;');

	_509();
} 

function _595(){ 
	
	FerantDHTMLWindow1._756  = document.getElementById('InputStatusBarHTML').value;
	
	if(FerantDHTMLWindow1._756 != '' )
		FerantDHTMLWindow1._766.innerHTML = FerantDHTMLWindow1._756; 
	else 
		FerantDHTMLWindow1._766.innerHTML = FerantDHTMLWindow1._759.replace(/</g,'&lt;').replace(/>/g,'&gt;');
			
	_509();
} 

function _660(){ 

	if(!_501(document.getElementById('InputContentPadding').value)){
		alert('Content Padding must be numeric.');
		return;
	}
	
	if(parseInt(document.getElementById('InputContentPadding').value)<0){
		alert('Content Padding must not be negative.');
		return;
	}

	FerantDHTMLWindow1._780  = document.getElementById('InputContentPadding').value;
	FerantDHTMLWindow1._786.style.padding = FerantDHTMLWindow1._780 + 'px ' + FerantDHTMLWindow1._780 + 'px ' +
										FerantDHTMLWindow1._780 + 'px ' + FerantDHTMLWindow1._780 + 'px';
	_509();
} 

function _654(){ 

	if(!_501(document.getElementById('InputContentRightBorderWidth').value)){
		alert('Content Right Border Width must be numeric.');
		return;
	}
	
	if(parseInt(document.getElementById('InputContentRightBorderWidth').value)<0){
		alert('Content Right Border Width must not be negative.');
		return;
	}

	FerantDHTMLWindow1._777  = parseInt(document.getElementById('InputContentRightBorderWidth').value);
	
	if(FerantDHTMLWindow1._797>0 && FerantDHTMLWindow1._777>0){
		alert('This action resets InnerBorderWidth to zero.');
		FerantDHTMLWindow1._797 = 0;	
		document.getElementById('InputInnerBorderWidth').value = 0;
		_637();	
	}

	FerantDHTMLWindow1._786.style.borderRightWidth = FerantDHTMLWindow1._777;
	FerantDHTMLWindow1._786.style.borderRightColor = FerantDHTMLWindow1._776;	
	_509();
} 

function _657(){ 

	document.getElementById('InputContentRightBorderColor').value = _507(document.getElementById('InputContentRightBorderColor').value);
	if(_506(document.getElementById('InputContentRightBorderColor').value)) return;	
	FerantDHTMLWindow1._776  = document.getElementById('InputContentRightBorderColor').value;	
	_509();
}

function _669(){ 

	if(!_501(document.getElementById('InputContentBottomBorderWidth').value)){
		alert('Content Bottom Border Width must be numeric.');
		return;
	}
	
	if(parseInt(document.getElementById('InputContentBottomBorderWidth').value)<0){
		alert('Content Bottom Border Width must not be negative.');
		return;
	}

	FerantDHTMLWindow1._775  = parseInt(document.getElementById('InputContentBottomBorderWidth').value);
	
	if(FerantDHTMLWindow1._797 > 0 && FerantDHTMLWindow1._775 > 0){
		alert('This action resets InnerBorderWidth to zero.');
		FerantDHTMLWindow1._797 = 0;	
		document.getElementById('InputInnerBorderWidth').value = 0;
		_637();	
	}
	
	FerantDHTMLWindow1._786.style.borderBottomColor = FerantDHTMLWindow1._774;
	FerantDHTMLWindow1._786.style.borderBottomWidth = FerantDHTMLWindow1._775;
	
	_509();
} 

function _672(){ 

	document.getElementById('InputContentBottomBorderColor').value = _507(document.getElementById('InputContentBottomBorderColor').value);
	if(_506(document.getElementById('InputContentBottomBorderColor').value)) return;
	
	FerantDHTMLWindow1._774  = document.getElementById('InputContentBottomBorderColor').value;
	
	FerantDHTMLWindow1._786.style.borderBottomColor = FerantDHTMLWindow1._774;
	FerantDHTMLWindow1._786.style.borderBottomWidth = FerantDHTMLWindow1._775;	

	_509();
}

function _663(){ 

	if(!_501(document.getElementById('InputContentLeftBorderWidth').value)){
		alert('Content Left Border Width must be numeric.');
		return;
	}
	
	if(parseInt(document.getElementById('InputContentLeftBorderWidth').value)<0){
		alert('Content Left Border Width must not be negative.');
		return;
	}

	FerantDHTMLWindow1._773  = parseInt(document.getElementById('InputContentLeftBorderWidth').value);
	
	if(FerantDHTMLWindow1._797>0 && FerantDHTMLWindow1._773>0){
		alert('This action resets InnerBorderWidth to zero.');
		FerantDHTMLWindow1._797 = 0;	
		document.getElementById('InputInnerBorderWidth').value = 0;
		_637();	
	}
	
	FerantDHTMLWindow1._786.style.borderLeftWidth = FerantDHTMLWindow1._773;
	FerantDHTMLWindow1._786.style.borderLeftColor = FerantDHTMLWindow1._772;	
	_509();
} 

function _666(){ 

	document.getElementById('InputContentLeftBorderColor').value = _507(document.getElementById('InputContentLeftBorderColor').value);
	if(_506(document.getElementById('InputContentLeftBorderColor').value)) return;

	FerantDHTMLWindow1._772  = document.getElementById('InputContentLeftBorderColor').value;	
	FerantDHTMLWindow1._786.style.borderLeftColor = FerantDHTMLWindow1._772;
	_509();
}

function _648(){ 

	if(!_501(document.getElementById('InputContentTopBorderWidth').value)){
		alert('Content Top Border Width must be numeric.');
		return;
	}
	
	if(parseInt(document.getElementById('InputContentTopBorderWidth').value)<0){
		alert('Content Top Border Width must not be negative.');
		return;
	}

	FerantDHTMLWindow1._779  = parseInt(document.getElementById('InputContentTopBorderWidth').value);
	
	if(FerantDHTMLWindow1._797>0 && FerantDHTMLWindow1._779>0){
		alert('This action resets InnerBorderWidth to zero.');
		FerantDHTMLWindow1._797 = 0;	
		document.getElementById('InputInnerBorderWidth').value = 0;
		_637();	
	}
	
	FerantDHTMLWindow1._786.style.borderTopWidth = FerantDHTMLWindow1._779;
	FerantDHTMLWindow1._786.style.borderTopColor = FerantDHTMLWindow1._778;
	_509();
} 

function _651(){ 
						
	document.getElementById('InputContentTopBorderColor').value = _507(document.getElementById('InputContentTopBorderColor').value);
	if(_506(document.getElementById('InputContentTopBorderColor').value)) return;	
	FerantDHTMLWindow1._778 = document.getElementById('InputContentTopBorderColor').value;
	_509(document.getElementById('InputContentTopBorderColor').value);
}


function _567(){ 
						
	if(document.form1.InputContentNoWrap[0].checked)
		FerantDHTMLWindow1._781 = 'nowrap';
	else
		FerantDHTMLWindow1._781 = 'Normal';
	
	FerantDHTMLWindow1._786.style.whiteSpace = FerantDHTMLWindow1._781; 
	_509();
}


function _553(){ 
	
	if(!_501(document.getElementById('InputTitleFontSize').value)){
		alert('Title Font Size must be numeric.');
		return;
	}
	
	if(parseInt(document.getElementById('InputTitleFontSize').value)<0){
		alert('Title Font Size must not be negative.');
		return;
	}
				
	if(FerantDHTMLWindow1._817 - 1 <= parseInt(document.getElementById('InputTitleFontSize').value)){
		alert('Invalid value!\n\nTitle Font Size must be less then Title Bar Height - 1.');
		return;	
	}	
			
	FerantDHTMLWindow1._796 = parseInt(document.getElementById('InputTitleFontSize').value);	
	FerantDHTMLWindow1._802.style.fontSize = FerantDHTMLWindow1._796;
	_509();
}

function _531(){ 

	if(!_501(document.getElementById('InputStatusFontSize').value)){
		alert('Status Font Size must be numeric.');
		return;
	}
	
	if(parseInt(document.getElementById('InputStatusFontSize').value)<0){
		alert('Status Font Size must not be negative.');
		return;
	}
				
	if(FerantDHTMLWindow1._812 -1 <= parseInt(document.getElementById('InputStatusFontSize').value)){
		alert('Invalid value!\n\nStatus Font Size must be less then Status Bar Height - 1.');
		return;	
	}	
			
	FerantDHTMLWindow1._764 = parseInt(document.getElementById('InputStatusFontSize').value);	
	FerantDHTMLWindow1._766.style.fontSize = FerantDHTMLWindow1._764;
	_509();
}

function _542(){ 
	if(!_501(document.getElementById('InputContentFontSize').value)){
		alert('Content Font Size must be numeric.');
		return;
	}
	
	if(parseInt(document.getElementById('InputContentFontSize').value)<=0){
		alert('Content Font Size must be > 0.');
		return;
	}
			
	FerantDHTMLWindow1._771 = parseInt(document.getElementById('InputContentFontSize').value);	
	FerantDHTMLWindow1._786.style.fontSize = FerantDHTMLWindow1._771;
	_509();
}

function _536(){ 
			
	if(document.form1.InputContentFontWeight[0].checked)
		FerantDHTMLWindow1._769 = 'Normal';
    else
		FerantDHTMLWindow1._769 = 'Bold';
	
	FerantDHTMLWindow1._786.style.fontWeight = FerantDHTMLWindow1._769;
	_509();
}

function _525(){ 
			
	if(document.form1.InputStatusFontWeight[0].checked)
		FerantDHTMLWindow1._762 = 'Normal';
    else
		FerantDHTMLWindow1._762 = 'Bold';
	
	FerantDHTMLWindow1._766.style.fontWeight = FerantDHTMLWindow1._762;
	_509();
}

function _547(){ 
			
	if(document.form1.InputTitleFontWeight[1].checked)
		FerantDHTMLWindow1._794 = 'Normal';
    else
		FerantDHTMLWindow1._794 = 'Bold';
	
	FerantDHTMLWindow1._802.style.fontWeight = FerantDHTMLWindow1._794;
	_509();
}

function _535(){
			
	document.getElementById('InputContentFontColor').value = _507(document.getElementById('InputContentFontColor').value);
	if(_506(document.getElementById('InputContentFontColor').value)) return;	
	FerantDHTMLWindow1._768 = document.getElementById('InputContentFontColor').value;	
	FerantDHTMLWindow1._786.style.color = FerantDHTMLWindow1._768;
	_509();
}

function _524(){ 
			
	document.getElementById('InputStatusFontColor').value = _507(document.getElementById('InputStatusFontColor').value);
	if(_506(document.getElementById('InputStatusFontColor').value)) return;	
	FerantDHTMLWindow1._761 = document.getElementById('InputStatusFontColor').value;	
	FerantDHTMLWindow1._766.style.color = FerantDHTMLWindow1._761;
	_509();
}

function _546(){ 
			
	document.getElementById('InputTitleFontColor').value = _507(document.getElementById('InputTitleFontColor').value);
	if(_506(document.getElementById('InputTitleFontColor').value)) return;	
	FerantDHTMLWindow1._793 = document.getElementById('InputTitleFontColor').value;	
	FerantDHTMLWindow1._802.style.color = FerantDHTMLWindow1._793;
	_509();
}

function _532(){ 
			
	if(document.form1.InputContentFontStyle[0].checked)
		FerantDHTMLWindow1._767 = 'Normal';
    else
		FerantDHTMLWindow1._767 = 'Italic';
	
	FerantDHTMLWindow1._786.style.fontStyle = FerantDHTMLWindow1._767;
	_509();
}

function _521(){ 
			
	if(document.form1.InputStatusFontStyle[0].checked)
		FerantDHTMLWindow1._760 = 'Normal';
    else
		FerantDHTMLWindow1._760 = 'Italic';
	
	FerantDHTMLWindow1._766.style.fontStyle = FerantDHTMLWindow1._760;
	_509();
}

function _543(){ 
			

	if(document.form1.InputTitleFontStyle[0].checked)
		FerantDHTMLWindow1._792 = 'Normal';
    else
		FerantDHTMLWindow1._792 = 'Italic';
	
	FerantDHTMLWindow1._802.style.fontStyle = FerantDHTMLWindow1._792;
	_509();
}

function _539(){ 
			
	FerantDHTMLWindow1._770 = document.getElementById('InputContentFontFamily').value;	
	FerantDHTMLWindow1._786.style.fontFamily = FerantDHTMLWindow1._770;
	_509();
}

function _528(){ 
			
	FerantDHTMLWindow1._763 = document.getElementById('InputStatusFontFamily').value;	
	FerantDHTMLWindow1._766.style.fontFamily = FerantDHTMLWindow1._763;
	_509();
}

function _550(){ 
			
	FerantDHTMLWindow1._795 = document.getElementById('InputTitleFontFamily').value;	
	FerantDHTMLWindow1._802.style.fontFamily = FerantDHTMLWindow1._795;
	_509();
}

var regex1 = /'/g;
var regex2 = /"/g;
var regex3 = /</g;
var regex4 = />/g;
var regex5 =new RegExp("\r","g");
var regex6 =new RegExp("\n","g");
		
function _354(){ 

	WebColors.CloseWindow(); 
	FerantDHTMLWindow1.CloseWindow(); 

	x = "var params =  	\n";
	x += "{	\n";
	
	for( y in _723){ 
	
		if(FerantDHTMLWindow1[_891[y]] != FerantDHTMLWindow1[_890[y]])
		
		if(_723[y])
			x += "   " + y + " : '"  + FerantDHTMLWindow1[_891[y]].replace(regex1,"\\'").replace(regex2,'\\"').replace(regex5,' ').replace(regex6,' ')  + "', \n";
		else
			x += "   " + y + " : "  + FerantDHTMLWindow1[_891[y]] + ", \n";
	}
	
	x += "   Id  : '" + _703 + "'\n"
	x += "} \n"; 
	x += "var " + _703 + " = new FerantDHTMLWindow(params); \n";
	document.form1.InputDeploy.value = x;
}

function _519(){  

	x = "var template =  	\n";
	x += "{	\n";
	
	for( y in _723){ 
	
		if(FerantDHTMLWindow1[_891[y]] != FerantDHTMLWindow1[_890[y]])
		
			if(_723[y])				
				x += "   " + y + " : '"  + FerantDHTMLWindow1[_891[y]].replace(regex1,"\\'").replace(regex2,'\\"').replace(regex5,' ').replace(regex6,' ')  + "', \n";
			else
				x += "   " + y + " : "  + FerantDHTMLWindow1[_891[y]] + ", \n";
	}
	x += "   Id  : '" + _703 + "'\n"
	x += "} \n"; 
	document.getElementById('OutputTemplate').value = x;
}

function _513(){

	FerantDHTMLWindow1._818	= parseInt(FerantDHTMLWindow1._817 - FerantDHTMLWindow1._816)/2;
	
	FerantDHTMLWindow1._808.style.borderStyle = FerantDHTMLWindow1._806;
	FerantDHTMLWindow1._808.style.borderWidth = FerantDHTMLWindow1._805;
	FerantDHTMLWindow1._808.style.borderColor = FerantDHTMLWindow1._804;
	FerantDHTMLWindow1._808.style.backgroundColor = FerantDHTMLWindow1._803;
 
	FerantDHTMLWindow1._802.style.left = FerantDHTMLWindow1._800 +  "px"; 
	FerantDHTMLWindow1._802.style.top = FerantDHTMLWindow1._800 + "px"; 

	FerantDHTMLWindow1._802.style.backgroundColor=FerantDHTMLWindow1._799;

	FerantDHTMLWindow1._802.style.borderColor = FerantDHTMLWindow1._798;

	FerantDHTMLWindow1._802.style.borderWidth=FerantDHTMLWindow1._797;

	if(_901=='_899')
		FerantDHTMLWindow1._802.style.height = FerantDHTMLWindow1._817   + 2 * FerantDHTMLWindow1._797;
	else
		FerantDHTMLWindow1._802.style.height = FerantDHTMLWindow1._817;
 
	FerantDHTMLWindow1._789.style.height = FerantDHTMLWindow1._816 + "px";
	FerantDHTMLWindow1._789.style.width = FerantDHTMLWindow1._810 + "px";
	FerantDHTMLWindow1._789.innerHTML = "<img src='" + FerantDHTMLWindow1._788 + "' height = " + 
							FerantDHTMLWindow1._816 + " width = " + FerantDHTMLWindow1._810 + " id='_787'>";

	FerantDHTMLWindow1._786.style.backgroundColor = FerantDHTMLWindow1._784; 

	FerantDHTMLWindow1._802.style.fontSize = FerantDHTMLWindow1._796;
	FerantDHTMLWindow1._802.style.fontFamily = FerantDHTMLWindow1._795;
	FerantDHTMLWindow1._802.style.fontWeight = FerantDHTMLWindow1._794;
	FerantDHTMLWindow1._802.style.color = FerantDHTMLWindow1._793;
	FerantDHTMLWindow1._802.style.fontStyle = FerantDHTMLWindow1._792; 

	FerantDHTMLWindow1._786.style.fontSize = FerantDHTMLWindow1._771;
	FerantDHTMLWindow1._786.style.fontFamily = FerantDHTMLWindow1._770;
	FerantDHTMLWindow1._786.style.fontWeight = FerantDHTMLWindow1._769;
	FerantDHTMLWindow1._786.style.color = FerantDHTMLWindow1._768;
	FerantDHTMLWindow1._786.style.fontStyle = FerantDHTMLWindow1._767; 
	FerantDHTMLWindow1._786.style.whiteSpace = FerantDHTMLWindow1._781;

	FerantDHTMLWindow1._766.style.fontSize = FerantDHTMLWindow1._764;
	FerantDHTMLWindow1._766.style.fontFamily = FerantDHTMLWindow1._763;
	FerantDHTMLWindow1._766.style.fontWeight = FerantDHTMLWindow1._762;
	FerantDHTMLWindow1._766.style.color = FerantDHTMLWindow1._761;
	FerantDHTMLWindow1._766.style.fontStyle = FerantDHTMLWindow1._760; 

	FerantDHTMLWindow1._786.innerHTML = FerantDHTMLWindow1._783;
	FerantDHTMLWindow1._786.style.left = FerantDHTMLWindow1._800;
	FerantDHTMLWindow1._786.style.top = parseInt(FerantDHTMLWindow1._800) + parseInt(FerantDHTMLWindow1._817) + parseInt(FerantDHTMLWindow1._797);
	FerantDHTMLWindow1._786.style.borderColor = FerantDHTMLWindow1._798; 	
	FerantDHTMLWindow1._786.style.borderStyle = FerantDHTMLWindow1._782;
	FerantDHTMLWindow1._786.style.borderWidth = FerantDHTMLWindow1._797;

	FerantDHTMLWindow1._786.style.padding = FerantDHTMLWindow1._780 + 'px ' + FerantDHTMLWindow1._780 + 'px ' +
										FerantDHTMLWindow1._780 + 'px ' + FerantDHTMLWindow1._780 + 'px';
 
	if(FerantDHTMLWindow1._779 != 0){ 
		FerantDHTMLWindow1._786.style.borderTopColor = FerantDHTMLWindow1._778;
		FerantDHTMLWindow1._786.style.borderTopWidth = FerantDHTMLWindow1._779;
	}
		
	if(FerantDHTMLWindow1._777 != 0){
		FerantDHTMLWindow1._786.style.borderRightColor = FerantDHTMLWindow1._776;
		FerantDHTMLWindow1._786.style.borderRightWidth = FerantDHTMLWindow1._777;
	}
		
	if(FerantDHTMLWindow1._775 != 0){
		FerantDHTMLWindow1._786.style.borderBottomColor = FerantDHTMLWindow1._774;
		FerantDHTMLWindow1._786.style.borderBottomWidth = FerantDHTMLWindow1._775;
	}

		
	if(FerantDHTMLWindow1._773 != 0){
		FerantDHTMLWindow1._786.style.borderLeftColor = FerantDHTMLWindow1._772;
		FerantDHTMLWindow1._786.style.borderLeftWidth = FerantDHTMLWindow1._773;
	}	
	
	FerantDHTMLWindow1._766.style.left=FerantDHTMLWindow1._800;
	FerantDHTMLWindow1._766.style.backgroundColor=FerantDHTMLWindow1._765; 
	if(_901=='_899')
		FerantDHTMLWindow1._766.style.height=FerantDHTMLWindow1._812 + 2*FerantDHTMLWindow1._797;
	else
		FerantDHTMLWindow1._766.style.height=FerantDHTMLWindow1._812;
		
	FerantDHTMLWindow1._766.style.borderColor = FerantDHTMLWindow1._798; 	
	FerantDHTMLWindow1._766.style.borderWidth=FerantDHTMLWindow1._797;
 
	if(FerantDHTMLWindow1._757 != '') 
		FerantDHTMLWindow1._802.innerHTML = FerantDHTMLWindow1._757; 
	else 
		FerantDHTMLWindow1._802.innerHTML = FerantDHTMLWindow1._791.replace(/</g,'&lt;').replace(/>/g,'&gt;');
		
	if(FerantDHTMLWindow1._756 != '') 
		FerantDHTMLWindow1._766.innerHTML = FerantDHTMLWindow1._756; 
	else 
		FerantDHTMLWindow1._766.innerHTML = FerantDHTMLWindow1._759.replace(/</g,'&lt;').replace(/>/g,'&gt;');

	FerantDHTMLWindow1._755.style.top = parseInt(FerantDHTMLWindow1._812) - parseInt(FerantDHTMLWindow1._814) + "px";
	FerantDHTMLWindow1._755.style.height = FerantDHTMLWindow1._814 + "px";
	FerantDHTMLWindow1._755.style.width = FerantDHTMLWindow1._813 + "px";

	FerantDHTMLWindow1._755.innerHTML = "<img src='" + FerantDHTMLWindow1._754 + "' height = " + FerantDHTMLWindow1._814 + 
									" width=" + FerantDHTMLWindow1._813 + " id='_753'>";
}

function _509(){

	if(FerantDHTMLWindow1._808.style.display == 'none') return;
	
	_907 = true;
	FerantDHTMLWindow1.OpenWindow();
	_907 = false;
	
}

function _704(x){

	for(j = x.length -1;j>=0;j--){
		if(x.charAt(j)!=' ') break; 
	 }
	 
	return x.substr(0,j+1);	 
}

function _705(x){

	for(i=0; i<x.length;i++){
		if(x.charAt(i)!=' ') break;	 
   }
	 
	return x.substr(i,x.length);	 
}

function _507(x){

	return _705(_704(x));
}

var _347 = new Array(
'transparent',
'aliceblue',
'antiquewhite',
'aqua',
'aquamarine',
'azure',
'beige',
'bisque',
'black',
'blanchedalmond',
'blue',
'blueviolet',
'brown',
'burlywood',
'cadetblue',
'chartreuse',
'chocolate',
'coral',
'cornflowerblue',
'cornsilk',
'crimson',
'cyan',
'darkblue',
'darkcyan',
'darkgoldenrod',
'darkgray',
'darkgreen',
'darkkhaki',
'darkmagenta',
'darkolivegreen',
'darkorange',
'darkorchid',
'darkred',
'darksalmon',
'darkseagreen',
'darkslateblue',
'darkslategray',
'darkturquoise',
'darkviolet',
'deeppink',
'deepskyblue',
'dimgray',
'dodgerblue',
'firebrick',
'floralwhite',
'forestgreen',
'fuchsia',
'gainsboro',
'ghostwhite',
'gold',
'goldenrod',
'gray',
'green',
'greenyellow',
'honeydew',
'hotpink',
'indianred',
'indigo',
'ivory',
'khaki',
'lavender',
'lavenderblush',
'lawngreen',
'lemonchiffon',
'lightblue',
'lightcoral',
'lightcyan',
'lightgoldenrodyellow',
'lightgreen',
'lightgrey',
'lightpink',
'lightsalmon',
'lightseagreen', 
'lightskyblue',
'lightslategray',
'lightsteelblue',
'lightyellow',
'lime',
'limegreen',
'linen',
'magenta',
'maroon',
'mediumaquamarine',
'mediumblue',
'mediumorchid',
'mediumpurple',
'mediumseagreen',
'mediumslateblue',
'mediumspringgreen',
'mediumturquoise',
'mediumvioletred',
'midnightblue',
'mintcream',
'mistyrose',
'moccasin',
'navajowhite',
'navy',
'oldlace',
'olive',
'olivedrab',
'orange',
'orangered',
'orchid',
'palegoldenrod',
'palegreen', 
'paleturquoise',
'palevioletred',
'papayawhip',
'peachpuff',
'peru',
'pink',
'plum',
'powderblue', 
'purple',
'red',
'rosybrown',
'royalblue',
'saddlebrown',
'salmon',
'sandybrown',
'seagreen',
'seashell',
'sienna',
'silver',
'skyblue',
'slateblue',
'slategray',
'snow',
'springgreen',
'steelblue',
'tan',
'teal',
'thistle',
'tomato',
'turquoise',
'violet',
'wheat',
'white',
'whitesmoke',
'yellow',
'yellowgreen');

function _506(y){
	var x = y.toLowerCase();
	var k = _347.length;
	var hex = '0123456789abcdef';
	
	for(i=0;i<k;i++)
		if(x==_347[i]) return false;
	
	if(x.length!=7){	
		alert('Error. \n\nInvalid Color!');
		return true;
	}
	
	if(x.charAt(0)!='#'){	
		alert('Error. \n\nInvalid Color!');
		return true;
	}
	
	for(i=1;i<=6;i++){
		if(hex.indexOf(x.charAt(i))==-1){
			alert('Error. \n\nInvalid Color!' + x.charAt(i));
			return true;			
		}
	}	
		
	return false;
}

function _501(x){

	if(_507(x)== parseInt(x))
		return true;
	else
		return false;

}

function Step2(){

	document.getElementById('menu2').style.display='block';
	document.getElementById('menu3').style.display='none';
	
	document.getElementById('navig2').style.display='block';
	document.getElementById('navig3').style.display='none';
	
	document.getElementById('iblock2').style.display='block';
	document.getElementById('iblock3').style.display='none';

	document.getElementById('div' + propOld).style.display ='block';	
	document.getElementById('div' + b2propOld).style.display ='none';
		 
}

function Step3(){ 

	document.getElementById('menu2').style.display='none';
	document.getElementById('menu3').style.display='block';	
	document.getElementById('navig2').style.display='none';
	document.getElementById('navig3').style.display='block';	
	document.getElementById('iblock2').style.display='none';
	document.getElementById('iblock3').style.display='block';	
	document.getElementById('div' + propOld).style.display ='none';
	document.getElementById('div' + b2propOld).style.display ='block';
	
	_354();
}