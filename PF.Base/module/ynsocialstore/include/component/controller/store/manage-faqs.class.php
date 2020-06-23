<?php

/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 10/18/16
 * Time: 21:27
 */
class Ynsocialstore_Component_Controller_Store_Manage_Faqs extends Phpfox_Component
{

    public function process()
    {
        Phpfox::isUser(true);
        $sError = '';

        $iEditId = $this->request()->getInt('id');

        if(!(int)$iEditId){
            $this->url()->send('ynsocialstore');
        }
        $aEditStore = Phpfox::getService('ynsocialstore')->getStoreById($iEditId);

        if(!$aEditStore)
        {
            $sError = _p('unable_to_find_the_store_you_are_looking_for');
        }
        if(!Phpfox::getService('ynsocialstore.permission')->canEditStore(false,$aEditStore['user_id']))
        {
            $sError = _p('you_do_not_have_permission_to_edit_this_store');
        }
        $aFAQs = Phpfox::getService('ynsocialstore')->getFAQsByStoreId($iEditId);
        //TODO show hide FAQs
        $this->template()
            ->setTitle(_p('manage_faqs'))
            ->setBreadcrumb(_p('social_store'), $this->url()->makeUrl('ynsocialstore'))
            ->setBreadcrumb($aEditStore['name'], $this->url()->makeUrl('ynsocialstore.store',$aEditStore['store_id']))
            ->setBreadcrumb(_p('manage_faqs'), $this->url()->makeUrl('ynsocialstore.store.manage-faqs','id_'.$aEditStore['store_id']),true)
            ->assign([
                         'sError' => $sError,
                         'iEditId' => $iEditId,
                         'aStore' => $aEditStore,
                         'aFAQs'  => $aFAQs
                     ]);
        Phpfox::getService('ynsocialstore.helper')->loadStoreJsCss();
    }

    function __call($name, $arguments)
    {

    }
}