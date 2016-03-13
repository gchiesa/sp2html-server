<?
require_once("dompdf_config.inc.php");

$html = file_get_contents('test.html');

$dompdf = new DOMPDF();
$dompdf->set_paper(array(0, 0, 680.00, 962.00), 'portrait');
//$dompdf->set_paper('a4', 'portrait');
echo "qui...\n";
$dompdf->load_html($html);
$dompdf->render();

file_put_contents('test.pdf', $dompdf->output());
?>