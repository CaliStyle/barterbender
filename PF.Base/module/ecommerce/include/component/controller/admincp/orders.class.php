<?php

defined('PHPFOX') or exit('NO DICE!');

class Ecommerce_Component_Controller_Admincp_Orders extends Phpfox_Component
{

    public function process()
    {
        Phpfox::isAdmin(true);
        $sModule = "admincp.ecommerce.orders";
        $this->template()->setTitle(_p('manage_orders'))->setBreadCrumb(_p("Apps"),
                $this->url()->makeUrl('admincp.apps'))->setBreadCrumb(_p('module_ecommerce'),
                $this->url()->makeUrl('admincp.app') . '?id=__module_ecommerce')->setBreadcrumb(_p('manage_orders'),
                $this->url()->makeUrl($sModule));

        // Page Number & Limit Per Page
        $iPage = $this->request()->getInt('page');

        // Variables
        $aConds = array();

        // Search Filter
        $oSearch = Phpfox::getLib('search')->set(array(
            'type' => 'request',
            'search' => 'search',
        ));

        $aForms['product_title'] = $oSearch->get('product_title');
        $aForms['seller_name'] = $oSearch->get('seller_name');
        $aForms['order_id'] = $oSearch->get('order_id');
        $aForms['order_status'] = $oSearch->get('order_status');
        $aForms['payment_status'] = $oSearch->get('payment_status');
        $aForms['item_type'] = $oSearch->get('item_type');
        $aForms['submit'] = $oSearch->get('submit');
        $aForms['reset'] = $oSearch->get('reset');

        if ($aForms['reset']) {
            $this->url()->send('admincp.ecommerce.orders');
        }

        if ($aForms['product_title']) {
            $aConds[] = 'AND eop.orderproduct_product_name LIKE "%' . Phpfox::getLib('parse.input')->clean($aForms['product_title']) . '%"';
        }

        if ($aForms['seller_name']) {
            $aConds[] = 'AND s.full_name LIKE "%' . Phpfox::getLib('parse.input')->clean($aForms['seller_name']) . '%"';
        }

        if ($aForms['order_id']) {
            $aConds[] = 'AND eo.order_code LIKE "%' . Phpfox::getLib('parse.input')->clean($aForms['order_id']) . '%"';
        }

        if ($aForms['order_status']) {
            $aConds[] = 'AND eo.order_status = "' . $aForms['order_status'] . '"';
        }

        if ($aForms['payment_status']) {
            $aConds[] = 'AND eo.order_payment_status = "' . $aForms['payment_status'] . '"';
        }

        if ($aForms['item_type'] && $aForms['item_type'] != 'all') {
            $aConds[] = 'AND e.product_creating_type = "' . Phpfox::getLib('parse.input')->clean($aForms['item_type']) . '"';
        }

        $aVals = $this->request()->getArray('val');
        $aForms = array_merge($aForms, $aVals);
        $iDay = 30;
        $sDefaultDateTo = PHPFOX_TIME;
        $sDefaultDateFrom = $sDefaultDateTo - ($iDay * 86400);
        if (empty($aForms['from_month']) && empty($aForms['valid_to_month'])) {
            $aForms['from_day'] = Phpfox::getTime('j', $sDefaultDateFrom);
            $aForms['from_month'] = Phpfox::getTime('n', $sDefaultDateFrom);
            $aForms['from_year'] = Phpfox::getTime('Y', $sDefaultDateFrom);

            $aForms['to_day'] = Phpfox::getTime('j', $sDefaultDateTo);
            $aForms['to_month'] = Phpfox::getTime('n', $sDefaultDateTo);
            $aForms['to_year'] = Phpfox::getTime('Y', $sDefaultDateTo);
        }
        $iStartTime = Phpfox::getLib('date')->mktime(0, 0, 0, $aForms['from_month'], $aForms['from_day'], $aForms['from_year']);
        $iEndTime = Phpfox::getLib('date')->mktime(23, 23, 59, $aForms['to_month'], $aForms['to_day'], $aForms['to_year']);

        $aConds[] = 'AND eo.order_creation_datetime > ' . $iStartTime . ' AND eo.order_creation_datetime < ' . $iEndTime;

        $sDisableOrderModuleIds = Phpfox::getService('ecommerce.order')->getDisableOrdersModuleIds();

        if (!empty($sDisableOrderModuleIds)) {
            $aConds[] = 'AND eo.module_id NOT IN (' . $sDisableOrderModuleIds . ')';
        }

        /*sort table*/
        if ($this->request()->get('sortfield') != '') {
            $sSortField = $this->request()->get('sortfield');
            Phpfox::getLib('session')->set('ynecommerce_orders_sortfield', $sSortField);
        }
        $sSortField = Phpfox::getLib('session')->get('ynecommerce_orders_sortfield');
        if (empty($sSortField)) {
            $sSortField = ($this->request()->get('sortfield') != '') ? $this->request()->get('sortfield') : 'time';
            Phpfox::getLib('session')->set('ynecommerce_orders_sortfield', $sSortField);
        }

        if ($this->request()->get('sorttype') != '') {
            $sSortType = $this->request()->get('sorttype');
            Phpfox::getLib('session')->set('ynecommerce_orders_sorttype', $sSortType);
        }
        $sSortType = Phpfox::getLib('session')->get('ynecommerce_orders_sorttype');
        if (empty($sSortType)) {
            $sSortType = ($this->request()->get('sorttype') != '') ? $this->request()->get('sorttype') : 'DESC';
            Phpfox::getLib('session')->set('ynecommerce_orders_sorttype', $sSortType);
        }

        $sSortFieldDB = 'eo.order_id';
        switch ($sSortField) {
            case 'order_id':
                $sSortFieldDB = 'eo.order_code';
                break;
            case 'order_buyer':
                $sSortFieldDB = 'u.full_name';
                break;
            case 'order_seller':
                $sSortFieldDB = 's.full_name';
                break;
            case 'order_date':
                $sSortFieldDB = 'eo.order_creation_datetime';
                break;
            case 'order_total':
                $sSortFieldDB = 'eo.order_total_price';
                break;
            case 'order_commission':
                $sSortFieldDB = 'eo.order_commission_value';
                break;
            default:
                break;
        }
        $aSort = array('field' => $sSortFieldDB, 'type' => $sSortType);
        $sSort = implode(" ", $aSort);

        $iLimit = 10;


        list($iCnt, $aOrderRows) = Phpfox::getService('ecommerce.order')->getOrders($aConds, $sSort,
            $oSearch->getPage(), $iLimit);

        $iTotalAmount = 0;
        $iTotalCommission = 0;
        foreach ($aOrderRows as $iKey => $aRow) {
            $iTotalAmount += $aRow['order_total_price'];
            $iTotalCommission += $aRow['order_commission_value'];
            $aOrderRows[$iKey]['sStatusTitle'] = _p('' . $aRow['order_status']);
            $aOrderRows[$iKey]['order_creation_datetime'] = Phpfox::getTime('d/m/Y',
                $aOrderRows[$iKey]['order_creation_datetime']);
            $aOrderRows[$iKey]['order_payment_status'] = _p('' . $aRow['order_payment_status']);

            $aLocation = array();
            if (!empty($aRow['order_delivery_location_address'])) {
                $aLocation[] = $aRow['order_delivery_location_address'];
            }

            $aLocation = array();
            if (!empty($aRow['order_delivery_location_address_2'])) {
                $aLocation[] = $aRow['order_delivery_location_address_2'];
            }

            if (!empty($aRow['order_delivery_province'])) {
                $aLocation[] = $aRow['order_delivery_province'];
            }
            if (!empty($aRow['order_delivery_city'])) {
                $aLocation[] = $aRow['order_delivery_city'];
            }
            if (!empty($aRow['order_delivery_country_iso'])) {
                $aLocation[] = Phpfox::getService('core.country')->getCountry($aRow['order_delivery_country_iso']);
            }
            if ($aRow['order_delivery_country_child_id']) {
                $aLocation[] = Phpfox::getService('core.country')->getChild($aRow['order_delivery_country_child_id']);
            }
            $aOrderRows[$iKey]['sLocation'] = implode(', ', $aLocation);

        }
        $sCustomBaseLink = Phpfox::getLib('url')->makeUrl($sModule);
        Phpfox::getLib('pager')->set(array(
            'page' => $iPage,
            'size' => $iLimit,
            'count' => $oSearch->getSearchTotal($iCnt)
        ));

        $this->template()->setHeader('cache', array(
                'ynecommerce.js' => 'module_ecommerce'
            ))->assign(array(
                'aOrderRows' => $aOrderRows,
                'sCustomBaseLink' => $sCustomBaseLink,
                'aForms' => $aForms,
                'iTotalAmount' => $iTotalAmount,
                'iTotalCommission' => $iTotalCommission,
                'sModule' => $sModule,
                'sSortType' => $sSortType,
                'sSortField' => $sSortField
            ));


    }

    /**
     * This function is used to add plugin. Do not delete.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('ynecommerce.component_controller_admincp_orders_clean')) ? eval($sPlugin) : false);
    }

}
