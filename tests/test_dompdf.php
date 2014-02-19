<?
  include('includes/dbconnect.php');
  include('includes/global.php');
	include('includes/functions.php');

  require_once("includes/dompdf/dompdf_config.inc.php");

$html =
  '<html><body>'.
  '<p>Put your html here, or generate it with your favourite '.
  'templating system.</p>'.
  '</body></html>';

$dompdf = new DOMPDF();
$dompdf->load_html($html);
$dompdf->render();
$dompdf->stream("sample.pdf");

?>
