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
class Suggestion_Component_Block_Friends extends Phpfox_Component
{

    /**
     * Class process method which is used to execute this component.
     */
    public function process()
    {
        Phpfox::isUser(true);
        $sSuggestionType = htmlentities($this->getParam('sSuggestionType', 'suggestion'));
        $sModule = htmlentities($this->getParam('sModule', 'suggestion_friend'));
        $iFriendId = (int)$this->getParam('iFriendId');
        $iSuggestion = (int)$this->getParam('iSuggestion');
        $sLink = $this->getParam('sLink', '');
        $sTitle = $this->getParam('sTitle', '');
        $iSuggestionPages = false;
        if ($sModule == 'suggestion_pages') {
            $iSuggestionPages = true;
        }
        if ($iFriendId == 0)
            exit();

        $_SESSION['iFriendId'] = (int)$iFriendId;

        switch ($sSuggestionType) {
            case 'suggestion':
                $sContinue = _p('suggestion.send_suggestion');
                break;
            default:
                switch ($sModule) {
                    case 'suggestion_friend';
                        $sContinue = _p('suggestion.add_friends');
                        break;
                }
                break;
        }
        if (isset($_SESSION['feed_added']))
            $bSocialPublishers = 1;
        else
            $bSocialPublishers = 0;

        $iMaxChars = (int)Phpfox::getUserParam('suggestion.max_message_chars');
        $sMaxChars = preg_replace('/{{max_chars}}/', $iMaxChars, _p('suggestion.chars_limit'));
        $this->template()->assign(array(
                'sMessage' => _p('suggestion.message_tip'),
                'sModule' => $sModule,
                'sTitle' => $sTitle,
                'sLink' => $sLink,
                'sSuggestionType' => $sSuggestionType,
                'iMaxChars' => $iMaxChars,
                'iSuggestion' => $iSuggestion,
                'sMaxChars' => $sMaxChars,
                'iFriendId' => $iFriendId,
                'bSocialPublishers' => $bSocialPublishers,
                'sContinue' => $sContinue,
                'bDontAskMeAgain' => !Phpfox::getService('suggestion')->isAllowContentSuggestionPopup(),
                'iSuggestionPages' => $iSuggestionPages
            )
        );


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