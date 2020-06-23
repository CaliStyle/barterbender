<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/19/16
 * Time: 10:19 AM
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Controller_Store_Manage_Packages extends Phpfox_Component
{
    public function process()
    {
        Phpfox::isUser(true);
        $iStoreId = 0;
        $sError = "";
        if ($this->request()->getInt('id')) {
            $iStoreId = $this->request()->getInt('id');
            $this->setParam('iStoreId', $iStoreId);
        }

        if(!(int)$iStoreId){
            $this->url()->send('ynsocialstore');
        }

        $aStore = Phpfox::getService('ynsocialstore')->getStoreById($iStoreId);
        if(!$aStore) {
            $sError = _p('unable_to_find_the_store_you_are_looking_for');
        }

        // check permission
        if(!Phpfox::getService('ynsocialstore.permission')->canEditStore(false, $aStore['user_id'])){
            $sError = _p('you_do_not_have_permission_to_edit_this_store');
        }

        $aPackages = Phpfox::getService('ynsocialstore')->getAllPackages();
        $aPackageStore = Phpfox::getService('ynsocialstore.package')->getById($aStore['package_id']);
        $aDefaultCurrency = Phpfox::getService('core.currency')->getDefault();
        $sDefaultSymbol = Phpfox::getService('core.currency')->getSymbol($aDefaultCurrency);
        $review_and_confirm_purchase = false;

        foreach ($aPackages as $key => $aItem)
        {
            if ($aItem['expire_number'] < $aPackageStore['expire_number']
                || $aItem['fee'] < $aPackageStore['fee']
                || $aItem['theme_editable'] < $aPackageStore['theme_editable']
                || $aItem['max_products'] < $aPackageStore['max_products']
                || $aItem['enable_attribute'] < $aPackageStore['enable_attribute']) {
                $aPackages[$key]['is_different'] = true;
            } else {
                $aPackages[$key]['is_different'] = false;
            }
        }

        $this->template()
            ->setBreadcrumb(_p('social_store'),$this->url()->makeUrl('ynsocialstore'))
            ->setEditor()
            ->setPhrase(array(
                'ynsocialstore.warning',
                'ynsocialstore.warning_package',
                'ynsocialstore.continue',
                'ynsocialstore.cancel',
            ))
            ->setHeader('cache', array(
                'pager.css' => 'style_css',
                'jquery/plugin/jquery.highlightFade.js' => 'static_script',
                'switch_legend.js' => 'static_script',
                'switch_menu.js' => 'static_script',
                'quick_edit.js' => 'static_script',
                'progress.js' => 'static_script',
                'share.js' => 'module_attachment',
                'country.js' => 'module_core',
            ))
        ;

        $this->template()->assign(array(
            'aStore'  =>  $aStore,
            'aPackages'  =>  $aPackages,
            'iStoreId' => $iStoreId,
            'aPackageStore' => $aPackageStore,
            'sDefaultSymbol' => $sDefaultSymbol,
            'sNextUrl' => $this->url()->makeUrl('ynsocialstore.store.add'),
            'defaultCurrency' => $aDefaultCurrency
        ));
        if(isset($aPackageStore['package_id'])){
            $this->template()->assign(array(
                'sError' => $sError,
                'aPackageStore' => $aPackageStore,
            ));
        }
        if(!$review_and_confirm_purchase)
        {
            $this->template()->setBreadcrumb($aStore['name'], $this->url()->permalink('ynsocialstore.store', $iStoreId, $aStore['name']));
            $this->template()->setBreadcrumb(_p('manage_packages'), $this->url()->permalink('ynsocialstore.store.manage-packages','id_'.$aStore['package_id']));
        }

        Phpfox::getService('ynsocialstore.helper')->loadStoreJsCss();
    }
}