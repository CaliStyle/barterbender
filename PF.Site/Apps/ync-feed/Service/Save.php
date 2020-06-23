<?php
namespace Apps\YNC_Feed\Service;

use Phpfox;
use Phpfox_Url;

defined('PHPFOX') or exit('NO DICE!');

class Save extends \Phpfox_Service
{
    protected $_sTable;
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('ynfeed_saved');
    }

    public function add($aParams, $callFromSavedApp = false) {
        $check = db()->select('saved_id')
                    ->from($this->_sTable)
                    ->where('user_id = ' . (int)$aParams['user_id'] . ' AND feed_id = ' . (int)$aParams['feed_id'] . ' AND feed_type = "' . $aParams['feed_type'] . '"')
                    ->execute('getSlaveField');
        if($check) {
            return false;
        }

        $callback = !empty($aParams['callback']) ? json_decode($aParams['callback'], true) : null;

        if(($success = db()->insert($this->_sTable, $aParams)) && !$callFromSavedApp && Phpfox::isAppActive('P_SavedItems') && Phpfox::hasCallback('saveditems', 'saveItem') && ($feed = Phpfox::getService('feed')->callback($callback)->getFeed($aParams['feed_id']))) {
            Phpfox::callback('saveditems.saveItem', [
                'type_id' => $feed['type_id'],
                'item_id' => $feed['item_id'],
                'link' => Phpfox::hasCallback($feed['type_id'], 'getLink') ? Phpfox::callback($feed['type_id'], ['item_id' => $feed['item_id']]) : '',
                'is_save' => 1,
                'user_id' => (int)$aParams['user_id']
            ]);
        }

        return $success;
    }
    public function delete($iUserId, $iFeedId, $module = null, $callFromSavedApp = false) {
        $moduleCallback = !empty($module) && Phpfox::hasCallback($module, 'getFeedDetails') ? Phpfox::callback($module . '.getFeedDetails', null) : null;
        $success = false;
        if(($feed = Phpfox::getService('feed')->callback($moduleCallback)->getFeed($iFeedId)) && ($success = db()->delete($this->_sTable, "user_id = " . (int) $iUserId . ' AND feed_id = ' . (int) $iFeedId . (!empty($module) ? ' AND (callback IS NOT NULL AND callback LIKE \'%"module":"' . $module .'"%\'' .')' : ' AND (callback is NULL OR callback = \'\')'))) && !$callFromSavedApp && Phpfox::isAppActive('P_SavedItems') && Phpfox::hasCallback('saveditems', 'saveItem')) {
            Phpfox::callback('saveditems.saveItem', [
                'type_id' => $feed['type_id'],
                'item_id' => $feed['item_id'],
                'is_save' => 0,
                'user_id' => $iUserId
            ]);
        }
        return $success;
    }

    public function isSaved($iUserId, $iFeedId) {
        if(db()->select('saved_id')->from($this->_sTable)->where('user_id = ' . (int) $iUserId . ' AND feed_id = ' . (int) $iFeedId)->execute('getField'))
            return true;
        return false;
    }

    public function getSavedIds($iUserId = null) {
        $aIds = array_column(db()->select('feed_id')->from($this->_sTable)->where('user_id = ' . (int) ($iUserId ? $iUserId : Phpfox::getUserId()))->execute('getRows'), 'feed_id');
        return $aIds;
    }
    public function getSavedFeeds($iPage = 1) {
        if(!$iPage)
            $iPage = 1;
        $iPageSize = (int) Phpfox::getParam('feed.feed_display_limit', 10);
        $iOffset = ($iPage - 1) * $iPageSize;
        $aSaves = db()->select('*')
                ->from($this->_sTable)
                ->where('user_id = ' . (int) Phpfox::getUserId())
                ->order('saved_id DESC')
                ->limit($iOffset, $iPageSize, null, false, true)
                ->execute('getSlaveRows');
        $aSavedFeeds = [];
        foreach($aSaves as $aSave) {
            $aCallback = json_decode($aSave['callback'], true);
            $aSavedFeeds = array_merge($aSavedFeeds, Phpfox::getService('ynfeed')->callback($aCallback)->get(null, $aSave['feed_id']));
        }
        //Add feed info
        foreach($aSavedFeeds as $key=>$aSavedFeed) {
            if(!isset($aSavedFeed['feed_info'])) {
                switch ($aSavedFeed['type_id']) {
                    case 'event_comment':
                        // get the event
                        $event_id = db()->select('parent_user_id')->from(Phpfox::getT('event_feed_comment'))->where('feed_comment_id = ' . $aSavedFeed['item_id'])->limit(1)->execute('getField');
                        $aEvent = Phpfox::getService('event')->getEvent($event_id, true);
                        if($aEvent) {
                            $aSavedFeeds[$key]['feed_info'] = _p('posted_in_event',
                                ['event' => '<a href="' . Phpfox::getService('event.callback')->getLink(['item_id' => $event_id]) . '">' . $aEvent['title'] . '</a>']);
                        }
                        break;
                }

            }
        }
        return $aSavedFeeds;
    }

}