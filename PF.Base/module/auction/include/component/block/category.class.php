<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Block_Category extends Phpfox_Component {

    public function process()
    {
        $aCategories = Phpfox::getService('ecommerce.category')->getAllCategories();
        
        if (!is_array($aCategories) && $aCategories)
        {
            return false;
        }
		
		//check limit
		$iCategoryLimit = Phpfox::getParam('auction.category_number');
		if(!isset($iCategoryLimit)) {
			$iCategoryLimit = 10;
		}
		$aCategories = array_slice($aCategories, 0, $iCategoryLimit); 
				
        $sReq2 = $this->request()->get('req2');
        $sReq3 = $this->request()->getInt('req3');
        
        $iCurrentCategoryId = 0;
        if ($sReq2 == 'category' && $sReq3 > 0)
        {
            $iCurrentCategoryId = $sReq3;
        }
        
        $iLimit = Phpfox::getParam('auction.max_items_sub_categories_list_display');
        if ($iLimit > 0)
        {
            foreach ($aCategories as $iKey => $aCategory)
            {
                foreach ($aCategories[$iKey]['sub_category'] as $iSubKey => $aSubCategory)
                {
                    if (count($aCategories[$iKey]['sub_category'][$iSubKey]['sub_category']) > $iLimit)
                    {
                        $aCategories[$iKey]['sub_category'][$iSubKey]['sub_category'] = array_slice($aCategories[$iKey]['sub_category'][$iSubKey]['sub_category'], 0, $iLimit);
                        $aCategories[$iKey]['sub_category'][$iSubKey]['show_view_more'] = true;
                    }
                }
            }
        }
        
        $this->template()->assign(array(
            'aCategories' => $aCategories,
            'iCurrentCategoryId' => $iCurrentCategoryId,
            'sHeader' => _p('categories'),
                )
        );

        return 'block';
    }

}

?>