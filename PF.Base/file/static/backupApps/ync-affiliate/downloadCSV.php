<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 2/24/17
 * Time: 15:19
 */


include "cli.php";

@ini_set('display_startup_errors',1);
@ini_set('display_errors',1);
@ini_set('error_reporting',-1);

$iUserId = $_GET['id'];
if(!$iUserId)
{
    return Phpfox::getLib('url')->send($_SERVER['HTTP_REFERER'], null,_p('Unable to find user'));
}
$aClients = Phpfox::getService('yncaffiliate.affiliate.affiliate')->getFattenClients($iUserId);
$aUser = Phpfox::getService('user')->getUser($iUserId);
$aFinalData = [];
foreach ($aClients as $aClient) {
    $aFinalData[] = [
        $aClient['user_name'],
        $aClient['level'],
        $aClient['user_group'],
        $aClient['registration_date'],
        $aClient['total_client'],
        $aClient['email']
    ];
}
$sFileName = 'Affiliate_' . $iUserId . '.csv';

$sFile = Phpfox::getParam('core.dir_file') . 'yncaffiliate' . PHPFOX_DS . md5($sFileName . PHPFOX_TIME . uniqid()) . '.csv';
if ( false !== $sFileName )
{
    touch( $sFileName );
    chmod( $sFileName, 0777 );
}
$handle = fopen( $sFileName, "w" );
$titleRow = array(
    _p('name_l'), _p('level_l'), _p('user_group'), _p('registration_date'), _p('total_affiliates'),_p('email')
);
fputcsv( $handle, $titleRow);
foreach ( $aFinalData as $finalRow )
{
    fputcsv( $handle, $finalRow);
}
fclose($handle);
$sNewName = _p('network_clients_of').' '.$aUser['full_name'].'.csv';
header("Content-Type: application/csv; charset=UTF-8");
header("Content-Disposition: attachment; filename=".$sNewName);
header("Content-Transfer-Encoding: binary");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Pragma: public");
header("Content-Length: " . filesize( $sFileName ));
header("Expires: 0");

readfile($sFileName);
unlink($sFileName);
