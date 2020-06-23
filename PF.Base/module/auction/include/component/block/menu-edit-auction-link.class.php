<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Block_Menu_Edit_Auction_Link extends Phpfox_Component
{

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $sTabView = $this->request()->get('req2');
        $iAuctionId = $this->request()->get('id');
    
        $this->template()->assign(array(
            'sHeader' => _p('menu_edit'),
            'iAuctionId' => $iAuctionId
            )
        );

        return 'block';
    }

}

?>