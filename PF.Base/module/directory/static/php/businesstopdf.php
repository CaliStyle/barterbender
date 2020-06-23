<?php

include('libs/mpdf/mpdf.php');

$file = $_POST['file'];
// $html = $_POST['html'];
$html = base64_decode($_POST['html']);
$css = $_POST['css'];

$mpdf = new mPDF();

// LOAD a stylesheet
// $mpdf->WriteHTML($css, 1);  // The parameter 1 tells that this is css/style only and no body/html/text

$mpdf->WriteHTML($html);

//$mpdf->Output('resume.pdf', 'D');
$mpdf->Output($file, 'F');

exit;

