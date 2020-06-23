<?php

defined('PHPFOX') or exit('NO DICE!');

class Ecommerce_Component_Controller_Admincp_Transactions extends Phpfox_Component
{

    public function process()
    {
        Phpfox::isAdmin(true);
        $sModule = "admincp.ecommerce.transactions";
        $this->template()->setTitle(_p('manage_transactions'))->setBreadCrumb(_p("Apps"),
            $this->url()->makeUrl('admincp.apps'))->setBreadCrumb(_p('module_ecommerce'),
            $this->url()->makeUrl('admincp.app') . '?id=__module_ecommerce')->setBreadcrumb(_p('manage_transactions'),
            $this->url()->makeUrl($sModule));

        // Page Number & Limit Per Page
        $iPage = $this->request()->getInt('page');

        // Variables
        $aConds = array();
        $status = array("'pending'", "'completed'");
        $aStatus = join(',', $status);
        $aConds[] = "AND invoice.status IN ($aStatus)";

        // Search Filter
        $oSearch = Phpfox::getLib('search')->set(array(
            'type' => 'request',
            'search' => 'search',
        ));

        $aForms['item_name'] = $oSearch->get('item_name');
        $aForms['seller_name'] = $oSearch->get('seller_name');
        $aForms['invoice_status'] = $oSearch->get('invoice_status');
        $aForms['submit'] = $oSearch->get('submit');
        $aForms['reset'] = $this->request()->get('reset');
        $aForms['item_type'] = $oSearch->get('item_type');

        if ($aForms['reset']) {
            $this->url()->send('admincp.ecommerce.transactions');
        }

        if ($aForms['item_name']) {
            $aConds[] = 'AND name LIKE "%' . Phpfox::getLib('parse.input')->clean($aForms['item_name']) . '%"';
        }

        if ($aForms['seller_name']) {
            $aConds[] = 'AND seller.full_name LIKE "%' . Phpfox::getLib('parse.input')->clean($aForms['seller_name']) . '%"';
        }

        if ($aForms['invoice_status']) {
            if ($aForms['invoice_status'] != 'all') {
                $aConds[] = 'AND invoice.status = "' . $aForms['invoice_status'] . '"';
            }
        }

        if ($aForms['item_type']) {
            if ($aForms['item_type'] != 'all') {
                $aConds[] = 'AND item_type = "' . $aForms['item_type'] . '"';
            }
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

        $aConds[] = 'AND invoice.time_stamp_paid > ' . $iStartTime . ' AND invoice.time_stamp_paid < ' . $iEndTime;

        /*sort table*/
        if ($this->request()->get('sortfield') != '') {
            $sSortField = $this->request()->get('sortfield');
            Phpfox::getLib('session')->set('ynecommerce_transactions_sortfield', $sSortField);
        }
        $sSortField = Phpfox::getLib('session')->get('ynecommerce_transactions_sortfield');
        if (empty($sSortField)) {
            $sSortField = ($this->request()->get('sortfield') != '') ? $this->request()->get('sortfield') : 'time';
            Phpfox::getLib('session')->set('ynecommerce_transactions_sortfield', $sSortField);
        }

        if ($this->request()->get('sorttype') != '') {
            $sSortType = $this->request()->get('sorttype');
            Phpfox::getLib('session')->set('ynecommerce_transactions_sorttype', $sSortType);
        }
        $sSortType = Phpfox::getLib('session')->get('ynecommerce_transactions_sorttype');
        if (empty($sSortType)) {
            $sSortType = ($this->request()->get('sorttype') != '') ? $this->request()->get('sorttype') : 'asc';
            Phpfox::getLib('session')->set('ynecommerce_transactions_sorttype', $sSortType);
        }

        $sSortFieldDB = 'invoice.invoice_id';
        switch ($sSortField) {
            case 'transaction_id':
                $sSortFieldDB = 'invoice.invoice_id';
                break;
            case 'item_name':
                $sSortFieldDB = 'name';
                break;
            case 'item_type':
                $sSortFieldDB = 'invoice.item_type';
                break;
            case 'seller_name':
                $sSortFieldDB = 'user.full_name';
                break;
            case 'purchase_date':
                $sSortFieldDB = 'invoice.time_stamp_paid';
                break;
            case 'fee':
                $sSortFieldDB = 'invoice.price';
                break;
            default:
                break;
        }
        $aSort = array('field' => $sSortFieldDB, 'type' => $sSortType);
        $sSort = implode(" ", $aSort);

        $iLimit = 10;


        list($iCnt, $aTransactionRows) = Phpfox::getService('ecommerce.invoice')->getInvoices($aConds, $sSort,
            $oSearch->getPage(), $iLimit);

        $sCustomBaseLink = Phpfox::getLib('url')->makeUrl($sModule);
        Phpfox::getLib('pager')->set(array(
            'page' => $iPage,
            'size' => $iLimit,
            'count' => $oSearch->getSearchTotal($iCnt)
        ));

        $this->template()->setHeader('cache', array(
            'ynecommerce.js' => 'module_ecommerce'
        ))->assign(array(
            'aTransactionRows' => $aTransactionRows,
            'sCustomBaseLink' => $sCustomBaseLink,
            'aForms' => $aForms,
            'iTotalAmount' => $iCnt,
            'sModule' => $sModule,
            'sSortField' => $sSortField,
            'sSortType' => $sSortType
        ));


    }

    /**
     * This function is used to add plugin. Do not delete.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('ynecommerce.component_controller_admincp_transactions_clean')) ? eval($sPlugin) : false);
    }

}
