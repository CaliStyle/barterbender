<?php

include 'cli.php';

$iContestId = isset($_REQUEST['contest_id']) ? $_REQUEST['contest_id'] : 0;
$iStatus = isset($_REQUEST['status']) ? $_REQUEST['status'] : 0;

$oAjax = Phpfox::getLib('ajax');

Phpfox::getBlock('contest.contest.contest-badge', array('contest_id' => $iContestId, 'status' => $iStatus));

$sContent = $oAjax->getContent();
$sContent =  stripslashes($sContent);
$sCorePath = Phpfox::getParam('core.path');

?>

<head>
</head>
<link rel="stylesheet" type="text/css" href="<?php echo $sCorePath; ?>/module/contest/static/css/default/default/yncontest.css" />
<body style="font-family: 'lucida grande',tahoma,verdana,arial,sans-serif;
font-size: 11px;">
	<div style="width:250px">
		<?php echo $sContent; ?>
	</div>
</body>



<?php
ob_flush();