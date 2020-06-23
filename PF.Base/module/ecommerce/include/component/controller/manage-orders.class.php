<?php

defined('PHPFOX') or exit('NO DICE!');

class Ecommerce_Component_Controller_Manage_Orders extends Phpfox_Component
{

    public function process()
    {
        Phpfox::isUser(true);
        $sModule = $this->request()->get('req1');

        // Page Number & Limit Per Page
        $iPage = $this->request()->getInt('page');

        // Variables
        $aVals = array();
        $aConds = array();

        /*
        * Type of this manage order, such as:
        * + Sales of all stores belong to current users
        * + Sales of all products belong to current users
        */

        $aTypeManage = $this->getParam('aTypeManage');

        if (!empty($aTypeManage))
        {
            switch ($aTypeManage['sType']) {
                case 'salse-of-stores':
                    $aConds[] = 'AND eo.order_buyfrom_id = '.$aTypeManage['iStoreId'];
                    break;
                case 'product-sales':
                    $aConds[] = 'AND e.product_id ='.$aTypeManage['iProductId'];

            }
        }

        if ($sModule != 'ecommerce') {
            $aConds[] = "AND eo.module_id = '$sModule'";
        }

        // Search Filter
        $oSearch = Phpfox::getLib('search')->set(array(
            'type' => 'request',
            'search' => 'search',
        ));

        $aForms['product_title'] = $oSearch->get('product_title');
        $aForms['order_id'] = $oSearch->get('order_id');
        $aForms['order_status'] = $oSearch->get('order_status');
        $aForms['item_type'] = $oSearch->get('item_type');
        $aForms['submit'] = $oSearch->get('submit');

        if ($aForms['product_title']) {
            $aConds[] = 'AND eop.orderproduct_product_name LIKE "%' . Phpfox::getLib('parse.input')->clean($aForms['product_title']) . '%"';
        }

        if ($aForms['order_id']) {
            $aConds[] = 'AND eo.order_code LIKE "%' . Phpfox::getLib('parse.input')->clean($aForms['order_id']) . '%"';
        }

        if ($aForms['order_status']) {
            if ($aForms['order_status'] != 'all') {
                $aConds[] = 'AND eo.order_status = "' . $aForms['order_status'] . '"';
            }
        }


        if ($aForms['item_type'] && $aForms['item_type'] != 'all') {
            $aConds[] = 'AND e.product_creating_type = "' . Phpfox::getLib('parse.input')->clean($aForms['item_type']) . '"';
        }


        $formatDatePicker = str_split(Phpfox::getParam('core.date_field_order'));


        $aFormatIntial = array();
        foreach ($formatDatePicker as $key => $value) {

            if ($formatDatePicker[$key] != 'Y') {
                $formatIntial = strtolower($formatDatePicker[$key]);
            } else {
                $formatIntial = $formatDatePicker[$key];
            }

            $aFormatIntial[] = $formatIntial;

            $formatDatePicker[$key] .= $formatDatePicker[$key];
            $formatDatePicker[$key] = strtolower($formatDatePicker[$key]);
        }

        $sFormatDatePicker = implode("/", $formatDatePicker);


        $sSearchFromDate = $oSearch->get('fromdate');
        $sSearchToDate = $oSearch->get('todate');
        $sFormatIntial = implode("/", $aFormatIntial);
        $sFromDate = Phpfox::getTime($sFormatIntial, PHPFOX_TIME, false);
        $sToDate = Phpfox::getTime($sFormatIntial, PHPFOX_TIME + 24 * 60 * 60, false);

        if (!empty($sSearchFromDate) && !empty($sSearchToDate)) {


            $aSearchFromDate = explode("/", $sSearchFromDate);
            $aSearchToDate = explode("/", $sSearchToDate);


            $aFromDate = array();
            $aToDate = array();

            if (!empty($aFormatIntial)) {
                foreach ($aFormatIntial as $key => $aItem) {
                    $aFromDate[$aItem] = $aSearchFromDate[$key];
                    $aToDate[$aItem] = $aSearchToDate[$key];
                }
            }


            if (count($aFromDate) && count($aToDate)) {
                $iStartTime = Phpfox::getLib('date')->mktime(0, 0, 0, $aFromDate['m'], $aFromDate['d'], $aFromDate['Y']);
                $iEndTime = Phpfox::getLib('date')->mktime(23, 59, 59, $aToDate['m'], $aToDate['d'], $aToDate['Y']);
                if ($iStartTime > $iEndTime) {
                    $iTemp = $iStartTime;
                    $iStartTime = $iEndTime;
                    $iEndTime = $iTemp;
                }

                $aConds[] = 'AND eo.order_creation_datetime > ' . $iStartTime . ' AND eo.order_creation_datetime < ' . $iEndTime;
            }

            $sFromDate = $iStartTime ? Phpfox::getTime($sFormatIntial, $iStartTime, false) : Phpfox::getTime($sFormatIntial, PHPFOX_TIME, false);

            $sToDate = $iEndTime ? Phpfox::getTime($sFormatIntial, $iEndTime, false) : Phpfox::getTime($sFormatIntial, PHPFOX_TIME, false);

        }

        $aConds[] = 'AND eo.seller_id = ' . Phpfox::getUserId();

        /*sort table*/
        if ($this->request()->get('sortfield') != '') {
            $sSortField = $this->request()->get('sortfield');
            Phpfox::getLib('session')->set('ynecommerce_manageorders_sortfield', $sSortField);
        }
        $sSortField = Phpfox::getLib('session')->get('ynecommerce_manageorders_sortfield');
        if (empty($sSortField)) {
            $sSortField = ($this->request()->get('sortfield') != '') ? $this->request()->get('sortfield') : 'time';
            Phpfox::getLib('session')->set('ynecommerce_manageorders_sortfield', $sSortField);
        }


        if ($this->request()->get('sorttype') != '') {
            $sSortType = $this->request()->get('sorttype');
            Phpfox::getLib('session')->set('ynecommerce_manageorders_sorttype', $sSortType);
        }
        $sSortType = Phpfox::getLib('session')->get('ynecommerce_manageorders_sorttype');
        if (empty($sSortType)) {
            $sSortType = ($this->request()->get('sorttype') != '') ? $this->request()->get('sorttype') : 'DESC';
            Phpfox::getLib('session')->set('ynecommerce_manageorders_sorttype', $sSortType);
        }

        $sSortFieldDB = 'eo.order_id';
        switch ($sSortField) {
            case 'order_id':
                $sSortFieldDB = 'eo.order_id';
                break;
            case 'buyer':
                $sSortFieldDB = 'u.full_name';
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

        if ($this->search()->getPage() <= 1) {
            $_SESSION[Phpfox::getParam('core.session_prefix') . "ecommerce_manage_orders_search"] = $aConds;
            $_SESSION[Phpfox::getParam('core.session_prefix') . "ecommerce_manage_orders_sort"] = $sSort;
        }

        if ($this->search()->getPage() > 1)
        {
            $aConds = $_SESSION[Phpfox::getParam('core.session_prefix')."ecommerce_manage_orders_search"];
            $sSort = $_SESSION[Phpfox::getParam('core.session_prefix') . "ecommerce_manage_orders_sort"];
        }

        $iLimit = 10;

        list($iCnt, $aManageOrdersRows, $iTotalAmount, $iTotalCommission) = Phpfox::getService('ecommerce.order')->getOrders($aConds, $sSort, $oSearch->getPage(), $iLimit);

        foreach ($aManageOrdersRows as $iKey => $aRow) {
            $aManageOrdersRows[$iKey]['sStatusTitle'] = _p('' . $aRow['order_status']);
            $aManageOrdersRows[$iKey]['order_creation_datetime'] = Phpfox::getTime('d/m/Y', $aManageOrdersRows[$iKey]['order_creation_datetime']);
            $aManageOrdersRows[$iKey]['order_payment_status'] = _p('' . $aRow['order_payment_status']);

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
            $aManageOrdersRows[$iKey]['sLocation'] = implode(', ', $aLocation);

        }

        $sCustomBaseLink = Phpfox::getLib('url')->makeUrl($sModule . '.manage-orders');

        Phpfox::getLib('pager')->set(array(
                'page' => $iPage,
                'size' => $iLimit,
                'count' => $oSearch->getSearchTotal($iCnt),
            )
        );

        $this->template()
            ->setHeader('cache', array(
                'magnific-popup.css' => 'module_ecommerce',
                'jquery.magnific-popup.js' => 'module_ecommerce',
                'ynecommerce.js' => 'module_ecommerce'
            ))
            ->assign(array(
                'aManageOrdersRows' => $aManageOrdersRows,
                'sCustomBaseLink' => $sCustomBaseLink,
                'aForms' => $aForms,
                'iTotalAmount' => $iTotalAmount,
                'iTotalCommission' => $iTotalCommission,
                'sFromDate' => $sFromDate,
                'sToDate' => $sToDate,
                'sFormatDatePicker' => $sFormatDatePicker,
                'sModule' => $sModule,
                'iPage' => $iPage,
                'core_path' => Phpfox::getParam('core.path'),
            ));


        $this->template()->setTitle(_p('manage_orders'))
            ->setBreadcrumb(_p('' . $sModule), $this->url()->makeUrl($sModule));

        if ($sModule == 'ecommerce') {
            $this->template()->setBreadcrumb(_p('manage_orders'), $this->url()->makeUrl('ecommerce.manage-orders'));
            Phpfox::getService('ecommerce.helper')->buildMenu();
        }

    }

    /**
     * This function is used to add plugin. Do not delete.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('ynecommerce.component_controller_my_orders_clean')) ? eval($sPlugin) : false);
    }

}

?>