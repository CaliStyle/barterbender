<?php

defined('PHPFOX') or exit('NO DICE!');

class Ecommerce_Component_Controller_Order_Detail extends Phpfox_Component {

    public function process()
    {
        $sModule = $this->request()->get('req1');

        $defaultImage = Phpfox::getParam('core.path') . 'module/ecommerce/static/image/default_ava.png';

        $this->template()
                ->setTitle(_p(''.$sModule))
                ->setBreadcrumb(_p(''.$sModule), $this->url()->makeUrl($sModule));
        
        $iViewerId = Phpfox::getUserId();
        $iOrderId  = $this->request()->getInt('req3');
        
        $aOrder = Phpfox::getService('ecommerce.order')->getOrder($iOrderId);
        if (!$aOrder)
        {
            return;
        }
        $bCanViewOrder = ($aOrder['buyer_user_id'] == $iViewerId || $aOrder['seller_user_id'] == $iViewerId);
        
        //check admin view
        if(!$bCanViewOrder) 
        {
            $bCanViewOrder = Phpfox::isAdmin();
        }
        
        if ($bCanViewOrder)
        {
            if (empty($aOrder['order_status']))
            {
                $aOrder['order_status'] = 'new';
            }
            
            $aOrder['status_title'] = _p('' . $aOrder['order_status']);
            $aLocation = array();
            if (!empty($aOrder['order_delivery_location_address']))
            {
                $aLocation[] = $aOrder['order_delivery_location_address'];
            }
            if (!empty($aOrder['order_delivery_location_address_2']))
            {
                $aLocation[] = $aOrder['order_delivery_location_address_2'];
            }
            if (!empty($aOrder['order_delivery_province']))
            {
                $aLocation[] = $aOrder['order_delivery_province'];
            }
            if (!empty($aOrder['order_delivery_city']))
            {
                $aLocation[] = $aOrder['order_delivery_city'];
            }
            if (!empty($aOrder['order_delivery_country_iso']))
            {
                $aLocation[] = Phpfox::getService('core.country')->getCountry($aOrder['order_delivery_country_iso']);
            }
            if ($aOrder['order_delivery_country_child_id'])
            {
                $aLocation[] = Phpfox::getService('core.country')->getChild($aOrder['order_delivery_country_child_id']);
            }
            $aOrder['sLocation'] = implode(', ', $aLocation);
            
            $aOrderDetails = Phpfox::getService('ecommerce.order')->getOrderDetails($iOrderId, $sModule);

            $fSubTotal = 0.0;
            foreach ($aOrderDetails as $aItem)
            {
                $fSubTotal += $aItem['orderproduct_product_price'] * $aItem['orderproduct_product_quantity'];
            }

            if ($aOrder['buyer_user_id'] == $iViewerId)
            {
                $this->template()
                    ->setBreadcrumb(_p('my_orders'), $this->url()->makeUrl($sModule.'.my-orders'));
            }
            elseif ($aOrder['seller_user_id'] == $iViewerId)
            {
                $this->template()->setBreadcrumb(_p('manage_orders'), $this->url()->makeUrl($sModule.'.manage-orders'));
            }

            $this->template()->setBreadCrumb($aOrder['order_code'], '', true);

            /*
             * Integrate with ynsocialstore and some module
             * 1. Has callback getItemBuyFromById to get buyfrom_item (Ex: getStore in ynsocialstore)
             * 2. Column order_buyfrom_type (Ex: store) mean item_type of item of that module to make a link to detail of this buyfrom_item (Ex: ynsocialstore.store)
             */
            if (!empty($aOrder['order_buyfrom_id']))
            {
                $callback = 'getItemBuyFromById';
                if (Phpfox::hasCallback($aOrder['module_id'], $callback))
                {
                    $aBuyFromItem = Phpfox::callback($aOrder['module_id'].'.'.$callback, $aOrder['order_buyfrom_id']);
                    $aOrder['order_buyfrom_name'] = $aBuyFromItem['name'];
                }
            }

            $aOrderStatus = array(
                'new' => _p('new'),
                'shipped' => _p('shipped'),
                'cancel' => _p('cancel')
            );

            $this->template()->assign(array('aOrderStatus' => $aOrderStatus, 'bCanViewOrder' => true,'fSubTotal'=> $fSubTotal, 'iViewerId' => $iViewerId, 'aOrder' => $aOrder, 'aOrderDetails' => $aOrderDetails));
        }
        else
        {
            $this->template()->assign(array('bCanViewOrder' => false));
        }
        $this->template()->assign([
           'isSocialStore' => $this->getParam('isSocialStore',false),
           'sDefaultImage' => $defaultImage
        ]);
    }

    /**
     * This function is used to add plugin. Do not delete.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('ynecommerce.component_controller_order_detail_clean')) ? eval($sPlugin) : false);
    }

}

?>