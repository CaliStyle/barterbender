<?php
$sLocation = $_GET['sLocation'];
$sUrl = urldecode($sLocation);
//echo($sUrl);
header('Location: ' . $sUrl);
?>