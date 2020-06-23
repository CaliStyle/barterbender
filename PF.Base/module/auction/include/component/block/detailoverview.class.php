<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Block_Detailoverview extends Phpfox_Component {

    public function process()
    {
        $aYnAuctionDetail = $this->getParam('aYnAuctionDetail');
        
        $aAuction = $aYnAuctionDetail['aAuction'];

        $aCategories = Phpfox::getService('ecommerce.category')->getForBrowseByAuctionId($aAuction['product_id']);

        $sTextCategories = "";
        foreach ($aCategories as $key_category => $aCategory)
        {
            $sTextCategories .= ' ' . $aCategory['title'];
            if (isset($aCategory['sub']) && count($aCategory['sub']))
            {
                foreach ($aCategory['sub'] as $key_sub_category => $aSubCategory)
                {
                    $sTextCategories .= ' >> ' . $aSubCategory['title'];
                }
            }
            $sTextCategories .= '|';
        }
        $sTextCategories = rtrim($sTextCategories, '|');

        $aVisitingHours = Phpfox::getService('auction.helper')->getVisitingHours();
        $aVisitingHoursDetail = array();
        foreach ($aVisitingHours['dayofweek'] as $key => $visit)
        {
            $aVisitingHoursDetail[$visit['id']] = $visit;
        }

        $aTodayOfWeek = array();
        $today_dayofweek = Phpfox::getTime('N', PHPFOX_TIME);
        
        $this->template()->assign(array(
            'sTextCategories' => $sTextCategories,
            'aAuction' => $aAuction,
            'aVisitingHoursDetail' => $aVisitingHoursDetail,
            'todayOfWeek' => $aTodayOfWeek,
            'todayOfWeek' => $aTodayOfWeek
                )
        );

        /* custom field */
        $iProductId = $aAuction['product_id'];
        $aMainCategory = Phpfox::getService('auction')->getAuctionMainCategory($iProductId);
        $aCustomFields = Phpfox::getService('auction')->getCustomFieldByCategoryId($aMainCategory['category_id']);

        $keyCustomField = array();
        $aCustomData = array();

        if ($iProductId)
        {
            $aCustomDataTemp = Phpfox::getService('ecommerce.custom')->getCustomFieldByProductId($iProductId);

            if (count($aCustomFields))
            {
                foreach ($aCustomFields as $aField)
                {
                    foreach ($aCustomDataTemp as $aFieldValue)
                    {
                        if ($aField['field_id'] == $aFieldValue['field_id'])
                        {
                            $aCustomData[$aFieldValue['group_phrase_var_name']][] = $aFieldValue;
                        }
                    }
                }
            }
        }
		
   
       if (count($aCustomData))
       {
           $aCustomFields = $aCustomData;
       }
	       /*
       echo "<pre>";
       print_r($aCustomFields);
       echo "</pre>";
       die;*/
       
		$isDisplayCustomField = false;
		foreach ($aCustomFields as $sGroupName => $aFields)
		{
			foreach ($aFields as $aField)
			{
				if (isset($aField['value']) && ($aField['value'] != ""))
				{
					$isDisplayCustomField = true;
				}
			}
		}
		
		if(!$isDisplayCustomField)
		{
			unset($aCustomFields);
			$aCustomFields = array();
		}
		
        $this->template()->assign(array(
            'aCustomFields' => $aCustomFields,
        ));
    }

}

?>
