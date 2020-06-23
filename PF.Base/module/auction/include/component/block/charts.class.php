<?php

defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Block_Charts extends Phpfox_Component
{

    public function process()
    {
        $iFromTimestamp = $this->getParam('iFromTimestamp');
        $iToTimestamp = $this->getParam('iToTimestamp');

      //  $aFirstChartData = Phpfox::getService('ecommerce.order')->getSoldAuctionsForChart($iFromTimestamp, $iToTimestamp);
        $aFirstChartData = Phpfox::getService('ecommerce.order')->getTotalSaleOfMyItemForChart($iFromTimestamp, $iToTimestamp,'auction');
    
        // For second chart.
        $aPublishFeeData = Phpfox::getService('ecommerce.invoice')->getTotalPriceForChart($iFromTimestamp, $iToTimestamp, 'publish','auction');
        $aFeatureFeeData = Phpfox::getService('ecommerce.invoice')->getTotalPriceForChart($iFromTimestamp, $iToTimestamp, 'feature','auction');
        //$aCommissionFeeData = Phpfox::getService('ecommerce.order')->getCommissionsForChart($iFromTimestamp, $iToTimestamp);
        $aCommissionFeeData = Phpfox::getService('ecommerce.order')->getTotalCommissionOfMyItemForChart($iFromTimestamp, $iToTimestamp,'auction');
        
        //$aThirdChartData = Phpfox::getService('ecommerce.order')->getNumberSoldProductsForChart($iFromTimestamp, $iToTimestamp);
        $aThirdChartData = Phpfox::getService('ecommerce.order')->getTotalSoldOfMyItemForChart($iFromTimestamp, $iToTimestamp,'auction');
    

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
			'sCommissionFeeChartData' => $sCommissionFeeChartData
                ));
        
        return 'block';
    }

}

?>