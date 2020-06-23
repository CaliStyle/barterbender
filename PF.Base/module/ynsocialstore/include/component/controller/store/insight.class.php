<?php

/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 10/13/16
 * Time: 22:17
 */
defined('PHPFOX') or exit('NO DICE!');

class Ynsocialstore_Component_Controller_Store_Insight extends Phpfox_Component
{
    public function process()
    {
        Phpfox::isUser(true);
        $sError = "";
        if ($iEditId = $this->request()->getInt('id')) {
            $this->setParam('iStoreId',$iEditId);
        }
        if(!(int)$iEditId){
            $this->url()->send('ynsocialstore');
        }
        $aEditStore = Phpfox::getService('ynsocialstore')->getStoreById($iEditId);
        $aPackageStore = [];

        if(!$aEditStore)
        {
            $sError = _p('unable_to_find_the_store_you_are_looking_for');
        }
        if(!Phpfox::getService('ynsocialstore.permission')->canEditStore(false,$aEditStore['user_id']))
        {
            $sError = _p('you_do_not_have_permission_to_edit_this_store');
        }
        if(empty($sError))
        {
            $aPackageStore = Phpfox::getService('ynsocialstore.package')->getById($aEditStore['package_id']);
        }

        Phpfox::getService('ynsocialstore')->getMoreInfomationForStoreInsight($aEditStore);
        
        $this->template()
            ->setTitle(_p('insight'))
            ->setBreadcrumb(_p('social_store'), $this->url()->makeUrl('ynsocialstore'))
            ->setBreadcrumb($aEditStore['name'], $this->url()->makeUrl('ynsocialstore.store',$aEditStore['store_id']))
            ->setBreadcrumb(_p('insight'), $this->url()->makeUrl('ynsocialstore.store.insight','id_'.$aEditStore['store_id']),true)
            ->assign([
                                   'sError' => $sError,
                                   'iStoreId' => $iEditId,
                                   'aStore' => $aEditStore,
                                   'aPackageStore' => $aPackageStore,
                                  ])
            ->setHeader('cache', array(
                'jquery.flot.js' => 'module_ecommerce',
                'jquery.flot.time.js' => 'module_ecommerce',
                'jquery.flot.stack.js' => 'module_ecommerce'
            ))->setPhrase([
                                      'ecommerce.publish_fee',
                                      'ecommerce.featured_fee',
                                      'ecommerce.commission_fee',
                                      'ecommerce.number_of_products_sold'
                                  ]);

        Phpfox::getService('ynsocialstore.helper')->loadStoreJsCss();
    }
}
