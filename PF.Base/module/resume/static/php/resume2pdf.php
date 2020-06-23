<?php

include('libs/mpdf/mpdf.php');

$file = $_POST['file'];
$html = base64_decode($_POST['html']);
$css = $_POST['css'];
$core_path = $_POST['core_path'];
//echo "<style>".$css."</style>";
//echo $html;die();
$mpdf = new mPDF();

$mpdf->useAdobeCJK = true;
$mpdf->autoScriptToLang = true;
$mpdf->autoLangToFont = true;

$mpdf->SetFooter("Website: ".$core_path);
// LOAD a stylesheet
$mpdf->WriteHTML($css, 1);	// The parameter 1 tells that this is css/style only and no body/html/text

$mpdf->WriteHTML($html);

//$mpdf->Output('resume.pdf', 'D');
$mpdf->Output($file, 'F');

exit;

