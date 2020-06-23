<?php

defined('PHPFOX') or exit('NO DICE!');

class Ecommerce_Component_Block_Search extends Phpfox_Component {

    public function process()
    {
        $sKeyword = $this->request()->get('keyword');
        $iCategoryId = $this->request()->get('category');
        
        $sSort = $this->request()->get('sort');
        $sWhen = $this->request()->get('when');
        $sShow = $this->request()->get('show');

        $this->template()->assign(array(
            'sKeyword' => $sKeyword,
            'sSort' => $sSort,
            'sWhen' => $sWhen,
            'sShow' => $sShow
                )
        );

        return 'block';
    }

}

?>