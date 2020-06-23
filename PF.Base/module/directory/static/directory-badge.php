<?php
include 'cli.php';
$iBusinessId = isset($_REQUEST['business_id']) ? $_REQUEST['business_id'] : 0;
$oAjax = Phpfox::getLib('ajax');
Phpfox::getBlock('directory.directorybadge', array('iBusinessId' => $iBusinessId));
$sContent = $oAjax->getContent();
$sContent =  stripslashes($sContent);
$sCorePath = Phpfox::getService('directory') -> getStaticPath();
?>

<head>
</head>
<body style="font-family: 'lucida grande',tahoma,verdana,arial,sans-serif; font-size: 11px;">
	<div class="yndirectory-cuz-body-promote">
		<?php echo $sContent; ?>
	</div>
</body>

<?php
ob_flush();