<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/31/16
 * Time: 9:07 AM
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Controller_Manage_Attributes extends Phpfox_Component
{
    public function process()
    {
        Phpfox::getService('ynsocialstore.helper')->buildMenu();

        if ($this->request()->getInt('id')) {
            $iProductId = $this->request()->getInt('id');
            if (!(int)$iProductId)
            {
                $this->url()->send('ynsocialstore');
            }

            $aProduct = Phpfox::getService('ynsocialstore.product')->getProductForManageAttr($iProductId);
            $this->template()->buildPageMenu('js_ynsocialstore_products_block', [], [
                'link' => Phpfox::permalink('social-store.product', $aProduct['product_id'], null),
                'phrase' => _p('ynsocialstore_view_product_detail')
            ]);
        }
        $sError = '';
        if(empty($aProduct))
        {
            $sError = _p('unable_to_find_the_product_you_are_looking_for');
            $this->setParam('sError',$sError);
            $this->template()->setBreadCrumb(_p('social_store'), $this->url()->makeUrl('ynsocialstore'));
            $this->template()->assign(['sError' => $sError]);
            return false;
        }

        if (empty($sError) && !Phpfox::getService('ynsocialstore.permission')->canEditProduct(false, $aProduct['user_id'])) {
            $sError = _p('you_do_not_have_permission_to_edit_this_product');
        }
        if(empty($sError) && $aProduct['product_type'] == 'digital')
        {
            $sError = _p('Digital product does not support attributes');
        }

        $this->template()->setBreadCrumb(_p('social_store'), $this->url()->makeUrl('ynsocialstore'))
            ->setBreadCrumb(_p('seller_section'), $this->url()->makeUrl('ynsocialstore.statistic'))
            ->setBreadCrumb(_p('my_stores'), $this->url()->makeUrl('ynsocialstore.manage-store'))
            ->setBreadcrumb($aProduct['store_name'], $this->url()->permalink('ynsocialstore.store', $aProduct['store_id'], $aProduct['store_name']));

        $aPackage = Phpfox::getService('ynsocialstore.package')->getPackageByStoreId($aProduct['store_id']);
        if ($aPackage['enable_attribute']) {
            $this->setParam('iProductId', $aProduct['product_id']);

            if (($aVals = $this->request()->getArray('val')))
            {
                $aVals['image'] = $this->request()->get('image');
                Phpfox::getService('ynsocialstore.product')->addElementAttribute($aVals);
            }

            if (($aVals = $this->request()->getArray('valAttr')))
            {
                if ($iProductId == $aVals['product_id']) {
                    Phpfox::getService('ynsocialstore.product')->addAttribute($aVals);
                } else {
                    $this->template()->assign(array(
                        'aForms' => $aVals,
                    ));
                }
            }
            /*
             * 0 mean unlimited
             */
            if (!$aProduct['enable_inventory'] || !$aProduct['product_quantity_main'])
            {
                $iAvailable = 0;
            }
            else
            {
                $iTotal = (int)Phpfox::getService('ynsocialstore.product')->getSumOfTotalAmountQuantityAttributes($iProductId, 0);
                $iAvailable = ($aProduct['product_quantity_main'] - $iTotal) > 0 ? ($aProduct['product_quantity_main'] - $iTotal) : -1;
            }

            $aAttributeElements = Phpfox::getService('ynsocialstore.product')->getAllElements($iProductId);

            $this->template()->assign(array(
                    'iEditId' => $iProductId,
                    'aProduct' => $aProduct,
                    'aForms' => array(
                        'style' => $aProduct['attribute_style'],
                        'title' => $aProduct['attribute_name'],
                    ),
                    'iAvailable' => $iAvailable,
                    'aAttributeElements' => $aAttributeElements,
                    'sError' => $sError,
                )
            );

            $this->template()->setBreadcrumb(_p('manage_attributes'), $this->url()->permalink('ynsocialstore.manage-attributes', 'id_' . $aProduct['product_id']));
        } else {
            $sError = _p('ynsocialstore_warning_when_not_supported_atrribute',['store_name' => $aProduct['store_name']]);
            $this->template()->assign(array(
                    'sError' => $sError
                )
            );
        }

        Phpfox::getService('ynsocialstore.helper')->loadStoreJsCss();
    }
}