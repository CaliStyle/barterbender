<?php

defined('PHPFOX') or exit('NO DICE!');

class Ecommerce_Service_Order_Order extends Phpfox_Service {

    public function getOrder($iOrderId)
    {
        $aRow = $this->database()
                ->select('o.*, c.*, ' . Phpfox::getUserField('b', 'buyer_') . ', ' . Phpfox::getUserField('s', 'seller_'))
                ->from(Phpfox::getT('ecommerce_order'), 'o')
                ->leftJoin(Phpfox::getT('ecommerce_order_credit'), 'c', 'c.ordercredit_order_id = o.order_id')
                
                ->join(Phpfox::getT('user'), 'b', 'b.user_id = o.user_id')
                ->join(Phpfox::getT('user'), 's', 's.user_id = o.seller_id')
                
                ->where('o.order_id = ' . (int) $iOrderId)
                ->execute('getRow');
        
        return $aRow;
    }
    
    public function getOrderById($iOrderId)
    {

        $aOrder = $this->database()
                ->select('o.*')
                ->from(Phpfox::getT('ecommerce_order'), 'o')
                ->where('o.order_id = ' . (int) $iOrderId)
                ->execute('getSlaveRow');

        $aProduct = $this->database()->select('eop.*')
                ->from(Phpfox::getT('ecommerce_order_product'), 'eop')
                ->where('eop.orderproduct_order_id = ' . (int) $iOrderId)
                ->execute('getSlaveRows');

        $aOrder['product'] = $aProduct;
        
        return $aOrder;
    }


    public function geQuickOrderById($iOrderId)
    {
        $aRow = $this->database()
                ->select('o.*')
                ->from(Phpfox::getT('ecommerce_order'), 'o')
                ->where('o.order_id = ' . (int) $iOrderId)
                ->execute('getSlaveRow');
        
        return $aRow;
    }

    public function getOrderDetails($iOrderId, $sModuleType = 'auction')
    {
        $sSelect = $this->database()
                ->select('eop.*, ep.logo_path, ep.server_id, ep.creating_item_currency,eu.title as uom_title')
                ->from(Phpfox::getT('ecommerce_order_product'), 'eop')
                ->join(Phpfox::getT('ecommerce_order'), 'eo', 'eop.orderproduct_order_id = eo.order_id')
                ->join(Phpfox::getT('ecommerce_product'), 'ep', 'eop.orderproduct_product_id = ep.product_id')
                ->leftJoin(Phpfox::getT('ecommerce_uom'), 'eu', 'eu.uom_id = ep.uom_id');

        if (Phpfox::isModule('ynsocialstore'))
            $sSelect->select(', epa.title as attribute_name')->leftJoin(Phpfox::getT('ecommerce_product_attribute'), 'epa', 'epa.attribute_id = eop.orderproduct_attribute_id');

        $aRows = $sSelect->where('eo.order_id = ' . (int) $iOrderId . ' AND ep.module_id LIKE "' . $sModuleType . '"')
                         ->execute('getRows');

        if(!empty($aRows)) {
            $module = $aRows[0]['orderproduct_module'];
            $callbackData = [];
            if(!empty($module) && Phpfox::hasCallback($module,'getMoreInfomationForProduct')) {
                $callbackData = Phpfox::callback($module . '.getMoreInfomationForProduct');
            }
            foreach ($aRows as $iKey => $aRow)
            {
                $aRows[$iKey]['sSymbolCurrency'] = Phpfox::getService('core.currency')->getSymbol($aRow['creating_item_currency']);
                $aRows[$iKey]['default_product_image'] = !empty($callbackData['default_product_image']) ? $callbackData['default_product_image'] : null;
            }
        }

        return $aRows;
    }
    
    public function getTotalSold()
    {
        return $this->database()
                ->select('SUM(o.order_total_price)')
                ->from(Phpfox::getT('ecommerce_order'), 'o')
                ->where('o.user_id = ' . Phpfox::getUserId() . ' AND o.order_status = "completed"')
                ->execute('getSlaveField');
    }

    public function getTotalCommissions()
    {
        return $this->database()
                ->select('SUM(o.order_commission_value)')
                ->from(Phpfox::getT('ecommerce_order'), 'o')
                ->where('o.user_id = ' . Phpfox::getUserId() . ' AND o.order_status = "completed"')
                ->execute('getSlaveField');
    }


    public function getTotalSoldOfMyItem($sType)
    {

        if($sType == 'ecommerce'){
            return $this->database()
                ->select('SUM(op.orderproduct_product_quantity)')
                ->from(Phpfox::getT('ecommerce_order'), 'o')
                ->join(Phpfox::getT('ecommerce_order_product'), 'op','op.orderproduct_order_id = o.order_id')
                ->join(Phpfox::getT('ecommerce_product'), 'ep','ep.product_id = op.orderproduct_product_id')
                ->where('o.seller_id = ' . Phpfox::getUserId() . ' AND o.order_payment_status = "completed"')
                ->execute('getSlaveField');    
        }
        else{
            return $this->database()
                ->select('SUM(op.orderproduct_product_quantity)')
                ->from(Phpfox::getT('ecommerce_order'), 'o')
                ->join(Phpfox::getT('ecommerce_order_product'), 'op','op.orderproduct_order_id = o.order_id')
                ->join(Phpfox::getT('ecommerce_product'), 'ep','ep.product_id = op.orderproduct_product_id')
                ->where('o.seller_id = ' . Phpfox::getUserId() . ' AND o.order_payment_status = "completed"'.' AND ep.product_creating_type = \''.$sType.'\'')
                ->execute('getSlaveField');    
        }
        
    }

    public function getTotalSoldOfMyItemForChart($iFromTimestamp, $iToTimestamp ,$sType,$iStoreId = 0)
    {

        $aConds = array(
            'AND eo.seller_id = ' . Phpfox::getUserId(),
            'AND eo.order_payment_status = "completed"',
            'AND eo.order_purchase_datetime > ' . (int) $iFromTimestamp,
            'AND eo.order_purchase_datetime < ' . ((int) $iToTimestamp + 86400),
        );
        if($iStoreId){
            $aConds[] = 'AND ep.item_id ='. $iStoreId;
        }
        if($sType != 'ecommerce'){
            $aConds[] =  'AND ep.product_creating_type = \''.$sType.'\'';         
        }
            
        $aRows = $this->database()
                ->select('MONTH(FROM_UNIXTIME(eo.order_purchase_datetime)) AS current_month, YEAR(FROM_UNIXTIME(eo.order_purchase_datetime)) AS current_year, SUM(op.orderproduct_product_quantity) as total_value')
                ->from(Phpfox::getT('ecommerce_order'), 'eo')
                ->join(Phpfox::getT('ecommerce_order_product'), 'op','op.orderproduct_order_id = eo.order_id')
                ->join(Phpfox::getT('ecommerce_product'), 'ep','ep.product_id = op.orderproduct_product_id')
                ->where($aConds)
                ->group('MONTH(FROM_UNIXTIME(eo.order_purchase_datetime)), YEAR(FROM_UNIXTIME(eo.order_purchase_datetime))')
                ->order('YEAR(FROM_UNIXTIME(eo.order_purchase_datetime)), MONTH(FROM_UNIXTIME(eo.order_purchase_datetime))')
                ->execute('getRows');

        $aTempChartData = array();
        foreach ($aRows as $iKey => $aItem)
        {
            $aTempChartData[$aItem['current_year']][$aItem['current_month']] = $aItem['total_value'];
        }
        
        return $aTempChartData;

    }


    public function getTotalSaleOfMyItem($sType)
    {
        return $this->database()
                ->select('SUM(o.order_total_price)')
                ->from(Phpfox::getT('ecommerce_order'), 'o')
                ->where('o.seller_id = ' . Phpfox::getUserId() . ' AND o.order_payment_status = "completed"')
                ->execute('getSlaveField');
    }


    public function getTotalSaleOfMyItemForChart($iFromTimestamp, $iToTimestamp,$sType,$iStoreId = 0)
    {

        $aConds = array(
            'AND eo.seller_id = ' . Phpfox::getUserId(),
            'AND eo.order_payment_status = "completed"',
            'AND eo.order_purchase_datetime > ' . (int) $iFromTimestamp,
            'AND eo.order_purchase_datetime < ' . ((int) $iToTimestamp + 86400),
        );
        if($iStoreId){
            $aConds[] = 'AND ep.item_id ='. $iStoreId;
        }
        if($sType != 'ecommerce'){
            $aConds[] =  'AND ep.product_creating_type = \''.$sType.'\'';         
        }
            
        
        $aRows = $this->database()
                ->select('MONTH(FROM_UNIXTIME(eo.order_purchase_datetime)) AS current_month, YEAR(FROM_UNIXTIME(eo.order_purchase_datetime)) AS current_year, SUM(op.orderproduct_product_price * op.orderproduct_product_quantity) as total_value')
                ->from(Phpfox::getT('ecommerce_order'), 'eo')
                ->join(Phpfox::getT('ecommerce_order_product'), 'op','op.orderproduct_order_id = eo.order_id')
                ->join(Phpfox::getT('ecommerce_product'), 'ep','ep.product_id = op.orderproduct_product_id')
                ->where($aConds)
                ->group('MONTH(FROM_UNIXTIME(eo.order_purchase_datetime)), YEAR(FROM_UNIXTIME(eo.order_purchase_datetime))')
                ->order('YEAR(FROM_UNIXTIME(eo.order_purchase_datetime)), MONTH(FROM_UNIXTIME(eo.order_purchase_datetime))')
                ->execute('getRows');

        $aTempChartData = array();
        foreach ($aRows as $iKey => $aItem)
        {
            $aTempChartData[$aItem['current_year']][$aItem['current_month']] = $aItem['total_value'];
        }
        
        return $aTempChartData;
    
    }
    

    public function getTotalCommissionOfMyItem($sType)
    {
       return $this->database()
                ->select('SUM(o.order_commission_value)')
                ->from(Phpfox::getT('ecommerce_order'), 'o')
                ->where('o.seller_id = ' . Phpfox::getUserId() . ' AND o.order_payment_status = "completed"')
                ->execute('getSlaveField');
    }

    public function getTotalCommissionOfMyItemForChart($iFromTimestamp, $iToTimestamp,$sType,$iStoreId = 0)
    {

        $aConds = array(
            'AND eo.seller_id = ' . Phpfox::getUserId(),
            'AND eo.order_payment_status = "completed"',
            'AND eo.order_purchase_datetime > ' . (int) $iFromTimestamp,
            'AND eo.order_purchase_datetime < ' . ((int) $iToTimestamp + 86400),
        );
        if($iStoreId){
            $aConds[] = 'AND ep.item_id ='. $iStoreId;
        }
        if($sType != 'ecommerce'){
            $aConds[] =  'AND ep.product_creating_type = \''.$sType.'\'';         
        }
            
        
        $aRows = $this->database()
                ->select('MONTH(FROM_UNIXTIME(eo.order_purchase_datetime)) AS current_month, YEAR(FROM_UNIXTIME(eo.order_purchase_datetime)) AS current_year, SUM(op.orderproduct_product_price * eo.order_commission_rate / 100) as total_value')
                ->from(Phpfox::getT('ecommerce_order'), 'eo')
                ->join(Phpfox::getT('ecommerce_order_product'), 'op','op.orderproduct_order_id = eo.order_id')
                ->join(Phpfox::getT('ecommerce_product'), 'ep','ep.product_id = op.orderproduct_product_id')
                ->where($aConds)
                ->group('MONTH(FROM_UNIXTIME(eo.order_purchase_datetime)), YEAR(FROM_UNIXTIME(eo.order_purchase_datetime))')
                ->order('YEAR(FROM_UNIXTIME(eo.order_purchase_datetime)), MONTH(FROM_UNIXTIME(eo.order_purchase_datetime))')
                ->execute('getRows');
        
        $aTempChartData = array();
        foreach ($aRows as $iKey => $aItem)
        {
            $aTempChartData[$aItem['current_year']][$aItem['current_month']] = $aItem['total_value'];
        }
        
        return $aTempChartData;
    
    }


    public function getTotalManageOrders($sType)
    {
        $sQuery = $this->database()
                ->select('o.order_id')
                ->from(Phpfox::getT('ecommerce_order'), 'o')
                ->where('o.seller_id = ' . Phpfox::getUserId() . ' AND o.order_payment_status != "initialized"'.' AND o.module_id = \''.$sType.'\'')
                ->group('o.order_id')
                ->execute('');
        $iTotal = $this->database()->select('COUNT(*)')->from('(' . $sQuery . ')', 'temp')->execute('getSlaveField');
        return $iTotal;
    }

    public function getTotalMyOrders($sType)
    {
        $sQuery = $this->database()
                ->select('o.order_id')
                ->from(Phpfox::getT('ecommerce_order'), 'o')
                ->where('o.user_id = ' . Phpfox::getUserId() . ' AND o.module_id = \''.$sType.'\'')
                ->group('o.order_id')
                ->execute('');
        $iTotal = $this->database()->select('COUNT(*)')->from('(' . $sQuery . ')', 'temp')->execute('getSlaveField');
        return $iTotal;
    }
    
    public function getSoldAuctionsForChart($iFromTimestamp, $iToTimestamp)
    {
        $aConds = array(
            'AND eo.user_id = ' . Phpfox::getUserId(),
            'AND eo.order_status = "completed"',
            'AND eo.order_finished_datetime > ' . (int) $iFromTimestamp,
            'AND eo.order_finished_datetime < ' . ((int) $iToTimestamp + 86400)
        );
        
        $aRows = $this->database()
                ->select('MONTH(FROM_UNIXTIME(eo.order_finished_datetime)) AS current_month, YEAR(FROM_UNIXTIME(eo.order_finished_datetime)) AS current_year, SUM(eo.order_total_price) as total_value')
                ->from(Phpfox::getT('ecommerce_order'), 'eo')
                ->where($aConds)
                ->group('MONTH(FROM_UNIXTIME(eo.order_finished_datetime)), YEAR(FROM_UNIXTIME(eo.order_finished_datetime))')
                ->order('YEAR(FROM_UNIXTIME(eo.order_finished_datetime)), MONTH(FROM_UNIXTIME(eo.order_finished_datetime))')
                ->execute('getRows');
        
        $aTempChartData = array();
        foreach ($aRows as $iKey => $aItem)
        {
            $aTempChartData[$aItem['current_year']][$aItem['current_month']] = $aItem['total_value'];
        }
        
        return $aTempChartData;
    }
    
    public function getCommissionsForChart($iFromTimestamp, $iToTimestamp)
    {
        $aConds = array(
            'AND eo.user_id = ' . Phpfox::getUserId(),
            'AND eo.order_status = "completed"',
            'AND eo.order_finished_datetime > ' . (int) $iFromTimestamp,
            'AND eo.order_finished_datetime < ' . ((int) $iToTimestamp + 86400)
        );
        
        $aRows = $this->database()
                ->select('MONTH(FROM_UNIXTIME(eo.order_finished_datetime)) AS current_month, YEAR(FROM_UNIXTIME(eo.order_finished_datetime)) AS current_year, SUM(eo.order_commission_value) as total_value')
                ->from(Phpfox::getT('ecommerce_order'), 'eo')
                ->where($aConds)
                ->group('MONTH(FROM_UNIXTIME(eo.order_finished_datetime)), YEAR(FROM_UNIXTIME(eo.order_finished_datetime))')
                ->order('YEAR(FROM_UNIXTIME(eo.order_finished_datetime)), MONTH(FROM_UNIXTIME(eo.order_finished_datetime))')
                ->execute('getRows');
        
        $aTempChartData = array();
        foreach ($aRows as $iKey => $aItem)
        {
            $aTempChartData[$aItem['current_year']][$aItem['current_month']] = $aItem['total_value'];
        }
        
        return $aTempChartData;
    }
    
    public function getNumberSoldProductsForChart($iFromTimestamp, $iToTimestamp)
    {
        $aConds = array(
            'AND eo.user_id = ' . Phpfox::getUserId(),
            'AND eo.order_status = "completed"',
            'AND eo.order_finished_datetime > ' . (int) $iFromTimestamp,
            'AND eo.order_finished_datetime < ' . ((int) $iToTimestamp + 86400)
        );
        
        $aRows = $this->database()
                ->select('MONTH(FROM_UNIXTIME(eo.order_finished_datetime)) AS current_month, YEAR(FROM_UNIXTIME(eo.order_finished_datetime)) AS current_year, SUM(eo.order_item_count) as total_value')
                ->from(Phpfox::getT('ecommerce_order'), 'eo')
                ->where($aConds)
                ->group('MONTH(FROM_UNIXTIME(eo.order_finished_datetime)), YEAR(FROM_UNIXTIME(eo.order_finished_datetime))')
                ->order('YEAR(FROM_UNIXTIME(eo.order_finished_datetime)), MONTH(FROM_UNIXTIME(eo.order_finished_datetime))')
                ->execute('getRows');
        
        $aTempChartData = array();
        foreach ($aRows as $iKey => $aItem)
        {
            $aTempChartData[$aItem['current_year']][$aItem['current_month']] = $aItem['total_value'];
        }
        
        return $aTempChartData;
    }
	
	public function get($aConds, $sSort = 'u.full_name ASC', $iPage = '', $iLimit = '')
	{
		$sQuery = $this->database()
                ->select('eo.order_id')
                ->from(Phpfox::getT('ecommerce_order'), 'eo')
                ->join(Phpfox::getT('ecommerce_order_product'), 'eop', 'eop.orderproduct_order_id = eo.order_id')
				->group('eo.order_id')
                ->where($aConds)
                ->execute('');
				
        $iCnt = $this->database()->select('COUNT(*)')->from('(' . $sQuery . ')', 'temp')->execute('getSlaveField');
		
        $aRows = array();
        
        if ($iCnt)
        {
            $aRows = $this->database()
                ->select('eo.*')
                ->from(Phpfox::getT('ecommerce_order'), 'eo')
                ->join(Phpfox::getT('ecommerce_order_product'), 'eop', 'eop.orderproduct_order_id = eo.order_id')
				->where($aConds)
				->group('eo.order_id')
                ->order($sSort)
                ->limit($iPage, $iLimit, $iCnt)
                ->execute('getRows');
        }
        
        return array($iCnt, $aRows);
	}
	

    public function getOrders($aConds, $sSort = 'eo.order_id DESC', $iPage = '', $iLimit = '',$sType = '')
    {

        $sQuery = $this->database()
                ->select('eo.order_id, eo.order_total_price, eo.order_commission_value,eop.orderproduct_product_quantity')
                ->from(Phpfox::getT('ecommerce_order'), 'eo')
                ->join(Phpfox::getT('ecommerce_order_product'), 'eop', 'eop.orderproduct_order_id = eo.order_id')
                ->join(Phpfox::getT('ecommerce_product'), 'e', 'e.product_id = eop.orderproduct_product_id')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = eo.user_id')
				->join(Phpfox::getT('user'), 's', 's.user_id = eo.seller_id')
                ->group('eo.order_id')
                ->where($aConds)
                ->execute('');
        $aRow = $this->database()->select('COUNT(*) as iCount, SUM(temp.order_total_price) as iTotalAmount, SUM(temp.order_commission_value) as iTotalCommission, SUM(temp.orderproduct_product_quantity) as iTotalQuantity')->from('(' . $sQuery . ')', 'temp')->execute('getRow');
        $iCnt = $aRow['iCount'];
        $iTotalAmount = $aRow['iTotalAmount'];
        $iTotalCommission = $aRow['iTotalCommission'];
        $iTotalQuantity = $aRow['iTotalQuantity'];

        $aRows = array();
        
        if ($iCnt)
        {
            $aRows = $this->database()
                ->select('eo.*,(eop.orderproduct_product_price * eop.orderproduct_product_quantity) as total_product_price,eop.orderproduct_product_quantity,'.Phpfox::getUserField())
                ->from(Phpfox::getT('ecommerce_order'), 'eo')
                ->join(Phpfox::getT('ecommerce_order_product'), 'eop', 'eop.orderproduct_order_id = eo.order_id')
                ->join(Phpfox::getT('ecommerce_product'), 'e', 'e.product_id = eop.orderproduct_product_id')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = eo.user_id')
				->join(Phpfox::getT('user'), 's', 's.user_id = eo.seller_id')
                ->where($aConds)
                ->group('eo.order_id')
                ->order($sSort)
                ->limit($iPage, $iLimit, $iCnt)
                ->execute('getRows');
				//->execute();
				//echo $aRows;die;

            foreach ($aRows as $iKey => $aRow)
            {
                $sSelect = $this->database()
                                ->select('eop.*')
                                ->from(Phpfox::getT('ecommerce_order_product'), 'eop');
                if (Phpfox::isModule('ynsocialstore'))
                    $sSelect->select(', epa.title as attribute_name')->leftJoin(Phpfox::getT('ecommerce_product_attribute'), 'epa', 'epa.attribute_id = eop.orderproduct_attribute_id');

                $aRows[$iKey]['products'] = $sSelect
                                                 ->where('eop.orderproduct_order_id = '.$aRow['order_id'])
                                                 ->execute('getSlaveRows');
            }
        }

        if($sType == 'product-sales'){
            return array($iCnt, $aRows, $iTotalAmount, $iTotalQuantity);
        }
        else{
            return array($iCnt, $aRows, $iTotalAmount, $iTotalCommission);
        }
    }

    /*
     * In Order table may contains many record with disable module.
     * We have to not query all orders which belonged to disable module.
     */
    public function getDisableOrdersModuleIds()
    {
        $aRows = $this->database()->select('module_id')
            ->from(Phpfox::getT('ecommerce_order'))
            ->group('module_id')
            ->execute('getRows');

        if (empty($aRows))
            return null;

        $aModuleIds = array();

        foreach ($aRows as $aRow)
        {
            $module_id = array_shift($aRow);
            if (!Phpfox::isModule($module_id)) {
                $aModuleIds[] = '\''. $module_id . '\'';
            }
        }

        return implode(',', $aModuleIds);
    }
}

?>
