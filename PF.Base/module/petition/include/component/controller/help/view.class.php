<?php

defined('PHPFOX') or exit('NO DICE!');

class Petition_Component_Controller_Help_View extends Phpfox_Component
{
    public function process()
    {
        $iId = $this->request()->getInt('req3');

        $aItem = Phpfox::getService('petition.help')->getHelpForEdit($iId);

        if (!isset($aItem['help_id'])) {
            return Phpfox_Error::display(Phpfox::getPhrase('petition.the_petition_help_you_are_looking_for_cannot_be_found'));
        }
        $this->setParam(array('aHelp' => $aItem));

        $this->template()->setTitle($aItem['title'])
            ->setBreadCrumb(Phpfox::getPhrase('petition.petitions_title'), $this->url()->makeUrl('petition.help'))
            ->setBreadCrumb($aItem['title'], $this->url()->permalink('petition.help', $aItem['help_id'], $aItem['title']), true)
            ->setMeta('description', $aItem['title'] . '.')
            ->setMeta('description', $aItem['content'] . '.')
            ->setMeta('keywords', $this->template()->getKeywords($aItem['title']))
            ->assign(array(
                    'aItem' => $aItem,
                    'bIsViewHelp' => true
                )
            );

        $aFilterMenu = array();

        if (!defined('PHPFOX_IS_USER_PROFILE') && !defined('PHPFOX_IS_PAGES_VIEW')) {
            $aFilterMenu = array(
                Phpfox::getPhrase('petition.all_petitions') => '',
                Phpfox::getPhrase('petition.my_petitions') => 'my',
            );

            if (!Phpfox::getParam('core.friends_only_community') && Phpfox::isModule('friend')) {
                $aFilterMenu[Phpfox::getPhrase('petition.friends_petitions')] = 'friend';
            }

            if (Phpfox::getUserParam('petition.can_approve_petitions')) {
                $iPendingTotal = Phpfox::getService('petition')->getPendingTotal();

                if ($iPendingTotal) {
                    $aFilterMenu[Phpfox::getPhrase('petition.pending_petitions') . (Phpfox::getUserParam('petition.can_approve_petitions') ? '<span class="pending">' . $iPendingTotal . '</span>' : 0)] = 'pending';
                }
            }
            $aFilterMenu[Phpfox::getPhrase('petition.help')] = 'petition.help.view_help';

            $this->template()->buildSectionMenu('petition', $aFilterMenu);
        }
    }

    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('petition.component_controller_help_view_clean')) ? eval($sPlugin) : false);
    }
}

?>