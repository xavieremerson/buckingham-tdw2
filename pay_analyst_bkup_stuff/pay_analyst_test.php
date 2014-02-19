  //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
  //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
  //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
  //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

	function xlrecalcfinalform(){  // recalc entire form on load and polulate
    
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
		//*********************************************************************
		
		//__<>__<>__<>__<>__<>__<>__<>__<>__<>__<>__<>__<>__<>__<>__<>__<>__<>__<>
		//put total for analyst.
		var id_cur_item, id_cfat;
		for (crow=1; crow < rcount+1; crow++) {
		var val_analyst_total = 0;
			
				id_totalanalyst = "at|"+ crow;
			
						for (k=1; k<(ccount+1); k++) {
						  id_coltotalval = "tot|" + k;
							id_cur_item = crow + "|" + k;
							id_cfat = "tot|" + k;
							//alert(id_cur_item + " >> " + val_analyst_total + " >> " + parseInt(document.getElementById(id_cur_item).value) / 100 + " >> " +  parseFloat(document.getElementById(id_cfat).innerHTML.replace(",","")) );
							val_analyst_total = val_analyst_total + ( ( parseInt(document.getElementById(id_cur_item).value) / 100 ) * parseFloat(document.getElementById(id_cfat).innerHTML.replace(",","")) );

							//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
							//put the calc val
							var curval_id, itemid;
							itemid = crow + "|" + k;
							curval_id = "curnum|" + itemid;
							
							curval_val = addCommas((( parseInt(document.getElementById(itemid).value) / 100 ) * parseFloat(document.getElementById(id_coltotalval).innerHTML.replace(",",""))).toFixed(2));
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
					val_summ_analyst_total = val_summ_analyst_total + parseFloat(document.getElementById(id_cur_item).innerHTML.replace(",",""));
					//alert(val_summ_analyst_total);
				}
				document.getElementById('sum_sat_total').innerHTML  = val_summ_analyst_total.toFixed(2);
						
				//populate Y
				var val_summ_percent_total=0;
				for (k=1; k<(rcount+1); k++) { //sat|8
					id_cur_item = "sap|" + k;
					val_summ_percent_total = val_summ_percent_total + parseFloat(document.getElementById(id_cur_item).innerHTML.replace(",",""));
					//alert(val_summ_analyst_total);
				}
				document.getElementById('sum_sap_total').innerHTML  = val_summ_percent_total.toFixed(0);
		//__^__^__^__^__^__^__^__^__^__^__^__^__^__^__^__^__^__^__^__^__^__^__^


		return false;

	}


  //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
  //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
  //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
  //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@