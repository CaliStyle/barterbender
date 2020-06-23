<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * @copyright        [YOUNETCO]
 * @author        NghiDV
 * @package        Module_Suggestion
 * @version        $Id: sample.class.php 1 2011-11-25 15:29:17Z YOUNETCO $
 */
class Suggestion_Component_Block_Lessfriends extends Phpfox_Component
{
    /**
     * Class process method which is used to execute this component.
     */
    public function process()
    {
        $sSuggest = _p('suggestion.suggest_to_friends_2');
        $bAllow = (Phpfox::getUserParam('suggestion.enable_friend_recommend'));
        $bAllow = $bAllow || (Phpfox::getUserParam('suggestion.enable_friend_suggestion'));
        $bAllow = $bAllow && Phpfox::getUserParam('suggestion.display_fewer_friend_block');
        if (!Phpfox::getUserParam('suggestion.enable_friend_suggestion'))
            $bAllow = Phpfox::getUserParam('suggestion.enable_friend_suggestion');
        $bIsAllowSuggestion = Phpfox::getUserParam('suggestion.enable_friend_suggestion');

        if ($bAllow) {
            //get total random friends
            $aFriends = Phpfox::getService('suggestion')->getLessFriendsList(Phpfox::getUserId());
        } else {
            $aFriends = array();
        }

        $linkMoreFriend = $this->url()->makeUrl('friend');
        if (count($aFriends)) {
            $sLink = Phpfox::permalink(Phpfox::getUserBy('full_name'), null);
            $sTitle = _p('suggestion.will_get_friends_suggestion_from');
            $sLinkUser = '<a target="_blank" href="' . $sLink . '">' . Phpfox::getUserBy('full_name') . '</a>';
            $sTitle = preg_replace('/{{friend_name}}/', $sLinkUser, $sTitle);
            $sHeader = _p('suggestion.help_your_friends_find_more_friends_2');
            foreach ($aFriends as &$aFriend) {
                $aFriend['url'] = '<a href="#" class="suggest-user" onclick="show_suggestfriend(this);return false;" rel="' . $aFriend['user_id'] . '">' . $sSuggest . '</a>';
                $aFriend['user_link'] = Phpfox::getService('suggestion')->getUserLink($aFriend['user_id'], false);
            }
            $showMore = true;
        } else {
            return false;
        }
        $this->template()->assign(array(
            'linkMoreFriend' => $linkMoreFriend,
            'showMore' => $showMore,
            'sHeader' => $sHeader,
            'sTitle' => $sTitle,
            'bIsAllowSuggestion' => (int)$bIsAllowSuggestion,
            'iCurrentUserId' => Phpfox::getUserId(),
            'aFriends' => $aFriends,
            'aFooter' => array(
                _p('view_more') => $linkMoreFriend
            ),
        ));
        return 'block';
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('suggestion.component_block_friends_clean')) ? eval($sPlugin) : false);
    }
}

?>