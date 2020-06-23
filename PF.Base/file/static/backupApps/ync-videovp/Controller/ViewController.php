<?php
/**
 * User: YouNetCo
 * Date: 5/9/18
 * Time: 5:51 PM
 */

namespace Apps\YNC_VideoViewPop\Controller;

use Phpfox;
use Phpfox_Component;
use Phpfox_Error;
use Phpfox_Plugin;
use Phpfox_Url;
use Phpfox_Module;

class ViewController extends Phpfox_Component
{
    public function process()
    {
        (($sPlugin = Phpfox_Plugin::get('yncvideovp.component_controller_view_process_1')) ? eval($sPlugin) : false);
        $module = $this->request()->get('module');

        $this->_checkPermission($module);

        $iVideoId = $this->request()->get('video_id');

        $oYvvpService = Phpfox::getService('yncvideovp');

        if (!($aVideo = $oYvvpService->getVideo($module, $iVideoId))) {
            Phpfox_Error::display($oYvvpService->wrapError(_p('the_video_you_are_looking_for_does_not_exist_or_has_been_removed')));
        }

        $sLink = $this->request()->get('slink');

        (($sPlugin = Phpfox_Plugin::get('yncvideovp.component_controller_view_process_2')) ? eval($sPlugin) : false);

        if (Phpfox::isUser() && Phpfox::getService('user.block')->isBlocked(null, $aVideo['user_id'])) {
            return Phpfox_Error::display($oYvvpService->wrapError(_p('The page may only be visible to an audience you\'re not in .')));
        }

        if (isset($aVideo['module_id']) && !empty($aVideo['item_id']) && !Phpfox::isModule($aVideo['module_id'])) {
            return Phpfox_Error::display($oYvvpService->wrapError(_p('cannot_find_the_parent_item')));
        }

        if (isset($aVideo['module_id']) && $aVideo['module_id'] != 'video' && !empty($aVideo['item_id']) && Phpfox::isModule($aVideo['module_id'])) {
            if ($aVideo['module_id'] == 'pages' && !Phpfox::getService('pages')->hasPerm($aVideo['item_id'],
                    'pf_video.view_browse_videos')) {
                return Phpfox_Error::display($oYvvpService->wrapError(_p('unable_to_view_this_item_due_to_privacy_settings')));
            } else {
                if (Phpfox::hasCallback($aVideo['module_id'],
                        'checkPermission') && !Phpfox::callback($aVideo['module_id'] . '.checkPermission',
                        $aVideo['item_id'], 'pf_video.view_browse_videos')) {
                    return Phpfox_Error::display($oYvvpService->wrapError(_p('unable_to_view_this_item_due_to_privacy_settings')));
                }
            }
        }

        if (Phpfox::isModule('privacy')) {
            Phpfox::getService('privacy')->check('v', $aVideo['video_id'], $aVideo['user_id'], $aVideo['privacy'],
                $aVideo['is_friend']);
        }

        (($sPlugin = Phpfox_Plugin::get('yncvideovp.component_controller_view_process_start')) ? eval($sPlugin) : false);

        $this->_updateCounter($aVideo);

        $this->setParam('aVideo', $aVideo);

        $this->_setFeedParam($aVideo);

        $aMetaTags = [
            'og:type' => 'video',
            'og:image' => $aVideo['image_path'],
            'og:image:width' => 640,
            'og:image:height' => 360
        ];

        $bLoadCheckin = false;
        if (Phpfox::isModule('feed') && Phpfox::getParam('feed.enable_check_in') && Phpfox::getParam('core.google_api_key')) {
            $this->template()->setHeader('cache', array(
                    'places.js' => 'module_feed'
                )
            );
            $bLoadCheckin = true;
        }

        $aTitleLabel = $this->_getTitleLabel($aVideo);
        $this->_setPendingItem($aVideo);

        define('PHPFOX_APP_DETAIL_PAGE', true);
        header("X-XSS-Protection: 0");

        $this->template()->setTitle($aVideo['title'])
            ->setMeta('description', $aVideo['text'])
            ->setMeta('keywords', $this->template()->getKeywords($aVideo['title']))
            ->setMeta($aMetaTags)
            ->assign(array(
                    'sLink' => $sLink,
                    'aItem' => $aVideo,
                    'sView' => 'play',
                    'sAddThisPubId' => setting('core.addthis_pub_id', ''),
                    'sShareDescription' => str_replace(array("\n", "\r", "\r\n"), '', $aVideo['text']),
                    'bLoadCheckin' => $bLoadCheckin,
                    'aTitleLabel' => $aTitleLabel
                )
            );
        (($sPlugin = Phpfox_Plugin::get('yncvideovp.component_controller_view_process_end')) ? eval($sPlugin) : false);
    }

    function _checkPermission($module = 'video')
    {
        switch ($module) {
            case 'video':
                $permission_key = 'pf_video_view';
                break;
            case 'ultimatevideo':
                $permission_key = 'ynuv_can_view_video';
                break;
            case 'videochannel':
                $permission_key = 'videochannel.can_access_videos';
                break;
        }

        if (!Phpfox::getUserParam($permission_key, false)) {
            Phpfox_Url::instance()->send('subscribe.message');
        }
    }

    /**
     * @param $aVideo
     * @return array
     */
    protected function _getTitleLabel($aVideo)
    {
        $aTitleLabel = [
            'type_id' => 'video'
        ];

        if ($aVideo['is_featured']) {
            $aTitleLabel['label']['featured'] = [
                'title' => '',
                'title_class' => 'flag-style-arrow',
                'icon_class' => 'diamond'
            ];
        }
        if ($aVideo['is_sponsor']) {
            $aTitleLabel['label']['sponsored'] = [
                'title' => '',
                'title_class' => 'flag-style-arrow',
                'icon_class' => 'sponsor'
            ];
        }

        if ($aVideo['view_id'] == 2) {
            $aTitleLabel['label']['pending'] = [
                'title' => '',
                'title_class' => 'flag-style-arrow',
                'icon_class' => 'clock-o'
            ];
        }

        $aTitleLabel['total_label'] = isset($aTitleLabel['label']) ? count($aTitleLabel['label']) : 0;

        return $aTitleLabel;
    }

    protected function _setFeedParam($aVideo)
    {
        $this->setParam('aFeed', array(
                'comment_type_id' => $aVideo['comment_type_id'],
                'like_type_id' => $aVideo['like_type_id'],
                'privacy' => $aVideo['privacy'],
                'comment_privacy' => $aVideo['comment_privacy'],
                'report_module' => $aVideo['report_module'],
                'feed_type' => $aVideo['feed_type'],
                'feed_link' => $aVideo['feed_link'],
                'feed_is_liked' => (isset($aVideo['is_liked']) ? $aVideo['is_liked'] : false),
                'feed_is_friend' => $aVideo['is_friend'],
                'item_id' => $aVideo['video_id'],
                'user_id' => $aVideo['user_id'],
                'total_comment' => $aVideo['total_comment'],
                'total_like' => $aVideo['total_like'],
                'feed_title' => $aVideo['title'],
                'feed_display' => 'view',
                'feed_total_like' => $aVideo['total_like'],
                'report_phrase' => _p('report_this_video')
            )
        );
    }

    /**
     * @param $aVideo
     */
    protected function _updateCounter($aVideo)
    {
        $bUpdateCounter = false;
        if (Phpfox::isModule('track')) {
            if (!$aVideo['video_is_viewed']) {
                $bUpdateCounter = true;
                Phpfox::getService('track.process')->add('v', $aVideo['video_id']);
            } else {
                if (!setting('track.unique_viewers_counter')) {
                    $bUpdateCounter = true;
                    Phpfox::getService('track.process')->add('v', $aVideo['video_id']);
                } else {
                    Phpfox::getService('track.process')->update('v', $aVideo['video_id']);
                }
            }
        } else {
            $bUpdateCounter = true;
        }

        if ($bUpdateCounter) {
            db()->updateCounter('video', 'total_view', 'video_id', $aVideo['video_id']);
        }
    }

    /**
     * @param $aVideo
     */
    protected function _setPendingItem($aVideo)
    {
        if ($aVideo['view_id'] == 2) {
            $aPendingItem = [
                'message' => _p('video_is_pending_approval'),
                'actions' => []
            ];
            if ($aVideo['canApprove']) {
                $aPendingItem['actions']['approve'] = [
                    'is_ajax' => true,
                    'label' => _p('approve'),
                    'action' => '$.ajaxCall(\'yncvideovp.approve\', \'video_id=' . $aVideo['video_id'] . '&video_type=' . $aVideo['video_type'] . '&is_detail=1\');'
                ];
            }
            if ($aVideo['canEdit']) {
                $aPendingItem['actions']['edit'] = [
                    'label' => _p('edit'),
                    'action' => $aVideo['sEditUrl'],
                ];
            }
            if ($aVideo['canDelete']) {
                $aPendingItem['actions']['delete'] = [
                    'is_ajax' => true,
                    'label' => _p('delete'),
                    'action' => '$Core.jsConfirm({message: \'' . _p('are_you_sure_you_want_to_delete_this_video_permanently') . '\'}, function () {$.ajaxCall(\'yncvideovp.delete\', \'video_id=' . $aVideo['video_id'] . '&video_type=' . $aVideo['video_type'] . '&is_detail=1\');}, function(){})'
                ];
            }
            $this->template()->assign([
                'aPendingItem' => $aPendingItem
            ]);
        }
    }
}