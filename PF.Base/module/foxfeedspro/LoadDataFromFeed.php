<?php
/**
 *
 * @copyright      YouNet Company
 * @author         TienNPL
 * @package        Module_NewsFeed
 * @version        3.02p5
 * 
 */
 ?>
 
<?php
/**
 * Key to include phpFox
 *
 */
define('PHPFOX', true);
define('PHPFOX_NO_SESSION', true);
define('PHPFOX_NO_USER_SESSION', true);
ob_start();

/**
 * Directory Seperator
 *
 */
define('PHPFOX_DS', DIRECTORY_SEPARATOR);

/**
 * phpFox Root Directory
 *
 */
define('PHPFOX_DIR', dirname(dirname(dirname(__FILE__))) . PHPFOX_DS);

define('PHPFOX_NO_RUN', true);
define('PHPFOX_START_TIME', array_sum(explode(' ', microtime())));
// Require phpFox Init
include PHPFOX_DIR . 'start.php';

// Get newsfeed service
$oFoxFeedsPro = Phpfox::getService('foxfeedspro');
$oFoxFeedsProProcess = Phpfox::getService('foxfeedspro.process');


// delete news in every provider by scrubs
$oFoxFeedsProProcess->deleteNewsInProviderByScrubs();

$aConds = array("nf.is_approved = 1 AND nf.is_active = 1");
//Get total feeds
$oTotal = storage()->get('foxfeedspro/feed/total');
if (empty($oTotal)) {
    $iTotal = Phpfox::getLib('database') -> select('COUNT(*)')
        -> from(Phpfox::getT('ynnews_feeds'),'nf')
        -> where($aConds)
        -> execute('getField');
} else {
    $iTotal = $oTotal->value;
}
storage()->del('foxfeedspro/feed/total');
storage()->set('foxfeedspro/feed/total', $iTotal);

$oLastPage = storage()->get('foxfeedspro/feed/page');
if (empty($oLastPage)) {
    $iLastPage = 1;
} else {
    $iLastPage = $oLastPage->value + 1;
}
//Reset page number when go to last page
if ($iLastPage * 10 > $iTotal) {
    $iLastPage = 1;
}
storage()->del('foxfeedspro/feed/page');
storage()->set('foxfeedspro/feed/page', $iLastPage);

// Get Feeds
$aFeeds = Phpfox::getLib('database') -> select('*')
				-> from(Phpfox::getT('ynnews_feeds'),'nf')
				-> where($aConds)
                -> limit($iLastPage, 10, $iTotal)
				-> execute('getRows');
//Get Data
foreach ($aFeeds as $aFeed) 
{
	 $oFoxFeedsPro->getNews($aFeed);
	 $oFoxFeedsProProcess->updateTimeOfFeed($aFeed['feed_id']);
	 $oFoxFeedsPro->sendSubscribeNotification($aFeed);
}


echo "Get Data Successfully! (Page {$iLastPage}) <br/>";

$bIsDeleteOldData = Phpfox::getParam('foxfeedspro.is_auto_delete');

if($bIsDeleteOldData)
{
	// Delete Old NewsPhpfox::getParam('foxfeedspro.number_day_delete');
	$iNumberDayToDelete = Phpfox::getParam('foxfeedspro.number_day_delete');
	$iTimePeriod = $iNumberDayToDelete * 24 * 60 * 60;

	$iTimePeriod = PHPFOX_TIME - $iTimePeriod;
	// Set query conditions
	$aConds = array("ni.item_pubDate < $iTimePeriod");

	$aNewsItems = $oFoxFeedsPro->getNewsItems($aConds, NULL, NULL, NULL);
	foreach($aNewsItems as $aNews)
	{
		$oFoxFeedsProProcess->deleteNews($aNews['item_id']);
	}

	echo("Delete Old News Successfully!");
}

?>