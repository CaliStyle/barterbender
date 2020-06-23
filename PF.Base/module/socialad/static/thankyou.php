<?php
$sLocation = $_GET['sLocation'];
$sUrl = urldecode($sLocation);
header('Location: ' . $sUrl);
