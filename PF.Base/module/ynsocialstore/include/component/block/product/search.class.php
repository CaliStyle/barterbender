<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/12/16
 * Time: 2:21 PM
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Block_Product_Search extends Phpfox_Component
{
    public function process()
    {
        $bIsAdvSearch = $this->request()->get('flag_advancedsearchproduct');
        $aSearch = $this->getParam('aSearch');
        $aCategories = Phpfox::getService('ecommerce.category')->getAllCategories();

        $this->template()->assign(array(
            'bIsAdvSearch' => $bIsAdvSearch,
            'sFormUrl' => $this->search()->getFormUrl().'/bIsAdvSearch_true',
            'aCategories' => $aCategories,
            'bIsSearchByCategory' => ($this->request()->get('req2') == 'category' && $this->request()->getInt('req3') > 0),
            'aSearch' => $aSearch,
        ));
    }
}