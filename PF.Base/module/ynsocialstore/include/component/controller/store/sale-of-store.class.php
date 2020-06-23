<?php

/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 10/19/16
 * Time: 09:17
 */
class Ynsocialstore_Component_Controller_Store_Sale_Of_Store extends Phpfox_Component
{
    public function process()
    {
        $aEditStore = Phpfox::getService('ynsocialstore')->getStoreForEdit($this->request()->getInt('id'));
        $sError = $this->_canManageOrderOfStore($aEditStore);

        if (!empty($sError))
        {
            $this->template()->assign(array('sError' => $sError));
            Phpfox_Error::display($sError);
            return;
        }

        $this->template()
            ->setBreadCrumb(_p('social_store'), $this->url()->makeUrl('ynsocialstore'))
            ->setBreadCrumb(_p('seller_section'), $this->url()->makeUrl('ynsocialstore.statistic'))
            ->setBreadCrumb(_p('my_stores'), $this->url()->makeUrl('ynsocialstore.manage-stores'))
            ->setBreadCrumb($aEditStore['name'], $this->url()->permalink('ynsocialstore.store', $aEditStore['store_id'], $aEditStore['name']));

        Phpfox::getService('ynsocialstore.helper')->loadStoreJsCss();

        $this->setParam('aTypeManage', array(
                'sType' => 'salse-of-stores',
                'iStoreId' => $this->request()->getInt('id'),
            )
        );

        return Phpfox::getLib('module')->setController('ecommerce.manage-orders');
    }

    private function _canManageOrderOfStore($aEditStore)
    {
        if(!$aEditStore)
        {
            return _p('unable_to_find_the_store_you_are_looking_for');
        }

        if(!Phpfox::getService('ynsocialstore.permission')->canEditStore(false,$aEditStore['user_id']))
        {
            return _p('you_do_not_have_permission_to_manage_orders_this_store');
        }

        return null;
    }
}