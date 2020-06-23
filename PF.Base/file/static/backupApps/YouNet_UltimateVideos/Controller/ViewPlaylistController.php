<?php
/**
 * Created by PhpStorm.
 * User: namnv
 * Date: 8/11/16
 * Time: 5:47 PM
 */

namespace Apps\YouNet_UltimateVideos\Controller;

use Core\Route\Controller;
use Phpfox_Component;
use Phpfox;
use Phpfox_Error;
use Privacy_Service_Privacy;

class ViewPlaylistController extends Phpfox_Component
{
    const MAX_CATEGORY_LEVEL = 3;

    public function process()
    {
        if (!setting('ynuv_app_enabled')) {
            return Phpfox::getLib('module')->setController('error.404');
        }
        Controller::$name = '';

        $id = $this->request()->getInt('req3');

        $sError = null;

        $this->template()->assign([
            'bShowModeration' => false,
        ]);

        $aItem = Phpfox::getService('ultimatevideo')->getPlaylist($id);
        $this->template()->setBreadCrumb(_p('ultimate_videos'), Phpfox::permalink('ultimatevideo.playlist', null, false))->setTitle($aItem['title'])
            ->setBreadCrumb($aItem['title'], $aItem['bookmark_url'], true)
        ;
        if (!user('ynuv_can_view_playlist')) {
            $sError = _p('you_dont_have_permission_to_view_this_playlist');
        }
        if (!$aItem) {
            $sError = _p('the_playlist_you_are_looking_for_does_not_exist_or_has_been_removed');
        }
        if (!user('ynuv_can_approve_playlist', 0) && !$sError) {
            if ($aItem['is_approved'] != '1' && $aItem['user_id'] != Phpfox::getUserId()) {
                $sError = _p('playlist_not_found');
            }
        }
        if ($sError) {
            $this->template()->assign([
                'sError' => $sError
            ]);
            return null;
        }
        define('ULTIMATE_PLAYLIST_ID', $id);
        define('ULTIMATE_PLAYLIST_OWNER_ID', $aItem['user_id']);
        define('ULTIMATE_PLAYLIST_USER_NAME', $aItem['full_name']);
        define('ULTIMATE_PLAYLIST_CATEGORY_ID', $aItem['category_id']);

        $this->setParam('playlist_id', $id);
        if (Phpfox::isModule('privacy')) {
            Phpfox::getService('privacy')->check('ultimatevideo_playlist', $aItem['playlist_id'], $aItem['user_id'], $aItem['privacy'], $aItem['is_friend']);
        }

        // add video to history list
        Phpfox::getService('ultimatevideo.history')->addPlaylist(Phpfox::getUserId(), $id);

        if (Phpfox::isModule('track')) {
            if (!$aItem['playlist_is_viewed']) {
                $bUpdateCounter = true;
                Phpfox::getService('track.process')->add('ultimatevideo', 'playlist_' . $aItem['playlist_id']);
            } else {
                if (!setting('track.unique_viewers_counter')) {
                    $bUpdateCounter = true;
                    Phpfox::getService('track.process')->add('ultimatevideo', 'playlist_' . $aItem['playlist_id']);
                } else {
                    Phpfox::getService('track.process')->update('ultimatevideo_playlist', $aItem['playlist_id']);
                }
            }
        } else {
            $bUpdateCounter = true;
        }

        if ($bUpdateCounter) {
            Phpfox::getService('ultimatevideo.playlist.process')->updateViewCount($id);
        }

        $bShowEditMenu = false;
        if (
            user('ynuv_can_approve_playlist') ||
            user('ynuv_can_delete_playlist_of_other_user') ||
            user('ynuv_can_edit_playlist_of_other_user') ||
            user('ynuv_can_feature_playlist')) {
            $bShowEditMenu = true;
        }

        if ((Phpfox::getUserId() == $aItem['user_id']) && (
                user('ynuv_can_delete_own_playlists') ||
                user('ynuv_can_edit_own_playlists'))) {
            $bShowEditMenu = true;
        }
        if (Phpfox::getParam('core.allow_html') && !empty($aItem['description'])) {
            $oFilter = Phpfox::getLib('parse.input');
            $aItem['description'] = $oFilter->prepare(htmlspecialchars_decode($aItem['description']));
        }

        if (!empty($aItem['image_path'])) {
            $sMetaImage = Phpfox::getLib('image.helper')->display(array(
                'server_id' => $aItem['image_server_id'],
                'path' => 'core.url_pic',
                'file' => $aItem['image_path'],
                'suffix' => '_1024',
                'return_url' => true
            ));
        } else {
            $sMetaImage = Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/YouNet_UltimateVideos/assets/image/noimg_playlist.jpg';
        }

        $aMetaTags = [
            'og:type' => 'video',
            'og:image' => $sMetaImage,
            'og:image:width' => '500',
            'og:image:height' => '500',
        ];
        //TODO: will add to detail page
        Phpfox::getLib('module')->appendPageClass('p-detail-page');

        $this->template()
            ->setMeta($aMetaTags)
            ->setMeta('description', $aItem['description'])
            ->setHeader([
                'jscript/mediaelementplayer/mediaelement-and-player.js' => 'app_YouNet_UltimateVideos',
                'jscript/masterslider.min.js' => 'app_YouNet_UltimateVideos'
            ])
            ->assign([
                'aPitem' => $aItem,
                'sShareDescription' => str_replace(array("\n", "\r", "\r\n"), '', $aItem['description']),
                'bShowEditMenu' => $bShowEditMenu,
                'bIsPagesView' => false,
                'sUrl' => Phpfox::permalink('ultimatevideo.embed', $id, ''),
                'bIsDetailViewPlaylist' => true,
                'iProfilePageId' => (int)Phpfox::getUserBy('profile_page_id')
            ]);


        $this->setParam('aFeed', array(
                'comment_type_id' => 'ultimatevideo_playlist',
                'privacy' => $aItem['privacy'],
                'comment_privacy' => Phpfox::getUserParam('ultimatevideo.ynuv_can_add_comment_on_playlist') ? 0 : 3,
                'like_type_id' => 'ultimatevideo_playlist',
                'feed_is_liked' => isset($aItem['is_liked']) ? $aItem['is_liked'] : false,
                'feed_is_friend' => $aItem['is_friend'],
                'item_id' => $aItem['playlist_id'],
                'user_id' => $aItem['user_id'],
                'total_comment' => $aItem['total_comment'],
                'feed_type' => 'ultimatevideo_playlist',
                'total_like' => $aItem['total_like'],
                'feed_link' => $aItem['bookmark_url'],
                'feed_title' => $aItem['title'],
                'feed_display' => 'view',
                'feed_total_like' => $aItem['total_like'],
                'report_module' => 'ultimatevideo_playlist',
                'report_phrase' => _p('report_this_playlist'),
                'time_stamp' => $aItem['time_stamp'],
            )
        );

        $aCategories = Phpfox::getService('ultimatevideo.category')->getCategoryAncestors($aItem['category_id'], self::MAX_CATEGORY_LEVEL);
        if (!empty($aCategories)) {
            $this->template()->assign(array(
                'aCategory' => end($aCategories),
            ));
        }
        Phpfox::getService('ultimatevideo.callback')->buildFilterMenu();
        foreach ($aCategories as $aItem) {
            $this->template()->setBreadCrumb(\Core\Lib::phrase()->isPhrase($aItem['title']) ? _p($aItem['title']) : $aItem['title'], Phpfox::permalink('ultimatevideo.playlist.category', $aItem['category_id'], $aItem['title']));
        }
        $this->template()
            ->setMeta('keywords', Phpfox::getParam('ultimatevideo.ynuv_meta_keywords'));
//            ->setMeta('description', Phpfox::getParam('ultimatevideo.ynuv_meta_description'));

        return null;
    }
}