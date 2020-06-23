<?php
/**
 * [PHPFOX_HEADER]
 */

namespace Apps\YouNet_UltimateVideos\Block;

use Phpfox;
use Phpfox_Component;

defined('PHPFOX') or exit('NO DICE!');

class RatingItem extends Phpfox_Component
{
    /**
     * Controller
     */
    public function process()
    {
        $iUserId = $this->getParam('user_id');
        $iRating = $this->getParam('rating');

        if (empty($iUserId)) {
            return false;
        }

        $aUser = Phpfox::getService('user')->getUser($iUserId);
        if (!empty($aUser['profile_page_id']) && Phpfox::isModule('pages') && $aPage = Phpfox::getService('like')->getLikedByPage($aUser['profile_page_id'], Phpfox::getUserId())) {
            $aUser['page'] = $aPage;
        }
        $bIsFriend = Phpfox::getService('friend')->isFriend(Phpfox::getUserId(), $iUserId);
        $this->template()->assign(compact('aUser', 'bIsFriend', 'iRating'));

        return 'block';
    }
}
