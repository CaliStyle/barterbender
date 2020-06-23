<?php
/**
 * Created by IntelliJ IDEA.
 * User: macpro
 * Date: 6/12/18
 * Time: 4:41 PM
 */

namespace Apps\YNC_VideoViewPop\Service;

use Phpfox_Service;
use Phpfox;

class Yncvideovp extends Phpfox_Service
{
    public function getVideo($sType, $iVideoId)
    {
        $sMethodName = "_get_{$sType}";
        if (method_exists($this, $sMethodName)) {

            $aVideo = $this->$sMethodName($iVideoId);
            $aVideo['video_type'] = $sType;
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
                if (isset($aVideo['embed_code'])) {
                    $aVideo['embed_code'] = str_replace('http://', 'https://', $aVideo['embed_code']);
                }
            }
            return $aVideo;
        } else {
            return false;
        }
    }

    protected function _get_video($iVideoId)
    {
        $aVideo = Phpfox::getService('v.video')->getVideo($iVideoId);
        $aVideo['duration'] = Phpfox::getService('v.video')->getDuration($aVideo['duration']);
        $aVideo['sEditUrl'] = Phpfox::getLib('url')->makeUrl('video.edit', array('id' => $iVideoId));

        $aVideo = array_merge($aVideo, array(
            'comment_type_id' => 'v',
            'like_type_id' => 'v',
            'comment_privacy' => user('pf_video_comment', 1) ? 0 : 3,
            'report_module' => 'v',
            'feed_type' => 'v',
            'feed_link' => Phpfox::permalink('video.play', $aVideo['video_id'], $aVideo['title']),
        ));

        return $aVideo;
    }

    protected function _get_ultimatevideo($iVideoId)
    {
        $aVideo = Phpfox::getService('ultimatevideo')->getVideo($iVideoId);

        $aCategory = Phpfox::getService('ultimatevideo.category')->getCategoryById($aVideo['category_id']);
        $aVideo['sHtmlCategories'] = sprintf('<a href="%s">%s</a>', Phpfox::permalink('ultimatevideo.category', $aCategory['category_id'], $aCategory['title']), _p($aCategory['title']));

        $aVideo['embed'] = Phpfox::permalink('ultimatevideo.embed', $iVideoId, '');
        $aVideo['text'] = $aVideo['description'];
        $aVideo['duration'] = ultimatevideo_duration($aVideo['duration']);
        $aVideo['sEditUrl'] = Phpfox::getLib('url')->makeUrl('ultimatevideo.add', array('id' => $iVideoId));

        $aVideo['canEdit'] = ((Phpfox::getUserId() == $aVideo['user_id'] && user('ynuv_can_edit_own_video'))
            || user('ynuv_can_edit_video_of_other_user'));
        $aVideo['canDelete'] = (user('ynuv_can_delete_video_of_other_user') || (Phpfox::getUserId() == $aVideo['user_id'] && user('ynuv_can_delete_own_video')));
        $aVideo['canFeature'] = user('ynuv_can_feature_video');
        $aVideo['canApprove'] = user('ynuv_can_approve_video');
        $aVideo['view_id'] = $aVideo['is_approved'] ? 0 : 2;

        $aVideo = array_merge($aVideo, array(
            'comment_type_id' => 'ultimatevideo_video',
            'like_type_id' => 'ultimatevideo_video',
            'comment_privacy' => $aVideo['privacy'],
            'report_module' => 'ultimatevideo_video',
            'feed_type' => 'ultimatevideo_video',
            'feed_link' => $aVideo['bookmark_url'],
        ));

        Phpfox::getService('ultimatevideo.history')
            ->addVideo(Phpfox::getUserId(), $iVideoId);

        // update view later status if the video is in watch later list
        Phpfox::getService('ultimatevideo.watchlater')
            ->updateViewStatus(Phpfox::getUserId(), $iVideoId);

        Phpfox::getService('ultimatevideo.process')
            ->updateViewCount($iVideoId);

        $isWatchLater = Phpfox::getService('ultimatevideo.watchlater')->findId(Phpfox::getUserId(), $aVideo['video_id']);
        if ($isWatchLater)
            $aVideo['watchlater'] = 1;
        else
            $aVideo['watchlater'] = 0;
        return $aVideo;
    }

    protected function _get_videochannel($iVideoId)
    {
        $oService = Phpfox::getService('videochannel');
        $aVideo = $oService->getVideo($iVideoId);

        $aVideo['canEdit'] = ($aVideo['user_id'] == Phpfox::getUserId() && Phpfox::getUserParam('videochannel.can_edit_own_video')) || (Phpfox::getUserParam('videochannel.can_edit_other_video') && $aVideo['user_id'] != Phpfox::getUserId());
        $aVideo['sEditUrl'] = Phpfox::getLib('url')->makeUrl('videochannel.edit', array('id' => $iVideoId));
        $aVideo['canDelete'] = ($aVideo['user_id'] == Phpfox::getUserId() && Phpfox::getUserParam('videochannel.can_delete_own_video')) || (Phpfox::getUserParam('videochannel.can_delete_other_video') && $aVideo['user_id'] != Phpfox::getUserId());
        $aVideo['canFeature'] = Phpfox::getUserParam('videochannel.can_feature_videos_');
        $aVideo['canApprove'] = Phpfox::getUserParam('videochannel.can_approve_videos');
        $aVideo['bIsFavourite'] = $oService->isFavourite($aVideo['video_id']);

        $aVideo = array_merge($aVideo, array(
            'comment_type_id' => 'videochannel',
            'like_type_id' => 'videochannel',
            'comment_privacy' => $aVideo['privacy_comment'],
            'report_module' => 'videochannel',
            'feed_type' => 'videochannel',
            'feed_link' => Phpfox::permalink('videochannel', $aVideo['video_id'], $aVideo['title']),
        ));

        return $aVideo;
    }

    public function wrapError($msg = '')
    {
        return '<div class="p-2"><div class="error_message">' . $msg . '</div></div>';
    }

    public function addJSApiParam($embedCode = '')
    {
        $pattern = '/(src=")([^"]+)(")/';

        return preg_replace_callback($pattern, function ($matches) {
            $sNewSrc = $matches[2];
            if (strpos($sNewSrc, '?') !== false) {
                $sNewSrc .= '&enablejsapi=1&api=1';
            } else {
                $sNewSrc .= '?enablejsapi=1&api=1';
            }
            $sNewSrc = $matches[1] . $sNewSrc . $matches[3];

            return $sNewSrc;
        }, $embedCode);
    }
}