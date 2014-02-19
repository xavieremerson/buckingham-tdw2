<script language="JavaScript" src="includes/prototype/prototype.js"></script>
<script language="javascript">
function getClientData()
{
  var url = 'http://192.168.20.63/tdw/client_master_test_ajx.php';
  var pars = 'user_id=79';
  pars = pars + '&mod_request=appr';
  var ran_number= Math.random()*5; 
	pars = pars + '&xrand=' + ran_number;

  var newurl = url + "?" + pars;
	$("content_area").src = newurl;
}
</script>
<input type="button" onClick="getClientData()" name="test" value="test">

    <iframe src="" id="content_area" scrolling="auto" height="100%" width="100%" style="border:none"></iframe>
<!--    <div id="content_area" name="content_area">

		</div>-->