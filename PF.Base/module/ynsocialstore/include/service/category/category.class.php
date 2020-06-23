<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/20/16
 * Time: 5:37 PM
 */
class Ynsocialstore_Service_Category_Category extends Phpfox_Service
{
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('ecommerce_category');
    }

    public function getCategoriesHaveProduct($iParentId, $iActive, $iStoreId)
    {
        $aLists = $this->database()
            ->select('ecp.product_id')
            ->from(Phpfox::getT('ecommerce_product'), 'ecp')
            ->where('ecp.item_id = '.$iStoreId)
            ->group('ecp.product_id')
            ->execute('getSlaveRows');

        if (empty($aLists))
        {
            return array();
        }

        $sIn = '(';
        foreach ($aLists as $aList)
        {
            $sIn .= $aList['product_id'] .',';
        }

        $sIn = rtrim($sIn, ',') .')';

        $aCategories = $this->database()
            ->select('ec.*')
            ->from($this->_sTable, 'ec')
            ->join(Phpfox::getT('ecommerce_category_data'), 'ecd', 'ec.category_id = ecd.category_id')
            ->where('ec.parent_id = ' . (int) $iParentId . ' AND ec.is_active = ' . (int) $iActive . ' AND ecd.product_type = \'ynsocialstore_product\' AND ecd.product_id IN '.$sIn)
            ->order('ec.ordering ASC')
            ->group('ec.category_id')
            ->execute('getSlaveRows');

        if ($aCategories)
        {
            foreach ($aCategories as $iKey => $aCategory)
            {
                $aCategories[$iKey]['title'] = Phpfox::getLib('locale')->convert($aCategories[$iKey]['title']);
                $aCategories[$iKey]['sub_category'] = $this->getCategoriesHaveProduct($aCategory['category_id'], $iActive, $iStoreId);

                $aCategories[$iKey]['url_photo'] = Phpfox::getLib('image.helper')->display(array(
                        'server_id' => $aCategories[$iKey]['server_id'],
                        'file' => $aCategories[$iKey]['image_path'],
                        'path' => 'core.url_pic',
                        'suffix' => '_16',
                        'return_url' => true
                    )
                );
                if($aCategories[$iKey]['url_photo'] == '<span class="no_image_item i_size__16"><span></span></span>')
                {
                    $aCategories[$iKey]['url_photo'] = '';
                }
                $class_category_item = str_replace(' ', '_', strtolower($aCategories[$iKey]['title']));
                $aCategories[$iKey]['class_category_item'] = $class_category_item;
            }
        }

        return $aCategories;
    }
    public function getFirstParentId($iCategoryId)
    {
        $aCategories = $this->database()->select('ec.parent_id')
            ->from(Phpfox::getT('ecommerce_category'), 'ec')
            ->where('ec.category_id = ' . (int) $iCategoryId)
            ->execute('getRow');

        $sCategories = '';

        if($aCategories['parent_id'] == 0){
            return $iCategoryId;
        }
        else{
            return $this->getFirstParentId($aCategories['parent_id']);
        }
    }

    public function getCategoriesOfListProducts($sProductsIds)
    {
        $aRows = $this->database()->select('category_id')->from(Phpfox::getT('ecommerce_category_data'))->where('product_type = \'ynsocialstore_product\' AND is_main = 1 AND product_id IN ('.$sProductsIds.')')->execute('getRows');

        if(!count($aRows))
            return null;

        $aCategoriesIds = array();

        foreach ($aRows as $aItem)
        {
            $aCategoriesIds[] = array_shift($aItem);
        }

        return implode(',', $aCategoriesIds);
    }
}