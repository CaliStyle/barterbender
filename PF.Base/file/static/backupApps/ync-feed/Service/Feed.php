<?php

namespace Apps\YNC_Feed\Service;

use Phpfox;
use Phpfox_Plugin;
use Phpfox_Request;
use Core;
use Phpfox_Ajax;
use Phpfox_Url;
use Phpfox_Template;
use Phpfox_Error;
use Phpfox_Database;

defined('PHPFOX') or exit('NO DICE!');

class Feed extends \Feed_Service_Feed
{
    /**
     * @var array
     */
    private $_aViewMoreFeeds = [];

    /**
     * @var array
     */
    private $_aCallback = [];

    /**
     * @var string
     */
    private $_sLastDayInfo = '';

    /**
     * @var array
     */
    private $_aFeedTimeline = ['left' => [], 'right' => []];

    /**
     * @var array
     */
    private $_params = [];

    public function getUploadPhotoParamsForEditFeed() {
        $params = [];
        $typeId = 'photo';

        (($sPlugin = Phpfox_Plugin::get('feed.service_feed_getuploadphotoparamsforeditfeed_start')) ? eval($sPlugin) : false);

        if(Phpfox::hasCallback($typeId,'getUploadParamsFeed')) {
            $params = Phpfox::callback($typeId .'.getUploadParamsFeed');
            $params = array_merge([
                'upload_icon' => 'ico ico-upload-cloud',
                'type_list' => ['jpg', 'gif', 'png'],
                'type' => 'photo_feed',
                'submit_button' => '#activity_feed_submit',
                'error_message' => json_encode([
                    'over_size' => _p('upload_failed_your_file_size_is_larger_then_our_limit_file_size', ['file_size' => number_format($params['max_size'], 1). 'Mb' ]),
                    'max_upload' => _p('dz_max_files_exceeded')
                ])
            ], $params);
            $params['type_list'] = json_encode($params['type_list']);
        }

        (($sPlugin = Phpfox_Plugin::get('feed.service_feed_getuploadphotoparamsforeditfeed_end')) ? eval($sPlugin) : false);

        return $params;
    }

    public function getCachedPhotos($feedId, $moduleId = null, $uploadedPhotos = [])
    {
        //Get file from cache database
        $cachePhotos = db()->select('file_name')
            ->from(Phpfox::getT('cache'))
            ->where('cache_data = "'. $feedId . '"')
            ->execute('getSlaveRows');
        if(!empty($cachePhotos)) {
            $tempIds = [];
            foreach($cachePhotos as $cachePhoto) {
                if((preg_match('/ynfeed_pending_photo_([0-9]+)/', $cachePhoto['file_name'], $match)) && !empty($match[1]) && is_numeric($match[1])) {
                    if(!empty($uploadedPhotos) && in_array($match[1], $uploadedPhotos)) {
                        continue;
                    }
                    $tempIds[] = (int)$match[1];
                }
            }
            if(count($tempIds)) {
                $tempPhotos = db()->select('*')
                    ->from(Phpfox::getT('photo'))
                    ->where('view_id = 1 AND photo_id IN ('. implode(',', $tempIds) .')' . ($moduleId ? ' AND module_id = "' . $moduleId . '"' : ''))
                    ->execute('getSlaveRows');
                return $tempPhotos;
            }
        }
        return [];
    }

    public function getPhotosForEditStatus($feedId, $moduleId = null) {
        $type = 'photo';

        (($sPlugin = Phpfox_Plugin::get('feed.service_feed_getphotosforeditstatus_start')) ? eval($sPlugin) : false);

        $photos = db()->select('p.*, pi.*')
                    ->from(Phpfox::getT($type), 'p')
                    ->join(Phpfox::getT($type.'_feed'), 'pf', 'pf.photo_id = p.photo_id AND pf.feed_id = '. (int)$feedId . ' AND pf.feed_table = "'. ($moduleId ? ($moduleId == 'groups' ? 'pages' : $moduleId) . '_' : '') . 'feed"' )
                    ->leftJoin(Phpfox::getT($type. '_info'), 'pi', 'pi.photo_id = p.photo_id')
                    ->execute('getSlaveRows');

        //Get file from cache database
        $tempPhotos = $this->getCachedPhotos($feedId, $moduleId);
        if(!empty($tempPhotos)) {
            $photos = array_merge($photos, $tempPhotos);
        }

        (($sPlugin = Phpfox_Plugin::get('feed.service_feed_getphotosforeditstatus_end')) ? eval($sPlugin) : false);

        return $photos;
    }

    /**
     * @param $type
     * @param $class
     * @param $params
     * @return string
     */
    public function getPreviewContent($type, $class, $params) {
        ob_start();
        if($type == 'block') {
            Phpfox::getBlock($class, $params, true);
        }
        else {
            Phpfox::getComponent($class, $params, 'controller', true);
        }
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    /**
     * @param int $iId
     *
     * @return void
     */
    public function processAjax($iId)
    {
        $oAjax = Phpfox_Ajax::instance();

        $aFeeds = Phpfox::getService('ynfeed')->get(null, $iId);

        if (!isset($aFeeds[0])) {
            $oAjax->alert(_p('this_item_has_successfully_been_submitted'));
            $oAjax->call('$Core.ynfeedResetActivityFeedForm();');
            return;
        }
        $aFeeds[0]['can_like'] = isset($aFeeds[0]['like_type_id']) && empty($aFeeds[0]['disable_like_function']) &&
            !Phpfox::getService('user.block')->isBlocked(null, $aFeeds[0]['user_id']);

        $aFeeds[0]['can_comment'] = Phpfox::isModule('comment') && isset($aFeeds[0]['comment_type_id']) &&
            Phpfox::getUserParam('comment.can_post_comments') &&
            Phpfox::isUser() && $aFeeds[0]['can_post_comment'] && Phpfox::getUserParam('feed.can_post_comment_on_feed');

        $aFeeds[0]['can_share'] = Phpfox::isModule('share') && !isset($aFeeds[0]['no_share']) && !empty($aFeeds[0]['type_id']) && isset($aFeeds[0]['privacy']) && $aFeeds[0]['privacy'] == 0 && !Phpfox::getService('user.block')->isBlocked(null, $aFeeds[0]['user_id']);

        $aFeeds[0]['total_action'] = intval($aFeeds[0]['can_like']) + intval($aFeeds[0]['can_comment']) + intval($aFeeds[0]['can_share']);

        $aShareServices = Phpfox::getService('ynfeed')->getShareProviders();
        if (isset($aFeeds[0]['type_id'])) {
            Phpfox_Template::instance()->assign([
                'aFeed' => $aFeeds[0],
                'aShareServices' => $aShareServices,
                'aFeedCallback' => [
                    'module' => !empty($this->_aCallback) ? $this->_aCallback['module'] : (preg_match('/_comment/') ? str_replace('_comment', '', $aFeeds[0]['type_id']) : null),
                    'item_id' => (!empty($this->_aCallback['item_id']) ? $this->_aCallback['item_id'] : (!empty($aFeeds[0]['item_id']) ? $aFeeds[0]['item_id'] : null))
                ],
            ])->getTemplate('ynfeed.block.entry');
        } else {
            Phpfox_Template::instance()->assign([
                'aFeed' => $aFeeds[0],
                'aShareServices' => $aShareServices
            ])->getTemplate('ynfeed.block.entry');
        }

        $sId = 'js_tmp_comment_' . md5('feed_' . uniqid() . Phpfox::getUserId()) . '';
        $sNewContent = '<div id="' . $sId . '" class="js_temp_new_feed_entry js_feed_view_more_entry_holder">' . $oAjax->getContent(false) . '</div>';
        $oAjax->insertAfter('#js_new_feed_comment', $sNewContent);

        $oAjax->removeClass('.js_user_feed', 'row_first');
        $oAjax->call("iCnt = 0; \$('.js_user_feed').each(function(){ iCnt++; if (iCnt == 1) { \$(this).addClass('row_first'); } });");
        if ($oAjax->get('force_form')) {
            $oAjax->call('tb_remove();');
            $oAjax->show('#js_main_feed_holder');
            $oAjax->call('setTimeout(function(){$Core.ynfeedResetActivityFeedForm();$Core.loadInit();}, 500);');
        } else {
            $oAjax->call('$Core.ynfeedResetActivityFeedForm();');
            $oAjax->call('$Core.loadInit();');
        }
    }

    public function getTaggedUsers($iItemId, $sItemType, $sTypeId = 'tag')
    {
        $aRows = $this->database()->select('' . Phpfox::getUserField())
            ->from(Phpfox::getT('user'), 'u')
            ->join(Phpfox::getT('ynfeed_feed_map'), 'm', 'u.user_id = m.parent_user_id')
            ->where("m.item_id = " . (int)$iItemId . " AND item_type = '" . $sItemType . "' AND type_id = '" . $sTypeId . "'")
            ->order('m.map_id')
            ->execute('getSlaveRows');

        if (db()->tableExists(Phpfox::getT('feed_tag_data'))) {
            $aIds = array_column($aRows, 'user_id');
            $aCoreTags = db()->select('td.*, ' . Phpfox::getUserField())
                ->from(':feed_tag_data', 'td')
                ->join(':user', 'u', 'u.user_id = td.user_id')
                ->where('td.item_id = ' . (int)$iItemId . ' AND td.type_id = \'' . $sItemType . '\'' . (count($aIds) ? ' AND td.user_id NOT IN (' . implode(',',
                            $aIds) . ')' : ''))
                ->execute('getSlaveRows');
            $aRows = array_merge($aRows, $aCoreTags);
        }
        return $aRows;
    }

    public function getTagInfo($iFeedId, $sItemType)
    {
        $aTagged = $this->getTaggedUsers($iFeedId, $sItemType);
        $iTotalTagged = count($aTagged);
        $sTagInfo = '';
        if ($iTotalTagged == 1) {
            $phrase0 = '<a href="' . Phpfox_Url::instance()->makeUrl($aTagged[0]['user_name']) . '">' . $aTagged[0]['full_name'] . '</a>';
            $sTagInfo = _p('with_somebody', array('somebody' => $phrase0));
        } else if ($iTotalTagged == 2) {
            $phrase0 = '<a href="' . Phpfox_Url::instance()->makeUrl($aTagged[0]['user_name']) . '">' . $aTagged[0]['full_name'] . '</a>';
            $phrase1 = '<a href="' . Phpfox_Url::instance()->makeUrl($aTagged[1]['user_name']) . '">' . $aTagged[1]['full_name'] . '</a>';
            $sTagInfo = _p('with_somebody_and_somebody', array('somebody0' => $phrase0, 'somebody1' => $phrase1));
        } else if ($iTotalTagged > 2) {
            $phrase0 = '<a href="' . Phpfox_Url::instance()->makeUrl($aTagged[0]['user_name']) . '">' . $aTagged[0]['full_name'] . '</a>';

            /*Get others*/
            $sTooltip = '';
            $sTaggedExpandIds = '';
            for ($i = 1; $i < $iTotalTagged; $i++) {
                $sTooltip .= $aTagged[$i]['full_name'] . '<br />';
                $sTaggedExpandIds .= $aTagged[$i]['user_id'] . ',';
            }
            $phrase1 = '<span class="ynfeed_popover ynfeed_expand_users" data-tagged="' . $sTaggedExpandIds . '" data-toggle="popover" data-trigger="hover" data-content="' . $sTooltip . '">' . _p('number_others', array('number' => $iTotalTagged - 1)) . '</span>';
            $sTagInfo = _p('with_somebody_and_somebody', array('somebody0' => $phrase0, 'somebody1' => $phrase1));
        }
        if (in_array(Phpfox::getUserId(), array_column(array_merge($aTagged, $this->getTaggedUsers($iFeedId, $sItemType, 'mention')), 'user_id')))
            $bIsTagged = true;
        else $bIsTagged = false;
        return array($aTagged, $sTagInfo, $bIsTagged);
    }


    /**
     * @param null|int|array $iUserId
     * @param null|int $iFeedId
     * @param int $iPage
     * @param bool $bForceReturn
     * @param bool $bLimit
     * @param null|int $iLastFeedId
     *
     * @return array
     */

    public function get($iUserId = null, $iFeedId = null, $iPage = 0, $bForceReturn = false, $bLimit = true, $iLastFeedId = null, $iSponsorFeedId = 0)
    {
        $sSubjectType = Phpfox::getService('ynfeed.user.process')->getUserType((int)$iUserId);
        static $iLoopCount = 0;
        $params = [];
        if (is_array($iUserId)) {
            $params = $iUserId;
            $iUserId = null;
            if (isset($params['id'])) {
                $iFeedId = $params['id'];
            }

            if (isset($params['page'])) {
                $iPage = $params['page'];
            }

            if (isset($params['user_id'])) {
                $iUserId = $params['user_id'];
            }
        }
        $this->_params = $params;

        $oReq = Phpfox_Request::instance();
        $bIsCheckForUpdate = defined('PHPFOX_CHECK_FOR_UPDATE_FEED') ? 1 : 0;
        $iLastFeedUpdate = defined('PHPFOX_CHECK_FOR_UPDATE_FEED_UPDATE') ? PHPFOX_CHECK_FOR_UPDATE_FEED_UPDATE : 0;
        $iLastStoreUpdate = Phpfox::getCookie('feed-last-check-id');
        if ($iLastFeedUpdate && $bIsCheckForUpdate && ($iLastStoreUpdate > $iLastFeedUpdate)) {
            $iLastFeedUpdate = $iLastStoreUpdate;
        }

        $iUserFeedSort = Phpfox::getUserBy('feed_sort');

        if ($iLastFeedUpdate != $iLastStoreUpdate) {
            Phpfox::removeCookie('feed-last-check-id');
            Phpfox::setCookie('feed-last-check-id', $iLastFeedUpdate);
        }
        if (!isset($params['bIsChildren']) || !$params['bIsChildren']) {
            if (($iCommentId = $oReq->getInt('comment-id'))) {
                if (isset($this->_aCallback['feed_comment'])) {
                    $aCustomCondition = ['feed.type_id = \'' . $this->_aCallback['feed_comment'] . '\' AND feed.item_id = ' . (int)$iCommentId . ' AND feed.parent_user_id = ' . (int)$this->_aCallback['item_id']];
                } else {
                    $aCustomCondition = ['feed.type_id IN(\'feed_comment\', \'feed_egift\') AND feed.item_id = ' . (int)$iCommentId . ' AND feed.parent_user_id = ' . (int)$iUserId];
                }

                $iFeedId = true;
            } elseif (($iStatusId = $oReq->getInt('status-id'))) {
                $aCustomCondition = ['feed.type_id = \'user_status\' AND feed.item_id = ' . (int)$iStatusId . ' AND feed.user_id = ' . (int)$iUserId];
                $iFeedId = true;
            } elseif (($iLinkId = $oReq->getInt('link-id'))) {
                $aCustomCondition = ['feed.type_id = \'link\' AND feed.item_id = ' . (int)$iLinkId . ' AND feed.user_id = ' . (int)$iUserId];
                $iFeedId = true;
            } elseif (($iLinkId = $oReq->getInt('plink-id'))) {
                $aCustomCondition = ['feed.type_id = \'link\' AND feed.item_id = ' . (int)$iLinkId . ' AND feed.parent_user_id  = ' . (int)$iUserId];
                $iFeedId = true;
            } elseif (($iPokeId = $oReq->getInt('poke-id'))) {
                $aCustomCondition = ['feed.type_id = \'poke\' AND feed.item_id = ' . (int)$iPokeId . ' AND feed.user_id = ' . (int)$iUserId];
                $iFeedId = true;
            }
        }
        $iTotalFeeds = (int)Phpfox::getComponentSetting(($iUserId === null ? Phpfox::getUserId() : $iUserId), 'feed.feed_display_limit_' . ($iUserId !== null ? 'profile' : 'dashboard'), Phpfox::getParam('feed.feed_display_limit'));
        if (isset($params['limit'])) {
            $iTotalFeeds = $params['limit'];
        }
        if (!$bLimit || (defined('FEED_LOAD_NEW_NEWS') && FEED_LOAD_NEW_NEWS)) {
            $iTotalFeeds = 101;
        }
        $sLoadMoreCond = null;
        $iOffset = (($iPage * $iTotalFeeds));
        if ($iOffset == '-1') {
            $iOffset = 0;
        }
        if ($iLastFeedId != null) {
            if ($iUserFeedSort || defined('PHPFOX_IS_USER_PROFILE')) {
                $iOffset = 0;
                $sLoadMoreCond = 'AND feed.feed_id < ' . (int)$iLastFeedId;
            }
            else {
                $aLastFeed = $this->getFeed($iLastFeedId);
                if (!empty($aLastFeed['time_update'])) {
                    $iOffset = 0;
                    $sLoadMoreCond = 'AND feed.time_update < ' . (int)$aLastFeed['time_update'];
                }
            }
        } elseif (isset($params['order']) && $params['order'] == 'feed.total_view DESC' && isset($params['v_page'])) {
            $iOffset = (int)($params['v_page'] * $iTotalFeeds);
        } elseif (isset($params['last-item']) && $params['last-item']) {
            $sLoadMoreCond = ' AND feed.feed_id < ' . (int)$params['last-item'];
        }
        $extra = '';

        if (Phpfox::isUser()) {
            $aBlockedUserIds = Phpfox::getService('user.block')->get(null, true);
            if (!empty($aBlockedUserIds)) {
                $extra .= ' AND feed.user_id NOT IN (' . implode(',', $aBlockedUserIds) . ')';
            }
        }

        if ($sLoadMoreCond != null) {
            $extra .= ' ' . $sLoadMoreCond;
        }
        (($sPlugin = Phpfox_Plugin::get('feed.service_feed_get_start')) ? eval($sPlugin) : false);

        /* Ynfeed filter */
        $aRequests = Phpfox_Request::instance()->getRequests();
        if (isset($aRequests['filter-id']) && is_numeric($aRequests['filter-id']) && isset($aRequests['filter-module']) && isset($aRequests['filter-type'])) {
            switch ($aRequests['filter-type']) {
                case '':
                case 'all':
                    break;
                case 'status':
                    $params['type_id'] = 'user_status';
                    break;
                case 'membership':
                    $aFriendIds = array_column(Phpfox::getService('friend')->getFromCache(), 'user_id');
                    $extra .= ' AND feed.user_id IN (' . implode(',', array_merge([0], $aFriendIds)) . ')';
                    break;
                case 'user_saved':
                    break;
//                    $extra .= ' AND feed.feed_id IN (' . implode(',', array_merge([0], Phpfox::getService('ynfeed.save')->getSavedIds())) . ')';
//                    $extra .= ' AND feed.feed_id IN (0)';
//                    break;
                case 'video':
                    $params['type_id'] = 'v';
                    break;
                default:
                    $extra .= " AND (feed.type_id = '" . $aRequests['filter-module'] . "' OR feed.type_id LIKE '" . $aRequests['filter-module'] . "_%')";
                    break;
            }
        }

        /* End filter */

        if (isset($params['type_id'])) {
            $extra .= ' AND feed.type_id ' . (is_array($params['type_id']) ? 'IN(' . implode(',', array_map(function ($value) {
                        return "'{$value}'";
                    }, $params['type_id'])) . ')' : '= \'' . $params['type_id'] . '\'') . '';
        }
        //Do not hide feed when login as pages
        if (!Phpfox::getUserBy('profile_page_id') && defined('PHPFOX_IS_USER_PROFILE') && PHPFOX_IS_USER_PROFILE) {
            //Hide feed add on other user wall
            if (isset($iUserId)) {
                $extra .= ' AND (feed.parent_user_id=0 OR feed.parent_user_id = ' . (int)$iUserId . ')';
            }
        }
        $sOrder = 'feed.time_update DESC';

        if ($iUserFeedSort || defined('PHPFOX_IS_USER_PROFILE')) {
            $sOrder = 'feed.time_stamp DESC';
        }

        // define where for check update
        $checkUpdateWhere = '';
        if ($bIsCheckForUpdate) {
            if ($iUserFeedSort || defined('PHPFOX_IS_USER_PROFILE')) {
                $checkUpdateWhere = 'feed.time_stamp > ' . intval($iLastFeedUpdate);
            } else {
                $checkUpdateWhere = 'feed.time_update > ' . intval($iLastFeedUpdate);
            }
        }

        if (isset($this->_params['order'])) {
            $sOrder = $this->_params['order'];
        }

        $aCond = [];

        $aHideConds = [];
        Phpfox::getService('ynfeed')->getHideCondition($aHideConds);
        if(!empty($aHideConds)) {
            $extra .= implode(' ', $aHideConds);
        }

        // Users must be active within 7 days or we skip their activity feed
        $iLastActiveTimeStamp = (((int)Phpfox::getParam('feed.feed_limit_days') <= 0 || !empty($this->_params['ignore_limit_feed'])) ? 0 : (PHPFOX_TIME - (86400 * Phpfox::getParam('feed.feed_limit_days'))));
        $is_app = false;
        if (isset($params['type_id']) && (new Core\App())->exists($params['type_id'])) {
            $is_app = true;
        }
        if (isset($this->_aCallback['module'])) {
            $aNewCond = [];
            if (($iCommentId = $oReq->getInt('comment-id'))) {
                if (!isset($this->_aCallback['feed_comment'])) {
                    $aCustomCondition = ['feed.type_id = \'' . $this->_aCallback['module'] . '_comment\' AND feed.item_id = ' . (int)$iCommentId . ''];
                }
            }
            $aNewCond[] = 'AND feed.parent_user_id = ' . (int)$this->_aCallback['item_id'];
            if ($iUserId !== null && $iFeedId !== null) {
                $aNewCond[] = 'AND feed.feed_id = ' . (int)$iFeedId . ' AND feed.user_id = ' . (int)$iUserId;
            }

            if ($iUserId === null && $iFeedId !== null) {
                $aNewCond = [];
                $aNewCond[] = 'AND feed.feed_id = ' . (int)$iFeedId;
            }

            if (Phpfox::isUser()) {
                $aBlockedUserIds = Phpfox::getService('user.block')->get(null, true);
                if (!empty($aBlockedUserIds)) {
                    $aNewCond[] = 'AND feed.user_id NOT IN (' . implode(',', $aBlockedUserIds) . ')';
                    if (!empty($aCustomCondition)) {
                        $aCustomCondition[] = 'AND feed.user_id NOT IN (' . implode(',', $aBlockedUserIds) . ')';
                    }
                }
            }

            if ($iFeedId === null && is_string($extra) && !empty($extra)) {
                $aNewCond[] = $extra;
            }

            if (isset($this->_params['search']) && !empty($this->_params['search'])) {
                $aNewCond[] = 'AND feed.content LIKE \'%' . $this->database()->escape($this->_params['search']) . '%\'';
            }

            if (isset($this->_params['parent_feed_id']) && is_numeric($this->_params['parent_feed_id'])) {
                $aNewCond[] = 'AND feed.parent_feed_id = ' . $this->_params['parent_feed_id'];
            }

            if ($is_app && isset($this->_params['when']) && $this->_params['when']) {
                $iTimeDisplay = Phpfox::getLib('date')->mktime(0, 0, 0, Phpfox::getTime('m'), Phpfox::getTime('d'), Phpfox::getTime('Y'));
                switch ($params['when']) {
                    case 'today':
                        $iEndDay = Phpfox::getLib('date')->mktime(23, 59, 0, Phpfox::getTime('m'), Phpfox::getTime('d'), Phpfox::getTime('Y'));
                        $aNewCond[] = ' AND (' . 'feed.time_stamp' . ' >= \'' . Phpfox::getLib('date')->convertToGmt($iTimeDisplay) . '\' AND ' . 'feed.time_stamp' . ' < \'' . Phpfox::getLib('date')->convertToGmt($iEndDay) . '\')';
                        break;
                    case 'this-week':
                        $aNewCond[] = ' AND ' . 'feed.time_stamp' . ' >= ' . (int)Phpfox::getLib('date')->convertToGmt(Phpfox::getLib('date')->getWeekStart());
                        $aNewCond[] = ' AND ' . 'feed.time_stamp' . ' <= ' . (int)Phpfox::getLib('date')->convertToGmt(Phpfox::getLib('date')->getWeekEnd());
                        break;
                    case 'this-month':
                        $aNewCond[] = ' AND ' . 'feed.time_stamp' . ' >= \'' . Phpfox::getLib('date')->convertToGmt(Phpfox::getLib('date')->getThisMonth()) . '\'';
                        $iLastDayMonth = Phpfox::getLib('date')->mktime(0, 0, 0, date('n'), Phpfox::getLib('date')->lastDayOfMonth(date('n')), date('Y'));
                        $aNewCond[] = ' AND ' . 'feed.time_stamp' . ' <= \'' . Phpfox::getLib('date')->convertToGmt($iLastDayMonth) . '\'';
                        break;
                    default:
                        break;
                }
            }

            /*Get mention feeds from feed table*/
            if (($this->_aCallback['module'] == 'pages' || $this->_aCallback['module'] == 'groups') && $iUserId) {
                $this->database()->select('feed.feed_id, feed.privacy, feed.privacy_comment, feed.type_id, feed.user_id, feed.parent_user_id, feed.item_id, feed.time_stamp, feed.parent_feed_id, feed.parent_module_id, feed.time_update, feed.content, feed.total_view')
                    ->from($this->_sTable, 'feed')
                    ->leftJoin(Phpfox::getT('ynfeed_feed_map'), 'fm', 'feed.item_id = fm.item_id AND feed.type_id = fm.item_type')
                    ->where('fm.parent_user_id = ' . $iUserId)
                    ->union();
            }

            /*Get feed from callback module*/
            $this->database()->select('feed.*')
                ->from(Phpfox::getT($this->_aCallback['table_prefix'] . 'feed'), 'feed')
                ->leftJoin(Phpfox::getT('ynfeed_feed_map'), 'fm', 'feed.item_id = fm.item_id')
                ->where((isset($aCustomCondition) ? $aCustomCondition : $aNewCond))
                ->union();

            $aRows = $this->database()->select('feed.*, ' . Phpfox::getUserField())
                ->unionFrom('feed')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = feed.user_id')
                ->order($sOrder)
                ->limit($iOffset, $iTotalFeeds, null, false, true)
                ->group('feed.feed_id')
                ->execute('getSlaveRows');

            // Fixes missing page_user_id, required to create the proper feed target
            if ($this->_aCallback['module'] == 'pages') {
                foreach ($aRows as $iKey => $aValue) {
                    $aRows[$iKey]['page_user_id'] = $iUserId;
                }
            }
        } // check feed id in exists list.
        elseif ($iUserId === null && $iFeedId === null && ($sIds = $oReq->get('ids'))) {
            $aParts = explode(',', $oReq->get('ids'));
            $sNewIds = '';
            foreach ($aParts as $sPart) {
                $sNewIds .= (int)$sPart . ',';
            }
            $sNewIds = rtrim($sNewIds, ',');

            $aRows = $this->database()->select('feed.*, ' . Phpfox::getUserField() . ', u.view_id')
                ->from($this->_sTable, 'feed')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = feed.user_id')
                ->where('feed.feed_id IN(' . $sNewIds . ')')
                ->order('feed.time_stamp DESC')
                ->execute('getSlaveRows');
        } // get particular feed by id
        elseif ($iUserId === null && $iFeedId !== null) {
            if (isset($this->_aCallback['module'])) {
                $this->_sTable = Phpfox::getT($this->_aCallback['table_prefix'] . 'feed');
            }

            $aRows = $this->database()->select('feed.*, ' . Phpfox::getUserField() . ', u.view_id')
                ->from($this->_sTable, 'feed')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = feed.user_id')
                ->where('feed.feed_id = ' . (int)$iFeedId)
                ->order('feed.time_stamp DESC')
                ->execute('getSlaveRows');
        } // get particular feed by id
        elseif ($iUserId !== null && $iFeedId !== null) {
            $aRows = $this->database()->select('feed.*, ' . Phpfox::getUserField() . ', u.view_id')
                ->from($this->_sTable, 'feed')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = feed.user_id')
                ->where((isset($aCustomCondition) ? $aCustomCondition : 'feed.feed_id = ' . (int)$iFeedId . ' AND feed.user_id = ' . (int)$iUserId . ''))
                ->order('feed.time_stamp DESC')
                ->limit(1)
                ->execute('getSlaveRows');
        } // get feed on particular profile, does not need to improve.
        elseif ($iUserId !== null) {
            $sOrder = 'feed.time_stamp desc';

            if ($iUserId == Phpfox::getUserId()) {
                $aCond[] = 'AND feed.privacy IN(0,1,2,3,4)';
            } else {
                $oUserObject = Phpfox::getService('user')->getUserObject($iUserId);
                if (isset($oUserObject->is_friend) && $oUserObject->is_friend) {
                    $aCond[] = 'AND feed.privacy IN(0,1,2) ';
                } else if (isset($oUserObject->is_friend_of_friend) && $oUserObject->is_friend_of_friend) {
                    $aCond[] = 'AND feed.privacy IN(0,2)';
                } else {
                    $aCond[] = 'AND feed.privacy IN(0)';
                }
            }
            $aCond[] = $extra;

            if (isset($this->_params['search']) && !empty($this->_params['search'])) {
                $aCond[] = 'AND feed.content LIKE \'%' . $this->database()->escape($this->_params['search']) . '%\'';
            }

            if (isset($this->_params['parent_feed_id']) && is_numeric($this->_params['parent_feed_id'])) {
                $aCond[] = 'AND feed.parent_feed_id = ' . $this->_params['parent_feed_id'];
            }

            if (!$this->_params) {
                $more = '';
                if ($iLastFeedId != null) {
                    $aLastFeed = $this->getFeed($iLastFeedId);
                    if (!empty($aLastFeed['time_update'])) {
                        $more = ' AND feed.time_update < ' . (int)$aLastFeed['time_update'];
                    }
                }
                // There is no reciprocal feed when you add someone as friend
                if (isset($this->_params['search']) && !empty($this->_params['search'])) {
                    $this->database()->join(Phpfox::getT('feed'), 'feed_search', 'feed_search.feed_id = feed.feed_id AND feed_search.content LIKE \'%' . $this->database()->escape($this->_params['search']) . '%\'');
                }

                $this->database()->select('DISTINCT feed.*')
                    ->from($this->_sTable, 'feed')
                    ->join(Phpfox::getT('ynfeed_feed_map'), 'fm', 'feed.item_id = fm.item_id AND feed.type_id = fm.item_type')
                    ->where('(feed.user_id = ' . (int)$iUserId . ' OR fm.parent_user_id = ' . (int)$iUserId . ') AND fm.parent_user_type = \'' . $sSubjectType . '\' ' . $more)
                    ->order($sOrder)
                    ->limit($iOffset, $iTotalFeeds, null, false, true)
                    ->union();
            }

            (($sPlugin = Phpfox_Plugin::get('feed.service_feed_get_userprofile')) ? eval($sPlugin) : '');

            $this->database()->select('DISTINCT feed.*')
                ->from($this->_sTable, 'feed')
                ->where(array_merge($aCond, ['AND type_id = \'feed_comment\' AND feed.user_id = ' . (int)$iUserId . '']))
                ->order($sOrder)
                ->limit($iOffset, $iTotalFeeds, null, false, true)
                ->union();

            $this->database()->select('DISTINCT feed.*')
                ->from($this->_sTable, 'feed')
                ->where(array_merge($aCond, ['AND feed.user_id = ' . (int)$iUserId . ' AND feed.feed_reference = 0 AND feed.parent_user_id = 0']))
                ->order($sOrder)
                ->limit($iOffset, $iTotalFeeds, null, false, true)
                ->union();

            if (Phpfox::isUser()) {
                if (Phpfox::isModule('privacy')) {
                    $this->database()->join(Phpfox::getT('privacy'), 'p', 'p.module_id = feed.type_id AND p.item_id = feed.item_id')
                        ->join(Phpfox::getT('friend_list_data'), 'fld', 'fld.list_id = p.friend_list_id AND fld.friend_user_id = ' . Phpfox::getUserId() . '');
                }

                $this->database()->select('DISTINCT feed.*')
                    ->from($this->_sTable, 'feed')
                    ->where(array_merge($aCond, ['AND feed.privacy IN(4) AND feed.user_id = ' . (int)$iUserId . ' AND feed.feed_reference = 0']))
                    ->order($sOrder)
                    ->limit($iOffset, $iTotalFeeds, null, false, true)
                    ->union();
            }

            $this->database()->select('DISTINCT feed.*')
                ->from($this->_sTable, 'feed')
                ->where(array_merge($aCond, ['AND feed.parent_user_id = ' . (int)$iUserId]))
                ->order($sOrder)
                ->limit($iOffset, $iTotalFeeds, null, false, true)
                ->union();
            $aRows = $this->database()->select('feed.*, ' . Phpfox::getUserField())
                ->unionFrom('feed')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = feed.user_id')
                ->order('feed.time_stamp DESC')
                ->limit(0, $iTotalFeeds, null, false, true)
                ->execute('getSlaveRows');
        } elseif (
            // get main feed on "feed_only_friends" ON.
            // case 01.
            ((Phpfox::getParam('feed.feed_only_friends') && !$is_app)
                || Phpfox::getParam('core.friends_only_community')
                || isset($this->_params['friends']))
            && !$this->isSearchHashtag()
        ) {
            $r_key = (auth()->isLoggedIn() ? 'feed_stream_' . user()->id : null);

            if (isset($this->_params['search']) && !empty($this->_params['search'])) {
                $extra .= ' AND feed.content LIKE \'%' . $this->database()->escape($this->_params['search']) . '%\'';
            }

            if (isset($this->_params['parent_feed_id']) && is_numeric($this->_params['parent_feed_id'])) {
                $extra .= ' AND feed.parent_feed_id = ' . $this->_params['parent_feed_id'];
            }

            if (Phpfox::isModule('friend')) {

                if ($sOrder == 'feed.time_update DESC')
                    $this->database()->forceIndex('time_update');

                // Get my friends feeds
                if (isset($this->_params['join_query']) && is_callable($this->_params['join_query'])) {
                    call_user_func($this->_params['join_query']);
                }
                $this->database()->select('DISTINCT feed.*')
                    ->from($this->_sTable, 'feed')
                    ->join(Phpfox::getT('friend'), 'f', 'f.user_id = feed.user_id AND f.friend_user_id = ' . Phpfox::getUserId())
                    ->where('feed.privacy IN(0,1,2) ' . $extra . ' AND feed.time_stamp > \'' . $iLastActiveTimeStamp . '\' AND feed.feed_reference = 0')
                    ->order($sOrder)
                    ->limit($iOffset, $iTotalFeeds, null, false, true)
                    ->union();
            }

            // Get my feeds
            if (!isset($this->_params['friends'])) {
                if (isset($this->_params['join_query']) && is_callable($this->_params['join_query'])) {
                    call_user_func($this->_params['join_query']);
                }
                $this->database()->select('DISTINCT feed.*')
                    ->from($this->_sTable, 'feed')
                    ->forceIndex('user_id')
                    ->where('feed.privacy IN(0,1,2,3,4) ' . $extra . ' AND feed.user_id = ' . Phpfox::getUserId() . ' AND feed.time_stamp > \'' . $iLastActiveTimeStamp . '\' AND feed.feed_reference = 0')
                    ->order($sOrder)
                    ->limit($iOffset, $iTotalFeeds, null, false, true)
                    ->union();
            }

            $sSelect = 'feed.*, u.view_id,  ' . Phpfox::getUserField();
            if (Phpfox::isModule('friend')) {
                $sSelect .= ', f.friend_id AS is_friend';
                $this->database()->leftJoin(Phpfox::getT('friend'), 'f', 'f.user_id = feed.user_id AND f.friend_user_id = ' . Phpfox::getUserId())
                    ->limit($iOffset, $iTotalFeeds, null, false, true)
                    ->order($sOrder);
            }

            $aRows = $this->database()->select($sSelect)
                ->unionFrom('feed')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = feed.user_id')
                ->where($checkUpdateWhere)
                ->order($sOrder)
                ->limit(0, $iTotalFeeds)
                ->execute('getSlaveRows');
        } elseif (!$this->isSearchHashtag()) {
            $r_key = 'public_feeds';
            // no search
            $sMoreWhere = '';
            if ($checkUpdateWhere) {
                $sMoreWhere = ' AND ' . $checkUpdateWhere;
            }
            (($sPlugin = Phpfox_Plugin::get('feed.service_feed_get_buildquery')) ? eval($sPlugin) : '');

            if (isset($this->_params['search']) && !empty($this->_params['search'])) {
                $extra .= ' AND feed.content LIKE \'%' . $this->database()->escape($this->_params['search']) . '%\'';
            }

            if (isset($this->_params['parent_feed_id']) && is_numeric($this->_params['parent_feed_id'])) {
                $extra .= ' AND feed.parent_feed_id = ' . $this->_params['parent_feed_id'];
            }

            if ($is_app && isset($this->_params['when']) && $this->_params['when']) {
                $iTimeDisplay = Phpfox::getLib('date')->mktime(0, 0, 0, Phpfox::getTime('m'), Phpfox::getTime('d'), Phpfox::getTime('Y'));
                switch ($params['when']) {
                    case 'today':
                        $iEndDay = Phpfox::getLib('date')->mktime(23, 59, 0, Phpfox::getTime('m'), Phpfox::getTime('d'), Phpfox::getTime('Y'));
                        $extra .= ' AND (' . 'feed.time_stamp' . ' >= \'' . Phpfox::getLib('date')->convertToGmt($iTimeDisplay) . '\' AND ' . 'feed.time_stamp' . ' < \'' . Phpfox::getLib('date')->convertToGmt($iEndDay) . '\')';
                        break;
                    case 'this-week':
                        $extra .= ' AND ' . 'feed.time_stamp' . ' >= ' . (int)Phpfox::getLib('date')->convertToGmt(Phpfox::getLib('date')->getWeekStart());
                        $extra .= ' AND ' . 'feed.time_stamp' . ' <= ' . (int)Phpfox::getLib('date')->convertToGmt(Phpfox::getLib('date')->getWeekEnd());
                        break;
                    case 'this-month':
                        $extra .= ' AND ' . 'feed.time_stamp' . ' >= \'' . Phpfox::getLib('date')->convertToGmt(Phpfox::getLib('date')->getThisMonth()) . '\'';
                        $iLastDayMonth = Phpfox::getLib('date')->mktime(0, 0, 0, date('n'), Phpfox::getLib('date')->lastDayOfMonth(date('n')), date('Y'));
                        $extra .= ' AND ' . 'feed.time_stamp' . ' <= \'' . Phpfox::getLib('date')->convertToGmt($iLastDayMonth) . '\'';
                        break;
                    default:
                        break;
                }
            }

            if (Phpfox::isModule('friend')) {
                // Get my friends feeds
                if (isset($this->_params['join_query']) && is_callable($this->_params['join_query'])) {
                    call_user_func($this->_params['join_query']);
                }
                $this->database()->select('DISTINCT feed.*')
                    ->from($this->_sTable, 'feed')
                    ->join(Phpfox::getT('friend'), 'f', 'f.user_id = feed.user_id AND f.friend_user_id = ' . Phpfox::getUserId())
                    ->where('feed.privacy IN(1,2) ' . $extra . ' AND feed.time_stamp > \'' . $iLastActiveTimeStamp . '\' AND feed.feed_reference = 0' . $sMoreWhere)
                    ->order($sOrder)
                    ->limit($iOffset, $iTotalFeeds, null, false, true)
                    ->group('feed.feed_id')
                    ->union();

                // Get my friends of friends feeds
                if (isset($this->_params['join_query']) && is_callable($this->_params['join_query'])) {
                    call_user_func($this->_params['join_query']);
                }
                $this->database()->select('DISTINCT feed.*')
                    ->from($this->_sTable, 'feed')
                    ->join(Phpfox::getT('friend'), 'f1', 'f1.user_id = feed.user_id')
                    ->join(Phpfox::getT('friend'), 'f2', 'f2.user_id = ' . Phpfox::getUserId() . ' AND f2.friend_user_id = f1.friend_user_id')
                    ->where('feed.privacy IN(2) ' . $extra . ' AND feed.time_stamp > \'' . $iLastActiveTimeStamp . '\' AND feed.feed_reference = 0' . $sMoreWhere)
                    ->group('feed.feed_id')
                    ->order($sOrder)
                    ->limit($iOffset, $iTotalFeeds, null, false, true)
                    ->union();
            }

            // Get my feeds
            if (isset($this->_params['join_query']) && is_callable($this->_params['join_query'])) {
                call_user_func($this->_params['join_query']);
            }
            $this->database()->select('DISTINCT feed.*')
                ->from($this->_sTable, 'feed')
                ->where('feed.privacy IN(1,2,3,4) ' . $extra . ' AND feed.user_id = ' . Phpfox::getUserId() . ' AND feed.time_stamp > \'' . $iLastActiveTimeStamp . '\' AND feed.feed_reference = 0' . $sMoreWhere)
                ->order($sOrder)
                ->limit($iOffset, $iTotalFeeds, null, false, true)
                ->union();

            // Get public feeds
            if (isset($this->_params['join_query']) && is_callable($this->_params['join_query'])) {
                call_user_func($this->_params['join_query']);
            }
            $this->database()->select('DISTINCT feed.*')
                ->from($this->_sTable, 'feed')
                ->where('feed.privacy IN(0) ' . $extra . ' AND feed.time_stamp > \'' . $iLastActiveTimeStamp . '\' AND feed.feed_reference = 0' . $sMoreWhere)
                ->order($sOrder)
                ->limit($iOffset, $iTotalFeeds, null, false, true)
                ->union();

            if (Phpfox::isModule('privacy')) {
                $this->database()->join(Phpfox::getT('privacy'), 'p', 'p.module_id = feed.type_id AND p.item_id = feed.item_id')
                    ->join(Phpfox::getT('friend_list_data'), 'fld', 'fld.list_id = p.friend_list_id AND fld.friend_user_id = ' . Phpfox::getUserId() . '');

            }
            // Get feeds based on custom friends lists
            if (isset($this->_params['join_query']) && is_callable($this->_params['join_query'])) {
                call_user_func($this->_params['join_query']);
            }
            $this->database()->select('DISTINCT feed.*')
                ->from($this->_sTable, 'feed')
                ->where('feed.privacy IN(4) ' . $extra . ' AND feed.time_stamp > \'' . $iLastActiveTimeStamp . '\' AND feed.feed_reference = 0 ' . $sMoreWhere)
                ->order($sOrder)
                ->limit($iOffset, $iTotalFeeds, null, false, true)
                ->union();

            $sSelect = 'feed.*, u.view_id,  ' . Phpfox::getUserField();
            if (Phpfox::isModule('friend')) {
                $sSelect .= ', f.friend_id AS is_friend';
                $this->database()->leftJoin(Phpfox::getT('friend'), 'f', 'f.user_id = feed.user_id AND f.friend_user_id = ' . Phpfox::getUserId());
            }

            $aRows = $this->database()->select($sSelect)
                ->unionFrom('feed')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = feed.user_id')
                ->where($checkUpdateWhere)
                ->order($sOrder)
                ->limit(0, $iTotalFeeds)
                ->execute('getSlaveRows');
        } else {
            // Search hashtag
            $sOrder = 'feed.time_update DESC';

            $sTag = $this->getSearchHashtag();
            $sTag = \Phpfox_Parse_Output::instance()->parse($sTag);
            //https://github.com/moxi9/phpfox/issues/595
            $sTag = Phpfox::getLib('parse.input')->clean($sTag, 255);
            $sTag = mb_convert_case($sTag, MB_CASE_LOWER, "UTF-8");
            $sTag = Phpfox_Database::instance()->escape($sTag);


            $sMoreWhere = '';
            if ($checkUpdateWhere) {
                $sMoreWhere = ' AND ' . $checkUpdateWhere;
            }

            $sMyFeeds = '0,1,2,3,4';

            (($sPlugin = Phpfox_Plugin::get('feed.service_feed_get_buildquery')) ? eval($sPlugin) : '');

            if (Phpfox::isModule('friend')) {
                // Get my friends feeds
                $this->database()->select('DISTINCT feed.*')
                    ->from($this->_sTable, 'feed')
                    ->join(Phpfox::getT('friend'), 'f', 'f.user_id = feed.user_id AND f.friend_user_id = ' . Phpfox::getUserId())
                    ->join(Phpfox::getT('tag'), 'hashtag', 'hashtag.item_id=feed.item_id AND hashtag.category_id = feed.type_id AND (tag_text = \'' . $sTag . '\' OR tag_url = \'' . $sTag . '\')')
                    ->where('feed.privacy IN(1,2) ' . $extra . ' AND feed.time_stamp > \'' . $iLastActiveTimeStamp . '\'' . $sMoreWhere)
                    ->order($sOrder)
                    ->limit($iOffset, $iTotalFeeds, null, false, true)
                    ->union();

                // Get my friends of friends feeds
                $this->database()->select('DISTINCT feed.*')
                    ->from($this->_sTable, 'feed')
                    ->join(Phpfox::getT('friend'), 'f1', 'f1.user_id = feed.user_id')
                    ->join(Phpfox::getT('friend'), 'f2', 'f2.user_id = ' . Phpfox::getUserId() . ' AND f2.friend_user_id = f1.friend_user_id')
                    ->join(Phpfox::getT('tag'), 'hashtag', 'hashtag.item_id=feed.item_id AND hashtag.category_id = feed.type_id AND (tag_text = \'' . $sTag . '\' OR tag_url = \'' . $sTag . '\')')
                    ->where('feed.privacy IN(2) ' . $extra . ' AND feed.time_stamp > \'' . $iLastActiveTimeStamp . '\'' . $sMoreWhere)
                    ->order($sOrder)
                    ->limit($iOffset, $iTotalFeeds, null, false, true)
                    ->union();
            }

            // Get my feeds
            $this->database()->select('DISTINCT feed.*')
                ->from($this->_sTable, 'feed')
                ->where('feed.privacy IN(' . $sMyFeeds . ') ' . $extra . ' AND feed.user_id = ' . Phpfox::getUserId() . ' AND feed.time_stamp > \'' . $iLastActiveTimeStamp . '\'' . $sMoreWhere)
                ->join(Phpfox::getT('tag'), 'hashtag', 'hashtag.item_id=feed.item_id AND hashtag.category_id = feed.type_id AND (tag_text = \'' . $sTag . '\' OR tag_url = \'' . $sTag . '\')')
                ->limit($iOffset, $iTotalFeeds, null, false, true)
                ->union();

            // Get public feeds
            $this->database()->select('DISTINCT feed.*')
                ->from($this->_sTable, 'feed')
                ->join(Phpfox::getT('tag'), 'hashtag', 'hashtag.item_id=feed.item_id AND hashtag.category_id = feed.type_id AND (tag_text = \'' . $sTag . '\' OR tag_url = \'' . $sTag . '\')')
                ->where('feed.privacy IN(0) ' . $extra . ' AND feed.time_stamp > \'' . $iLastActiveTimeStamp . '\' AND feed.feed_reference = 0' . $sMoreWhere)
                ->order($sOrder)
                ->limit($iOffset, $iTotalFeeds, null, false, true)
                ->union();

            if (Phpfox::isModule('privacy')) {
                $this->database()->join(Phpfox::getT('privacy'), 'p', 'p.module_id = feed.type_id AND p.item_id = feed.item_id')
                    ->join(Phpfox::getT('friend_list_data'), 'fld', 'fld.list_id = p.friend_list_id AND fld.friend_user_id = ' . Phpfox::getUserId() . '');

            }
            // Get feeds based on custom friends lists
            $this->database()->select('DISTINCT feed.*')
                ->from($this->_sTable, 'feed')
                ->join(Phpfox::getT('tag'), 'hashtag', 'hashtag.item_id=feed.item_id AND hashtag.category_id = feed.type_id AND (tag_text = \'' . $sTag . '\' OR tag_url = \'' . $sTag . '\')')
                ->where('feed.privacy IN(4) ' . $extra . ' AND feed.time_stamp > \'' . $iLastActiveTimeStamp . '\'' . $sMoreWhere)
                ->order($sOrder)
                ->limit($iOffset, $iTotalFeeds, null, false, true)
                ->union();

            $sSelect = 'feed.*, u.view_id,  ' . Phpfox::getUserField();
            if (Phpfox::isModule('friend')) {
                $sSelect .= ', f.friend_id AS is_friend';
                $this->database()->leftJoin(Phpfox::getT('friend'), 'f', 'f.user_id = feed.user_id AND f.friend_user_id = ' . Phpfox::getUserId());
            }

            $aRows = $this->database()->select($sSelect)
                ->unionFrom('feed')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = feed.user_id')
                ->order($sOrder)
                ->limit(0, $iTotalFeeds)
                ->execute('getSlaveRows');
        }

        if ($bForceReturn === true) {
            return $aRows;
        }

        $bFirstCheckOnComments = false;
        if (Phpfox::isUser() && Phpfox::isModule('comment')) {
            $bFirstCheckOnComments = true;
        }

        $aFeeds = [];
        $aParentFeeds = [];
        foreach ($aRows as $sKey => $aRow) {
            if ($iLastFeedId) $iLastFeedId = $aRow['feed_id'];
            if ($aRow['parent_module_id'] && !Phpfox::hasCallback($aRow['parent_module_id'], 'getActivityFeed')) continue;
            $aRow['feed_time_stamp'] = $aRow['time_stamp'];

            if (($aReturn = $this->_processFeed($aRow, $sKey, $iUserId, $bFirstCheckOnComments))) {
                if (isset($aReturn['force_user'])) {
                    $aReturn['user_name'] = $aReturn['force_user']['user_name'];
                    $aReturn['full_name'] = $aReturn['force_user']['full_name'];
                    $aReturn['user_image'] = $aReturn['force_user']['user_image'];
                    $aReturn['server_id'] = $aReturn['force_user']['server_id'];
                }

                $aReturn['feed_month_year'] = date('m_Y', $aRow['feed_time_stamp']);
                $aReturn['feed_time_stamp'] = $aRow['feed_time_stamp'];

                /* Lets figure out the phrases for like.display right here */
                if (Phpfox::isModule('like')) {
                    $this->getPhraseForLikes($aReturn);
                }
                $aFeeds[] = $aReturn;
            }

            // Show the feed properly. If user A posted on page 1, then feed will say "user A > page 1 posted ..."
            $aCustomModule = [
                'pages',
                'groups',
            ];
            (($sPlugin = Phpfox_Plugin::get('feed.service_feed_get_custom_module')) ? eval($sPlugin) : false);

            if (isset($this->_aCallback['module']) && in_array($this->_aCallback['module'], $aCustomModule)) {
                // If defined parent user, and the parent user is not the same page (logged in as a page)
                if (isset($aRow['page_user_id']) && $aReturn['page_user_id'] != $aReturn['user_id']) {
                    $aParentFeeds[$aReturn['feed_id']] = $aRow['page_user_id'];
                }
            } elseif (isset($this->_aCallback['module']) && $this->_aCallback['module'] == 'event') {
                // Keep it empty
                $aParentFeeds = [];
            } elseif (isset($aRow['parent_user_id']) && !isset($aRow['parent_user']) && $aRow['type_id'] != 'friend') {
                if (!empty($aRow['parent_user_id'])) {
                    $aParentFeeds[$aRow['feed_id']] = $aRow['parent_user_id'];
                }
            }
        }

        if ($iLoopCount <= 50 && empty($aFeeds) && (count($aRows) == $iTotalFeeds)) {
            $iLoopCount++;

            return $this->get($iUserId, $iFeedId, ++$iPage, $bForceReturn, $bLimit, $iLastFeedId);
        }

        // Get the parents for the feeds so it displays arrow.png
        if (!empty($aParentFeeds)) {
            $search = implode(',', array_values($aParentFeeds));
            if (!empty($search)) {
                $aParentUsers = $this->database()->select(Phpfox::getUserField())
                    ->from(Phpfox::getT('user'), 'u')
                    ->where('user_id IN (' . $search . ')')
                    ->execute('getSlaveRows');

                $aFeedsWithParents = array_keys($aParentFeeds);
                foreach ($aFeeds as $sKey => $aRow) {
                    if (in_array($aRow['feed_id'], $aFeedsWithParents) && $aRow['type_id'] != 'photo_tag') {
                        foreach ($aParentUsers as $aUser) {
                            if ($aUser['user_id'] == $aRow['parent_user_id']) {
                                $aTempUser = [];
                                foreach ($aUser as $sField => $sVal) {
                                    $aTempUser['parent_' . $sField] = $sVal;
                                }
                                $aFeeds[$sKey]['parent_user'] = $aTempUser;
                            }
                        }
                    }
                }
            }
        }

        $oReq = Phpfox_Request::instance();
        if (($oReq->getInt('status-id')
                || $oReq->getInt('comment-id')
                || $oReq->getInt('link-id')
                || $oReq->getInt('poke-id')
            )
            && isset($aFeeds[0])
        ) {
            $aFeeds[0]['feed_view_comment'] = true;
        }

        return $aFeeds;
    }

    /**
     * @param array $aRow
     * @param string $sKey
     * @param int $iUserId
     * @param bool $bFirstCheckOnComments
     *
     * @return array|bool
     */
    private function _processFeed($aRow, $sKey, $iUserId, $bFirstCheckOnComments)
    {
        $original = (isset($aRow['content']) ? $aRow['content'] : '');
        switch ($aRow['type_id']) {
            case 'comment_profile':
            case 'comment_profile_my':
                $aRow['type_id'] = 'profile_comment';
                break;
            case 'profile_info':
                $aRow['type_id'] = 'custom';
                break;
            case 'comment_photo':
                $aRow['type_id'] = 'photo_comment';
                break;
            case 'comment_blog':
                $aRow['type_id'] = 'blog_comment';
                break;
            case 'comment_video':
                $aRow['type_id'] = 'video_comment';
                break;
            case 'comment_group':
                $aRow['type_id'] = 'pages_comment';
                break;
        }

        /*Ynfeed view pricacy*/
        $iViewerId = Phpfox::getUserId();
        $bCanViewTaggedPosts = Phpfox::getService('user.privacy')->hasAccess($iUserId, 'ynfeed_view_tagged_posts_on_wall');
        $bCanViewCommentPosts = Phpfox::getService('user.privacy')->hasAccess($iUserId, 'ynfeed_view_other_posts_on_wall');
        if ($iUserId) { //view someone's wall
            if (($aRow['user_id'] != $iUserId) &&      //Post by other
                ($iViewerId != $iUserId)    //View by other
            ) {
                if (($aRow['parent_user_id'] == $iUserId)) { //comment on wall
                    if (!$bCanViewCommentPosts)
                        return false;
                } else { // tagged feed
                    if (!$bCanViewTaggedPosts)
                        return false;
                }
            }
        }

        if (preg_match('/(.*)_feedlike/i', $aRow['type_id'])
            || $aRow['type_id'] == 'profile_design'
        ) {
            $this->database()->delete(Phpfox::getT('feed'), 'feed_id = ' . (int)$aRow['feed_id']);

            return false;
        }
        try {
            $App = (new Core\App())->get($aRow['type_id']);
            $isApp = true;
        } catch (\Exception $e) {
            $isApp = false;
        }

        if (!$isApp && !Phpfox::hasCallback($aRow['type_id'], 'getActivityFeed')) {
            return false;
        }

        if (isset($App) && $isApp) {
            $aMap = $aRow;
            if ($aRow['parent_feed_id']) {
                $aRow['main_feed_id'] = $aRow['feed_id'];
                $aMap['feed_id'] = $aRow['parent_feed_id'];
                $aRow['feed_id'] = $aRow['parent_feed_id'];

            }
            $aRow['ori_item_id'] = $aRow['feed_id'];
            $aRow['item_id'] = $aRow['feed_id'];
            $Map = $App->map($aRow['content'], $aMap);
            $Map->data_row = $aRow;
            Core\Event::trigger('feed_map', $Map);
            //add the app_id for event name to avoid conflict with another apps. (Rob)
            Core\Event::trigger('feed_map_' . $App->id, $Map);
            if ($Map->error) {
                return false;
            }

            $aFeed = [
                'feed_table_prefix' => $Map->feed_table_prefix,
                'is_app' => true,
                'app_object' => $App->id,
                'feed_link' => $Map->link,
                'feed_title' => $Map->title,
                'feed_info' => $Map->feed_info,
                'item_id' => $aRow['feed_id'],
                'comment_type_id' => 'app',
                'like_type_id' => 'app',
                'feed_total_like' => (int)$this->database()->select('COUNT(*)')->from(':like')->where(['type_id' => 'app', 'item_id' => $aRow['feed_id'], 'feed_table' => ($Map->feed_table_prefix . 'feed')])->execute('getSlaveField'),
                'total_comment' => (int)$this->database()->select('COUNT(*)')->from(':comment')->where(['type_id' => 'app', 'item_id' => $aRow['feed_id'], 'feed_table' => ($Map->feed_table_prefix . 'feed')])->execute('getSlaveField'),
                'feed_is_liked' => ($this->database()->select('COUNT(*)')->from(':like')->where(['type_id' => 'app', 'item_id' => $aRow['feed_id'], 'user_id' => Phpfox::getUserId()])->execute('getSlaveField') ? true : false),
            ];

            if ($Map->content) {
                $aFeed['app_content'] = $Map->content;
            }

            if ($Map->more_params) {
                $aFeed = array_merge($aFeed, $Map->more_params);
            }
        } else {
            if ($aRow['type_id'] == 'photo') {
                $aFeed = $this->getActivityFeedPhoto($aRow, (isset($this->_aCallback['module']) ? $this->_aCallback : null));
            } else {
                $aFeed = Phpfox::callback($aRow['type_id'] . '.getActivityFeed', $aRow, (isset($this->_aCallback['module']) ? $this->_aCallback : null));
            }

            if (!empty($aRow['parent_feed_id']) && (new Core\App())->exists($aRow['parent_module_id'])) {
                $parent = $this->get(['id' => $aRow['parent_feed_id'], 'bIsChildren' => true]);
                if (isset($parent[0]) && isset($parent[0]['feed_id']) && (new Core\App())->exists($parent[0]['type_id'])) {
                    $aFeed['parent_is_app'] = $parent[0]['feed_id'];
                    if (Phpfox::hasCallback($parent[0]['type_id'], 'getActivityFeed')) {
                        $aFeed['parent_module_id'] = $parent[0]['type_id'];
                    }
                }
            }

            if ($aFeed === false) {
                return false;
            }
        }

        if (isset($this->_aViewMoreFeeds[$sKey])) {
            foreach ($this->_aViewMoreFeeds[$sKey] as $iSubKey => $aSubRow) {
                $mReturnViewMore = $this->_processFeed($aSubRow, $iSubKey, $iUserId, $bFirstCheckOnComments);

                if ($mReturnViewMore === false) {
                    continue;
                }
                $mReturnViewMore['call_displayactions'] = true;
                $aFeed['more_feed_rows'][] = $mReturnViewMore;
            }
        }

        if (Phpfox::isModule('like') && (isset($aFeed['like_type_id']) || isset($aRow['item_id'])) && ((isset($aFeed['enable_like']) && $aFeed['enable_like'])) || (!isset($aFeed['enable_like'])) && (isset($aFeed['feed_total_like']) && (int)$aFeed['feed_total_like'] > 0)) {
            $aFeed['likes'] = Phpfox::getService('like')->getLikesForFeed($aFeed['like_type_id'], (isset($aFeed['like_item_id']) ? $aFeed['like_item_id'] : $aRow['item_id']), ((int)$aFeed['feed_is_liked'] > 0 ? true : false), Phpfox::getParam('feed.total_likes_to_display'), true, (isset($aFeed['feed_table_prefix']) ? $aFeed['feed_table_prefix'] : ''));
            $aFeed['feed_total_like'] = Phpfox::getService('like')->getTotalLikeCount();
        }

        if (isset($aFeed['comment_type_id']) && (int)$aFeed['total_comment'] > 0 && Phpfox::isModule('comment')) {
            $aFeed['comments'] = Phpfox::getService('comment')->getCommentsForFeed($aFeed['comment_type_id'], $aRow['item_id'], Phpfox::getParam('comment.comment_page_limit'), null, null, (isset($aFeed['feed_table_prefix']) ? $aFeed['feed_table_prefix'] : ''));
        }

        $aRow['can_post_comment'] = true;
        $aFeed['bShowEnterCommentBlock'] = false;

        $aOut = array_merge($aRow, $aFeed);
        $aOut['_content'] = $original;
        $aOut['type_id'] = $aRow['type_id'];


        /*Ynfeed extra process*/
        $this->getExtraInfo($aOut);

        // check status background
        if (Phpfox::isAppActive('P_StatusBg')) {
            $aOut['status_background'] = Phpfox::getService('pstatusbg')->getFeedStatusBackground($aOut['item_id'], $aOut['type_id'], $aOut['user_id']);
        }
        
        if (($sPlugin = Phpfox_Plugin::get('feed.service_feed_processfeed'))) {
            eval($sPlugin);
        }
        return $aOut;
    }

    /**
     * @param $aFeed
     */
    public function getExtraInfo(&$aFeed)
    {
        $userId = Phpfox::getUserId();
        $isSavedFeed = null;
        $sCacheId = $this->cache()->set('ynfeed_extra_' . $aFeed['feed_id'] . '_' . $userId);
        if(($aFeedExtra = $this->cache()->get($sCacheId, 5)) === false) {
            $aExtra = db()->select('*')
                ->from(Phpfox::getT('ynfeed_extra_info'), 'ex')
                ->where("item_id = " . $aFeed['item_id'] . " AND ex.type_id = '" . $aFeed['type_id'] . "'")
                ->execute('getSlaveRow');

            list($aTagged, $aFeed['sTagInfo'], $bIsTagged) = $this->getTagInfo($aFeed['item_id'], $aFeed['type_id']);

            $aFeed['is_tagged'] = $bIsTagged;

            $aFeed['tagged'] = implode(',', array_column($aTagged, 'user_id'));

            if (!empty($aExtra)) {
                if (isset($aExtra['feeling_id']))
                    $aFeed['aFeeling'] = Phpfox::getService('ynfeed.feeling')->getFeelingFromExtraInfo($aExtra);
                if (isset($aExtra['business_id']) && $aExtra['business_id'] && Phpfox::isModule('directory')) {
                    /*Get business*/
                    if ($aBusiness = Phpfox::getService('ynfeed.directory')->getBussinessForMap([' AND dbus.business_id = ' . $aExtra['business_id']])) {
                        $aFeed['business_id'] = $aExtra['business_id'];
                        $aFeed['aBusiness'] = $aBusiness[0];
                        $aFeed['aBusiness']['sCheckinsInfo'] = Phpfox::getService('ynfeed.directory')->getCheckinsInfo($aExtra['business_id']);
                        $aFeed['aBusiness']['url'] = Phpfox::permalink('directory.detail', $aFeed['aBusiness']['business_id'], $aFeed['aBusiness']['name']);
                    }
                }
                if (isset($aExtra['location_latlng']) && isset($aExtra['location_name'])) {
                    $aFeed['location_latlng'] = json_decode($aExtra['location_latlng'], true);
                    $aFeed['location_name'] = $aExtra['location_name'];
                }
            }
            if (isset($aFeed['load_block']) && $aFeed['load_block'] != '') {
                $sFeedContent = isset($aFeed['feed_content']) ? $aFeed['feed_content'] : '';
                $aFeed['full_feed_content'] = ynfeed_strip($sFeedContent);
                $aFeed['feed_content'] = $sFeedContent;
            }
            $aFeed['report_module'] = $aFeed['type_id'];
            $aFeed['report_phrase'] = 'Report this feed';
            $aFeed['user_profile'] = ($aFeed['profile_page_id'] ? Phpfox::getService($aFeed['item_type'] == 1 ? 'groups' : 'pages')->getUrl($aFeed['profile_page_id'], '', $aFeed['user_name']) : Phpfox_Url::instance()->makeUrl($aFeed['user_name']));

            $isSavedFeed = $aFeed['is_saved'] = Phpfox::getService('ynfeed.save')->isSaved(Phpfox::getUserId(), $aFeed['feed_id']);
            $aTurnoff = db()->select('*')
                ->from(Phpfox::getT('ynfeed_turnoff_notification'))
                ->where("item_id = " . $aFeed['item_id'] . " AND user_id = " . Phpfox::getUserId() . " AND type_id = '" . $aFeed['type_id'] . "'")
                ->execute('getSlaveRow');
            if ($this->_aCallback)
                $aFeed['callback'] = $this->_aCallback;
            if (isset($aTurnoff['turnoff_id']) && $aTurnoff['turnoff_id'])
                $aFeed['is_noti_off'] = true;
            $this->cache()->save($sCacheId, $aFeed);
            $this->cache()->group('ynfeed_extra_feed_ ' . $aFeed['feed_id'], $sCacheId);
        }

        if(!isset($isSavedFeed)) {
            $aFeed['is_saved'] = Phpfox::getService('ynfeed.save')->isSaved(Phpfox::getUserId(), $aFeed['feed_id']);
        }

        $aFeed = !empty($aFeedExtra) ? array_merge($aFeedExtra, $aFeed) : $aFeed;
    }


    /**
     * @param $aCond
     * @param null $iUserId
     */
    public function getHideCondition(&$aCond, $iUserId = null)
    {
        $aHides = Phpfox::getService('ynfeed.hide')->getHide($iUserId);
        foreach ($aHides as $key => $aHide) {
            $aCond[] = ' AND feed.' . $key . '_id NOT IN (' . implode(',', $aHide) . ')';
        }
        if(Phpfox::VERSION >= '4.7.6' && ($coreFeedHideService = Phpfox::getService('feed.hide'))) {
            $coreHideConds = $coreFeedHideService->getHideCondition($iUserId);
            $textCondition = '';
            foreach($coreHideConds as $coreHideCond) {
                $textCondition .= ' AND ' . trim($coreHideCond, ' AND ');
            }
            if(!empty($textCondition)) {
                $aCond[] = $textCondition;
            }
        }
    }

    /**
     * @param array $aCallback
     *
     * @return $this
     */
    public function callback($aCallback)
    {
        $this->_aCallback = $aCallback;
        return $this;
    }

    /**
     * @param $aVals
     */
    public function setExtraInfo($aVals)
    {
        $aInsert = [
            'module' => (isset($aVals['module']) ? $aVals['module'] : 'feed'),
            'type_id' => (isset($aVals['type_id']) ? $aVals['type_id'] : ''),
            'table_prefix' => (isset($aVals['table_prefix']) ? $aVals['table_prefix'] : ''),
            'item_id' => (isset($aVals['item_id']) ? $aVals['item_id'] : 0),
        ];
        $aParams = [];
        if (isset($aVals['is_update']) && $aVals['is_update']) {
            $aInfo = db()->select('*')
                ->from(Phpfox::getT('ynfeed_extra_info'))
                ->where("item_id = " . (int)$aVals['item_id'] . " AND type_id = '" . $aVals['type_id'] . "'")
                ->execute('getSlaveRow');
            if ($aInfo) {
                $aInsert = [
                    'module' => $aInfo['module'],
                    'type_id' => $aInfo['type_id'],
                    'table_prefix' => $aInfo['table_prefix'],
                    'item_id' => $aInfo['item_id'],
                ];
            }
            /*delete old info*/
            db()->delete(Phpfox::getT('ynfeed_extra_info'), "item_id = " . (int)$aVals['item_id'] . " AND type_id = '" . $aVals['type_id'] . "'");
            db()->delete(Phpfox::getT('ynfeed_feed_map'), "item_id = " . (int)$aVals['item_id'] . " AND item_type = '" . $aVals['type_id'] . "'");
        }

        /*location*/
        if (isset($aVals['location']) && isset($aVals['location']['latlng']) && !empty($aVals['location']['latlng'])) {
            $aMatch = explode(',', $aVals['location']['latlng']);
            $aMatch['latitude'] = floatval($aMatch[0]);
            $aMatch['longitude'] = floatval($aMatch[1]);
            $aInsert['location_latlng'] = json_encode(array('latitude' => $aMatch['latitude'], 'longitude' => $aMatch['longitude']));
        }

        if (isset($aInsert['location_latlng']) && !empty($aInsert['location_latlng']) && isset($aVals['location']) && isset($aVals['location']['name']) && !empty($aVals['location']['name'])) {
            $aInsert['location_name'] = Phpfox::getLib('parse.input')->clean($aVals['location']['name']);
        }

        /*feeling*/
        if (isset($aVals['feeling']) && $iFeelingId = (int)$aVals['feeling']) {
            if (Phpfox::getService('ynfeed.feeling')->hasFeelingId($iFeelingId)) {
                $aInsert['feeling_id'] = $iFeelingId;
            } else {
                $aInsert['feeling_id'] = 0;
            }
            if (isset($aVals['custom_feeling_text']) && !empty($aVals['custom_feeling_text'])) {
                $aParams = array_merge($aParams, ['custom_feeling_text' => $aVals['custom_feeling_text']]);
            }
            if (isset($aVals['custom_feeling_image']) && !empty($aVals['custom_feeling_image'])) {
                $aParams = array_merge($aParams, ['custom_feeling_image' => $aVals['custom_feeling_image']]);
            }
        }

        /* Notify business checkin */
        if (isset($aVals['business']) && !empty($aVals['business']) && Phpfox::isModule('directory')) {
            $iBusinessId = (int)$aVals['business'];
            if ($iBusinessId) {
                $aInsert['business_id'] = $iBusinessId;
                /*Add checkin business*/
                Phpfox::getService('directory.process')->addCheckinhere($iBusinessId, Phpfox::getUserId());
                /* TODO: Send notification to business owner */
            }

        }

        $aInsert['params'] = json_encode($aParams);
        $iInfoId = db()->insert(Phpfox::getT('ynfeed_extra_info'), $aInsert);
        /*Tagged*/
        $aTaggedIds = array_filter(array_unique(explode(',', $aVals['tagged'])),
            function ($arrayEntry) {
                return is_numeric($arrayEntry);
            }
        );

        foreach ($aTaggedIds as $iTaggedId) {
            db()->insert(Phpfox::getT('ynfeed_feed_map'), array(
                'item_id' => $aVals['item_id'],
                'item_type' => $aVals['type_id'],
                'user_id' => (int)Phpfox::getUserId(),
                'parent_user_id' => $iTaggedId,
                'parent_user_type' => 'user',
                'type_id' => 'tag'
            ));
        }
        Phpfox::getService('feed.process')->updateTaggedUsers($aVals['item_id'], $aVals['type_id'], $aTaggedIds);

        /*Mention*/
        /* Support types: user/group/page */
        $sContent = (isset($aVals['user_status']) && !empty($aVals['user_status']) ? $aVals['user_status'] : (isset($aVals['status_info']) && !empty($aVals['status_info'] ? $aVals['status_info'] : '')));
        $aMentions = Phpfox::getService('ynfeed.process')->getMentions($sContent);

        foreach ($aMentions as $type => $aMention) {
            if (empty($aMention)) {
                continue;
            }

            foreach ($aMention as $iParentId) {
                /*Add to feed map*/
                db()->insert(Phpfox::getT('ynfeed_feed_map'), array(
                    'item_id' => $aVals['item_id'],
                    'item_type' => $aVals['type_id'],
                    'user_id' => (int)Phpfox::getUserId(),
                    'parent_user_id' => $iParentId,
                    'parent_user_type' => $type,
                    'type_id' => 'mention'
                ));
            }
        }

        (($sPlugin = Phpfox_Plugin::get('ynfeed.service_feed_set_extra_info__end')) ? eval($sPlugin) : false);
        return array($iInfoId, $aInsert);
    }

    /**
     * @param $iId
     * @param null $iUserId
     */
    public function processUpdateAjaxWithUserId($iId, $iUserId = null, $customError = false)
    {
        $oAjax = Phpfox_Ajax::instance();
        $aFeeds = Phpfox::getService('ynfeed')->get($iUserId, $iId);
        if (!isset($aFeeds[0])) {
            if($customError) {
                $oAjax->call("tb_remove();");
                $oAjax->call('setTimeout(function(){$Core.ynfeedResetActivityFeedForm();$Core.loadInit();}, 500);');
                $oAjax->call('setTimeout(function(){window.parent.sCustomMessageString = \'' . _p('this_item_has_successfully_been_submitted') . '\';tb_show(\'Notice\', $.ajaxBox(\'core.message\', \'height=150&width=300\'));}, 500);');
            }
            else {
                $oAjax->alert(_p('this_item_has_successfully_been_submitted'));
                $oAjax->call('setTimeout(function(){$Core.ynfeedResetActivityFeedForm();$Core.loadInit();}, 500);');
            }

            return;
        }

        $aFeeds[0]['can_like'] = isset($aFeeds[0]['like_type_id']) && empty($aFeeds[0]['disable_like_function']) &&
            !Phpfox::getService('user.block')->isBlocked(null, $aFeeds[0]['user_id']);

        $aFeeds[0]['can_comment'] = Phpfox::isModule('comment') && isset($aFeeds[0]['comment_type_id']) &&
            Phpfox::getUserParam('comment.can_post_comments') &&
            Phpfox::isUser() && $aFeeds[0]['can_post_comment'] && Phpfox::getUserParam('feed.can_post_comment_on_feed');

        $aFeeds[0]['can_share'] = Phpfox::isModule('share') && !isset($aFeeds[0]['no_share']) && !empty($aFeeds[0]['type_id']) && isset($aFeeds[0]['privacy']) && $aFeeds[0]['privacy'] == 0 && !Phpfox::getService('user.block')->isBlocked(null, $aFeeds[0]['user_id']);
        $aFeeds[0]['total_action'] = intval($aFeeds[0]['can_like']) + intval($aFeeds[0]['can_comment']) + intval($aFeeds[0]['can_share']);

        if (isset($this->_aCallback['module'])) {
            unset($aFeeds[0]['parent_user']);
        }

        $aShareServices = Phpfox::getService('ynfeed')->getShareProviders();
        if (isset($aFeeds[0]['type_id'])) {
            Phpfox_Template::instance()->assign([
                'aFeed' => $aFeeds[0],
                'aShareServices' => $aShareServices,
                'aFeedCallback' => [
                    'module' => !empty($this->_aCallback) ? $this->_aCallback['module'] : (preg_match('/_comment/') ? str_replace('_comment', '', $aFeeds[0]['type_id']) : null),
                    'item_id' => (!empty($this->_aCallback['item_id']) ? $this->_aCallback['item_id'] : (!empty($aFeeds[0]['item_id']) ? $aFeeds[0]['item_id'] : null))
                ],
            ])->getTemplate('ynfeed.block.entry');
        } else {
            Phpfox_Template::instance()->assign(['aFeed' => $aFeeds[0]])->getTemplate('ynfeed.block.entry');
        }

        $sId = 'js_tmp_comment_' . md5('feed_' . uniqid() . $iUserId) . '';

        $sNewContent = '<div id="' . $sId . '" class="js_temp_new_feed_entry js_feed_view_more_entry_holder">' . $oAjax->getContent(false) . '</div>';

        $oAjax->replaceWith('#js_item_feed_' . $iId, $sNewContent);
        $oAjax->call("tb_remove();");
        $oAjax->call('setTimeout(function(){$Core.ynfeedResetActivityFeedForm();$Core.loadInit();}, 500);');
    }

    public function getSponsoredFeed()
    {
        $aHides = Phpfox::getService('ynfeed.hide')->getHide();
        $sNotIn = '';
        if (isset($aHides['feed']) && count($aHides['feed'])) {
            $sNotIn .= ' AND sponsor.item_id NOT IN (' . implode(',', $aHides['feed']) . ')';
        }
        if (isset($aHides['user']) && count($aHides['user'])) {
            $sNotIn .= ' AND feed.user_id NOT IN (' . implode(',', $aHides['user']) . ')';
        }


        if(Phpfox::VERSION >= '4.7.6' && ($coreFeedHideService = Phpfox::getService('feed.hide'))) {
            $coreHideConds = $coreFeedHideService->getHideCondition();
            $textCondition = '';
            foreach($coreHideConds as $coreHideCond) {
                $textCondition .= ' AND ' . trim($coreHideCond, ' AND ');
            }
            if(!empty($textCondition)) {
                $sNotIn .= $textCondition;
            }
        }

        $do_cache = false;
        if (empty($sNotIn)) {
            $cache = cache('feed/ad');
            if (!$cache->exists()) {
                $do_cache = true;
            } else {
                $v = $cache->get(null, 60);
                return ($v === true ? false : (int)$v);
            }
        }

        $iSponsoredAdItem = $this->database()->select('sponsor.item_id as item_id')
            ->from(Phpfox::getT('ad_sponsor'), 'sponsor')
            ->join(Phpfox::getT('feed'), 'feed', 'feed.feed_id = sponsor.item_id')
            ->where('sponsor.is_active = 1 AND sponsor.module_id = \'feed\' AND sponsor.is_custom IN (0,3)' . $sNotIn)// 0 => free, 1 => pending payment, 2 => pending approval, 3 => approved, 4 => denied
            ->group('sponsor.item_id', true)
            ->order('rand()')
            ->execute('getSlaveField');

        if ($do_cache) {
            $cache->set($iSponsoredAdItem);
        }

        if ($iSponsoredAdItem) {
            return $iSponsoredAdItem;
        } else {
            return false;
        }
    }

    public function getFeed($iId, $sPrefix = '')
    {
        return $this->database()->select('*')
            ->from(Phpfox::getT(($sPrefix ? $sPrefix : (isset($this->_aCallback['table_prefix']) ? $this->_aCallback['table_prefix'] : '')) . 'feed'))
            ->where('feed_id =' . (int)$iId)
            ->executeRow();
    }

    /**
     * @param string $sTypeId
     * @param int $iItemId
     *
     * @return array
     */
    public function getParentFeedItem($sTypeId, $iItemId)
    {
        $aRow = $this->database()->select('f.*,' . Phpfox::getUserField('u'))
            ->from(':feed', 'f')
            ->join(':user', 'u', 'u.user_id=f.user_id')
            ->where('type_id=\'' . $sTypeId . '\' AND item_id=' . (int)$iItemId)
            ->executeRow();
        return $aRow;
    }

    public function createPreviewFeed($aVals)
    {
        $aFeed = array();
        $aFeed['parent_module_id'] = $aVals['sShareModule'];
        $aFeed['parent_feed_id'] = $aVals['iFeedId'];
        return $aFeed;
    }

    public function getActivityFeedPhoto($aItem, $aCallback = null, $bIsChildItem = false)
    {
        $iNumberOfPhoto = 5;
        $sThickbox = '';
        $sFeedTable = 'feed';
        $iFeedId = isset($aItem['feed_id']) ? $aItem['feed_id'] : 0;

        $cache = storage()->get('photo_parent_feed_' . $iFeedId);
        if ($cache) {
            $iFeedId = $cache->value;
        }

        $aPhotoIte = Phpfox::getService('photo')->getPhotoItem($aItem['item_id']);
        if (isset($aPhotoIte['module_id']) && $aPhotoIte['module_id'] && !Phpfox::isModule($aPhotoIte['module_id'])) {
            return false;
        }

        (($sPlugin = Phpfox_Plugin::get('photo.component_service_callback_getactivityfeed__get_item_before')) ? eval($sPlugin) : false);

        if ($aCallback === null) {
            db()->select(Phpfox::getUserField('u', 'parent_') . ', ')->leftJoin(Phpfox::getT('user'), 'u',
                'u.user_id = photo.parent_user_id');
        }

        if ($bIsChildItem) {
            db()->select(Phpfox::getUserField('u2') . ', ')->join(Phpfox::getT('user'), 'u2',
                'u2.user_id = photo.user_id');
        }

        $sSelect = 'photo.*, pi.description, pi.location_latlng, pi.location_name, pfeed.photo_id AS extra_photo_id, pa.album_id, pa.name, pa.timeline_id';

        if (Phpfox::isModule('like')) {
            $sSelect .= ', l.like_id AS is_liked';
            db()->leftJoin(Phpfox::getT('like'), 'l',
                'l.type_id = \'photo\' AND l.item_id = photo.photo_id AND l.user_id = ' . Phpfox::getUserId());
        }
        $aPhotoIds = [$aItem['item_id']];

        $aRow = db()->select($sSelect)
            ->from(Phpfox::getT('photo'), 'photo')
            ->join(Phpfox::getT('photo_info'), 'pi', 'pi.photo_id = photo.photo_id')
            ->leftJoin(Phpfox::getT('photo_feed'), 'pfeed', 'pfeed.feed_id = ' . (int)$iFeedId)
            ->leftJoin(Phpfox::getT('photo_album'), 'pa', 'pa.album_id = photo.album_id')
            ->where('photo.photo_id = ' . (int)$aItem['item_id'])
            ->execute('getSlaveRow');

        if ($bIsChildItem) {
            $aItem = $aRow;
        }

        if (!isset($aRow['photo_id'])) {
            return false;
        }

        if (Phpfox::getUserParam('photo.can_view_photos')) {
            $sThickbox .= ' js_photo_item_' . $aRow['photo_id'] . ' ';
        }

        if (((defined('PHPFOX_IS_PAGES_VIEW') && defined('PHPFOX_PAGES_ITEM_TYPE') && !Phpfox::getService(PHPFOX_PAGES_ITEM_TYPE)->hasPerm(null,
                        'photo.view_browse_photos'))
                || (!defined('PHPFOX_IS_PAGES_VIEW') && $aRow['module_id'] == 'pages' && Phpfox::isModule('pages') && !Phpfox::getService('pages')->hasPerm($aRow['group_id'],
                        'photo.view_browse_photos')))
            || ($aRow['module_id'] && Phpfox::isModule($aRow['module_id']) && Phpfox::hasCallback($aRow['module_id'],
                    'canShareOnMainFeed') && !Phpfox::callback($aRow['module_id'] . '.canShareOnMainFeed',
                    $aRow['group_id'], 'photo.view_browse_photos', $bIsChildItem))
        ) {
            return false;
        }

        $bIsPhotoAlbum = false;
        if ($aRow['album_id'] && $aRow['timeline_id'] == 0) {
            $bIsPhotoAlbum = true;
        }
        $sLink = Phpfox::permalink('photo', $aRow['photo_id'],
                $aRow['title']) . ('feed_' . $iFeedId);
        $sFeedImageOnClick = '';

        if (($aRow['mature'] == 0 || (($aRow['mature'] == 1 || $aRow['mature'] == 2) && Phpfox::getUserId() && Phpfox::getUserParam('photo.photo_mature_age_limit') <= Phpfox::getUserBy('age'))) || $aRow['user_id'] == Phpfox::getUserId()) {
            $iImageMaxSuffix = 1024;
            $sCustomCss = '' . $sThickbox . ' photo_holder_image';
            $sImage = Phpfox::getLib('image.helper')->display(array(
                    'server_id' => $aRow['server_id'],
                    'path' => 'photo.url_photo',
                    'file' => Phpfox::getService('photo')->getPhotoUrl(array_merge($aRow,
                        array('full_name' => $aItem['full_name']))),
                    'suffix' => '_' . $iImageMaxSuffix,
                    'class' => 'photo_holder',
                    'defer' => true
                )
            );

            $sImageReturnUrl = Phpfox::getLib('image.helper')->display(array(
                    'server_id' => $aRow['server_id'],
                    'path' => 'photo.url_photo',
                    'file' => Phpfox::getService('photo')->getPhotoUrl(array_merge($aRow,
                        array('full_name' => $aItem['full_name']))),
                    'suffix' => '_' . (isset($iImageMaxSuffix) ? $iImageMaxSuffix : 1024),
                    'return_url' => true,
                    'class' => 'photo_holder',
                    'defer' => true
                )
            );
        } else {
            $sImage = Phpfox::getLib('image.helper')->display(array(
                    'theme' => 'misc/mature.jpg'
                )
            );

            $sImageReturnUrl = Phpfox::getLib('image.helper')->display([
                    'theme' => 'misc/mature.jpg',
                    'return_url' => true
                ]
            );

            $sFeedImageOnClick = ' onclick="tb_show(\'' . _p('warning') . '\', $.ajaxBox(\'photo.warning\', \'height=300&width=350&link=' . $sLink . '\')); return false;" ';
            $sCustomCss = 'no_ajax_link';
        }

        $aListPhotos = array();

        if ($aRow['extra_photo_id'] > 0) {
            $total = db()->select('count(*)')
                ->from(Phpfox::getT('photo_feed'), 'pfeed')
                ->join(Phpfox::getT('photo'), 'p',
                    'p.photo_id = pfeed.photo_id' . (!empty($aRow['module_id']) ? ' AND p.module_id = \'' . db()->escape($aRow['module_id']) . '\'' : '') . ' AND pfeed.feed_table = \'' . $sFeedTable . '\'')
                ->where('pfeed.feed_id = ' . (isset($iFeedId) ? (int)$iFeedId : 0) . ' AND p.album_id = ' . (int)$aRow['album_id'])
                ->executeField();

            $aPhotos = db()
                ->select('p.photo_id, p.album_id, p.user_id, p.title, p.server_id, p.destination, p.mature')
                ->from(Phpfox::getT('photo_feed'), 'pfeed')
                ->join(Phpfox::getT('photo'), 'p',
                    'p.photo_id = pfeed.photo_id' . (!empty($aRow['module_id']) ? ' AND p.module_id = \'' . db()->escape($aRow['module_id']) . '\'' : '') . ' AND pfeed.feed_table = \'' . $sFeedTable . '\'')
                ->where('pfeed.feed_id = ' . (isset($iFeedId) ? (int)$iFeedId : 0) . ' AND p.album_id = ' . (int)$aRow['album_id'])
                ->limit($iNumberOfPhoto - 1)
                ->order('p.time_stamp DESC')
                ->execute('getSlaveRows');
            $aExtraPhotoId = array_map(function ($item) {
                return $item['photo_id'];
            }, $aPhotos);

            $iRemain = $total - count($aPhotos);
            $sMore = ($iRemain < 1000) ? '+' . $iRemain : Phpfox::getService('core.helper')->shortNumber($total - count($aPhotos));
            $aPhotoIds = array_merge($aPhotoIds, $aExtraPhotoId);

            foreach ($aPhotos as $aPhoto) {
                $indexing = count($aListPhotos);
                if ($indexing > $iNumberOfPhoto - 2) {
                    continue;
                }
                $sPhotoImage = Phpfox::getLib('image.helper')->display([
                        'server_id' => $aPhoto['server_id'],
                        'path' => 'photo.url_photo',
                        'file' => Phpfox::getService('photo')->getPhotoUrl(array_merge($aPhoto,
                            array('full_name' => $aItem['full_name']))),
                        'suffix' => '_500',
                        'return_url' => true,
                        'class' => 'photo_holder',
                        'userid' => isset($aItem['user_id']) ? $aItem['user_id'] : '',
                        'defer' => true // Further controlled in the library image.helper.
                    ]
                );

                if (($aPhoto['mature'] == 0 || (($aPhoto['mature'] == 1 || $aPhoto['mature'] == 2) && Phpfox::getUserId() && Phpfox::getUserParam('photo.photo_mature_age_limit') <= Phpfox::getUserBy('age'))) || $aPhoto['user_id'] == Phpfox::getUserId()) {
                    if ($indexing == $iNumberOfPhoto - 2 && $total > $iNumberOfPhoto - 1) {
                        $aListPhotos[] = '<a href="' . Phpfox::permalink('photo', $aPhoto['photo_id'],
                                $aPhoto['title']) . ('feed_' . $iFeedId) . '/" class="' . $sThickbox . ' photo_holder_image" rel="' . $aPhoto['photo_id'] . '" style="background-image: url(\'' . $sPhotoImage . '\')"><span>' . $sMore . '</span></a>';
                    } else {
                        $aListPhotos[] = '<a href="' . Phpfox::permalink('photo', $aPhoto['photo_id'],
                                $aPhoto['title']) . ('feed_' . $iFeedId) . '/" class="' . $sThickbox . ' photo_holder_image" rel="' . $aPhoto['photo_id'] . '" style="background-image: url(\'' . $sPhotoImage . '\')"></a>';
                    }
                } else {
                    if ($indexing == $iNumberOfPhoto - 2 && $total > $iNumberOfPhoto - 1) {
                        $aListPhotos[] = '<a href="' . Phpfox::permalink('photo', $aPhoto['photo_id'],
                                $aPhoto['title']) . ('feed_' . $iFeedId) . '/" class="' . $sThickbox . ' photo_holder_image" rel="' . $aPhoto['photo_id'] . '" style="background-image: url(\'' . $sPhotoImage . '\')"><span>' . $sMore . '</span></a>';
                    } else {
                        $aListPhotos[] = '<a href="#" class="no_ajax_link photo_holder_image" onclick="tb_show(\'' . _p('warning') . '\', $.ajaxBox(\'photo.warning\', \'height=300&width=350&link=' . Phpfox::permalink('photo',
                                $aPhoto['photo_id'],
                                $aPhoto['title']) . ('feed_' . $iFeedId) . '/\')); return false;" style="background-image: url(\'' . Phpfox::getLib('image.helper')->display([
                                'theme' => 'misc/mature.jpg',
                                'return_url' => true
                            ]) . '\')"></a>';
                    }
                }
            }
        }

        if (empty($aListPhotos)) {
            // in case we have only one image, use <img> instead of background-image
            $aListPhotos = [
                strtr('<a href="{href}" {other}><img src="{img_src}"></a>', [
                    '{href}' => Phpfox::permalink('photo', $aRow['photo_id'], $aRow['title']) . ('feed_' . $iFeedId),
                    '{other}' => empty($sFeedImageOnClick) ? ' class="' . $sThickbox . ' photo_holder_image photo-single-image" rel="' . $aRow['photo_id'] . '" ' : $sFeedImageOnClick . ' class="no_ajax_link photo-single-image"',
                    '{img_src}' => $sImageReturnUrl
                ])
            ];
        } else {
            $aListPhotos = array_merge(array(
                '<a href="' . Phpfox::permalink('photo', $aRow['photo_id'],
                    $aRow['title']) . ('feed_' . $iFeedId) . '/" ' . (empty($sFeedImageOnClick) ? ' class="' . $sThickbox . ' photo_holder_image" rel="' . $aRow['photo_id'] . '" ' : $sFeedImageOnClick . ' class="no_ajax_link photo_holder_image"') . ' style="background-image: url(\'' . $sImageReturnUrl . '\')"></a>'
            ), $aListPhotos);
        }

        $aListTags = Phpfox::getService('photo.tag')->getTagByIds($aPhotoIds, $aItem['user_id']);
        $sExtraTitle = '';
        $iTotalTag = count($aListTags);
        if ($iTotalTag) {
            if ($iTotalTag == 1) {
                $sExtraTitle .= _p('with_a_link', [
                    'link_name' => $aListTags[0]['user_name'],
                    'link' => Phpfox::getLib('phpfox.url')->makeUrl('profile',
                        $aListTags[0]['user_name']),
                    'link_title' => $aListTags[0]['full_name']
                ]);
            } elseif ($iTotalTag == 2) {
                $sExtraTitle .= _p('with_a_link_and_a_link', [
                    'link_name_1' => $aListTags[0]['user_name'],
                    'link_1' => Phpfox::getLib('phpfox.url')->makeUrl('profile',
                        $aListTags[0]['user_name']),
                    'link_title_1' => $aListTags[0]['full_name'],
                    'link_name_2' => $aListTags[1]['user_name'],
                    'link_2' => Phpfox::getLib('phpfox.url')->makeUrl('profile',
                        $aListTags[1]['user_name']),
                    'link_title_2' => $aListTags[1]['full_name']
                ]);
            } else {
                foreach ($aListTags as $iKey => $aUser) {
                    if ($iKey == 0) {
                        $sExtraTitle .= _p('with') . ' <span class="user_profile_link_span" id="js_user_name_link_' . $aUser['user_name'] . '"><a href="' . Phpfox::getLib('phpfox.url')->makeUrl('profile',
                                $aUser['user_name']) . '">' . $aUser['full_name'] . '</a></span>';
                        $sExtraTitle .= ' ' . _p('and') . ' ' . '<div class="dropdown" style="display: inline-block;"><a href="#" role="button" data-toggle="dropdown">' . ($iTotalTag - 1) . ' ' . _p('others') . '</a>';
                        $sExtraTitle .= '<ul class="dropdown-menu">';
                    } else {
                        $sExtraTitle .= '<li class="item"><span class="user_profile_link_span" id="js_user_name_link_' . $aUser['user_name'] . '"><a href="' . Phpfox::getLib('phpfox.url')->makeUrl('profile',
                                $aUser['user_name']) . '">' . $aUser['full_name'] . '</a></span></li>';
                    }
                }
                $sExtraTitle .= '</ul></div>';
            }
        }
        $aReturn = array(
            'feed_title' => '',
            'total_image' => ($total >= $iNumberOfPhoto ? 'more' : (count($aListPhotos) ? count($aListPhotos) : 1)),
            'feed_image' => (count($aListPhotos) ? $aListPhotos : $sImage),
            'feed_status' => $aRow['description'],
            'feed_link' => $sLink,
            'total_comment' => $aRow['total_comment'],
            'feed_total_like' => $aRow['total_like'],
            'feed_is_liked' => isset($aRow['is_liked']) ? $aRow['is_liked'] : false,
            'feed_icon' => Phpfox::getLib('image.helper')->display(array(
                'theme' => 'module/photo.png',
                'return_url' => true
            )),
            'time_stamp' => $aRow['time_stamp'],
            'enable_like' => true,
            'comment_type_id' => 'photo',
            'like_type_id' => 'photo',
            'custom_css' => $sCustomCss,
            'custom_rel' => $aRow['photo_id'],
            'custom_js' => $sFeedImageOnClick,
            'no_target_blank' => true,
            'custom_data_cache' => $aRow
        );

        if (!empty($aRow['location_name'])) {
            $aReturn['location_name'] = $aRow['location_name'];
        }
        if (!empty($aRow['location_latlng'])) {
            $aReturn['location_latlng'] = json_decode($aRow['location_latlng'], true);
        }
        if ($aRow['module_id'] == 'pages' || $aRow['module_id'] == 'groups' || $aRow['module_id'] == 'event') {
            $aRow['parent_user_id'] = '';
            $aRow['parent_user_name'] = '';
        }

        if (empty($aRow['parent_user_id'])) {
            if ($bIsPhotoAlbum) {
                $aReturn['feed_status'] = '';
                $aReturn['feed_info'] = _p('added_new_photos_to_gender_album_a_href_link_name_a', array(
                    'gender' => Phpfox::getService('user')->gender($aItem['gender'], 1),
                    'link' => Phpfox::permalink('photo.album', $aRow['album_id'], $aRow['name']),
                    'name' => Phpfox::getLib('locale')->convert(Phpfox::getLib('parse.output')->shorten(htmlspecialchars($aRow['name']),
                        (Phpfox::isModule('notification') ? Phpfox::getParam('notification.total_notification_title_length') : $this->_iFallbackLength),
                        '...'))
                ));
            } else {
                $aReturn['feed_info'] = (count($aListPhotos) > 1 ? _p('shared_a_few_photos') : _p('shared_a_photo'));
            }
        }

        if ($aCallback === null) {
            if (!empty($aRow['parent_user_name']) && !defined('PHPFOX_IS_USER_PROFILE') && empty($_POST)) {
                $aReturn['parent_user'] = Phpfox::getService('user')->getUserFields(true, $aRow, 'parent_');
            }

            if (!PHPFOX_IS_AJAX && defined('PHPFOX_IS_USER_PROFILE') && !empty($aRow['parent_user_name']) && $aRow['parent_user_id'] != Phpfox::getService('profile')->getProfileUserId()) {
                $aReturn['feed_mini'] = true;
                $aReturn['feed_mini_content'] = _p('full_name_posted_a_href_link_photo_a_photo_a_on_a_href_link_user_parent_full_name_a_s_a_href_link_wall_wall_a',
                    array(
                        'full_name' => Phpfox::getService('user')->getFirstName($aItem['full_name']),
                        'link_photo' => Phpfox::permalink('photo', $aRow['photo_id'], $aRow['title']),
                        'link_user' => Phpfox::getLib('url')->makeUrl($aRow['parent_user_name']),
                        'parent_full_name' => $aRow['parent_full_name'],
                        'link_wall' => Phpfox::getLib('url')->makeUrl($aRow['parent_user_name'])
                    ));

                unset($aReturn['feed_status'], $aReturn['feed_image'], $aReturn['feed_content']);
            }
        }

        if ($bIsChildItem) {
            $aReturn = array_merge($aReturn, $aItem);
        }

        $aReturn['type_id'] = 'photo';

        if (!defined('PHPFOX_IS_PAGES_VIEW') && (($aRow['module_id'] == 'groups' && Phpfox::isModule('groups')) || ($aRow['module_id'] == 'pages' && Phpfox::isModule('pages')))) {
            $aPage = db()->select('p.*, pu.vanity_url, ' . Phpfox::getUserField('u', 'parent_'))
                ->from(':pages', 'p')
                ->join(':user', 'u', 'p.page_id=u.profile_page_id')
                ->leftJoin(Phpfox::getT('pages_url'), 'pu', 'pu.page_id = p.page_id')
                ->where('p.page_id=' . (int)$aRow['group_id'])
                ->execute('getSlaveRow');

            if (empty($aPage)) {
                return false;
            }

            $aReturn['parent_user_name'] = Phpfox::getService($aRow['module_id'])->getUrl($aPage['page_id'],
                $aPage['title'], $aPage['vanity_url']);
            $aReturn['feed_table_prefix'] = 'pages_';
            if ($aRow['user_id'] != $aPage['parent_user_id']) {
                $aReturn['parent_user'] = Phpfox::getService('user')->getUserFields(true, $aPage, 'parent_');
                unset($aReturn['feed_info']);
            }
        }
        if (!empty($sExtraTitle)) {
            $aReturn['feed_info'] = (!empty($aReturn['feed_info']) ? $aReturn['feed_info'] : '') . ' - ' . $sExtraTitle;
        }
        (($sPlugin = Phpfox_Plugin::get('photo.component_service_callback_getactivityfeed__1')) ? eval($sPlugin) : false);
        return $aReturn;
    }

    public function getShareProviders()
    {
        $aShareServices = array(
            'facebook' => array(
                'icon' => 'ico-facebook-circle-o',
                'label' => _p('share_to_facebook')
            ),
            'twitter' => array(
                'icon' => 'ico-twitter-circle-o',
                'label' => _p('share_to_twitter')
            ),
            'linkedin' => array(
                'icon' => 'ico-linkedin-circle-o',
                'label' => _p('share_to_linkedin')
            ),
            'pinterest' => array(
                'icon' => 'ico-pinterest-circle-o',
                'label' => _p('share_to_pinterest')
            ),
            'gmail' => array(
                'icon' => 'ico-gplus-circle-alt-o',
                'label' => _p('share_to_gmail')
            ),
            'google_plusone_share' => array(
                'icon' => 'ico-gplus-circle-o',
                'label' => _p('share_to_google_plus')
            ),
            'tumblr' => array(
                'icon' => 'ico-share-alt-o',
                'label' => _p('share_to_tumblr')
            ),
        );

        return $aShareServices;
    }
}