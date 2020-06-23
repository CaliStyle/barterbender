<?php
/**
 * Created by PhpStorm.
 * User: davidnguyen
 * Date: 7/25/16
 * Time: 6:08 PM
 */

namespace Apps\YouNet_UltimateVideos\Install;

use Phpfox;

class UltimateVideosv401p3
{
    public function process()
    {
        db()->delete(':user_group_setting', 'module_id= "ultimatevideo" AND name = "ynuv_can_control_user_can_add_comment_on_their_video"');
        db()->delete(':user_group_setting', 'module_id= "ultimatevideo" AND name = "ynuv_can_control_user_can_add_comment_on_their_playlist"');
        $aUpdatePhrase = [
            'user_setting_ynuv_can_delete_playlist_of_other_user' => 'Can delete all playlists?',
            'user_setting_ynuv_can_delete_video_of_other_user' => 'Can delete all videos?',
            'user_setting_ynuv_can_edit_video_of_other_user' => 'Can edit all video?',
            'user_setting_ynuv_can_edit_playlist_of_other_user' => 'Can edit all playlists?'
        ];
        $this->_updatePhrases($aUpdatePhrase);
        //remove block edit menu
        db()->delete(':block', 'm_connection = \'ultimatevideo.addplaylist\' AND component = \'editplaylistmenu\'');
        db()->delete(':block', 'm_connection = \'ultimatevideo.add\' AND component = \'editvideomenu\'');
    }

    //This function to support version < 4.5.3
    private function _updatePhrases($aPhrases = [])
    {
        if (!is_array($aPhrases)) {
            return false;
        }
        foreach ($aPhrases as $sVarName => $sText) {
            db()->update(Phpfox::getT('language_phrase'), [
                'text' => Phpfox::getLib('parse.input')->convert($sText),
                'text_default' => Phpfox::getLib('parse.input')->convert($sText)
            ], 'var_name=\'' . $sVarName . '\' AND text=text_default');
        }

        return true;
    }
}