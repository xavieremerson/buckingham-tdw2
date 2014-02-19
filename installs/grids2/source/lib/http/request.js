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

AW.HTTP.Request = AW.System.Model.subclass();

AW.HTTP.Request.create = function(){

/****************************************************************

	Generic HTTP request class.

*****************************************************************/

	var obj = this.prototype;

/****************************************************************

	Sets or retrieves the remote data URL.

*****************************************************************/

	obj.defineProperty("URL");

/****************************************************************

	Indicates whether asynchronous download is permitted.

*****************************************************************/

	obj.defineProperty("async", true);

/****************************************************************

	Specifies HTTP request method.

*****************************************************************/

	obj.defineProperty("requestMethod", "GET");

/****************************************************************

	Allows to send data with the request.

*****************************************************************/

	obj.defineProperty("requestData", "");

/****************************************************************

	Returns response text.

*****************************************************************/

	obj.defineProperty("responseText", function(){return this._http ? this._http.responseText : ""});

/****************************************************************

	Returns response XML.

*****************************************************************/

	obj.defineProperty("responseXML", function(){return this._http ? this._http.responseXML : ""});

/****************************************************************

	Sets or retrieves the user name.

*****************************************************************/

	obj.defineProperty("username", null);

/****************************************************************

	Sets or retrieves the password.

*****************************************************************/

	obj.defineProperty("password", null);

/****************************************************************

	Allows to specify namespaces for use in XPath expressions.

	@param name (String) The namespace alias.
	@param value (String) The namespace URL.

*****************************************************************/

	obj.setNamespace = function(name, value){
		this._namespaces += " xmlns:" + name + "=\"" + value + "\"";
	};

	obj._namespaces = "";

/****************************************************************

	Allows to specify the request arguments/parameters.

	@param name (String) The parameter name.
	@param value (String) The parameter value.

*****************************************************************/

	obj.setParameter = function(name, value){
		this["_" + name + "Parameter"] = value;
		if (!this._parameters.match(new RegExp(" " + name + "( |$)"))) {this._parameters += " " + name}
	};

	obj._parameters = "";

/****************************************************************

	Sets HTTP request header.

	@param name (String) The request header name.
	@param value (String) The request header value.

*****************************************************************/

	obj.setRequestHeader = function(name, value){
		this["_" + name + "Header"] = value;
		if (!this._headers.match(new RegExp(" " + name + "( |$)"))) {this._headers += " " + name}
	};

	obj._headers = "";

	obj.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

/****************************************************************

	Returns HTTP response header (for example "Content-Type").

*****************************************************************/

	obj.getResponseHeader = function(name){
		return this._http ? this._http.getResponseHeader(name) : "";
	};


/****************************************************************

	Sends the request.

*****************************************************************/

	obj.request = function(){
		var self = this;

		this._ready = false;

		var i, j, name, value, data = "", params = this._parameters.split(" ");
		for (i=1; i<params.length; i++){
			name = params[i];
			value = this["_" + name + "Parameter"];
			if (typeof value == "function") { value = value(); }
			if (typeof value == "object" && value.constructor == Array){
				for (j=0; j<value.length; j++){
					data += name + "=" + encodeURIComponent(value[j]) + "&";
				}
			}
			else {
				data += name + "=" + encodeURIComponent(value) + "&";
			}
		}

		var URL = this._URL;

		if ((this._requestMethod != "POST") && data) {
			URL += "?" + data;
			data = null;
		}

		this._http = AW.createXMLHttpRequest();
		this._http.open(this._requestMethod, URL, this._async, this._username, this._password);

		var headers = this._headers.split(" ");
		for (i=1; i<headers.length; i++){
			name = headers[i];
			value = this["_" + name + "Header"];
			if (typeof value == "function") { value = value(); }
			this._http.setRequestHeader(name, value);
		}

		this._http.send(data);

		if (this._async) {
			this.setTimeout(wait, 200);
		}
		else {
			returnResult();
		}

		function wait(){
			if (self._http.readyState == 4) {
				self._ready = true;
				returnResult();
			}
			else {
				self.setTimeout(wait, 200);
			}
		}

		function returnResult(){
			var xml = self._http.responseXML;

			if (xml && xml.firstChild && xml.hasChildNodes() &&
				  !(xml.firstChild &&
					xml.firstChild.firstChild &&
					xml.firstChild.firstChild.firstChild &&
					xml.firstChild.firstChild.firstChild.nodeName == "parsererror")) {
				self.response(xml);
				xml = null;
				return;
			}

			xml = null;
			self.response(self._http.responseText);
		}
	};

/****************************************************************

	Allows to process the received data.

	@param result (Object) The downloaded data (XML DOMDocument object).

*****************************************************************/

	obj.response = function(result){
		if (this.$owner) {this.$owner.refresh()}
	};

/****************************************************************

	Indicates whether the request is already completed.

*****************************************************************/

	obj.isReady = function(){
		return this._ready;
	};

};

