<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\YNC_Feed\Ajax;

use Phpfox;
use Phpfox_Ajax;
use Phpfox_Database;
use Phpfox_Error;
use Phpfox_Image;
use Phpfox_Plugin;
use Phpfox_Url;

defined('PHPFOX') or exit('NO DICE!');

class Ajax extends Phpfox_Ajax
{
    public function checkNew()
    {
        $iLastFeedUpdate = $this->get('iLastFeedUpdate');
        define('PHPFOX_CHECK_FOR_UPDATE_FEED', true);
        define('PHPFOX_CHECK_FOR_UPDATE_FEED_UPDATE', $iLastFeedUpdate);

        Phpfox::getBlock('ynfeed.checknew');
        $this->call('ynfeedCheckNewReturn("' . $this->get('filter-module') . '", "' . $this->get('filter-type') . '","' . base64_encode($this->getContent(false)) . '")');
    }

    public function loadNew()
    {
        $iLastFeedUpdate = $this->get('iLastFeedUpdate');

        define('FEED_LOAD_MORE_NEWS', false);
        define('FEED_LOAD_NEW_NEWS', true);

        define('PHPFOX_CHECK_FOR_UPDATE_FEED', true);
        define('PHPFOX_CHECK_FOR_UPDATE_FEED_UPDATE', $iLastFeedUpdate);

        if ($this->get('callback_module_id') == 'pages' && Phpfox::getService('pages')->isTimelinePage($this->get('callback_item_id'))) {
            define('PAGE_TIME_LINE', true);
        }

        Phpfox::getBlock('ynfeed.display', ['bIsFilterPosts' => true]);
        $content = $this->getContent(false);
        if(!strpos($content, 'js_no_feed_to_show')) {

            if (!$this->get('forceview') && !$this->get('resettimeline')) {
                $this->html('#js_new_feed_comment', '');
                $this->insertAfter('#js_new_feed_comment', $content);
            } else {
                $this->html('#js_new_feed_comment', '');
                $this->insertAfter('#js_new_feed_comment', $content);
            }
            $this->call('$Core.loadInit();');
        }
    }

    public function loadDropDates()
    {
        Phpfox::getBlock('feed.loaddates');

        $sContent = $this->getContent(false);
        $sContent = str_replace(array("\n", "\t"), '', $sContent);

        $this->html('.timeline_date_holder_share', $sContent);
    }

    public function share()
    {
        $aPost = $this->get('val');
        if ($aPost['post_type'] == '2') {
            if (!isset($aPost['friends']) || (isset($aPost['friends']) && !count($aPost['friends']))) {
                Phpfox_Error::set(_p('select_a_friend_to_share_this_with_dot'));
            } else {
                $iCnt = 0;
                foreach ($aPost['friends'] as $iFriendId) {
                    $aVals = array(
                        'user_status' => $aPost['post_content'],
                        'parent_user_id' => $iFriendId,
                        'parent_feed_id' => $aPost['parent_feed_id'],
                        'parent_module_id' => $aPost['parent_module_id'],
                        'tagged' => $aPost['tagged'],
                        'location' => $aPost['location'],
                        'feeling' => $aPost['feeling'],
                        'custom_feeling_text' => $aPost['custom_feeling_text'],
                        'custom_feeling_image' => $aPost['custom_feeling_image'],
                    );

                    if (Phpfox::getService('user.privacy')->hasAccess($iFriendId,
                            'feed.share_on_wall') && Phpfox::getUserParam('profile.can_post_comment_on_profile')
                    ) {
                        $iCnt++;

                        Phpfox::getService('ynfeed.process')->addComment($aVals);
                    }
                }

                $sMessage = '<div class="">' . str_replace("'", "\\'",
                        _p('successfully_shared_this_item_on_your_friends_wall')) . '</div>';
                if (!$iCnt) {
                    $sMessage = '<div class="error_message">' . str_replace("'", "\\'",
                            _p('unable_to_share_this_post_due_to_privacy_settings')) . '</div>';
                }
                $this->call('$(\'#\' + tb_get_active()).find(\'.js_box_content:first\').html(\'' . $sMessage . '\');');
                $this->call('$(\'#\' + tb_get_active()).removeClass(\'ynfeed-popup-share-feed\');');
                if ($iCnt) {
                    $this->call('setTimeout(\'tb_remove();\', 2000);');
                    if (!empty($aVals['parent_module_id']) && !empty($aVals['parent_feed_id'])) {
                        $this->call('$Core.updateShareFeedCount(\'' . $aVals['parent_module_id'] . '\', ' . $aVals['parent_feed_id'] . ', \'+\', ' . $iCnt . ');');
                    }
                }
            }
            $this->call('$("#btnShareFeed").removeAttr("disabled");');
            return null;
        }

        $aVals = array(
            'user_status' => $aPost['post_content'],
            'privacy' => $aPost['privacy'],
            'privacy_comment' => $aPost['privacy'],
            'parent_feed_id' => $aPost['parent_feed_id'],
            'parent_module_id' => $aPost['parent_module_id'],
            'no_check_empty_user_status' => true,
            'tagged' => $aPost['tagged'],
            'location' => $aPost['location'],
            'feeling' => $aPost['feeling'],
            'custom_feeling_text' => $aPost['custom_feeling_text'],
            'custom_feeling_image' => $aPost['custom_feeling_image'],
        );

        if (($iId = Phpfox::getService('ynfeed.user.process')->updateStatus($aVals))) {
            $this->call('$(\'#\' + tb_get_active()).find(\'.js_box_content:first\').html(\'<div>' . str_replace("'", "\\'", _p('successfully_shared_this_item')) . '</div>\'); setTimeout(\'tb_remove();\', 2000);');
            $this->call('$(\'#\' + tb_get_active()).removeClass(\'ynfeed-popup-share-feed\');');
            if (!empty($aVals['parent_module_id']) && !empty($aVals['parent_feed_id'])) {
                $this->call('$Core.updateShareFeedCount(\'' . $aVals['parent_module_id'] . '\', ' . $aVals['parent_feed_id'] . ', \'+\', 1);');
            }
        } else {
            $this->call("$('#btnShareFeed').attr('disabled', false); $('#imgShareFeedLoading').hide();");
        }
    }

    public function addComment()
    {
        Phpfox::isUser(true);

        $aVals = (array)$this->get('val');

        if (Phpfox::getLib('parse.format')->isEmpty($aVals['user_status'])) {
            $this->alert(_p('add_some_text_to_share'));
            $this->call('$Core.activityFeedProcess(false);');
            return false;
        }

        if (isset($aVals['parent_user_id']) && $aVals['parent_user_id'] > 0 && !($aVals['parent_user_id'] == Phpfox::getUserId() || (Phpfox::getUserParam('profile.can_post_comment_on_profile') && Phpfox::getService('user.privacy')->hasAccess('' . $aVals['parent_user_id'] . '',
                        'feed.share_on_wall')))
        ) {
            $this->alert(_p('You don\'t have permission to post comment on this profile.'));
            $this->call('$Core.activityFeedProcess(false);');
            return false;
        }

        /* Check if user chose an egift */
        if (Phpfox::isModule('egift') && isset($aVals['egift_id']) && !empty($aVals['egift_id'])) {
            /* is this gift a free one? */
            $aGift = Phpfox::getService('egift')->getEgift($aVals['egift_id']);
            if (!empty($aGift)) {
                $bIsFree = true;
                foreach ($aGift['price'] as $sCurrency => $fVal) {
                    if ($fVal > 0) {
                        $bIsFree = false;
                    }
                }
                /* This is an important change, in v2 birthday_id was the mail_id, in v3
                 * birthday_id is the feed_id
                */
                $aVals['feed_type'] = 'feed_egift';
                $iId = Phpfox::getService('ynfeed.process')->addComment($aVals);
                $aVals['type_id'] = 'feed_comment';
                $aFeed = Phpfox::getService('ynfeed')->getFeed($iId);
                Phpfox::getService('ynfeed')->setExtraInfo(array_merge($aVals, $aFeed));
                /* Notify tagged users */

                // Always make an invoice, so the feed can check on the state
                $iInvoice = Phpfox::getService('egift.process')->addInvoice($iId, $aVals['parent_user_id'], $aGift);

                if (!$bIsFree) {
                    Phpfox::getBlock('api.gateway.form', [
                        'gateway_data' => [
                            'item_number' => 'egift|' . $iInvoice,
                            'currency_code' => Phpfox::getService('user')->getCurrency(),
                            'amount' => $aGift['price'][Phpfox::getService('user')->getCurrency()],
                            'item_name' => _p('egift_card_with_message') . ': ' . $aVals['user_status'] . '',
                            'return' => Phpfox_Url::instance()->makeUrl('friend.invoice'),
                            'recurring' => 0,
                            'recurring_cost' => '',
                            'alternative_cost' => 0,
                            'alternative_recurring_cost' => 0
                        ]
                    ]);
                    $this->call('$("#js_activity_feed_form").hide().after("' . $this->getContent(true) . '");');
                } else {
                    // egift is free
                    Phpfox::getService('ynfeed')->processAjax($iId);
                }
            }

        } else {
            if (isset($aVals['user_status']) && ($iId = Phpfox::getService('ynfeed.process')->addComment($aVals))) {
                $aVals['type_id'] = 'feed_comment';
                $aFeed = Phpfox::getService('ynfeed')->getFeed($iId);
                Phpfox::getService('ynfeed')->processAjax($iId);
            } else {
                $this->call('$Core.activityFeedProcess(false);');
            }
        }

    }

    public function viewMore()
    {
        define('FEED_LOAD_MORE_NEWS', true);
        if ($this->get('callback_module_id') == 'pages' && Phpfox::getService('pages')->isTimelinePage($this->get('callback_item_id'))) {
            define('PAGE_TIME_LINE', true);
        }

        Phpfox::getBlock('ynfeed.display');

        $this->remove('#feed_view_more');
        $this->remove('.js_no_feed_to_show');
        if (!$this->get('forceview') && !$this->get('resettimeline')) {
            $this->append('#js_feed_content', $this->getContent(false));
        } else {
            $this->call('$.scrollTo(\'.timeline_left\', 800);');
            $this->html('#js_feed_content', $this->getContent(false));
        }
        $this->call('$Core.loadInit();');
    }

    public function rate()
    {
        Phpfox::isUser(true);

        list($sRating, $iLastVote) = Phpfox::getService('ynfeed.process')->rate($this->get('id'), $this->get('type'));
        Phpfox::getBlock('feed.rating', array(
                'sRating' => (int)$sRating,
                'iFeedId' => $this->get('id'),
                'bHasRating' => true,
                'iLastVote' => $iLastVote
            )
        );
        $this->html('#js_feed_rating' . $this->get('id'), $this->getContent(false));
    }

    public function delete()
    {
        if (Phpfox::getService('ynfeed.process')->deleteFeed($this->get('id'),
            $this->get('module'), $this->get('item'))
        ) {
            $this->slideUp('#js_item_feed_' . $this->get('id'));

            if (Phpfox::getParam('feed.refresh_activity_feed') > 0) {
                $aRows = Phpfox::getService('ynfeed')->get(null, null, 0);
                $aFeed = array_pop($aRows);

                $this->template()->assign(array(
                        'aFeed' => $aFeed
                    )
                );

                $this->template()->getTemplate('ynfeed.block.entry');
                $sHtml = '<div class="js_feed_view_more_entry_holder">' . $this->getContent(true) . '</div>';

                $this->call("$('#feed_view_more').before('" . $sHtml . "');");
            }

            $this->alert(_p('feed_successfully_deleted'), _p('feed_deletion'), 300, 150, true);
        } else {
            $this->alert(_p('unable_to_delete_this_entry'));
        }
    }

    /* Loads Pages and results from Google Places Autocomplete given a latitude and longitude
     * This function populates $Core.Feed.aPlaces with new items by passing parameters in jSon format */

    public function loadEstablishments()
    {
        $aPages = array();
        if (Phpfox::isModule('pages')) {
            $aPages = Phpfox::getService('pages')->getPagesByLocation($this->get('latitude'), $this->get('longitude'));
        }

        if (count($aPages)) {
            foreach ($aPages as $iKey => $aPage) {
                $aPages[$iKey]['geometry'] = array(
                    'latitude' => $aPage['location_latitude'],
                    'longitude' => $aPage['location_longitude']
                );
                $aPages[$iKey]['name'] = $aPage['title'];
                unset($aPages[$iKey]['location_latitude']);
                unset($aPages[$iKey]['location_longitude']);
            }
        }

        if (!empty($aPages)) {
            $jPages = json_encode($aPages);
            $this->call('$Core.Feed.storePlaces(\'' . $jPages . '\');');
        }
    }

    public function editUserStatus()
    {
        $iFeedId = $this->get('id');
        Phpfox::getBlock('ynfeed.edit-user-status', ['id' => $iFeedId]);
    }


    public function showSharePopup()
    {
        Phpfox::getBlock('ynfeed.share.frame', array(
                'type' => htmlspecialchars($this->get('type')),
                'url' => $this->get('url'),
                'title' => htmlspecialchars($this->get('title'))
            )
        );
    }

    public function toggleFilter()
    {
        $iFilterId = $this->get('id');
        $iShow = $this->get('active');
        Phpfox::getService('ynfeed.filter')->toggleFilter($iFilterId, $iShow);
    }

    public function AdminDeleteFilter()
    {
        if (($iDelete = (int)$this->get('delete'))) {
            if (Phpfox::getService('ynfeed.filter')->delete($iDelete)) {
                $this->alert(_p('successfully_deleted_the_filter'));
                $this->call('setTimeout(function() {$(\'#ajax-response-custom\').html(\'\');location.reload();},1000);');
            }
        }
    }

    public function updateStatus()
    {
        Phpfox::isUser(true);
        $aVals = (array)$this->get('val');
        if (isset($aVals['user_status']) && ($iId = Phpfox::getService('ynfeed.user.process')->updateStatus($aVals))) {
            if (isset($aVals['feed_id'])) {
                //Mean edit already status
                Phpfox::getService('ynfeed')->processUpdateAjax($aVals['feed_id']);
            } else {
                //Mean add new status
                (($sPlugin = Phpfox_Plugin::get('user.component_ajax_updatestatus')) ? eval($sPlugin) : false);
                Phpfox::getService('ynfeed')->processAjax($iId);
            }
        } else {
            $this->call('$Core.activityFeedProcess(false);');
        }
    }

    public function updatePost()
    {
        Phpfox::isUser(true);

        $aVals = (array)$this->get('val');
        $uploadedPhotos = json_decode(urldecode($this->get('uploaded_photos')), true);

        if(empty($aVals)) {
            return false;
        }
        if((in_array($aVals['type_id'], ['link', 'user_status']) || preg_match('/_comment/', $aVals['type_id'])) && Phpfox::getLib('parse.format')->isEmpty($aVals['user_status'])) {
            $this->alert(_p('add_some_text_to_share'), null, 300, 150, true);
            $this->call('$Core.activityFeedProcess(false);');
            $this->call('$(".ynfeed_tb").closest(".js_box_holder").remove();');
            $this->call('$Core.ynfeed.removeFocusForm();');
            return;
        }

        $iUserId = null;
        if (isset($aVals['callback_user_id']) && (int)$aVals['callback_user_id']) {
            $iUserId = (int)$aVals['callback_user_id'];
        }
        $aFeedCallback = [];
        if (isset($aVals['callback_module'])) {
            $aFeedCallback = [
                'module' => $aVals['callback_module'],
                'table_prefix' => $aVals['callback_module'] . '_',
                'item_id' => $aVals['callback_item_id']
            ];
            if ($aFeedCallback['module'] == 'groups') {
                $aFeedCallback['table_prefix'] = 'pages_';
            }
            if ($aFeedCallback['module'] == 'ynsocialstore') {
                $aFeedCallback['table_prefix'] = 'ynstore_';
            }
        }
        (($sPlugin = Phpfox_Plugin::get('ynfeed.component_ajax_update_post__start')) ? eval($sPlugin) : false);
        Phpfox::isUser(true);
        if (isset($aVals['type_id']) && isset($aVals['item_id']) && $aVals['item_id']) {
            switch ($aVals['type_id']) {
                case 'user_status':
                    if (isset($aVals['feed_id']) && ($feed = Phpfox::getService('feed')->callback($aFeedCallback)->getFeed($aVals['feed_id']))) {
                        Phpfox::getService('ynfeed.user.process')->updateStatus($aVals);
                        Phpfox::getService('ynfeed')->setExtraInfo(array_merge($aVals, ['is_update' => 1]));
                        Phpfox::getService('ynfeed')->callback($aFeedCallback)->processUpdateAjaxWithUserId($aVals['feed_id'],
                            $feed['user_id']);
                    }
                    break;
                case 'photo':
                case 'advancedphoto':
                    if($feed = Phpfox::getService('ynfeed')->callback($aFeedCallback)->getFeed($aVals['feed_id'])) {
                        $hasUploadedPhotos = !empty($uploadedPhotos);
                        $hasEditedPhotos = !empty($aVals['edited_photos']);
                        $editedPhotos = $aVals['edited_photos'];

                        $currentFeedPhotos = db()->select('*')
                            ->from(Phpfox::getT('photo_feed'))
                            ->where("feed_id = " . $aVals['feed_id'] . " AND feed_table = '" . (isset($aFeedCallback['table_prefix']) ? $aFeedCallback['table_prefix'] : '') . "feed'")
                            ->execute('getSlaveRows');

                        $photoIds = array_column($currentFeedPhotos, 'photo_id');
                        $deletedPhotos = $hasEditedPhotos ? array_diff($photoIds, $editedPhotos) : $photoIds;
                        $noDeletePhotos = $hasEditedPhotos ? array_unique(array_merge($editedPhotos, [$feed['item_id']])) : [$feed['item_id']];
                        if($hasUploadedPhotos) {
                            $noDeletePhotos = array_merge($noDeletePhotos, $uploadedPhotos);
                        }

                        if(!empty($noDeletePhotos)) {
                            db()->update(Phpfox::getT('photo_info'), ['description' => $aVals['user_status']],
                                'photo_id IN ( ' . implode(',', $noDeletePhotos) . ")");
                            if(isset($aVals['privacy'])) {
                                db()->update(Phpfox::getT('photo'), ['privacy' => $aVals['privacy']],'photo_id IN ('. implode(',', $noDeletePhotos) . ")");
                            }
                        }

                        Phpfox::getService('ynfeed.photo.process')->processPhotoForFeed($aVals['feed_id'], $editedPhotos, $deletedPhotos, $uploadedPhotos, $aFeedCallback, ['privacy' => $aVals['privacy']]);

                        Phpfox::getService('ynfeed.process')->update($aVals['type_id'], $aVals['item_id'], $aVals['privacy'],
                            $aVals['privacy_comment']);
                        Phpfox::getService('ynfeed')->setExtraInfo(array_merge($aVals, ['is_update' => 1]));
                        if ($aVals['action'] == 'upload_photo_via_share') {
                            if ($aFeedCallback && in_array($aFeedCallback['module'], ['groups', 'pages']) && Phpfox::getLib('pages.facade')->getPageItemType($aFeedCallback['item_id']) !== false && !defined('PHPFOX_IS_PAGES_VIEW')) {
                                define('PHPFOX_IS_PAGES_VIEW', true);
                            }
                        }
                        Phpfox::getService('ynfeed')->processUpdateAjaxWithUserId($aVals['feed_id'], $feed['user_id'], true);
                        $this->call('setTimeout(function(){$Core.Photo.processUploadImageForAdvFeed.resetFormAfterEdit();}, 500);');
                    }
                    break;
                case 'v':
                    db()->update(Phpfox::getT('video'), [
                        'status_info' => $aVals['user_status'],
                        'privacy' => $aVals['privacy'],
                    ],
                        'video_id = ' . (int)$aVals['item_id']);
                    Phpfox::getService('ynfeed.process')->update($aVals['type_id'], $aVals['item_id'], $aVals['privacy'],
                        $aVals['privacy_comment']);
                    Phpfox::getService('ynfeed')->setExtraInfo(array_merge($aVals, ['is_update' => 1]));
                    Phpfox::getService('ynfeed')->callback($aFeedCallback)->processUpdateAjaxWithUserId($aVals['feed_id'],
                        null);
                    break;
                case 'ultimatevideo_video':
                    db()->update(Phpfox::getT('ynultimatevideo_videos'), ['description' => $aVals['user_status']],
                        'video_id = ' . (int)$aVals['item_id']);
                    Phpfox::getService('ynfeed.process')->update($aVals['type_id'], $aVals['item_id'], $aVals['privacy'],
                        $aVals['privacy_comment']);
                    Phpfox::getService('ynfeed')->setExtraInfo(array_merge($aVals, ['is_update' => 1]));
                    Phpfox::getService('ynfeed')->callback($aFeedCallback)->processUpdateAjaxWithUserId($aVals['feed_id'],
                        $iUserId);
                    break;
                case 'link':
                    $aUpdate = ['status_info' => $aVals['user_status']];
                    if (isset($aVals['link']) && !empty($aVals['link'])) {
                        $aLink = $aVals['link'];
                        $aUpdate['link'] = $aLink['url'];
                        $aUpdate['image'] = $aLink['image'];
                        $aUpdate['title'] = $aLink['title'];
                        $aUpdate['description'] = $aLink['description'];
                    }
                    db()->update(Phpfox::getT('link'), $aUpdate, 'link_id = ' . (int)$aVals['item_id']);
                    Phpfox::getService('ynfeed.process')->update($aVals['type_id'], $aVals['item_id'], $aVals['privacy'],
                        $aVals['privacy_comment']);
                    Phpfox::getService('ynfeed')->setExtraInfo(array_merge($aVals, ['is_update' => 1]));
                    Phpfox::getService('ynfeed')->callback($aFeedCallback)->processUpdateAjaxWithUserId($aVals['feed_id'],
                        null);
                    break;
                case 'groups_comment':
                    db()->update(Phpfox::getT('pages_feed_comment'), ['content' => $aVals['user_status']],
                        'feed_comment_id = ' . (int)$aVals['item_id']);
                    Phpfox::getService('ynfeed')->setExtraInfo(array_merge($aVals, ['is_update' => 1]));
                    Phpfox::getService('ynfeed')->callback($aFeedCallback)->processUpdateAjaxWithUserId($aVals['feed_id'],
                        $iUserId);
                    break;
                default:
                    if (isset($aVals['type_id'])) {
                        if (substr($aVals['type_id'], -8) == '_comment') {
                            if (!isset($aVals['table_prefix'])) {
                                $aType = explode('_', $aVals['type_id']);
                                if ($aType[0] != 'feed') {
                                    $sTable = $aType[0] . '_feed_comment';
                                } else {
                                    $sTable = 'feed_comment';
                                }
                            } else {
                                $sTable = $aVals['table_prefix'] . 'feed_comment';
                            }
                            db()->update(Phpfox::getT($sTable), [
                                'content' => $aVals['user_status']
                            ], 'feed_comment_id = ' . $aVals['item_id']);
                            Phpfox::getService('ynfeed')->setExtraInfo(array_merge($aVals, ['is_update' => 1]));
                            Phpfox::getService('ynfeed')->callback($aFeedCallback)->processUpdateAjaxWithUserId($aVals['feed_id'],
                                null);
                        }
                    }

                    break;
            }
        }
        (($sPlugin = Phpfox_Plugin::get('ynfeed.component_ajax_update_post__end')) ? eval($sPlugin) : false);
    }


    public function processPhoto()
    {
        $aPostPhotos = $this->get('photos');
        $iTimeStamp = $this->get('timestamp', 0);

        if (is_array($aPostPhotos)) {
            $aImages = array();
            foreach ($aPostPhotos as $aPostPhoto) {
                $aPart = json_decode(urldecode($aPostPhoto), true);
                $aImages[] = $aPart[0];
            }
        } else {
            $aImages = json_decode(urldecode($aPostPhotos), true);
        }

        $oImage = Phpfox_Image::instance();
        $iFileSizes = 0;
        foreach ($aImages as $iKey => $aImage) {
            $aImage['destination'] = urldecode($aImage['destination']);
            if ($aImage['completed'] == 'false') {
                $aPhoto = Phpfox::getService('photo')->getForProcess($aImage['photo_id'], $this->get('user_id', 0));
                if (isset($aPhoto['photo_id'])) {
                    if (Phpfox::getParam('core.allow_cdn')) {
                        Phpfox::getLib('cdn')->setServerId($aPhoto['server_id']);
                    }

                    $sFileName = $aPhoto['destination'];
                    $sFile = Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, '');
                    if (!file_exists(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, ''))
                        && Phpfox::getParam('core.allow_cdn')
                        && !Phpfox::getParam('core.keep_files_in_server')
                    ) {
                        if (Phpfox::getParam('core.allow_cdn') && $aPhoto['server_id'] > 0) {
                            $sActualFile = Phpfox::getLib('image.helper')->display(array(
                                    'server_id' => $aPhoto['server_id'],
                                    'path' => 'photo.url_photo',
                                    'file' => $aPhoto['destination'],
                                    'suffix' => '',
                                    'return_url' => true
                                )
                            );

                            $aExts = preg_split("/[\/\\.]/", $sActualFile);
                            $iCnt = count($aExts) - 1;
                            $sExt = strtolower($aExts[$iCnt]);

                            $aParts = explode('/', $aPhoto['destination']);
                            $sFile = Phpfox::getParam('photo.dir_photo') . $aParts[0] . '/' . $aParts[1] . '/' . md5($aPhoto['destination']) . '.' . $sExt;

                            // Create a temp copy of the original file in local server
                            if (filter_var($sActualFile, FILTER_VALIDATE_URL) !== false) {
                                file_put_contents($sFile, fox_get_contents($sActualFile));
                            } else {
                                copy($sActualFile, $sFile);
                            }
                            //Delete file in local server
                            register_shutdown_function(function () use ($sFile) {
                                @unlink($sFile);
                            });
                        }
                    }

                    list($width, $height, ,) = getimagesize($sFile);
                    $aPhotoPicSizes = Phpfox::isModule('photo') ? Phpfox::getParam('photo.photo_pic_sizes') : Phpfox::getService('photo')->getPhotoPicSizes();


                    foreach ($aPhotoPicSizes as $iSize) {
                        // Create the thumbnail
                        if ($oImage->createThumbnail($sFile,
                                Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, '_' . $iSize), $iSize,
                                $height, true,
                                ((Phpfox::getParam('photo.enabled_watermark_on_photos') && Phpfox::getParam('core.watermark_option') != 'none') ? (Phpfox::getParam('core.watermark_option') == 'image' ? 'force_skip' : true) : false)) === false
                        ) {
                            continue;
                        }

                        $iFileSizes += filesize(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName,
                                '_' . $iSize));

                        if (defined('PHPFOX_IS_HOSTED_SCRIPT')) {
                            unlink(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, '_' . $iSize));
                        }
                    }
                    //Crop original image
                    $iWidth = (int)Phpfox::getUserParam('photo.maximum_image_width_keeps_in_server');
                    if ($iWidth < $width) {
                        $bIsCropped = $oImage->createThumbnail(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName,
                                ''), Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, ''), $iWidth, $height,
                            true,
                            ((Phpfox::getParam('photo.enabled_watermark_on_photos') && Phpfox::getParam('core.watermark_option') != 'none') ? (Phpfox::getParam('core.watermark_option') == 'image' ? 'force_skip' : true) : false));
                        if ($bIsCropped !== false) {
                            //Rename file
                            $iFileSizes += filesize(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, ''));
                            if (defined('PHPFOX_IS_HOSTED_SCRIPT')) {
                                unlink(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, ''));
                            }
                        }
                    }
                    //End Crop
                    $aImages[$iKey]['completed'] = 'true';

                    (($sPlugin = Phpfox_Plugin::get('photo.component_ajax_ajax_process__1')) ? eval($sPlugin) : false);

                    break;
                }
            }
        }

        // Update the user space usage
        Phpfox::getService('user.space')->update(Phpfox::getUserId(), 'photo', $iFileSizes);

        $iNotCompleted = 0;
        foreach ($aImages as $iKey => $aImage) {
            if ($aImage['completed'] == 'false') {
                $iNotCompleted++;
            }
        }

        if ($iNotCompleted === 0) {
            $aCallback = ($this->get('callback_module') ? Phpfox::callback($this->get('callback_module') . '.addPhoto',
                $this->get('callback_item_id')) : null);

            $iFeedId = 0;
            $bNewFeed = false;
            if (!Phpfox::getUserParam('photo.photo_must_be_approved') && !$this->get('is_cover_photo')) {
                if (Phpfox::isModule('feed')) {
                    if ($iTimeStamp && !empty($_SESSION['upload_photo_' . $iTimeStamp])) {
                        $iFeedId = $_SESSION['upload_photo_' . $iTimeStamp];
                    } else {
                        /* Notify tagged users */
                        $aInsert = [
                            'module' => (isset($aCallback['module']) ? $aCallback['module'] : 'feed'),
                            'type_id' => 'photo',
                            'table_prefix' => (isset($aCallback['table_prefix']) ? $aCallback['table_prefix'] : ''),
                            'item_id' => $aPhoto['photo_id']
                        ];

                        $iFeedId = Phpfox::getService('ynfeed.process')->callback($aCallback)->add('photo',
                            $aPhoto['photo_id'], $aPhoto['privacy'], $aPhoto['privacy_comment'],
                            (int)$this->get('parent_user_id', 0));
                        if ($aCallback && defined('PHPFOX_NEW_FEED_LOOP_ID') && PHPFOX_NEW_FEED_LOOP_ID) {
                            storage()->set('photo_parent_feed_' . PHPFOX_NEW_FEED_LOOP_ID, $iFeedId);
                        }
                        Phpfox::getService('ynfeed')->setExtraInfo(array_merge($aInsert, $this->get('val')));

                        $bNewFeed = true;
                        if ($iTimeStamp) {
                            $_SESSION['upload_photo_' . $iTimeStamp] = $iFeedId;
                        }

                        if ($aCallback && Phpfox::isModule('notification') && Phpfox::isModule($aCallback['module']) && Phpfox::hasCallback($aCallback['module'],
                                'addItemNotification')
                        ) {
                            Phpfox::callback($aCallback['module'] . '.addItemNotification', [
                                'page_id' => $aCallback['item_id'],
                                'item_perm' => 'photo.view_browse_photos',
                                'item_type' => 'photo',
                                'item_id' => $aPhoto['photo_id'],
                                'owner_id' => $aPhoto['user_id']
                            ]);
                        }
                    }

                }
                if (count($aImages)) {
                    foreach ($aImages as $aImage) {
                        if ($aImage['photo_id'] == $aPhoto['photo_id'] && $bNewFeed) {
                            continue;
                        }

                        Phpfox_Database::instance()->insert(Phpfox::getT('photo_feed'), array(
                                'feed_id' => $iFeedId,
                                'photo_id' => $aImage['photo_id'],
                                'feed_table' => (empty($aCallback['table_prefix']) ? 'feed' : $aCallback['table_prefix'] . 'feed')
                            )
                        );
                    }
                }
            }

            // this next if is the one you will have to bypass if they come from sharing a photo in the activity feed.
            if (($this->get('page_id') > 0)) {
                $this->call('window.location.href = "' . Phpfox_Url::instance()->permalink('pages',
                        $this->get('page_id'), '') . 'coverupdate_1";');
            } else {
                if (($this->get('groups_id') > 0)) {
                    $this->call('window.location.href = "' . Phpfox_Url::instance()->permalink('groups',
                            $this->get('groups_id'), '') . 'coverupdate_1";');
                } else {
                    if ($this->get('action') == 'upload_photo_via_share') {
                        if ($this->get('is_cover_photo')) {
                            Phpfox::getService('user.process')->updateCoverPhoto($aImage['photo_id']);

                            $this->call('window.location.href = \'' . Phpfox_Url::instance()->makeUrl('profile',
                                    array('coverupdate' => '1')) . '\';');
                        } else {
                            if ($aCallback && Phpfox::getLib('pages.facade')->getPageItemType($aCallback['item_id']) !== false && !defined('PHPFOX_IS_PAGES_VIEW')) {
                                define('PHPFOX_IS_PAGES_VIEW', true);
                            }
                            Phpfox::getService('ynfeed')->callback($aCallback)->processAjax($iFeedId);
                            (($sPlugin = Phpfox_Plugin::get('photo.component_ajax_process_done')) ? eval($sPlugin) : false);
                        }
                    } else {
                        foreach ($aImages as $aImage) {
                            // use the JS var set at progress.js
                            $this->call('sImages += "&photos[]=' . $aImage['photo_id'] . '";');
                        }

                        if (Phpfox::getParam('photo.html5_upload_photo') && $this->get('action') != 'picup') {
                            if ($aCallback !== null) {
                                $sModule = isset($aCallback['module']) ? $aCallback['module'] : 'pages';
                                $this->call('var sCurrentProgressLocation = \'' . Phpfox_Url::instance()->makeUrl($sModule . '.' . $aCallback['item_id'] . '.photo',
                                        ['view' => 'my', 'mode' => 'edit']) . '\';');
                            } else {
                                if (Phpfox::getParam('photo.photo_upload_process')) {
                                    // Make a call similar to the non HTML5 uploads.
                                    $this->call('var sCurrentProgressLocation = \'' . Phpfox_Url::instance()->makeUrl('photo',
                                            array('view' => 'my', 'mode' => 'edit')) . '\';');
                                } else {
                                    $this->call('var sCurrentProgressLocation = \'' . Phpfox_Url::instance()->permalink('photo',
                                            $aPhoto['photo_id'],
                                            $aPhoto['title']) . 'userid_' . Phpfox::getUserId() . '/\';');
                                }
                            }
                        } else {
                            // Only display the photo block if the user plans to upload more pictures
                            if ($this->get('action') == 'view_photo') {
                                Phpfox::addMessage((count($aImages) == 1 ? _p('photo_successfully_uploaded') : _p('photos_successfully_uploaded')));

                                $this->call('window.parent.location.href = \'' . Phpfox_Url::instance()->permalink('photo',
                                        $aPhoto['photo_id'],
                                        $aPhoto['title']) . 'userid_' . Phpfox::getUserId() . '/\';');
                            } elseif ($this->get('action') == 'view_album' && isset($aImages[0]['album'])) {
                                Phpfox::addMessage((count($aImages) == 1 ? _p('photo_successfully_uploaded') : _p('photos_successfully_uploaded')));

                                $this->call('window.location.href = \'' . Phpfox_Url::instance()->permalink('photo.album',
                                        $aImages[0]['album']['album_id'], $aImages[0]['album']['name']) . '\';');
                            } else {
                                Phpfox::addMessage((count($aImages) == 1 ? _p('photo_successfully_uploaded') : _p('photos_successfully_uploaded')));

                                if (Phpfox::getParam('photo.photo_upload_process')) {
                                    $sImages = '';
                                    foreach ($aImages as $aImage) {
                                        $sImages .= $aImage['photo_id'] . ',';
                                    }
                                    $this->call('window.location.href = \'' . Phpfox_Url::instance()->makeUrl('photo',
                                            array(
                                                'view' => 'my',
                                                'mode' => 'edit',
                                                'photos' => urlencode(base64_encode($sImages))
                                            )) . '\';');
                                } else {
                                    $this->call('window.location.href = \'' . Phpfox_Url::instance()->permalink('photo',
                                            $aPhoto['photo_id'],
                                            $aPhoto['title']) . 'userid_' . Phpfox::getUserId() . '/\';');
                                }
                            }
                        }

                        $this->call('hasUploaded++; completeProgress();');
                    }
                }
            }
        } else {
            $this->call('$(\'#js_progress_cache_holder\').html(\'\' + $.ajaxProcess(\'' . _p('processing_image_current_total',
                    array(
                        'phpfox_squote' => true,
                        'current' => (count($aImages) - $iNotCompleted),
                        'total' => count($aImages)
                    )) . '\', \'large\') + \'\');');
            $this->html('#js_photo_upload_process_cnt', (count($aImages) - $iNotCompleted));

            $sExtra = '';
            if ($this->get('callback_module')) {
                $sExtra .= '&callback_module=' . $this->get('callback_module') . '&callback_item_id=' . $this->get('callback_item_id') . '';
            }
            if ($this->get('parent_user_id')) {
                $sExtra .= '&parent_user_id=' . $this->get('parent_user_id');
            }

            if ($this->get('start_year') && $this->get('start_month') && $this->get('start_day')) {
                $sExtra .= '&start_year= ' . $this->get('start_year') . '&start_month= ' . $this->get('start_month') . '&start_day= ' . $this->get('start_day') . '';
            }

            if ($this->get('custom_pages_post_as_page')) {
                $sExtra .= '&custom_pages_post_as_page= ' . $this->get('custom_pages_post_as_page');
            }

            $sExtra .= '&is_cover_photo=' . $this->get('is_cover_photo');
            $aVals = $this->get('val');
            $sExtra .= '&val[tagged]=' . $aVals['tagged'] . '&val[status_info]=' . $aVals['status_info'] . '&val[user_status]=' . $aVals['user_status'] . '&val[location][latlng]=' . @$aVals['location']['latlng'] . '&val[location][name]=' . @$aVals['location']['name'] . '&val[feeling]=' . $aVals['feeling'] . '&val[business]=' . $aVals['business'] . '&val[custom_feeling_text]=' . @$aVals['custom_feeling_text'] . '&val[custom_feeling_image]=' . @$aVals['custom_feeling_image'];
            $this->call('$.ajaxCall(\'ynfeed.processPhoto\', \'&action=' . $this->get('action') . '&js_disable_ajax_restart=true&photos=' . json_encode($aImages) . $sExtra . '\');');
        }

        $aVals = $this->get('core');

        if (isset($aVals['profile_user_id']) && !empty($aVals['profile_user_id']) && $aVals['profile_user_id'] != Phpfox::getUserId() && Phpfox::isModule('notification')) {
            Phpfox::getService('notification.process')->add('photo_feed_profile', $aPhoto['photo_id'],
                $aVals['profile_user_id']);
        }

    }

    public function processFeedPhoto()
    {
        $aPostPhotos = $this->get('photos');
        $iTimeStamp = $this->get('timestamp', 0);
        $aVals = $this->get('val');

        if (is_array($aPostPhotos)) {
            $aImages = array();
            foreach ($aPostPhotos as $aPostPhoto) {
                $aPart = json_decode(urldecode($aPostPhoto), true);
                $aImages[] = $aPart[0];
            }
        } else {
            $aImages = json_decode(urldecode($aPostPhotos), true);
        }

        $oImage = Phpfox_Image::instance();
        $aPhoto = [];
        $aImage = [];

        foreach ($aImages as $iKey => $aImage) {
            $aImage['destination'] = urldecode($aImage['destination']);
            if ($aImage['completed'] == 'false') {
                $aPhoto = Phpfox::getService('photo')->getForProcess($aImage['photo_id'], $this->get('user_id', 0));
                if (isset($aPhoto['photo_id'])) {
                    if (Phpfox::getParam('core.allow_cdn')) {
                        Phpfox::getLib('cdn')->setServerId($aPhoto['server_id']);
                    }

                    $sFileName = $aPhoto['destination'];
                    $sFile = Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, '');
                    if (!file_exists(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, ''))
                        && Phpfox::getParam('core.allow_cdn')
                        && !Phpfox::getParam('core.keep_files_in_server')
                    ) {
                        if (Phpfox::getParam('core.allow_cdn') && $aPhoto['server_id'] > 0) {
                            $sActualFile = Phpfox::getLib('image.helper')->display(array(
                                    'server_id' => $aPhoto['server_id'],
                                    'path' => 'photo.url_photo',
                                    'file' => $aPhoto['destination'],
                                    'suffix' => '',
                                    'return_url' => true
                                )
                            );

                            $aExts = preg_split("/[\/\\.]/", $sActualFile);
                            $iCnt = count($aExts) - 1;
                            $sExt = strtolower($aExts[$iCnt]);

                            $aParts = explode('/', $aPhoto['destination']);
                            $sFile = Phpfox::getParam('photo.dir_photo') . $aParts[0] . '/' . $aParts[1] . '/' . md5($aPhoto['destination']) . '.' . $sExt;

                            // Create a temp copy of the original file in local server
                            if (filter_var($sActualFile, FILTER_VALIDATE_URL) !== false) {
                                file_put_contents($sFile, fox_get_contents($sActualFile));
                            } else {
                                copy($sActualFile, $sFile);
                            }
                            //Delete file in local server
                            register_shutdown_function(function () use ($sFile) {
                                @unlink($sFile);
                            });
                        }
                    }
                    list($width, $height, ,) = getimagesize($sFile);
                    foreach (Phpfox::getService('photo')->getPhotoPicSizes() as $iSize) {
                        // Create the thumbnail
                        if ($oImage->createThumbnail($sFile,
                                Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, '_' . $iSize), $iSize,
                                $height, true,
                                ((Phpfox::getParam('core.watermark_option') != 'none') ? (Phpfox::getParam('core.watermark_option') == 'image' ? 'force_skip' : true) : false)) === false
                        ) {
                            continue;
                        }

                        if (defined('PHPFOX_IS_HOSTED_SCRIPT')) {
                            unlink(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, '_' . $iSize));
                        }
                    }
                    //Crop original image
                    $iWidth = (int)Phpfox::getUserParam('photo.maximum_image_width_keeps_in_server');
                    if ($iWidth < $width) {
                        $bIsCropped = $oImage->createThumbnail(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName,
                                ''), Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, ''), $iWidth, $height,
                            true,
                            ((Phpfox::getParam('core.watermark_option') != 'none') ? (Phpfox::getParam('core.watermark_option') == 'image' ? 'force_skip' : true) : false));
                        if ($bIsCropped !== false) {
                            //Rename file
                            if (defined('PHPFOX_IS_HOSTED_SCRIPT')) {
                                unlink(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, ''));
                            }
                        }
                    }
                    //End Crop
                    $aImages[$iKey]['completed'] = 'true';

                    (($sPlugin = Phpfox_Plugin::get('photo.component_ajax_ajax_process__1')) ? eval($sPlugin) : false);

                    break;
                }
            }
        }

        $iNotCompleted = 0;
        $isEditFeed = false;
        $userId = (int)$this->get('user_id', Phpfox::getUserId());
        foreach ($aImages as $iKey => $aImage) {
            if ($aImage['completed'] == 'false') {
                $iNotCompleted++;
            } else {
                $aPhoto = Phpfox::getService('photo')->getForProcess($aImage['photo_id'], $userId);
            }
        }
        if ($iNotCompleted === 0) {
            $aCallback = ($this->get('callback_module') ? Phpfox::callback($this->get('callback_module') . '.addPhoto',
                $this->get('callback_item_id')) : null);
            $iFeedId = 0;
            $bNewFeed = false;
            $uploadFromShare = isset($aVals['action']) && $aVals['action'] == 'upload_photo_via_share';
            if (!Phpfox::getUserParam('photo.photo_must_be_approved') && !$this->get('is_cover_photo') && !$this->get('no_feed')) {
                if (Phpfox::isModule('feed')) {
                    if ($iTimeStamp && !empty($_SESSION['upload_photo_' . $iTimeStamp . '_' . $aPhoto['album_id']])) {
                        $iFeedId = $_SESSION['upload_photo_' . $iTimeStamp . '_' . $aPhoto['album_id']];
                    } else {
                        if(!empty($aVals['feed_id'])) {
                            $iFeedId = $aVals['feed_id'];
                            $isEditFeed = true;
                        }
                        else {
                            $iFeedId = Phpfox::getService('feed.process')->callback($aCallback)->add('photo',
                                $aPhoto['photo_id'], $aPhoto['privacy'], $aPhoto['privacy_comment'],
                                (int)$this->get('parent_user_id', 0));
                            $bNewFeed = true;
                        }

                        if ($aCallback && defined('PHPFOX_NEW_FEED_LOOP_ID') && PHPFOX_NEW_FEED_LOOP_ID) {
                            storage()->set('photo_parent_feed_' . PHPFOX_NEW_FEED_LOOP_ID, $iFeedId);
                        }


                        if ($iTimeStamp) {
                            $_SESSION['upload_photo_' . $iTimeStamp . '_' . $aPhoto['album_id']] = $iFeedId;
                        }
                        if ($uploadFromShare) {
                            Phpfox::getService('photo.process')->notifyTaggedInFeed($aVals['status_info'],
                                $aPhoto['photo_id'], $aPhoto['user_id']);
                        }
                        if ($aCallback && Phpfox::isModule('notification') && Phpfox::isModule($aCallback['module']) && Phpfox::hasCallback($aCallback['module'],
                                'addItemNotification')
                        ) {
                            Phpfox::callback($aCallback['module'] . '.addItemNotification', [
                                'page_id' => $aCallback['item_id'],
                                'item_perm' => 'photo.view_browse_photos',
                                'item_type' => 'photo',
                                'item_id' => $aPhoto['photo_id'],
                                'owner_id' => $aPhoto['user_id'],
                                'items_phrase' => _p('photos__l')
                            ]);
                        }
                    }

                }
                if (count($aImages) && !$isEditFeed) {
                    foreach ($aImages as $aImage) {
                        if ($aImage['photo_id'] == $aPhoto['photo_id']) {
                            continue;
                        }

                        db()->insert(Phpfox::getT('photo_feed'), array(
                                'feed_id' => $iFeedId,
                                'photo_id' => $aImage['photo_id'],
                                'feed_table' => (empty($aCallback['table_prefix']) ? 'feed' : $aCallback['table_prefix'] . 'feed')
                            )
                        );
                    }
                }
            }

            // this next if is the one you will have to bypass if they come from sharing a photo in the activity feed.
            if (($this->get('page_id') > 0)) {
                if ($this->get('is_cover_photo')) {
                    Phpfox::getService('pages.process')->updateCoverPhoto($aImage['photo_id'], $this->get('page_id'));
                }
                $this->call('window.location.href = "' . Phpfox::getLib('url')->permalink('pages',
                        $this->get('page_id'), '') . 'coverupdate_1";');
            } else {
                if (($this->get('groups_id') > 0)) {
                    if ($this->get('is_cover_photo')) {
                        Phpfox::getService('groups.process')->updateCoverPhoto($aImage['photo_id'], $this->get('groups_id'));
                    }
                    $this->call('window.location.href = "' . Phpfox::getLib('url')->permalink('groups',
                            $this->get('groups_id'), '') . 'coverupdate_1";');
                } else {
                    if ($this->get('action') == 'upload_photo_via_share') {
                        if ($this->get('is_cover_photo')) {
                            Phpfox::getService('user.process')->updateCoverPhoto($aImage['photo_id']);

                            $this->call('window.location.href = \'' . Phpfox::getLib('url')->makeUrl('profile',
                                    array('coverupdate' => '1')) . '\';');
                        } else {
                            if ($aCallback && Phpfox::getLib('pages.facade')->getPageItemType($aCallback['item_id']) !== false && !defined('PHPFOX_IS_PAGES_VIEW')) {
                                define('PHPFOX_IS_PAGES_VIEW', true);
                            }
                            
                            if(Phpfox::isModule('feed')) {
                                if(!isset($aVals['feed_id'])) {
                                    Phpfox::getService('ynfeed')->callback($aCallback)->processAjax($iFeedId);
                                }
                            }

                            (($sPlugin = Phpfox_Plugin::get('photo.component_ajax_process_done')) ? eval($sPlugin) : false);
                        }
                    } else {
                        foreach ($aImages as $aImage) {
                            // use the JS var set at progress.js
                            $this->call('sImages += "&photos[]=' . $aImage['photo_id'] . '";');
                        }
                        if (Phpfox::getParam('photo.photo_upload_process', 0)) {
                            if ($aCallback !== null) {
                                $sModule = isset($aCallback['module']) ? $aCallback['module'] : 'pages';
                                $this->call('var sCurrentProgressLocation = \'' . Phpfox::getLib('url')->makeUrl($sModule . '.' . $aCallback['item_id'] . '.photo',
                                        ['view' => 'my', 'mode' => 'edit']) . '\';');
                            } else {
                                $this->call('var sCurrentProgressLocation = \'' . Phpfox::getLib('url')->makeUrl('photo',
                                        array('view' => 'my', 'mode' => 'edit')) . '\';');
                            }
                            $this->call('var edit_after_upload = true;');
                        } else {
                            $this->call('sImages = "";');
                            $this->call('var sCurrentProgressLocation = \'' . Phpfox::getLib('url')->permalink('photo',
                                    $aPhoto['photo_id'],
                                    $aPhoto['title']) . '/\';');
                        }
                        $this->call('hasUploaded++; if ((hasUploaded + hasErrors) == iTotalUploadedFiles) completeProgress();');
                    }
                }
            }
        } else {
            $this->call('$(\'#js_progress_cache_holder\').html(\'\' + $.ajaxProcess(\'' . _p('processing_image_current_total',
                    array(
                        'phpfox_squote' => true,
                        'current' => (count($aImages) - $iNotCompleted),
                        'total' => count($aImages)
                    )) . '\', \'large\') + \'\');');
            $this->html('#js_photo_upload_process_cnt', (count($aImages) - $iNotCompleted));

            $sExtra = '';
            if ($this->get('callback_module')) {
                $sExtra .= '&callback_module=' . $this->get('callback_module') . '&callback_item_id=' . $this->get('callback_item_id') . '';
            }
            if ($this->get('parent_user_id')) {
                $sExtra .= '&parent_user_id=' . $this->get('parent_user_id');
            }

            if ($this->get('start_year') && $this->get('start_month') && $this->get('start_day')) {
                $sExtra .= '&start_year= ' . $this->get('start_year') . '&start_month= ' . $this->get('start_month') . '&start_day= ' . $this->get('start_day') . '';
            }

            if ($this->get('custom_pages_post_as_page')) {
                $sExtra .= '&custom_pages_post_as_page= ' . $this->get('custom_pages_post_as_page');
            }
            if (isset($aVals['action']) && $aVals['action'] == 'upload_photo_via_share') {
                $sExtra .= '&val[action]=' . $aVals['action'] . '&val[status_info]=' . $aVals['status_info'];
            }
            $sExtra .= '&is_cover_photo=' . $this->get('is_cover_photo');
            $this->call('$.ajaxCall(\'photo.process\', \'&action=' . $this->get('action') . '&js_disable_ajax_restart=true&photos=' . json_encode($aImages) . $sExtra . '\');');
        }

        $aVals = $this->get('core');

        if (isset($aVals['profile_user_id']) && !empty($aVals['profile_user_id']) && $aVals['profile_user_id'] != Phpfox::getUserId() && Phpfox::isModule('notification')) {
            Phpfox::getService('notification.process')->add('photo_feed_profile', $aPhoto['photo_id'],
                $aVals['profile_user_id']);
        }
    }

    public function processAdvancedphoto()
    {
        $aPostPhotos = $this->get('photos');
        $iTimeStamp = $this->get('timestamp', 0);
        if (is_array($aPostPhotos)) {
            $aImages = array();
            foreach ($aPostPhotos as $aPostPhoto) {
                $aPart = json_decode(urldecode($aPostPhoto), true);
                $aImages[] = $aPart[0];
            }
        } else {

            $aImages = json_decode(urldecode($aPostPhotos), true);
        }

        $oImage = Phpfox::getLib('image');
        $iFileSizes = 0;

        foreach ($aImages as $iKey => $aImage) {
            $aImage['destination'] = urldecode($aImage['destination']);
            if ($aImage['completed'] == 'false') {
                $aPhoto = Phpfox::getService('advancedphoto')->getForProcess($aImage['photo_id']);
                if (isset($aPhoto['photo_id'])) {
                    if (Phpfox::getParam('core.allow_cdn')) {
                        Phpfox::getLib('cdn')->setServerId($aPhoto['server_id']);
                    }
                    $sFileName = $aPhoto['destination'];
                    $sFile = Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, '');

                    if (!file_exists($sFile)
                        && Phpfox::getParam('core.allow_cdn')
                        && !Phpfox::getParam('core.keep_files_in_server')
                    ) {
                        if (Phpfox::getParam('core.allow_cdn') && $aPhoto['server_id'] > 0) {
                            $sActualFile = Phpfox::getLib('image.helper')->display(array(
                                    'server_id' => $aPhoto['server_id'],
                                    'path' => 'photo.url_photo',
                                    'file' => $aPhoto['destination'],
                                    'suffix' => '',
                                    'return_url' => true
                                )
                            );

                            $aExts = preg_split("/[\/\\.]/", $sActualFile);
                            $iCnt = count($aExts) - 1;
                            $sExt = strtolower($aExts[$iCnt]);

                            $aParts = explode('/', $aPhoto['destination']);
                            $sFile = Phpfox::getParam('photo.dir_photo') . $aParts[0] . '/' . $aParts[1] . '/' . md5($aPhoto['destination']) . '.' . $sExt;

                            // Create a temp copy of the original file in local server, deleted later in line 606
                            if (filter_var($sActualFile, FILTER_VALIDATE_URL) !== FALSE) {
                                file_put_contents($sFile, fox_get_contents($sActualFile));
                            } else {
                                copy($sActualFile, $sFile);
                            }
                            //Delete file in local server
                            register_shutdown_function(function () use ($sFile) {
                                @unlink($sFile);
                            });
                        }
                    }
                    list($width, $height, $type, $attr) = getimagesize($sFile);
                    foreach (Phpfox::getService('advancedphoto.helper')->getPhotoPicSizes() as $iSize) {
                        // Create the thumbnail
                        if ($oImage->createThumbnail($sFile, Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, '_' . $iSize), $iSize, $height, true, ((Phpfox::getParam('advancedphoto.enabled_watermark_on_photos') && Phpfox::getParam('core.watermark_option') != 'none') ? (Phpfox::getParam('core.watermark_option') == 'image' ? 'force_skip' : true) : false)) === false) {
                            continue;
                        }
                        if (Phpfox::getParam('advancedphoto.enabled_watermark_on_photos')) {
                            $oImage->addMark(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, '_' . $iSize));
                        }

                        // Add the new file size to the total file size variable
                        $iFileSizes += filesize(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, '_' . $iSize));
                        if (defined('PHPFOX_IS_HOSTED_SCRIPT')) {
                            unlink(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, '_' . $iSize));
                        }
                    }
                    if (Phpfox::getParam('advancedphoto.enabled_watermark_on_photos')) {
                        $oImage->addMark(Phpfox::getParam('photo.dir_photo') . sprintf($sFileName, ''));
                    }
                    $aImages[$iKey]['completed'] = 'true';
                    (($sPlugin = Phpfox_Plugin::get('advancedphoto.component_ajax_ajax_process__1')) ? eval($sPlugin) : false);

                    break;
                }
            }
        }
        // Update the user space usage
        Phpfox::getService('user.space')->update(Phpfox::getUserId(), 'photo', $iFileSizes);
        $iNotCompleted = 0;
        foreach ($aImages as $iKey => $aImage) {
            if ($aImage['completed'] == 'false') {
                $iNotCompleted++;
            }
        }

        if ($iNotCompleted === 0) {
            $aCallback = ($this->get('callback_module') ? Phpfox::callback($this->get('callback_module') . '.addPhoto',
                $this->get('callback_item_id')) : null);
            $iFeedId = 0;
            $bNewFeed = false;
            if (!Phpfox::getUserParam('advancedphoto.photo_must_be_approved') && !$this->get('is_cover_photo')) {
                if (Phpfox::isModule('feed')) {
                    if ($iTimeStamp && !empty($_SESSION['upload_photo_' . $iTimeStamp])) {
                        $iFeedId = $_SESSION['upload_photo_' . $iTimeStamp];
                    } else {
                        /* Notify tagged users */
                        $aInsert = [
                            'module' => (isset($aCallback['module']) ? $aCallback['module'] : 'feed'),
                            'type_id' => 'advancedphoto',
                            'table_prefix' => (isset($aCallback['table_prefix']) ? $aCallback['table_prefix'] : ''),
                            'item_id' => $aPhoto['photo_id']
                        ];


                        $iFeedId = Phpfox::getService('ynfeed.process')->callback($aCallback)->add('advancedphoto',
                            $aPhoto['photo_id'], $aPhoto['privacy'], $aPhoto['privacy_comment'],
                            (int)$this->get('parent_user_id', 0));
                        $bNewFeed = true;
                        if ($iTimeStamp) {
                            $_SESSION['upload_photo_' . $iTimeStamp] = $iFeedId;
                        }
                        Phpfox::getService('ynfeed')->setExtraInfo(array_merge($aInsert, $this->get('val')));
                    }
                }
                if (count($aImages)) {
                    foreach ($aImages as $aImage) {
                        if ($aImage['photo_id'] == $aPhoto['photo_id'] && $bNewFeed) {
                            continue;
                        }

                        Phpfox::getLib('database')->insert(Phpfox::getT('photo_feed'), array(
                                'feed_id' => $iFeedId,
                                'photo_id' => $aImage['photo_id']
                            )
                        );
                    }
                }
            }

            // this next if is the one you will have to bypass if they come from sharing a photo in the activity feed.
            if (($this->get('page_id') > 0)) {
                $this->call('window.location.href = "' . Phpfox::getLib('url')->permalink('pages',
                        $this->get('page_id'), '') . 'coverupdate_1";');
            } else {
                if (($this->get('groups_id') > 0)) {
                    $this->call('window.location.href = "' . Phpfox_Url::instance()->permalink('groups',
                            $this->get('groups_id'), '') . 'coverupdate_1";');

                } else {
                    if ($this->get('action') == 'upload_photo_via_share') {
                        if ($this->get('is_cover_photo')) {
                            Phpfox::getService('user.process')->updateCoverPhoto($aImage['photo_id']);
                            $this->call('window.location.href = \'' . Phpfox::getLib('url')->makeUrl('profile',
                                    array('coverupdate' => '1')) . '\';');
                        } else {
                            if ($aCallback && Phpfox::getLib('pages.facade')->getPageItemType($aCallback['item_id']) !== false && !defined('PHPFOX_IS_PAGES_VIEW')) {
                                define('PHPFOX_IS_PAGES_VIEW', true);
                            }


                            Phpfox::getService('ynfeed')->callback($aCallback)->processAjax($iFeedId);
                            (($sPlugin = Phpfox_Plugin::get('advancedphoto.component_ajax_process_done')) ? eval($sPlugin) : false);
                            $this->call('$Core.ynfeedResetActivityFeedForm();');
                        }
                    } else {
                        foreach ($aImages as $aImage) {
                            // use the JS var set at progress.js
                            $this->call('sImages += "&photos[]=' . $aImage['photo_id'] . '";');
                        }
                        if (Phpfox::getParam('advancedphoto.html5_upload_photo') && $this->get('action') != 'picup') {
                            if ($aCallback !== null) {
                                $sModule = isset($aCallback['module']) ? $aCallback['module'] : 'pages';
                                $this->call('var sCurrentProgressLocation = \'' . Phpfox_Url::instance()->makeUrl($sModule . '.' . $aCallback['item_id'] . '.advancedphoto',
                                        ['view' => 'my', 'mode' => 'edit']) . '\';');
                            } else {
                                if (Phpfox::getParam('advancedphoto.photo_upload_process')) {
                                    // Make a call similar to the non HTML5 uploads.
                                    $this->call('var sCurrentProgressLocation = \'' . Phpfox_Url::instance()->makeUrl('advancedphoto',
                                            array('view' => 'my', 'mode' => 'edit')) . '\';');
                                } else {
                                    $this->call('var sCurrentProgressLocation = \'' . Phpfox_Url::instance()->permalink('advancedphoto',
                                            $aPhoto['photo_id'],
                                            $aPhoto['title']) . 'userid_' . Phpfox::getUserId() . '/\';');
                                }
                            }
                        } else {
                            // Only display the photo block if the user plans to upload more pictures
                            if ($this->get('action') == 'view_photo') {
                                Phpfox::addMessage((count($aImages) == 1 ? _p('advancedphoto.photo_successfully_uploaded') : _p('advancedphoto.photos_successfully_uploaded')));

                                $this->call('window.parent.location.href = \'' . Phpfox::getLib('url')->permalink('advancedphoto',
                                        $aPhoto['photo_id'],
                                        $aPhoto['title']) . 'userid_' . Phpfox::getUserId() . '/\';');
                            } elseif ($this->get('action') == 'view_album' && isset($aImages[0]['album'])) {
                                Phpfox::addMessage((count($aImages) == 1 ? _p('advancedphoto.photo_successfully_uploaded') : _p('advancedphoto.photos_successfully_uploaded')));

                                $this->call('window.location.href = \'' . Phpfox::getLib('url')->permalink('advancedphoto.album',
                                        $aImages[0]['album']['album_id'], $aImages[0]['album']['name']) . '\';');
                            } else {
                                Phpfox::addMessage((count($aImages) == 1 ? _p('advancedphoto.photo_successfully_uploaded') : _p('advancedphoto.photos_successfully_uploaded')));

                                if (Phpfox::getParam('advancedphoto.photo_upload_process')) {
                                    $sImages = '';
                                    foreach ($aImages as $aImage) {
                                        $sImages .= $aImage['photo_id'] . ',';
                                    }
                                    $this->call('window.location.href = \'' . Phpfox::getLib('url')->makeUrl('advancedphoto',
                                            array(
                                                'view' => 'my',
                                                'mode' => 'edit',
                                                'photos' => urlencode(base64_encode($sImages))
                                            )) . '\';');
                                } else {
                                    $this->call('window.location.href = \'' . Phpfox::getLib('url')->permalink('advancedphoto',
                                            $aPhoto['photo_id'],
                                            $aPhoto['title']) . 'userid_' . Phpfox::getUserId() . '/\';');
                                }
                            }
                        }
                        $this->call('hasUploaded++; completeProgress();');
                    }
                }
            }
        } else {
            $this->call('$(\'#js_progress_cache_holder\').html(\'\' + $.ajaxProcess(\'' . _p('advancedphoto.processing_image_current_total',
                    array(
                        'phpfox_squote' => true,
                        'current' => (count($aImages) - $iNotCompleted),
                        'total' => count($aImages)
                    )) . '\', \'large\') + \'\');');
            $this->html('#js_photo_upload_process_cnt', (count($aImages) - $iNotCompleted));

            $sExtra = '';
            if ($this->get('callback_module')) {
                $sExtra .= '&callback_module=' . $this->get('callback_module') . '&callback_item_id=' . $this->get('callback_item_id') . '';
            }
            if ($this->get('parent_user_id')) {
                $sExtra .= '&parent_user_id=' . $this->get('parent_user_id');
            }

            if ($this->get('start_year') && $this->get('start_month') && $this->get('start_day')) {
                $sExtra .= '&start_year= ' . $this->get('start_year') . '&start_month= ' . $this->get('start_month') . '&start_day= ' . $this->get('start_day') . '';
            }

            $sExtra .= '&is_cover_photo=' . $this->get('is_cover_photo');
            $aVals = $this->get('val');
            $sExtra .= '&val[tagged]=' . $aVals['tagged'] . '&val[status_info]=' . $aVals['status_info'] . '&val[user_status]=' . $aVals['user_status'] . '&val[location][latlng]=' . @$aVals['location']['latlng'] . '&val[location][name]=' . @$aVals['location']['name'] . '&val[feeling]=' . $aVals['feeling'] . '&val[business]=' . $aVals['business'] . '&val[custom_feeling_text]=' . @$aVals['custom_feeling_text'] . '&val[custom_feeling_image]=' . @$aVals['custom_feeling_image'];
            $this->call('$.ajaxCall(\'ynfeed.processAdvancedphoto\', \'&action=' . $this->get('action') . '&js_disable_ajax_restart=true&photos=' . json_encode($aImages) . $sExtra . '\');');
        }

        $aVals = $this->get('core');

        if (isset($aVals['profile_user_id']) && !empty($aVals['profile_user_id']) && $aVals['profile_user_id'] != Phpfox::getUserId() && Phpfox::isModule('notification')) {
            Phpfox::getService('notification.process')->add('feed_comment_profile', $aPhoto['photo_id'],
                $aVals['profile_user_id']);
        }
    }

    public function buildMentionCache()
    {
        $aMentions = Phpfox::getService('ynfeed.process')->getUsersForMention();
        $this->call('$Cache.mentions = ' . json_encode($aMentions) . ';');
        $this->call('$Core.loadInit();');
    }

    public function buildBusinessCache()
    {
        if (Phpfox::isModule('directory')) {
            $this->call('$Cache.businesses = ' . json_encode(Phpfox::getService('ynfeed.directory')->getFromCache()) . ';');
            $this->call('$Core.loadInit();');
        }
    }

    public function buildFeelingCache()
    {
        $this->call('$Cache.feelings = ' . json_encode(Phpfox::getService('ynfeed.feeling')->getFromCache()) . ';');
        $this->call('$Core.loadInit();');
    }

    public function save()
    {
        $iFeedId = (int)$this->get('id');
        $sModule = $this->get('module');
        $sType = $this->get('type');
        $sTablePrefix = $this->get('table_prefix');
        $iUserId = Phpfox::getUserId();
        if ($iFeedId && $iUserId) {
            if (Phpfox::getService('ynfeed.save')->add([
                'user_id' => $iUserId,
                'feed_id' => $iFeedId,
                'feed_type' => $sType,
                'callback' => !empty($sModule) ? json_encode([
                    'module' => $sModule,
                    'table_prefix' => $sTablePrefix
                ]) : null
            ])
            ) {
                Phpfox::getLib('cache')->remove('ynfeed_extra_' . $iFeedId . '_' . $iUserId);
                $this->alert(_p('Save feed successfully!'));
                $this->call("\$Core.ynfeed.updateSavedStatus(" . $iFeedId . ",'" . _p('unsave_feed') . "', 1);");
                return;
            }
        }
        $this->alert(_p('Could not save this feed!'));
    }

    public function unsave()
    {
        $iFeedId = (int)$this->get('id');
        $iUserId = Phpfox::getUserId();
        $module = $this->get('module');
        if ($iFeedId && $iUserId) {
            if (Phpfox::getService('ynfeed.save')->delete($iUserId, $iFeedId, $module)) {
                Phpfox::getLib('cache')->remove('ynfeed_extra_' . $iFeedId . '_' . $iUserId);
                $this->alert(_p('Unsave feed successfully!'));
                $this->call("\$Core.ynfeed.updateSavedStatus(" . $iFeedId . ",'" . _p('save_feed') . "', 0);");
                return;
            }
        }
        $this->alert(_p('Could not unsave this feed!'));
    }

    public function hideFeed()
    {
        $iFeedId = (int)$this->get('id');
        if (!($iUserId = Phpfox::getUserId())) {
            $this->alert(_p('Please sign in to continue this action!'));
            return false;
        }
        if ($iFeedId) {
            if (Phpfox::getService('ynfeed.hide')->add($iUserId, $iFeedId, 'feed')) {
                $this->call("\$Core.ynfeed.hideFeed(" . json_encode([$iFeedId]) . ", " . json_encode([]) . ");");
                return true;
            }
        }
        $this->alert(_p('Could not hide this feed!'));
        $this->call("\$Core.ynfeed.hideFeedFail(" . json_encode([$iFeedId]) . ", " . json_encode([]) . ");");
        return false;
    }

    public function hideAllFromUser()
    {
        $iResourceId = (int)$this->get('id');
        if (!($iUserId = Phpfox::getUserId())) {
            $this->alert(_p('Please sign in to continue this action!'));
            return false;
        }
        if ($iResourceId) {
            if (Phpfox::getService('ynfeed.hide')->add($iUserId, $iResourceId, 'user')) {
                $this->call("\$Core.ynfeed.hideFeed(" . json_encode([]) . ", " . json_encode([$iResourceId]) . ");");
                return true;
            }
        }
        $this->alert(_p('Could not hide feed from this user!'));
        $this->call("\$Core.ynfeed.hideFeedFail(" . json_encode([]) . ", " . json_encode([$iResourceId]) . ");");
        return false;
    }

    public function manageHidden()
    {
        $this->error(false);
        Phpfox::getBlock('ynfeed.manage-hidden');
        $iPage = $this->get('page');
        if ($iPage) {
            $content = $this->getContent(false);
            $this->call('$("#ynfeed_list_hidden").find(".js_pager_popup_view_more_link").remove();');
            if ($iPage == 1) {
                $this->html('.ynfeed-hidden-items', $content);
                $this->call('$Core.ynfeed.updateSelectedUnhideNumber();');
            } else {
                $this->append('.ynfeed-hidden-items', $content);
            }
        }
    }

    public function unhide()
    {
        $iUserId = Phpfox::getUserId();
        $iHideId = $this->get('hide_id');
        $iResourceId = $this->get('resource_id');
        $sResourceType = $this->get('resource_type');

        if ($iUserId && $iHideId && $iResourceId && $sResourceType != '') {
            if (Phpfox::getService('ynfeed.hide')->delete($iUserId, $iResourceId, $sResourceType)) {
                $this->call('$("#ynfeed_item_hidden_' . $iHideId . '").hide("fast", function() {$(this).remove();$Core.ynfeed.updateSelectedUnhideNumber();} );');
            } else {
                $this->alert(_p('Could not unhide from this user'));
            }
        }
    }

    public function undoHideFeed()
    {
        $iUserId = Phpfox::getUserId();
        $iFeedId = $this->get('id');
        if ($iFeedId && $iUserId) {
            Phpfox::getService('ynfeed.hide')->delete($iUserId, $iFeedId, 'feed');
        }
    }

    public function undoHideAllFromUser()
    {
        $iUserId = Phpfox::getUserId();
        $iHideUserId = $this->get('id');
        if ($iUserId && $iHideUserId) {
            Phpfox::getService('ynfeed.hide')->delete($iUserId, $iHideUserId, 'user');
        }
    }

    public function multiUnhide()
    {
        $iUserId = Phpfox::getUserId();
        $aIds = explode(',', $this->get('ids', ''));
        if ($iUserId && count($aIds)) {
            $aHideIds = [];
            foreach ($aIds as $key => $iHideId) {
                if (is_numeric($iHideId)) {
                    $aHideIds[] = $iHideId;
                }
            }
            if (Phpfox::getService('ynfeed.hide')->multiDelete($aHideIds, $iUserId)) {
                $this->call('$Core.ynfeed.deleteElemsById("ynfeed_item_hidden_", ' . json_encode($aHideIds) . ', $Core.ynfeed.resetSelectedUnhide);');
                return true;
            }
        }
        $this->alert(_p('Could not unhide selected items'));
        return false;
    }

    public function showUsers()
    {
        $sTagged = $this->get('ids');
        $aTagged = array_diff(explode(',', $sTagged), array(""));
        $aUsers = Phpfox::getService('ynfeed.user.process')->getUsersById($aTagged);
        $this->template()->assign(array(
                'aUsers' => $aUsers
            )
        );
        $this->template()->getTemplate('ynfeed.block.show-users');
    }

    public function removeTag()
    {
        $iFeedId = $this->get('feed_id');
        $iUserId = $this->get('user_id');
        $sItemType = $this->get('feed_item_type');
        $iItemId = $this->get('feed_item_id');
        $aFeedCallback = [];
        if ($this->get('module')) {
            $aFeedCallback = [
                'module' => $this->get('module'),
                'table_prefix' => $this->get('module') . '_',
                'item_id' => $this->get('item_id')
            ];
            if ($aFeedCallback['module'] == 'groups') {
                $aFeedCallback['table_prefix'] = 'pages_';
            }
        }

        /*Remove hyperlink*/
        switch ($sItemType) {
            case 'user_status':
                $sContent = db()->select('content')->from(Phpfox::getT('user_status'))->where([
                    'status_id' => $iItemId
                ])->execute('getField');
                if ($sContent != "") {
                    $sContent = removeMentionInText(Phpfox::getUserId(), $sContent);
                    db()->update(Phpfox::getT('user_status'), [
                        'content' => $sContent
                    ], "status_id = " . $iItemId);
                }
                Phpfox::getService('ynfeed.process')->removeTag($iItemId, $sItemType, $iUserId);
                Phpfox::getService('ynfeed')->callback($aFeedCallback)->processUpdateAjaxWithUserId($iFeedId, $iUserId);
                break;
            case 'photo':
            case 'advancedphoto':
                $sContent = db()->select('description')->from(Phpfox::getT('photo_info'))->where("photo_id = " . $iItemId)->execute('getField');
                if ($sContent != "") {
                    $sContent = removeMentionInText(Phpfox::getUserId(), $sContent);
                    db()->update(Phpfox::getT('photo_info'), ['description' => $sContent], 'photo_id = ' . $iItemId);
                }
                Phpfox::getService('ynfeed.process')->removeTag($iItemId, $sItemType, $iUserId);
                Phpfox::getService('ynfeed')->callback($aFeedCallback)->processUpdateAjaxWithUserId($iFeedId, $iUserId);
                break;
            case 'v':
                $sContent = db()->select('status_info')->from(Phpfox::getT('video'))->where("video_id = " . $iItemId)->execute('getField');
                if ($sContent != "") {
                    $sContent = removeMentionInText(Phpfox::getUserId(), $sContent);
                    db()->update(Phpfox::getT('video'), ['status_info' => $sContent], 'video_id = ' . $iItemId);
                }
                Phpfox::getService('ynfeed.process')->removeTag($iItemId, $sItemType, $iUserId);
                Phpfox::getService('ynfeed')->callback($aFeedCallback)->processUpdateAjaxWithUserId($iFeedId, $iUserId);
                break;
            case 'ultimatevideo_video':
                $sContent = db()->select('description')->from(Phpfox::getT('ynultimatevideo_videos'))->where("video_id = " . $iItemId)->execute('getField');
                if ($sContent != "") {
                    $sContent = removeMentionInText(Phpfox::getUserId(), $sContent);
                    db()->update(Phpfox::getT('ynultimatevideo_videos'), ['description' => $sContent],
                        'video_id = ' . $iItemId);
                }
                Phpfox::getService('ynfeed.process')->removeTag($iItemId, $sItemType, $iUserId);
                Phpfox::getService('ynfeed')->callback($aFeedCallback)->processUpdateAjaxWithUserId($iFeedId, $iUserId);
                break;
            case 'link':
                $sContent = db()->select('status_info')->from(Phpfox::getT('link'))->where("link_id = " . $iItemId)->execute('getField');
                if ($sContent != "") {
                    $sContent = removeMentionInText(Phpfox::getUserId(), $sContent);
                    db()->update(Phpfox::getT('link'), ['status_info' => $sContent], 'link_id = ' . $iItemId);
                }
                Phpfox::getService('ynfeed.process')->removeTag($iItemId, $sItemType, $iUserId);
                Phpfox::getService('ynfeed')->callback($aFeedCallback)->processUpdateAjaxWithUserId($iFeedId, $iUserId);
                break;
            case 'groups_comment':
            case 'pages_comment':
                $sContent = db()->select('content')->from(Phpfox::getT('pages_feed_comment'))->where("feed_comment_id = " . $iItemId)->execute('getField');
                if ($sContent != "") {
                    $sContent = removeMentionInText(Phpfox::getUserId(), $sContent);
                    db()->update(Phpfox::getT('pages_feed_comment'), ['content' => $sContent],
                        'feed_comment_id = ' . $iItemId);
                }
                Phpfox::getService('ynfeed.process')->removeTag($iItemId, $sItemType, $iUserId);
                Phpfox::getService('ynfeed')->callback($aFeedCallback)->processUpdateAjaxWithUserId($iFeedId, $iUserId);
                break;
            default:
                if (substr($sItemType, -8) == '_comment') {
                    $aType = explode('_', $sItemType);
                    if ($aType[0] != 'feed') {
                        $sTable = $aType[0] . '_feed_comment';
                    } else {
                        $sTable = 'feed_comment';
                    }
                    if (db()->tableExists(Phpfox::getT($sTable))) {
                        $sContent = db()->select('content')->from(Phpfox::getT($sTable))->where("feed_comment_id = " . $iItemId)->execute('getField');
                        if ($sContent != "") {
                            $sContent = removeMentionInText(Phpfox::getUserId(), $sContent);
                            db()->update(Phpfox::getT($sTable), [
                                'content' => $sContent
                            ], 'feed_comment_id = ' . $iItemId);
                        }
                    }
                }
                Phpfox::getService('ynfeed.process')->removeTag($iItemId, $sItemType, $iUserId);
                Phpfox::getService('ynfeed')->callback($aFeedCallback)->processUpdateAjaxWithUserId($iFeedId, $iUserId);
                break;

        }
    }

    public function turnoffNotification()
    {
        $iFeedId = (int)$this->get('feed_id');
        $iItemId = (int)$this->get('item_id');
        $sItemType = $this->get('item_type');
        $iUserId = Phpfox::getUserId();
        if ($iItemId && $iUserId && $sItemType) {

            /*add to database*/
            db()->insert(Phpfox::getT('ynfeed_turnoff_notification'), [
                'item_id' => $iItemId,
                'type_id' => $sItemType,
                'user_id' => $iUserId
            ]);
            Phpfox::getLib('cache')->remove('ynfeed_extra_' . $iFeedId . '_' . $iUserId);
            $this->alert(_p('Turn off notifications successfully!'));
            $this->call("\$Core.ynfeed.updateNotificationStatus(" . $iFeedId . ",'" . _p('turnon_notifications_for_this_feed') . "', 1);");
            return;
        }
        $this->alert(_p('Can not turn off notifications for this feed!'));
    }

    public function turnonNotification()
    {
        $iFeedId = (int)$this->get('feed_id');
        $iItemId = (int)$this->get('item_id');
        $sItemType = $this->get('item_type');
        $iUserId = Phpfox::getUserId();
        if ($iItemId && $iUserId && $sItemType) {

            /*add to database*/
            db()->delete(Phpfox::getT('ynfeed_turnoff_notification'),
                "item_id = " . $iItemId . " AND user_id = " . $iUserId . " AND type_id = '" . $sItemType . "'");
            Phpfox::getLib('cache')->remove('ynfeed_extra_' . $iFeedId . '_' . $iUserId);

            $this->alert(_p('Turn on notifications successfully!'));
            $this->call("\$Core.ynfeed.updateNotificationStatus(" . $iFeedId . ",'" . _p('turnoff_notifications_for_this_feed') . "', 0);");
            return;
        }
        $this->alert(_p('Can not turn on notifications for this feed!'));
    }

    public function reload()
    {
        Phpfox::getBlock('ynfeed.display', ['bIsFilterPosts' => true]);
        $this->show('#js_feed_content');
        $this->hide('#ynfeed_filtering');
        $sContent = $this->getContent(false);
        $this->html('#js_feed_content', '<div id="js_new_feed_comment"></div>' . $sContent);
        $this->call('$Core.loadInit();');
    }

    public function loadFeelingImages()
    {
        $aFeelings = Phpfox::getService('ynfeed.feeling')->getFeelingIcons();
        $this->template()->assign(array(
                'aFeelings' => $aFeelings
            )
        );
        $this->template()->getTemplate('ynfeed.block.show-feeling-icons');
    }

    public function addLinkViaStatusUpdate()
    {
        Phpfox::isUser(true);

        define('PHPFOX_FORCE_IFRAME', true);

        $aVals = (array)$this->get('val');
        $aCallback = null;
        if (isset($aVals['callback_module']) && Phpfox::hasCallback($aVals['callback_module'], 'addLink')) {
            $aCallback = Phpfox::callback($aVals['callback_module'] . '.addLink', $aVals);
        }

        if (!empty($aCallback) && $aCallback['module'] == 'pages') {
            $aPage = Phpfox::getService('pages')->getForView($aCallback['item_id']);
            if (isset($aPage['use_timeline']) && $aPage['use_timeline']) {
                if (!defined('PAGE_TIME_LINE')) {
                    define('PAGE_TIME_LINE', true);
                }
            }
        }

        if (($iId = Phpfox::getService('link.process')->add($aVals, false, $aCallback))) {
            (($sPlugin = Phpfox_Plugin::get('link.component_ajax_addviastatusupdate')) ? eval($sPlugin) : false);
            Phpfox::getService('ynfeed')->callback($aCallback)->processAjax($iId);
        }
        $this->call("$('#js_preview_link_attachment_custom_form_sub').html(''); \$Core.ynfeed.resetBackground();");
    }

    public function userUpdateFeedSort()
    {
        Phpfox::isUser(true);
        if (Phpfox::getService('user.process')->updateFeedSort($this->get('order'))) {
            $this->call('$(".ynfeed_filter.active a").trigger("click");');
            $this->call('$(".ynfeed_filter_more_item.active a").trigger("click");');
        }
    }
}
