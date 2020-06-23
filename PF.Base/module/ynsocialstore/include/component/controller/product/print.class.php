<?php

/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 11/4/16
 * Time: 11:36
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Controller_Product_Print extends Phpfox_Component
{
    public function process()
    {
        if (!$this->request()->getInt('req4'))
        {
            exit(_p('invalid_param'));
        }

        $iProductId = $this->request()->getInt('req4');
        $aProduct = Phpfox::getService('ynsocialstore.product')->getProductForDetailById($iProductId);
        if(!$aProduct)
        {
            exit(_p('unable_to_find_the_product_you_are_looking_for'));
        } else {
            if (!Phpfox::getUserParam('ynsocialstore.can_view_product') || (Phpfox::getUserId() != $aProduct['user_id'] && in_array($aProduct['product_status'], array('denied', 'pending', 'closed')))) {
                exit(_p('you_do_not_have_permission_to_view_this_product'));
            }
        }
        if (Phpfox::isModule('privacy'))
        {
            Phpfox::getService('privacy')->check('ynsocialstore_product', $aProduct['product_id'], $aProduct['user_id'], $aProduct['privacy'], $aProduct['is_friend']);
        }
        if($aProduct['feature_start_time'] <= PHPFOX_TIME && $aProduct['feature_end_time'] >= PHPFOX_TIME) {
            $aProduct['is_featured'] = 1;
        } else {
            $aProduct['is_featured'] = 0;
        }
        $aProduct['remaining'] = $aProduct['product_quantity_main'] - $aProduct['total_orders'];
        $aDefaultCurrency = Phpfox::getService('core.currency')->getDefault();
        $sDefaultSymbol = Phpfox::getService('core.currency')->getSymbol($aDefaultCurrency);
        $this->setParam(array(
                            'aProduct' => $aProduct,
                            'iProductId' => $aProduct['product_id'],
                            'sDefaultSymbol' => $sDefaultSymbol,
                            'aDefaultCurrency' => $aDefaultCurrency,
                        ));
        $isNoAttribute = false;
        $aAttributeInfo = ['type' => $aProduct['attribute_style'],'name' => $aProduct['attribute_name']];
        $aElements = Phpfox::getService('ynsocialstore.product')->getAllElements($iProductId);
        $aPackage = Phpfox::getService('ynsocialstore.package')->getPackageByStoreId($aProduct['item_id']);
        if(!$aPackage ||  $aPackage['enable_attribute'] != 1 || $aProduct['product_type'] == 'digital')
        {
            $isNoAttribute = true;
        }
        $sDefaultSymbol = $this->getParam('sDefaultSymbol');
        if(empty($aElements)){
            $isNoAttribute = true;
        }
        Phpfox::getService('ynsocialstore.helper')->loadStoreJsCss();
        $aStaticFiles = $this->template()->getHeader(true);
        foreach($aStaticFiles as $key => $sFile)
        {
            if(!preg_match('/jscript\/jquery\/jquery.js/i',$sFile)){
                unset($aStaticFiles[$key]);
            }
        }
        $sJs = $this->template()->getHeader();
        $this -> template() -> assign(array(
                              'aItem' => $aProduct,
                              'iProductId' => $aProduct['product_id'],
                              'sDefaultSymbol' => $sDefaultSymbol,
                              'aDefaultCurrency' => $aDefaultCurrency,
                              'sCorePath' => Phpfox::getParam('core.path_file'),
                              'sJs' => $sJs,
                              'aFiles' => $aStaticFiles,
                              'aAttributeInfo' => $aAttributeInfo,
                              'aElements' => $aElements,
                              'isNoAttribute' => $isNoAttribute,
                                      ));
        Phpfox_Module::instance()->getControllerTemplate();
        die;
    }
}