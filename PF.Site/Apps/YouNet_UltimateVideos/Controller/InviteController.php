<?php
/**
 * Created by PhpStorm.
 * User: namnv
 * Date: 8/30/16
 * Time: 10:58 AM
 */

namespace Apps\YouNet_UltimateVideos\Controller;


use Phpfox;
use Phpfox_Component;

class InviteController extends Phpfox_Component
{

    public function process()
    {
        Phpfox::isUser(true);

        title(_p('invite_friends'));
        $id = $this->request()->get('req3');
        $type = $this->request()->getInt('type');
        if ($type == 1)
            $aItem = Phpfox::getService('ultimatevideo')->getVideo($id);
        else
            $aItem = Phpfox::getService('ultimatevideo.playlist')->getPlaylistById($id);

        if ($val = $this->request()->get('val')) {
            if (!empty($val['invite'])) {
                if ($type == 1) {
                    Phpfox::getService('ultimatevideo.process')->sendVideoInvitations($id, $val, $type);
                    $sUrl = Phpfox::permalink('ultimatevideo', $id, $aItem['title']);
                    \Phpfox_Url::instance()->send($sUrl, [], _p('sent_invitations_successfully'));
                    return;
                } else {
                    Phpfox::getService('ultimatevideo.process')->sendVideoInvitations($id, $val, $type);
                    $sUrl = Phpfox::permalink('ultimatevideo.playlist', $id, $aItem['title']);
                    \Phpfox_Url::instance()->send($sUrl, [], _p('sent_invitations_successfully'));
                    return;
                }
            }
        }

        $aUser = Phpfox::getService('user')
            ->getUser(Phpfox::getUserId());

        $subject = _p('invitation_from_name_for_viewing_video', [
            'name' => $aUser['full_name'],
            'video' => $aItem['title']
        ]);
        if ($type == 1) {
            $message = _p("i_would_like_to_invite_you_to_enjoy_my_new_video_video_you_must_love_it_thank_you_and_have_fun", [
                'video' => Phpfox::permalink('ultimatevideo', $id),
            ]);
        } else {
            $message = _p("i_would_like_to_invite_you_to_enjoy_my_new_playlist_video_you_must_love_it_thank_you_and_have_fun", [
                'video' => Phpfox::permalink('ultimatevideo.playlist', $id),
            ]);
        }

        $this->template()->assign([
            'type' => $type,
            'aItem' => $aItem,
            'video_id' => $id,
            'subject' => $subject,
            'message' => $message,
        ]);
    }
}