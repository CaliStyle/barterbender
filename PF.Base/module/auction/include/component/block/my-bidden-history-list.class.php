<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Block_My_Bidden_History_List extends Phpfox_Component {

    public function process()
    {
        $aAuction = $this->getParam('aAuction');
        $aRows = $this->getParam('aRows');
        $sCustomPagination = $this->getParam('sCustomPagination');
        $iPage = $this->getParam('iPage');
        $sSortAlias = $this->getParam('sSortAlias');
        
        $this->template()->assign(array(
            'sSortAlias' => $sSortAlias,
            'iPage' => $iPage,
            'aAuction' => $aAuction,
            'sCustomPagination' => $sCustomPagination,
            'aRows' => $aRows
                )
        );
    }

}

?>