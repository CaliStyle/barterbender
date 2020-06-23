<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Block_Custom_View extends Phpfox_Component {

    public function process()
    {
        $iAuctionId = $this->getParam('product_id');
        $aMainCategory = Phpfox::getService('auction')->getAuctionMainCategory($iAuctionId);
        $aCustomFields = Phpfox::getService('auction')->getCustomFieldByCategoryId($aMainCategory['category_id']);

        $keyCustomField = array();
        $aCustomData = array();

        if ($iAuctionId)
        {
            $aCustomDataTemp = Phpfox::getService('auction.custom')->getCustomFieldByBusinessId($iAuctionId);

            if (count($aCustomFields))
            {
                foreach ($aCustomFields as $aField)
                {
                    foreach ($aCustomDataTemp as $aFieldValue)
                    {
                        if ($aField['field_id'] == $aFieldValue['field_id'])
                        {
                            $aCustomData[] = $aFieldValue;
                        }
                    }
                }
            }
        }

        if (count($aCustomData))
        {
            $aCustomFields = $aCustomData;
        }
        $this->template()->assign(array(
            'aCustomFields' => $aCustomFields
        ));
    }

}

?>
