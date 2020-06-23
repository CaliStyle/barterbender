<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Block_Search extends Phpfox_Component {

    public function process()
    {
        $sKeyword = $this->request()->get('keyword');
        $iCategoryId = $this->request()->get('category');
        $sSort = $this->request()->get('sort');
        $sWhen = $this->request()->get('when');
        $sShow = $this->request()->get('show');
        $sCategories = Phpfox::getService('ecommerce.category')->display('searchblock')->get($iCategoryId);
        $sFullControllerName = Phpfox::getLib('module')->getFullControllerName();
        
        $this->template()->assign(array(
            'sKeyword' => $sKeyword,
            'sSort' => $sSort,
            'sWhen' => $sWhen,
            'sShow' => $sShow,
            'sCategories' => $sCategories,
            'sFullControllerName' => $sFullControllerName
                )
        );

        return 'block';
    }

}

?>