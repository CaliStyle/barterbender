<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');


class Directory_Component_Controller_Admincp_transaction extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        // Page Number & Limit Per Page
        $iPage = $this->request()->getInt('page');
        $iPageSize = 10;

        // Variables
        $aVals = array();
        $aConds = array();
        $aConds[] = " AND inv.status IS NOT NULL ";

        // Search Filter
        $oSearch = Phpfox::getLib('search')->set(array(
            'type' => 'request',
            'search' => 'search',
        ));
        $aValsDate = $this->request()->get('val');
        if (isset($aValsDate)) {
            if (isset($aValsDate['from_month'])) {
                $aVals['fromdate'] = $aValsDate['from_month'] . '/' . $aValsDate['from_day'] . '/' . $aValsDate['from_year'];
            }
            if (isset($aValsDate['to_month'])) {
                $aVals['todate'] = $aValsDate['to_month'] . '/' . $aValsDate['to_day'] . '/' . $aValsDate['to_year'];
            }
        }
        $aVals['from_day'] = Phpfox::getTime('j');
        $aVals['from_month'] = Phpfox::getTime('n');
        $aVals['from_year'] = Phpfox::getTime('Y');
        $aVals['to_day'] = Phpfox::getTime('j');
        $aVals['to_month'] = Phpfox::getTime('n');
        $aVals['to_year'] = Phpfox::getTime('Y');
        $aVals['title'] = $oSearch->get('title');
        $aVals['type'] = $oSearch->get('type');
        $aVals['payment_status'] = $oSearch->get('payment_status');
        $aVals['sort_by'] = $oSearch->get('sort_by');
        if ($aVals['sort_by'] == '') {
            $aVals['sort_by'] = 'purchase_date';
        }
        $aVals['sort_by_vector'] = $oSearch->get('sort_by_vector');
        if ($aVals['sort_by_vector'] == '') {
            $aVals['sort_by_vector'] = 'descending';
        }
        $aVals['submit'] = $oSearch->get('submit');

        if ($aVals['title']) {
            $aConds[] = "AND dbus.name like '%{$aVals['title']}%'";
        }
        if ($aVals['type']) {
            $aConds[] = "AND inv.pay_type like '%{$aVals['type']}%'";
        }
        if ($aVals['fromdate']) {
            $iFromTime = strtotime($aVals['fromdate']);
            $aConds[] = "AND inv.time_stamp_paid >= {$iFromTime}";
        }
        if ($aVals['todate']) {
            $iToTime = strtotime($aVals['todate']) + 23 * 60 * 60 + 59 * 60 + 59;
            $aConds[] = "AND inv.time_stamp_paid <= {$iToTime}";
        }
        if ($aVals['payment_status']) {
            $aConds[] = "AND inv.status = '{$aVals['payment_status']}'";
        }
        $sOrder = 'inv.time_stamp_paid';
        switch ($aVals['sort_by']) {
            case 'purchase_date':
                $sOrder = 'inv.time_stamp_paid';
                break;
            case 'business':
                $sOrder = 'dbus.name';
                break;
        }
        $sVector = ' DESC';
        switch ($aVals['sort_by_vector']) {
            case 'descending':
                $sVector = ' DESC';
                break;
            case 'ascending':
                $sVector = ' ASC';
                break;
        }
        $sOrder .= $sVector;
        // Set pager
        $iCount = 0;
        list($iCount, $aTransactions) = Phpfox::getService('directory')->getTransactionBusiness($aConds, $iPage,
            $iPageSize, null, $sOrder);
        $aList = array();
        foreach ($aTransactions as $key => $value) {
            $invoice_data = is_null($value['invoice_data']) ? (array)json_decode($value['invoice_data']) : array();
            $param = is_null($value['param']) ? array() : (array)json_decode($value['param']);
            if (isset($param['ref'])) {
                $aCurrency = Phpfox::getService('directory.helper')->getCurrencyById($value['currency_id']);
                $description = '';
                switch (trim($value['pay_type'], '|')) {
                    case 'feature':
                        $description = _p('directory.feature');
                        break;
                    case 'package':
                        $description = _p('directory.buy_package') . ' "' . $value['package_name'] . '"';
                        break;
                    case 'package|feature':
                    case 'feature|package':
                        $description = _p('directory.feature') . ' ' . _p('directory.and') . ' ' . _p('directory.buy_package') . ' "' . $value['package_name'] . '"';
                        break;
                }
                if (trim($value['pay_type'], '|') == '') {

                }
                $aList[] = array(
                    'transaction_id' => $param['ref'],
                    'business_name' => $value['name'],
                    'purchase_date' => Phpfox::getService('directory.helper')->convertTime($value['time_stamp_paid']),
                    'fee' => (isset($aCurrency['currency_id']) ? $aCurrency['symbol'] : '') . $value['price'],
                    'payment_method' => ucfirst($value['payment_method']),
                    'payment_status' => ($value['status'] == null) ? '' : ucfirst($value['status']),
                    'description' => $description,
                );
            }
        }

        phpFox::getLib('pager')->set(array(
            'page' => $iPage,
            'size' => $iPageSize,
            'count' => $iCount
        ));

        $this->template()->setTitle(_p('directory.manage_transactions'));

        $this->template()
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('controller_directory'), $this->url()->makeUrl('admincp.app') . '?id=__module_directory')
            ->setBreadcrumb(_p('directory.manage_transactions'),
                $this->url()->makeUrl('admincp.directory.transaction'));
        $this->template()->assign(array(
            'aTransactions' => $aList,
            'aForms' => $aVals
        ));
    }

}

?>