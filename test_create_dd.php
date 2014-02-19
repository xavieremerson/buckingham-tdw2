<?

  include('includes/dbconnect.php');
  include('includes/global.php'); 
	include('includes/functions.php');

////
// Creates a dropdown option values with recordset
function create_option_values($data_query) {
	$result = mysql_query($data_query) or die(mysql_error("Function create_option_values has errors"));
	while ($row = mysql_fetch_array($result)) {
		echo '<option value="' . $row["d_value"] . '">' . $row["d_option"] . '</option>'."\n";
	}
}
?>

<select name="sel_emp" id="sel_emp" size="1">
	<option value="" selected>Select Employee</option>
	<?=create_option_values("select ID as d_value, Fullname as d_option from users where user_isactive = 1 order by Fullname")?>
</select>