<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Block_My_Offer_History_List extends Phpfox_Component {

    public function process()
    {
        $aProduct = $this->getParam('aProduct');
        $aRows = $this->getParam('aRows');
        $sCustomPagination = $this->getParam('sCustomPagination');
        $iPage = $this->getParam('iPage');
        $sSortAlias = $this->getParam('sSortAlias');
        
        $this->template()->assign(array(
            'sSortAlias' => $sSortAlias,
            'iPage' => $iPage,
            'aProduct' => $aProduct,
            'sCustomPagination' => $sCustomPagination,
            'aRows' => $aRows
                )
        );
    }

}

?>