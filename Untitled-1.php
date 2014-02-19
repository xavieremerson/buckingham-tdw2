<script type="text/javascript">
<!--
function MM_jumpMenuGo(objId,targ,restore){ //v9.0
  var selObj = null;  with (document) { 
  if (getElementById) selObj = getElementById(objId);
  if (selObj) eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0; }
}
//-->
</script>
<form name="form" id="form">
  <select name="jumpMenu" id="jumpMenu">
    <option>item1</option>
  </select>
  <input type="button" name="go_button" id= "go_button" value="Go" onClick="MM_jumpMenuGo('jumpMenu','parent',0)">
</form>
