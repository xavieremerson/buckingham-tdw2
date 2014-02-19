<script language="javascript">

  //prevent form submit on pressing enter
	function noenter() {
  	return !(window.event && window.event.keyCode == 13); 
	}


	function check_frm_criteria() {
	//alert("Are you sure?");
	return true;
	}

	function selitem(itemid) {
		document.getElementById(itemid).select();
	}

 	/* 37:"Arrow Left"; 	38:"Arrow Up";	39:"Arrow Right";	40:"Arrow Down";	*/
			
	function xlmove(evt, itemid){
	//alert(itemid);
	var k=evt.keyCode;
	 if (k==40) {
		var rc = itemid.split("|");
		var nextid;
		nextid = (parseInt(rc[0]) + 1) + "|" + rc[1];

			if (document.getElementById(nextid)) {
				document.getElementById(nextid).focus();
				document.getElementById(nextid).select();
			} else {
				nextid = 1 + "|" + rc[1];
				document.getElementById(nextid).focus();
				document.getElementById(nextid).select();
			}

	 } else if (k==38) { 
		var rc = itemid.split("|");
		var nextid;
		nextid = (parseInt(rc[0]) - 1) + "|" + rc[1];

			if (document.getElementById(nextid)) {
				document.getElementById(nextid).focus();
				document.getElementById(nextid).select();
			} else {
				nextid = 1 + "|" + rc[1];
				document.getElementById(nextid).focus();
				document.getElementById(nextid).select();
			}

	 } else if (k==39) { 
		var rc = itemid.split("|");
		var nextid;
		nextid = rc[0] + "|" + (parseInt(rc[1]) + 1);

			if (document.getElementById(nextid)) {
				document.getElementById(nextid).focus();
				document.getElementById(nextid).select();
			} else {
				nextid = 1 + "|" + rc[1];
				document.getElementById(nextid).focus();
				document.getElementById(nextid).select();
			}

	 } else if (k==37) { 
		var rc = itemid.split("|");
		var nextid;
		nextid = rc[0] + "|" + (parseInt(rc[1]) - 1);

			if (document.getElementById(nextid)) {
				document.getElementById(nextid).focus();
				document.getElementById(nextid).select();
			} else {
				nextid = 1 + "|" + rc[1];
				document.getElementById(nextid).focus();
				document.getElementById(nextid).select();
			}

	 } else if (k==13) { 
		var rc = itemid.split("|");
		var nextid;
		nextid = (parseInt(rc[0]) + 1) + "|" + rc[1];

			if (document.getElementById(nextid)) {
				document.getElementById(nextid).focus();
				document.getElementById(nextid).select();
			} else {
				nextid = 1 + "|" + rc[1];
				document.getElementById(nextid).focus();
				document.getElementById(nextid).select();
			}

	 }
	return k!=13;
	}

	function xlrecalc(itemid){

		var rcount = parseInt(document.getElementById('id_rcount').value);
		var ccount = parseInt(document.getElementById('id_ccount').value);
		var rc = itemid.split("|");
		var crow, ccol, ctotal, currid, nextid, totalid, totalanalystid;
		crow = parseInt(rc[0]);
		ccol = parseInt(rc[1]);
		ctotal = 0;
		for (i=1; i < rcount + 1 ; i++) {
			nextid = i+"|"+ccol;
			if (document.getElementById(nextid)) {
			  ctotal = ctotal + parseFloat(document.getElementById(nextid).value);
			}
		}
		
		//convert ctotal to 2 decimals
		ctotal = ctotal.toFixed(2)
		
		//totals for the column, must add to 100%
		totalid = "total|"+ ccol;
		if (isNaN(ctotal)) ctotal = 'Error';
		
		document.getElementById(totalid).innerHTML = ctotal +"%";
		if (ctotal == 100) {
			document.getElementById(totalid).className  = "valgreen";
		} else {
			document.getElementById(totalid).className  = "valred";
		}

		//put total for analyst.
		id_totalanalyst = "at|"+ crow;
		id_coltotalval = "tot|" + ccol;

		var val_analyst_total=0;
    var id_cfat; // cfat  coltotalval for analyst total
		for (k=1; k<(ccount+1); k++) {
			id_cur_item = crow + "|" + k;
			id_cfat = "tot|" + k;
			//alert(id_cur_item + " >> " + val_analyst_total + " >> " + parseInt(document.getElementById(id_cur_item).value) / 100 + " >> " +  parseFloat(document.getElementById(id_cfat).innerHTML.replace(",","")) );
					//alert(parseFloat(document.getElementById(id_cfat).innerHTML.replace(/,/g,"")));

			val_analyst_total = val_analyst_total + ( ( parseFloat(document.getElementById(id_cur_item).value) / 100 ) * parseFloat(document.getElementById(id_cfat).innerHTML.replace(/,/g,"")) );
		}

		//if (isNaN(val_analyst_total)) val_analyst_total = 'Error';  //on error the previous value is preserved.
		//document.getElementById(id_totalanalyst).innerHTML  = addCommas(val_analyst_total.toFixed(2));

		cursat_id = "sat|" + crow;
		document.getElementById(cursat_id).innerHTML  = addCommas(val_analyst_total.toFixed(2));
		//alert(addCommas(val_analyst_total.toFixed(2)));
		//get the total as a percentage.
		gtotal_val =  parseFloat(document.getElementById('id_gtotal').value);
		percent_of_total = ((val_analyst_total / gtotal_val)*100).toFixed(2);
		idval_sap = 'sap|' + crow;
		document.getElementById(idval_sap).innerHTML  = percent_of_total;
		

		//put the calc val
		curval_id = "curnum|" + itemid;
		curval_val = addCommas((( parseFloat(document.getElementById(itemid).value) / 100 ) * parseFloat(document.getElementById(id_coltotalval).innerHTML.replace(/,/g,""))).toFixed(2));
		//alert(curval_id + "..." + curval_val);
		if (curval_val != "0.00") {
			document.getElementById(curval_id).innerHTML  = curval_val;
		} else {
			document.getElementById(curval_id).innerHTML  = "";
		}
		
		//populate X
		var val_summ_analyst_total=0;
		for (k=1; k<(rcount+1); k++) { //sat|8
			id_cur_item = "sat|" + k;
			val_summ_analyst_total = val_summ_analyst_total + parseFloat(document.getElementById(id_cur_item).innerHTML.replace(/,/g,""));
			//alert(val_summ_analyst_total);
		}
		document.getElementById('sum_sat_total').innerHTML  = addCommas(val_summ_analyst_total.toFixed(2));
				
		//populate Y
		var val_summ_percent_total=0;
		for (k=1; k<(rcount+1); k++) { //sat|8
			id_cur_item = "sap|" + k;
			val_summ_percent_total = val_summ_percent_total + parseFloat(document.getElementById(id_cur_item).innerHTML.replace(/,/g,""));
			//alert(val_summ_analyst_total);
		}
		document.getElementById('sum_sap_total').innerHTML  = val_summ_percent_total.toFixed(0);
		
		return false;
	}

  //**************************************************************************************************
  //**************************************************************************************************
  //**************************************************************************************************
  //**************************************************************************************************
  //**************************************************************************************************
	function xlrecalcform(){  // recalc entire form on load and polulate
    
		var rcount = parseInt(document.getElementById('id_rcount').value);
		var ccount = parseInt(document.getElementById('id_ccount').value);
		
		var	itemid = new Array();
	  var k = 0;
		for ( i=1; i < rcount+1; i++ ) {
			for ( j=1; j < ccount+1; j++ ) {
				itemid[k] = i+"|"+j;
				k = k + 1;
			}
		}
		
		//alert("Size of data =" + itemid.length);	
		
		//*********************************************************************
		for (ccol=1; ccol < ccount + 1 ; ccol++) {
				var ctotal = 0;
				for (i=1; i < rcount + 1 ; i++) {
					nextid = i+"|"+ccol;
					if (document.getElementById(nextid)) {
						ctotal = ctotal + parseFloat(document.getElementById(nextid).value);
					}
				}
			
				//convert ctotal to 2 decimals
				ctotal = ctotal.toFixed(2)

				//totals for the column, must add to 100%
				totalid = "total|"+ ccol;
				if (isNaN(ctotal)) ctotal = 'Error';
				
				document.getElementById(totalid).innerHTML = ctotal + "%";
				if (ctotal == 100) {
					document.getElementById(totalid).className  = "valgreen";
				} else {
					document.getElementById(totalid).className  = "valred";
				}
		}
		//*********************************************************************
		
		//__<>__<>__<>__<>__<>__<>__<>__<>__<>__<>__<>__<>__<>__<>__<>__<>__<>__<>
		//put total for analyst.
		var id_cur_item, id_cfat;
		for (crow=1; crow<rcount+1; crow++) {
		var val_analyst_total = 0;
			
				id_totalanalyst = "at|"+ crow;
			
						for (k=1; k<(ccount+1); k++) {
						  id_coltotalval = "tot|" + k;
							id_cur_item = crow + "|" + k;
							id_cfat = "tot|" + k;
							//alert(id_cur_item + " >> " + val_analyst_total + " >> " + parseInt(document.getElementById(id_cur_item).value) / 100 + " >> " +  parseFloat(document.getElementById(id_cfat).innerHTML.replace(",","")) );
							val_analyst_total = val_analyst_total + ( ( parseFloat(document.getElementById(id_cur_item).value) / 100 ) * parseFloat(document.getElementById(id_cfat).innerHTML.replace(/,/g,"")) );

							//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
							//put the calc val
							var curval_id, itemid;
							itemid = crow + "|" + k;
							curval_id = "curnum|" + itemid;
							
							curval_val = addCommas((( parseFloat(document.getElementById(itemid).value) / 100 ) * parseFloat(document.getElementById(id_coltotalval).innerHTML.replace(/,/g,""))).toFixed(2));
							//alert(curval_id + "..." + curval_val);
							if (curval_val != "0.00") {
								document.getElementById(curval_id).innerHTML  = curval_val;
							} else {
								document.getElementById(curval_id).innerHTML  = "";
							}
							//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
						}
				
						//if (isNaN(val_analyst_total)) val_analyst_total = 'Error';  //on error the previous value is preserved.
						//document.getElementById(id_totalanalyst).innerHTML  = addCommas(val_analyst_total.toFixed(2));
				
						cursat_id = "sat|" + crow;
						document.getElementById(cursat_id).innerHTML  = addCommas(val_analyst_total.toFixed(2));
				
				//get the total as a percentage.
				gtotal_val =  parseFloat(document.getElementById('id_gtotal').value);
				percent_of_total = ((val_analyst_total / gtotal_val)*100).toFixed(2);
				idval_sap = 'sap|' + crow;
				document.getElementById(idval_sap).innerHTML  = percent_of_total;
		}
		//__<>__<>__<>__<>__<>__<>__<>__<>__<>__<>__<>__<>__<>__<>__<>__<>__<>__<>

		//__^__^__^__^__^__^__^__^__^__^__^__^__^__^__^__^__^__^__^__^__^__^__^
				//populate X
				var val_summ_analyst_total=0;
				for (k=1; k<(rcount+1); k++) { //sat|8
					id_cur_item = "sat|" + k;
					val_summ_analyst_total = val_summ_analyst_total + parseFloat(document.getElementById(id_cur_item).innerHTML.replace(/,/g,""));
					//alert(parseFloat(document.getElementById(id_cur_item).innerHTML.replace(",","")) + "/" + val_summ_analyst_total);
				}
				document.getElementById('sum_sat_total').innerHTML  = addCommas(val_summ_analyst_total.toFixed(2));
						
				//populate Y
				var val_summ_percent_total=0;
				for (k=1; k<(rcount+1); k++) { //sat|8
					id_cur_item = "sap|" + k;
					val_summ_percent_total = val_summ_percent_total + parseFloat(document.getElementById(id_cur_item).innerHTML.replace(/,/g,""));
					//alert(val_summ_analyst_total);
				}
				document.getElementById('sum_sap_total').innerHTML  = val_summ_percent_total.toFixed(0);
		//__^__^__^__^__^__^__^__^__^__^__^__^__^__^__^__^__^__^__^__^__^__^__^

		return false;
	}
  //**************************************************************************************************
  //**************************************************************************************************
  //**************************************************************************************************
  //**************************************************************************************************
  //**************************************************************************************************


function addCommas(nStr) //addCommas
{
	nStr += '';
	x = nStr.split('.');
	x1 = x[0];
	x2 = x.length > 1 ? '.' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1)) {
		x1 = x1.replace(rgx, '$1' + ',' + '$2');
	}
	return x1 + x2;
}

function zzzaddCommas(number) {
number = '' + number;
if (number.length > 3) {
var mod = number.length % 3;
var output = (mod > 0 ? (number.substring(0,mod)) : '');
for (i=0 ; i < Math.floor(number.length / 3); i++) {
if ((mod == 0) && (i == 0))
output += number.substring(mod+ 3 * i, mod + 3 * i + 3);
else
output+= ',' + number.substring(mod + 3 * i, mod + 3 * i + 3);
}
return (output);
}
else return number;
}
</script>