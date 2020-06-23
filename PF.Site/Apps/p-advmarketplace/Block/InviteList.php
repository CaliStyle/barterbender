<?php

namespace Apps\P_AdvMarketplace\Block;

use Phpfox;
use Phpfox_Component;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class InviteList extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $iPage = $this->request()->getInt('page');
        $iType = $this->request()->getInt('type', 1);
        $iPageSize = 6;

        if (PHPFOX_IS_AJAX) {
            $aListing = Phpfox::getService('advancedmarketplace')->getListing($this->request()->get('id'), true);
            $this->template()->assign('aListing', $aListing);
        } else {
            $aListing = $this->getParam('aListing');
        }

        list($iCnt, $aInvites) = Phpfox::getService('advancedmarketplace')->getInvites($aListing['listing_id'], $iType,
            $iPage, $iPageSize);

        Phpfox::getLib('pager')->set(array(
            'ajax' => 'advancedmarketplace.listInvites',
            'page' => $iPage,
            'size' => $iPageSize,
            'count' => $iCnt,
            'aParams' => array('id' => $aListing['listing_id'])
        ));

        $this->template()->assign(array(
                'aInvites' => $aInvites,
                'iType' => $iType,
                'iPage' => $iPage,
                'sCustomClassName' => 'ync-block',
            )
        );

        if (!PHPFOX_IS_AJAX) {
            $this->template()->assign(array(
                    'sHeader' => _p('advancedmarketplace.invites'),
                    'sBoxJsId' => 'advancedmarketplace_members'
                )
            );

            $this->template()->assign(array(
                    'aMenu' => array(
                        _p('advancedmarketplace.visited') => '#advancedmarketplace.listInvites?type=1&amp;id=' . $aListing['listing_id'],
                        _p('advancedmarketplace.not_responded') => '#advancedmarketplace.listInvites?type=0&amp;id=' . $aListing['listing_id']
                    )
                )
            );

            return 'block';
        }
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('advancedmarketplace.component_block_invite_list_clean')) ? eval($sPlugin) : false);
    }
}
