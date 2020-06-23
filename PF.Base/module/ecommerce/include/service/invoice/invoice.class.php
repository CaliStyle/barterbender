<?php

defined('PHPFOX') or exit('NO DICE!');

class Ecommerce_Service_Invoice_Invoice extends Phpfox_Service {

    public function getTotalPriceForChart($iFromTimestamp, $iToTimestamp, $sPayType ,$sType,$iStoreId = 0)
    {
        if (empty($sPayType) || !in_array($sPayType, array('publish', 'feature')))
        {
            return array();
        }
        
        $aConds = array(
            'AND ei.user_id = ' . Phpfox::getUserId(),
            'AND ei.status = "completed"',
            'AND ei.time_stamp_paid > ' . (int) $iFromTimestamp,
            'AND ei.time_stamp_paid < ' . ((int) $iToTimestamp + 86400)
        );
        if($iStoreId){
            $aConds[] = 'AND ep.item_id ='. $iStoreId;
        }
        if($sType != 'ecommerce'){
            $aConds[] = 'AND ep.product_creating_type = "'.$sType.'"';
        }
        if ($sPayType == 'publish')
        {
            $aConds[] = 'AND ei.pay_type = "publish"';
        }
        else
        {
            $aConds[] = 'AND ei.pay_type = "feature"';
        }
        
        $aRows = $this->database()
                ->select('MONTH(FROM_UNIXTIME(ei.time_stamp_paid)) AS current_month, YEAR(FROM_UNIXTIME(ei.time_stamp_paid)) AS current_year, SUM(ei.price) as total_value')
                ->from(Phpfox::getT('ecommerce_invoice'), 'ei')
                ->join(Phpfox::getT('ecommerce_product'), 'ep','ep.product_id = ei.item_id')
                ->where($aConds)
                ->group('MONTH(FROM_UNIXTIME(ei.time_stamp_paid)), YEAR(FROM_UNIXTIME(ei.time_stamp_paid))')
                ->order('YEAR(FROM_UNIXTIME(ei.time_stamp_paid)), MONTH(FROM_UNIXTIME(ei.time_stamp_paid))')
                ->execute('getRows');

        $aTempChartData = array();
        foreach ($aRows as $iKey => $aItem)
        {
            $aTempChartData[$aItem['current_year']][$aItem['current_month']] = $aItem['total_value'];
        }
        
        return $aTempChartData;
    }
    
    public function getInvoices($aConds, $sSort = 'invoice.invoice_id DESC', $iPage = '', $iLimit = '')
    {
        if (Phpfox::isModule('ynsocialstore')) {
            $this->database()
                ->select('invoice.invoice_id, invoice.user_id')
                ->from(Phpfox::getT('ecommerce_invoice'), 'invoice')
                ->join(Phpfox::getT('ynstore_store'), 'store', 'store.store_id = invoice.item_id AND invoice.item_type IN (\'store\')')
                ->join(Phpfox::getT('user'), 'seller', 'seller.user_id = store.user_id')
                ->where($aConds)
                ->union();
        }
        $this->database()
            ->select('invoice.invoice_id, invoice.user_id')
            ->from(Phpfox::getT('ecommerce_invoice'), 'invoice')
            ->join(Phpfox::getT('ecommerce_product'), 'product', 'product.product_id = invoice.item_id AND invoice.item_type IN (\'auction\', \'product\')')
            ->join(Phpfox::getT('user'), 'seller', 'seller.user_id = product.user_id')
            ->where($aConds)
            ->union();

        $sQuery = $this->database()
            ->select('invoice.invoice_id')
            ->unionFrom('invoice')
            ->join(Phpfox::getT('user'), 'user', 'user.user_id = invoice.user_id')
            ->group('invoice.invoice_id')
            ->execute('');

        $iCnt = $this->database()->select('COUNT(*)')->from('(' . $sQuery . ')', 'temp')->execute('getSlaveField');

        $aRows = array();

        if ($iCnt)
        {
            if (Phpfox::isModule('ynsocialstore')) {
                $this->database()
                    ->select('invoice.*, store.name as name')
                    ->from(Phpfox::getT('ecommerce_invoice'), 'invoice')
                    ->join(Phpfox::getT('ynstore_store'), 'store', 'store.store_id = invoice.item_id AND invoice.item_type IN (\'store\')')
                    ->join(Phpfox::getT('user'), 'seller', 'seller.user_id = store.user_id')
                    ->where($aConds)
                    ->union();
            }
            $this->database()
                ->select('invoice.*, product.name as name')
                ->from(Phpfox::getT('ecommerce_invoice'), 'invoice')
                ->join(Phpfox::getT('ecommerce_product'), 'product', 'product.product_id = invoice.item_id AND invoice.item_type IN (\'auction\', \'product\')')
                ->join(Phpfox::getT('user'), 'seller', 'seller.user_id = product.user_id')
                ->where($aConds)
                ->union();

            $aRows = $this->database()
                ->select('invoice.*')
                ->unionFrom('invoice')
                ->join(Phpfox::getT('user'), 'user', 'user.user_id = invoice.user_id')
                ->group('invoice.invoice_id')
                ->order($sSort)
                ->limit($iPage, $iLimit, $iCnt)
                ->execute('getRows');
        }
        
        return array($iCnt, $aRows);
    }
    
}

?>
