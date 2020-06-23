<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 20/02/2017
 * Time: 10:55
 */

if(Phpfox::isModule('socialpublishers'))
{
    $sUrl = Phpfox::permalink('ynblog', $iId, $aVals['title']);
    $sType = 'ynblog';
    $iUserId = Phpfox::getUserId();
    $sMessage = $aVals['text'];
    $aVal['text'] = $aVals['text'];
    $aVal['url'] = $sUrl;
    $aVal['content'] = $sMessage;
    $aVal['title'] = $aVals['title'];
    $bIsFrame = 3;
    $aShareFeedInsert = array(
        'sType' => 'ynblog',
        'iItemId' => $iId,
        'bIsCallback' => 3,
        'aCallback' => null,
        'iPrivacy' => (int) (isset($aVals['privacy']) ? (int) $aVals['privacy'] : 0),
        'iPrivacyComment' => 0,
    );
    $sIdCache = Phpfox::getLib('cache')->set("socialpublishers_feed_" . $iUserId);
    Phpfox::getLib('cache')->save($sIdCache,$aShareFeedInsert);
    Phpfox::getService('socialpublishers')->showPublisher($sType,$iUserId,$aVal,$bIsFrame);
}
?>