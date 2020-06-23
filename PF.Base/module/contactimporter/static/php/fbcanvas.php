<?php

ob_start();

define('PHPFOX', TRUE);
define('PHPFOX_NO_SESSION', TRUE);
define('PHPFOX_NO_USER_SESSION', TRUE);
define('PHPFOX_DS', DIRECTORY_SEPARATOR);

define('PHPFOX_DIR', dirname(dirname(dirname(dirname(dirname(__FILE__))))) . PHPFOX_DS);

define('PHPFOX_NO_RUN', true);
define('PHPFOX_START_TIME', array_sum(explode(' ', microtime())));
// Require phpFox Init
include PHPFOX_DIR . 'start.php';

$requestIds = empty($_REQUEST['request_ids']) ? array() : explode(',', $_REQUEST['request_ids']);

$from = array();

$user_id = 0;
$arqName = array();
$arqid = array();

$requestIds = array_reverse($requestIds);

foreach ($requestIds as $rid) {
    $request = Phpfox::getService('socialbridge.provider.facebook')->getUserInfo(array('request_id' => $rid));
    if ($request && !in_array($request['from']['id'], $arqid)) {
        $message = $request['message'];
        $user_id = (!empty($request['data'])) ? $request['data'] : 0;
        $arqid[] = $request['from']['id'];
        $arqName[] = $request['from']['name'];
    }
}


$rqName = '';

foreach ($arqName as $key => $name) {
    if ($key == 0) {
        $rqName .= '<b>' . $name . '</b>';
    } elseif ($key == count($arqName) - 1) {
        $rqName .= ' ' . _p('and') . ' <b>' . $name . '</b>';
    } else {
        $rqName .= ', <b>' . $name . '</b>';
    }
}

$link = Phpfox::getLib('url')->makeUrl('contactimporter.inviteuser', array('id' => $user_id));

$site_name = '<b>' . Phpfox::getParam('core.site_title') . '</b>';

if (count($arqName) == 1) {
    $content = _p('name_invite_you_to_join_site', array('name' => $rqName, 'site' => $site_name));
} else {
    $content = _p('name_invites_you_to_join_site', array('name' => $rqName, 'site' => $site_name));
}

?>

<div style="text-align: center;">
    <?php echo $content; ?>
    <button onclick='window.open("<?php echo $link ?>"); return false;'><?php echo _p('visit_and_register') ?></button>
</div>
<div style="text-align: center;">
    <p><?php echo $message ?></p>
</div>


