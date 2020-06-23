<?php

include '../../cli.php';


$id = $_GET['id'];
$en_photo = isset($_GET['en_photo']) ? 1 : 0;
$en_description = isset($_GET['en_desciption']) ? 1 : 0;

Phpfox::getBlock('jobposting.job.embed',
	array('id' => $id, 'en_photo' => $en_photo,
		'en_desciption' => $en_description));
$sContent = Phpfox::getLib('ajax')->getContent();
$sContent = stripslashes($sContent);

?>
<body style="font-family: 'lucida grande',tahoma,verdana,arial,sans-serif; font-size: 11px; margin: 0;">
	<div style="width:170px; text-align: center;">
		<?php echo $sContent; ?>
	</div>
</body>

<?php

ob_flush();

