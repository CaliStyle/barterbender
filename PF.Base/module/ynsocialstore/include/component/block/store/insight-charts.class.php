<?php

defined('PHPFOX') or exit('NO DICE!');

class Ynsocialstore_Component_Block_Store_Insight_Charts extends Phpfox_Component
{

    public function process()
    {
        $iFromTimestamp = $this->getParam('iFromTimestamp');
        $iToTimestamp = $this->getParam('iToTimestamp');
        $sType = $this->getParam('sType','insight');
        $iStoreId = $this->getParam('iStoreId',0);
        if($sType == 'statistic'){
            $aFirstChartData = Phpfox::getService('ecommerce.order')->getTotalSaleOfMyItemForChart($iFromTimestamp, $iToTimestamp,'ynsocialstore_product');

            // For second chart.
            $aPublishFeeData = Phpfox::getService('ecommerce.invoice')->getTotalPriceForChart($iFromTimestamp, $iToTimestamp, 'publish','ynsocialstore_product');
            $aFeatureFeeData = Phpfox::getService('ecommerce.invoice')->getTotalPriceForChart($iFromTimestamp, $iToTimestamp, 'feature','ynsocialstore_product');
            $aCommissionFeeData = Phpfox::getService('ecommerce.order')->getTotalCommissionOfMyItemForChart($iFromTimestamp, $iToTimestamp,'ynsocialstore_product');

            $aThirdChartData = Phpfox::getService('ecommerce.order')->getTotalSoldOfMyItemForChart($iFromTimestamp, $iToTimestamp,'ynsocialstore_product');
        }
        else{
            $aFirstChartData = Phpfox::getService('ecommerce.order')->getTotalSaleOfMyItemForChart($iFromTimestamp, $iToTimestamp,'ynsocialstore_product',$iStoreId);

            // For second chart.
            $aPublishFeeData = Phpfox::getService('ecommerce.invoice')->getTotalPriceForChart($iFromTimestamp, $iToTimestamp, 'publish','ynsocialstore_product',$iStoreId);
            $aFeatureFeeData = Phpfox::getService('ecommerce.invoice')->getTotalPriceForChart($iFromTimestamp, $iToTimestamp, 'feature','ynsocialstore_product',$iStoreId);
            $aCommissionFeeData = Phpfox::getService('ecommerce.order')->getTotalCommissionOfMyItemForChart($iFromTimestamp, $iToTimestamp,'ynsocialstore_product',$iStoreId);

            $aThirdChartData = Phpfox::getService('ecommerce.order')->getTotalSoldOfMyItemForChart($iFromTimestamp, $iToTimestamp,'ynsocialstore_product',$iStoreId);
        }

        $aChartData = Phpfox::getService('ecommerce.helper')->getMonthsBetweenTwoDays($iFromTimestamp, $iToTimestamp);

        $aTempFirstChartData = array();
        $aPublishFeeTempSecondChartData = array();
        $aFeatureFeeTempSecondChartData = array();
        $aCommissionFeeTempSecondChartData = array();
        $aTempThirdChartData = array();
        $aTempXAxis = array();
		
        foreach ($aChartData as $iKey => $aItem)
        {
            $aTempFirstChartData[] = '[' . $iKey . ',' . (isset($aFirstChartData[$aItem['iYear']][$aItem['iMonth']]) ? $aFirstChartData[$aItem['iYear']][$aItem['iMonth']] : 0) . ']';
            
            $aPublishFeeTempSecondChartData[] = '[' . $iKey . ',' . (isset($aPublishFeeData[$aItem['iYear']][$aItem['iMonth']]) ? $aPublishFeeData[$aItem['iYear']][$aItem['iMonth']] : 0) . ']';
            $aFeatureFeeTempSecondChartData[] = '[' . $iKey . ',' . (isset($aFeatureFeeData[$aItem['iYear']][$aItem['iMonth']]) ? $aFeatureFeeData[$aItem['iYear']][$aItem['iMonth']] : 0) . ']';
            $aCommissionFeeTempSecondChartData[] = '[' . $iKey . ',' . (isset($aCommissionFeeData[$aItem['iYear']][$aItem['iMonth']]) ? $aCommissionFeeData[$aItem['iYear']][$aItem['iMonth']] : 0) . ']';
            
            $aTempThirdChartData[] = '[' . $iKey . ',' . (isset($aThirdChartData[$aItem['iYear']][$aItem['iMonth']]) ? $aThirdChartData[$aItem['iYear']][$aItem['iMonth']] : 0) . ']';
			$aTempXAxis[] = '['  . $iKey . ',"' . $aItem['sMonth'] . '"]';
        }
        
        $sFirstChartData = '[' . implode(',', $aTempFirstChartData) . ']';
		$sPublishFeeChartData = '[' . implode(',', $aPublishFeeTempSecondChartData) . ']';
		$sFeatureFeeChartData = '[' . implode(',', $aFeatureFeeTempSecondChartData) . ']';
		$sCommissionFeeChartData = '[' . implode(',', $aCommissionFeeTempSecondChartData) . ']';
        $sThirdChartData = '[' . implode(',', $aTempThirdChartData) . ']';
        
		$sTempXAxis = '[' . implode(',', $aTempXAxis) . ']';
		
        $sDefaultCurrency = Phpfox::getService('core.currency')->getDefault();
        $sCurrencySymbol = Phpfox::getService('core.currency')->getSymbol($sDefaultCurrency);
        

        $this->template()->assign(array(
            'sCurrencySymbol' => $sCurrencySymbol,
            'sFirstChartData' => $sFirstChartData,
            'sThirdChartData' => $sThirdChartData,
			'sTempXAxis' => $sTempXAxis,
			'sPublishFeeChartData' => $sPublishFeeChartData,
			'sFeatureFeeChartData' => $sFeatureFeeChartData,
			'sCommissionFeeChartData' => $sCommissionFeeChartData,
            'sType' => $sType
                ));
        
        return 'block';
    }

}

?>