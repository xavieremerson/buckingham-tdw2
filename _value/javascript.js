// JavaScript Document
function seq_get_data() {

		
		for (i=0;i<($("count_total").value);i++)
		{
			//alert(i + ">>" + $("data_"+i).value)
			
			//$("temp_val").innerHTML = $("data_"+i).value;
			if (i < 10000) {
				get_price(i, $("data_"+i).value);
			}
			//alert(">");

		}
		
}


function get_price(id, str_input)
{
	//alert("test");
	//return false;
	var url = 'http://192.168.20.63/tdw/_value/response_ajax.php';

	var pars = "str_input=" + str_input;
	pars = pars + '&id=' + id;
	pars = pars + '&mod_request=d';
  var ran_number= Math.random()*5; 
	pars = pars + '&xrand=' + ran_number;
  
	//alert(pars);
	//return false;
  new Ajax.Request
	(
		url,   
		{     
			method:'get', 
			parameters:pars,    
			onSuccess: 
				function(transport){       
					var response = transport.responseText;       

					//alert(response);
					
					var arr_response = new Array();
					arr_response=response.split("^");
					
					$("desc_" + arr_response[0]).innerHTML = arr_response[1];
					
					if (arr_response[2] != "0.00" && !arr_response[3]) {
						$("price_" + arr_response[0]).innerHTML = arr_response[2];
						$("price_val_" + arr_response[0]).value = arr_response[2]; 
						$("desc_val_" + arr_response[0]).value = arr_response[1]; 
					} else if (arr_response[2] != "0.00" && arr_response[3] != "") {
					  $("price_" + arr_response[0]).innerHTML = '<input type="text" size="10" id="price_' + arr_response[0] + '" name="price_' + arr_response[0] + '" value="' + arr_response[2] + '">';
						$("desc_val_" + arr_response[0]).value = arr_response[1]; 
						$("tr_" + arr_response[0]).setAttribute("className", "missing"); 
						$("comment_" + arr_response[0]).innerHTML = arr_response[3];
					} else {
					  $("price_" + arr_response[0]).innerHTML = '<input type="text" size="10" id="price_' + arr_response[0] + '" name="price_' + arr_response[0] + '" value="">';
						$("desc_val_" + arr_response[0]).value = arr_response[1]; 
						$("tr_" + arr_response[0]).setAttribute("className", "missing"); 
						$("comment_" + arr_response[0]).innerHTML = arr_response[3];
					}
					
					
          /*
					//hide the order submit section
					$("trd_order").style.display = "block";
					$("trd_order").style.visibility = "none";

					//show the trades section
					$("section_orders").style.display = "block";
					$("section_orders").style.visibility = "visible";*/
										
				},     
			onFailure: 
			function(){ alert('Something went wrong...') }
		}
	);

  /*
  //document.getElementById("abc").innerHTML = pars;
	var myAjax = new Ajax.Updater(
				{success:	'trd_container'}, 
				url, 
				{
					method: 'get', 
					parameters: pars, 
					onFailure: reportError
				});
  */
}
