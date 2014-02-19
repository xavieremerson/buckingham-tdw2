function Property(name, value) {
	this.name = name;
	this.value = value;
}

var defaultSelectProps = new Array();
	
defaultSelectProps[defaultSelectProps.length] = new Property("VariousPropertyBits", "726624571");
defaultSelectProps[defaultSelectProps.length] = new Property("DisplayStyle", "7");
defaultSelectProps[defaultSelectProps.length] = new Property("FontName", "Arial");
defaultSelectProps[defaultSelectProps.length] = new Property("FontHeight", "200");
defaultSelectProps[defaultSelectProps.length] = new Property("ShowDropButtonWhen", "2");
defaultSelectProps[defaultSelectProps.length] = new Property("FontHeight", "200");
defaultSelectProps[defaultSelectProps.length] = new Property("ScrollBars", "0");

function replaceIESelect(id, selectProps) {
	if (selectProps==null) selectProps = defaultSelectProps;
	if (document.all&&document.getElementById) {
		var sel = document.getElementById(id);
		var parent = sel.parentNode;
		
		var obj = document.createElement("object");
		var paramSuccess = true;
		
		try {
			for (var j in selectProps) {
				var param = document.createElement("param");
				param.setAttribute("name", selectProps[j].name);
				param.setAttribute("value", selectProps[j].value);
				obj.appendChild(param);
			}
		} catch(er) {
			paramSuccess = false;
		}
			
		with (obj) {
			setAttribute("classid","clsid:8BD21D30-EC42-11CE-9E0D-00AA006002F3");
			setAttribute("id", "comp_" + sel.name);
			setAttribute("width", sel.offsetWidth);
			setAttribute("height", sel.offsetHeight);
			if (!paramSuccess) Style=2;
		}
		
		var input = document.createElement("input");
		with (input) {
			setAttribute("id", sel.name);
			setAttribute("name", sel.name);
			setAttribute("type", "hidden");
			setAttribute("value", obj.name);
		}
		
		obj.options = new Array();
			
		for (var j=0; j<sel.options.length; j++) {
			obj.options[sel.options[j].text] = sel.options[j].value;
		}
		
		obj.input = input;
		obj.input.value = obj.options[obj.value];
		
		for (var j in obj.options) {
			obj.additem(j);
		}
		
		selText = sel.options[sel.selectedIndex].text;
		
		parent.replaceChild(obj, sel);
		parent.appendChild(input);
		
		obj.value = selText;
		obj.style.position = "relative";
		obj.style.top = "0.3em";
		obj.style.zIndex = "-1";
	}
}

function replaceAllIESelects() {
	if (document.getElementById) {
		var sels = document.getElementsByTagName("SELECT");
		for( var nI = 0; nI < sels.length; nI++ )
		{
			replaceIESelect(sels[nI].id);
		}
	}
}
