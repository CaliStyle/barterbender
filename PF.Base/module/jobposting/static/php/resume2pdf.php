<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('libs/mpdf/mpdf.php');

$file = $_POST['file'];
$html = base64_decode($_POST['html']);
$css = $_POST['css'];

$mpdf = new mPDF();

$mpdf->useAdobeCJK = true;
$mpdf->autoScriptToLang = true;
$mpdf->autoLangToFont = true;

// LOAD a stylesheet
$mpdf->WriteHTML($css, 1);	// The parameter 1 tells that this is css/style only and no body/html/text

$mpdf->WriteHTML($html);

$mpdf->Output($file, 'F');

exit;

