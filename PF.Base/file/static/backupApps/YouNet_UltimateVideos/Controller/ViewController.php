<?php
/**
 * Created by PhpStorm.
 * User: namnv
 * Date: 8/12/16
 * Time: 11:09 AM
 */

namespace Apps\YouNet_UltimateVideos\Controller;

use Core\Route\Controller;
use Phpfox;
use Phpfox_Error;
use Privacy_Service_Privacy;

class ViewController extends \Phpfox_Component
{
    const MAX_CATEGORY_LEVEL = 3;

    public function process()
    {
        if (!setting('ynuv_app_enabled')) {
            return Phpfox::getLib('module')->setController('error.404');
        }
        Controller::$name = '';

        $id = $this->request()->getInt('req2');

        $aItem = Phpfox::getService('ultimatevideo')
            ->getVideo($id);
        Phpfox::getService('ultimatevideo')->getPermissions($aItem);
        $iUserRating = Phpfox::getService('ultimatevideo.rating')->getAVGRatingOfVideo($id);
        $iViewerRating = (int)Phpfox::getService('ultimatevideo.rating')->getRatingVideoByUser(Phpfox::getUserId(), $id);
        $aItem['viewer_rating'] = $iViewerRating;

        if ((int)$iUserRating) {
            $aItem['rating'] = round($iUserRating, 1);
        }
        $sError = null;
        if (Phpfox::isModule('privacy')) {
            Phpfox::getService('privacy')->check('ultimatevideo', $aItem['video_id'], $aItem['user_id'], $aItem['privacy'], $aItem['is_friend']);
        }
        if (!user('ynuv_can_view_video')) {
            $sError = _p('you_dont_have_permission_to_view_this_video');
        }
        if (!$aItem) {
            $sError = _p('the_video_you_are_looking_for_does_not_exist_or_has_been_removed');
        } elseif ($aItem['status'] == 0) {
            $sError = _p('the_video_you_are_looking_for_does_not_exist_or_has_not_been_processed_yet');
        } elseif ($aItem['status'] != 1 && $aItem['status'] != 0 && $aItem['status'] != 2) {
            $sError = _p('the_video_you_are_looking_for_was_failed_to_upload');
        }

        $this->template()->setTitle($aItem['title']);
        if (!user('ynuv_can_approve_video', 0) && !$sError) {
            if ($aItem['is_approved'] != '1' && $aItem['user_id'] != Phpfox::getUserId()) {
                $sError = _p('video_not_found');
            }
        }
        if ($sError) {
            $this->template()->setBreadCrumb(_p('ultimate_videos'), Phpfox::permalink('ultimatevideo', false));
            $this->template()->assign([
                'sError' => $sError
            ]);
            return null;
        }
        define('ULTIMATE_VIDEO_ID', $id);
        define('ULTIMATE_VIDEO_OWNER_ID', $aItem['user_id']);
        define('ULTIMATE_VIDEO_USER_NAME', $aItem['full_name']);
        define('ULTIMATE_VIDEO_CATEGORY_ID', $aItem['category_id']);

        // add video to history list
        Phpfox::getService('ultimatevideo.history')
            ->addVideo(Phpfox::getUserId(), $id);

        // update view later status if the video is in watch later list
        Phpfox::getService('ultimatevideo.watchlater')
            ->updateViewStatus(Phpfox::getUserId(), $id);

        if (Phpfox::isModule('track')) {
            if (!$aItem['video_is_viewed']) {
                $bUpdateCounter = true;
                Phpfox::getService('track.process')->add('ultimatevideo', 'video_' . $aItem['video_id']);
            } else {
                if (!setting('track.unique_viewers_counter')) {
                    $bUpdateCounter = true;
                    Phpfox::getService('track.process')->add('ultimatevideo', 'video_' . $aItem['video_id']);
                } else {
                    Phpfox::getService('track.process')->update('ultimatevideo_video', $aItem['video_id']);
                }
            }
        } else {
            $bUpdateCounter = true;
        }

        if ($bUpdateCounter) {
            Phpfox::getService('ultimatevideo.process')->updateViewCount($id);
        }

        $corePath = Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/YouNet_UltimateVideos';
        $isWatchLater = Phpfox::getService('ultimatevideo.watchlater')->findId(Phpfox::getUserId(), $aItem['video_id']);
        if ($isWatchLater)
            $aItem['watchlater'] = 1;
        else
            $aItem['watchlater'] = 0;

        $bShowEditMenu = false;
        if (
            user('ynuv_can_approve_video') ||
            user('ynuv_can_delete_video_of_other_user') ||
            user('ynuv_can_edit_video_of_other_user') ||
            user('ynuv_can_feature_video')) {
            $bShowEditMenu = true;
        }

        if ((Phpfox::getUserId() == $aItem['user_id']) && (
                user('ynuv_can_delete_own_video') ||
                user('ynuv_can_edit_own_video'))) {
            $bShowEditMenu = true;
        }
        if (Phpfox::getParam('core.allow_html') && !empty($aItem['description'])) {
            $oFilter = Phpfox::getLib('parse.input');
            $aItem['description'] = $oFilter->prepare(htmlspecialchars_decode($aItem['description']));
        }
        $aMetaTags = [
            'og:type' => 'image.gallery',
            'og:image:width' => '500',
            'og:image:height' => '500',
        ];
        if (!empty($aItem['image_path'])) {
            $aMetaTags['og:image'] = $aItem['image_server_id'] == -1 ? (Phpfox::getParam('ultimatevideo.ynuv_video_s3_url') . $aItem['image_path']) :  Phpfox::getLib('image.helper')->display(array(
                'server_id' => $aItem['image_server_id'],
                'path' => 'core.url_pic',
                'file' => $aItem['image_path'],
                'suffix' => '_1024',
                'return_url' => true
            ));
        } else {
            $aMetaTags['og:image'] = Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/YouNet_UltimateVideos/assets/image/noimg_video.jpg';
        }

        $aLatlng = str_replace("{", "", $aItem['location_latlng']);
        $aLatlng = str_replace("}", "", $aLatlng);
        $aLatlng = explode(",", $aLatlng);
        $aLatitude = substr($aLatlng[0], 11);
        $aLongitude = substr($aLatlng[1], 12);
        if ((int)$aItem['is_approved'] == 0) {
            $aTitleLabel = [
                'type_id' => 'blog',
                'label' => [
                    'pending' => [
                        'title' => '',
                        'title_class' => 'flag-style-arrow',
                        'icon_class' => 'clock-o'
                    ]
                ]
            ];
            $aPendingItem = [
                'message' => _p('ultimatevideos_pending_approval'),
                'actions' => []
            ];
            if (user('ultimatevideo.ynuv_can_approve_video')) {
                $aPendingItem['actions']['approve'] = [
                    'is_ajax' => true,
                    'label' => _p('approve'),
                    'action' => '$.ajaxCall(\'ultimatevideo.approve_video\', \'iVideoId=' . $aItem['video_id'] . '\')'
                ];
            }
            if ((int)$aItem['user_id'] == Phpfox::getUserId() && user('ultimatevideo.ynuv_can_edit_own_video')) {
                $aPendingItem['actions']['edit'] = [
                    'label' => _p('edit'),
                    'action' => $this->url()->makeUrl('ultimatevideo.add', ['id' => $aItem['video_id']]),
                ];
            }
            if ((int)$aItem['user_id'] == Phpfox::getUserId() && user('ultimatevideo.ynuv_can_delete_own_video')) {
                $aPendingItem['actions']['delete'] = [
                    'is_ajax' => true,
                    'label' => _p('delete'),
                    'action' => '$Core.jsConfirm({message: \'' . _p('are_you_sure') . '\'}, function () {$.ajaxCall(\'ultimatevideo.delete_video\',\'iVideoId=' . $aItem['video_id'] . '&isDetail=true\');}, function () {})'
                ];
            }
            $this->template()->assign([
                'aPendingItem' => $aPendingItem,
                'aTitleLabel' => $aTitleLabel,
            ]);
        }
        //TODO: will add to detail page
        Phpfox::getLib('module')->appendPageClass('p-detail-page');

        $this->template()
            ->setMeta($aMetaTags)
            ->setMeta('description', $aItem['description'])
            ->assign([
                'bShowModeration' => false,
                'corePath' => $corePath,
                'aItem' => $aItem,
                'latitude' => $aLatitude,
                'longitude' => $aLongitude,
                'sShareDescription' => str_replace(array("\n", "\r", "\r\n"), '', $aItem['description']),
                'bShowEditMenu' => $bShowEditMenu,
                'sUrl' => Phpfox::permalink('ultimatevideo.embed', $id, ''),
                'bIsPagesView' => false,
                'bIsDetailView' => true,
                'iProfilePageId' => (int)Phpfox::getUserBy('profile_page_id')
            ])->setHeader('cache', array(
                    'jscript/clipboard.min.js' => 'app_YouNet_UltimateVideos'
                )
            );

        if (!user('ynuv_can_approve_video', 0)) {
            if ($aItem['is_approved'] != '1' && $aItem['user_id'] != Phpfox::getUserId()) {
                return \Phpfox_Error::display('video_not_found', 404);
            }
        }
        $this->setParam('aFeed', array(
                'comment_type_id' => 'ultimatevideo_video',
                'privacy' => $aItem['privacy'],
                'comment_privacy' => Phpfox::getUserParam('ultimatevideo.ynuv_can_add_comment_on_video') ? 0 : 3,
                'like_type_id' => 'ultimatevideo_video',
                'feed_is_liked' => isset($aItem['is_liked']) ? $aItem['is_liked'] : false,
                'feed_is_friend' => $aItem['is_friend'],
                'item_id' => $aItem['video_id'],
                'user_id' => $aItem['user_id'],
                'total_comment' => $aItem['total_comment'],
                'feed_type' => 'ultimatevideo_video',
                'total_like' => $aItem['total_like'],
                'feed_link' => $aItem['bookmark_url'],
                'feed_title' => $aItem['title'],
                'feed_display' => 'view',
                'feed_total_like' => $aItem['total_like'],
                'report_module' => 'ultimatevideo_video',
                'report_phrase' => _p('report_this_video'),
                'time_stamp' => $aItem['time_stamp'],
            )
        );

        $bLoadCheckin = false;
        if (Phpfox::isModule('feed') && Phpfox::getParam('feed.enable_check_in') && Phpfox::getParam('core.google_api_key')) {
            $this->template()->setHeader('cache', array(
                    'places.js' => 'module_feed'
                )
            );
            $bLoadCheckin = true;
        }

        $this->template()
            ->assign(array(
                    'sAddThisPubId' => setting('core.addthis_pub_id', ''),
                    'bLoadCheckin' => $bLoadCheckin,
                )
            );

        $aCallback = false;

        if (!empty($aItem['module_id']) && $aItem['module_id'] != 'ynultimatevideo' && Phpfox::isModule($aItem['module_id'])) {
            if (Phpfox::hasCallback($aItem['module_id'], 'getVideoDetails')) {
                $aCallback = Phpfox::callback($aItem['module_id'] . '.getVideoDetails', $aItem);
            } else {
                $aCallback = $this->getVideoDetails($aItem);

            }
            $this->template()->setBreadcrumb($aCallback['breadcrumb_title'], $aCallback['breadcrumb_home']);

            $this->template()->setBreadcrumb($aCallback['title'], $aCallback['url_home']);
            if (($aItem['module_id'] == 'pages' && !Phpfox::getService('pages')->hasPerm($aCallback['item_id'], '')) || $aItem['module_id'] == 'groups' && !Phpfox::getService('groups')->hasPerm($aCallback['item_id'], '')) {
                return \Phpfox_Error::display(_p('unable_to_view_this_item_due_to_privacy_settings'));
            }

        } else {
            $this->template()->setBreadCrumb(_p('ultimate_videos'), Phpfox::permalink('ultimatevideo', false))
                ->setBreadCrumb($aItem['title'], $aItem['bookmark_url'], true)
            ;
        }

        $aCategories = Phpfox::getService('ultimatevideo.category')->getCategoryAncestors($aItem['category_id'], self::MAX_CATEGORY_LEVEL);
        $sCategories = Phpfox::getService('ultimatevideo.category')->getCategoriesBreadcrumbByVideoId($id);

        Phpfox::getService('ultimatevideo.callback')->buildFilterMenu();
        $this->template()->assign(
            array(
                'sCategories' => $sCategories
            )
        );
        foreach ($aCategories as $aItem) {
            $this->template()->setBreadCrumb(\Core\Lib::phrase()->isPhrase($aItem['title']) ? _p($aItem['title']) : $aItem['title'], Phpfox::permalink('ultimatevideo.category', $aItem['category_id'], $aItem['title']));
        }

        $this->template()
            ->setMeta('keywords', Phpfox::getParam('ultimatevideo.ynuv_meta_keywords'));

        return null;
    }

    public function getVideoDetails($aItem)
    {

        if (Phpfox::isModule('pages')) {
            Phpfox::getService('pages')->setIsInPage();
        }

        $aRow = Phpfox::getService('groups')->getPage($aItem['item_id']);

        if (!isset($aRow['page_id'])) {
            return false;
        }

        Phpfox::getService('groups')->setMode();

        $sLink = Phpfox::getService('groups')->getUrl($aRow['page_id'], $aRow['title'], $aRow['vanity_url']);

        return array(
            'breadcrumb_title' => _p('Groups'),
            'breadcrumb_home' => \Phpfox_Url::instance()->makeUrl('groups'),
            'module_id' => 'groups',
            'item_id' => $aRow['page_id'],
            'title' => $aRow['title'],
            'url_home' => $sLink,
            'url_home_photo' => $sLink . 'video/',
            'theater_mode' => _p('pages.in_the_page_link_title', array('link' => $sLink, 'title' => $aRow['title']))
        );
    }
}