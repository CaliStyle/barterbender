<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/13/16
 * Time: 8:52 AM
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Controller_Categories extends Phpfox_Component
{
    public function process()
    {
        Phpfox::getService('ynsocialstore.helper')->buildMenu();
        $this->template()->setBreadCrumb(_p('social_store'), $this->url()->makeUrl('ynsocialstore'));

        if ($this->request()->get('type') == 'store') {
            $sType = 'store';
            $this->template()->setBreadCrumb(_p('all_stores'), $this->url()->makeUrl('ynsocialstore.store'));
            $this->template()->setTitle(_p('all_stores'));
        } else {
            $sType = '';
            $this->template()->setTitle(_p('all_products'));
        }

        $aAlfabet = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
        if (($iStoreId = $this->request()->getInt('storeid'))) {
            $aStore = Phpfox::getService('ynsocialstore')->getStoreById($iStoreId);
            $this->template()->setBreadCrumb(_p('all_stores'), $this->url()->makeUrl('ynsocialstore.store'));
            $this->template()->setBreadCrumb($aStore['name'], $this->url()->permalink('ynsocialstore.store', $iStoreId, $aStore['name']));
            $aControllerCategories = Phpfox::getService('ynsocialstore.category')->getCategoriesHaveProduct(0, 1, $iStoreId);
        } else {
            $aControllerCategories = Phpfox::getService('ecommerce.category')->getAllCategories();
        }

        $this->template()->setBreadCrumb(_p('all_categories'));

        $iLimitNumberOfCategories = Phpfox::getParam('ynsocialstore.max_items_sub_categories_list_display', 10);

        $this->template()->assign(array(
            'aControllerCategories' => $aControllerCategories,
            'aAlfabet' => $aAlfabet,
            'iLimitNumberOfCategories' => $iLimitNumberOfCategories,
            'sType' => $sType
        ));
    }
}