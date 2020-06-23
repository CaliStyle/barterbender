<?php
/**
 * Created by PhpStorm.
 * User: namnv
 * Date: 8/24/16
 * Time: 3:32 PM
 */

namespace Apps\YouNet_UltimateVideos\Controller;


use Core\Route\Controller;
use Phpfox;
use Phpfox_Error;
use Privacy_Service_Privacy;

class EmbedController extends \Phpfox_Component
{
    const MAX_CATEGORY_LEVEL = 3;

    public function process()
    {
        Controller::$name = '';

        $id = $this->request()->get('req3');

        $aItem = Phpfox::getService('ultimatevideo')
            ->getVideo($id, false);

        if (Phpfox::isModule('privacy')) {
            if (!Phpfox::getService('privacy')->check('ultimatevideo', $aItem['video_id'], $aItem['user_id'], $aItem['privacy'], $aItem['is_friend'], true)) {
                exit(_p('privacy.the_item_or_section_you_are_trying_to_view_has_specific_privacy_settings_enabled_and_cannot_be_viewed_at_this_time'));
            }
        }

        if (!$aItem) {
            exit(_p('the_video_you_are_looking_for_does_not_exist_or_has_been_removed'));
        } elseif ($aItem['status'] == 0) {
            exit(_p('the_video_you_are_looking_for_does_not_exist_or_has_not_been_processed_yet'));
        } elseif ($aItem['status'] != 1 && $aItem['status'] != 0 && $aItem['status'] != 2) {
            exit(_p('the_video_you_are_looking_for_was_failed_to_upload'));
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

        Phpfox::getService('ultimatevideo.process')
            ->updateViewCount($id);

        $this->template()->assign([
            'aItem' => $aItem,
        ]);


        if (!user('ynuv_can_approve_video', 0)) {
            if ($aItem['is_approved'] != '1' && $aItem['user_id'] != Phpfox::getUserId()) {
                exit('Video not found.');
            }
        }
        \Phpfox_Module::instance()->getControllerTemplate();
        die;
    }
}