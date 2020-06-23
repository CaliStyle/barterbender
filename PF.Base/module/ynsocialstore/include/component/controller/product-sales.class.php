<?php

/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/31/16
 * Time: 9:08 AM
 */
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Controller_Product_Sales extends Phpfox_Component
{
    public function process()
    {

        Phpfox::isUser(true);
        Phpfox::getService('ynsocialstore.helper')->loadStoreJsCss();
        Phpfox::getService('ynsocialstore.helper')->buildMenu();
        $this->template()->setBreadCrumb(_p('social_store'), $this->url()->makeUrl('ynsocialstore'));

        if ($this->request()->getInt('id')) {
            $iProductId = $this->request()->getInt('id');
            if (!(int)$iProductId) {
                $this->url()->send('ynsocialstore');
            }
            $aProduct = Phpfox::getService('ynsocialstore.product')->getProductForManageAttr($iProductId);
            $this->template()->buildPageMenu('js_ynsocialstore_products_block', [], [
                'link' => Phpfox::permalink('social-store.product', $aProduct['product_id'], null),
                'phrase' => _p('ynsocialstore_view_product_detail')
            ]);
        }
        $sError = '';

        if(!$aProduct)
        {
            $sError = _p('unable_to_find_the_product_you_are_looking_for');
        }

        // Check if user has permission edit their own products
        if (empty($sError) && !Phpfox::getService('ynsocialstore.permission')->canEditProduct(false, $aProduct['user_id'])) {
            $sError = _p('you_do_not_have_permission_to_edit_this_product');
        }

        if(!empty($sError))
        {
            $this->setParam('sError',$sError);
            $this->template()->assign(array(
                                          'sError' => $sError,
                                      ));
            return false;
        }
        $sModule = $this->request()->get('req1');

        // Page Number & Limit Per Page
        $iPage = $this->request()->getInt('page');

        // Variables
        $aVals = array();
        $aConds = array();
        $aConds[] = 'AND e.product_id ='.$iProductId;
        $aConds[] = "AND eo.module_id = '$sModule'";

        // Search Filter
        $oSearch = Phpfox::getLib('search')->set(array(
                                                     'type' => 'request',
                                                     'search' => 'search',
                                                 ));

        $aForms['buyer'] = $oSearch->get('buyer');
        $aForms['order_id'] = $oSearch->get('order_id');
        $aForms['order_status'] = $oSearch->get('order_status');
        $aForms['payment_status'] = $oSearch->get('payment_status');
        $aForms['submit'] = $oSearch->get('submit');
        if ($aForms['buyer']) {
            $aConds[] = 'AND u.full_name LIKE "%' . Phpfox::getLib('parse.input')->clean($aForms['buyer']) . '%"';
        }
        if ($aForms['order_id']) {
            $aConds[] = 'AND eo.order_code LIKE "%' . Phpfox::getLib('parse.input')->clean($aForms['order_id']) . '%"';

        }

        if ($aForms['order_status']) {
            if ($aForms['order_status'] != 'all') {
                $aConds[] = 'AND eo.order_status = "' . $aForms['order_status'] . '"';
            }
        }


        if ($aForms['payment_status'] && $aForms['payment_status'] != 'all') {
            $aConds[] = 'AND eo.order_payment_status = "' . Phpfox::getLib('parse.input')->clean($aForms['payment_status']) . '"';
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

        if ($this->search()->getPage() <= 1) {
            $_SESSION[Phpfox::getParam('core.session_prefix') . "ynsocialstore_product_sales_search"] = $aConds;
        }

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
            $sSortType = ($this->request()->get('sorttype') != '') ? $this->request()->get('sorttype') : 'asc';
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
            case 'order_quantity':
                $sSortFieldDB = 'eop.orderproduct_product_quantity';
                break;
            default:
                break;
        }
        $aSort = array('field' => $sSortFieldDB, 'type' => $sSortType);
        $sSort = implode(" ", $aSort);

        $iLimit = 10;
        list($iCnt, $aManageOrdersRows, $iTotalAmount, $iTotalQuantity) = Phpfox::getService('ecommerce.order')->getOrders($aConds, $sSort, $oSearch->getPage(), $iLimit,'product-sales');

        foreach ($aManageOrdersRows as $iKey => $aRow) {
            $aManageOrdersRows[$iKey]['sStatusTitle'] = _p('ecommerce.' . $aRow['order_status']);
            $aManageOrdersRows[$iKey]['order_creation_datetime'] = Phpfox::getTime('d/m/Y', $aManageOrdersRows[$iKey]['order_creation_datetime']);
            $aManageOrdersRows[$iKey]['order_payment_status'] = _p('ecommerce.' . $aRow['order_payment_status']);

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

        $sCustomBaseLink = Phpfox::getLib('url')->makeUrl('ynsocialstore.product-sales').'id_'.$iProductId.'/';

        Phpfox::getLib('pager')->set(array(
                                         'page' => $iPage,
                                         'size' => $iLimit,
                                         'count' => $oSearch->getSearchTotal($iCnt),
                                     )
        );

        $this->setParam('is_seller', false);

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
                         'iTotalQuantity' => $iTotalQuantity,
                         'sFromDate' => $sFromDate,
                         'sToDate' => $sToDate,
                         'sFormatDatePicker' => $sFormatDatePicker,
                         'sModule' => $sModule,
                         'iPage' => $iPage,
                         'sCorePath' => Phpfox::getParam('core.path_file'),
                         'iProductId' => $iProductId
                     ));


        $this->template()->setTitle(_p('Product Sales'))
            ->setBreadcrumb(_p('ecommerce.' . $sModule), $this->url()->makeUrl($sModule));

        if ($sModule == 'ecommerce') {
            $this->template()->setBreadcrumb(_p('ecommerce.manage_orders'), $this->url()->makeUrl('ecommerce.manage-orders'));
            Phpfox::getService('ecommerce.helper')->buildMenu();
        }

    }
}