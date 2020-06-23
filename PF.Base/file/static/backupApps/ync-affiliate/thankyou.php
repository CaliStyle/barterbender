<?php

include 'cli.php';

$aParams = $_GET + $_POST;

if(isset($aParams['sLocation'])){
	$sLocation = $_GET['sLocation'];
	$sUrl = urldecode($sLocation);
	header('Location: ' . $sUrl);
}

print_r($aParams); die;


?>