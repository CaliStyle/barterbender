<?php

	
//$aParts = explode('|', $_POST['custom']);

//$aNewParts = explode(':', $aParts[1]);
//$sUrl = $aNewParts[1] . ":" .$aNewParts[2];
$sLocation = $_GET['sLocation'];
$sUrl = urldecode($sLocation);
//echo($sUrl);
header('Location: ' . $sUrl);
?>