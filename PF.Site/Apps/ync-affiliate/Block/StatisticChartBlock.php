<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 3/8/17
 * Time: 15:35
 */
namespace Apps\YNC_Affiliate\Block;

defined('PHPFOX') or exit('NO DICE!');
use Phpfox;
class StatisticChartBlock extends \Phpfox_Component
{
    public function process()
    {
        $iFromTimestamp = $this->getParam('iFromTimestamp');
        $iToTimestamp = $this->getParam('iToTimestamp');
        $sLabel = $this->getParam('sLabel');
        $sStatus = $this->getParam('sStatus');
        $sData = $this->getParam('sData');
        $sGroupBy = $this->getParam('sGroupBy');
        $iUserId = $this->getParam('iUserId',0);
        $aChartTicks = [];
        $sChartName = ($sData == 'number_transaction') ? _p('number_of_transaction') : _p('earning_l');
        $sChartName .= ' '._p('by').' ';
        switch ($sGroupBy)
        {
            case 'day':
                $sChartName .= _p('day');
                $aChartTicks = Phpfox::getService('yncaffiliate.helper')->getDaysBetweenTwoDays($iFromTimestamp,$iToTimestamp);
                break;
            case 'week':
                $sChartName .= _p('week');
                $aChartTicks = Phpfox::getService('yncaffiliate.helper')->getWeeksBetweenToDay($iFromTimestamp,$iToTimestamp);
                break;
            case 'month':
                $sChartName .= _p('month');
                $aChartTicks = Phpfox::getService('yncaffiliate.helper')->getMonthsBetweenTwoDays($iFromTimestamp,$iToTimestamp);
                break;
            case 'year':
                $sChartName .= _p('year');
                $aChartTicks = Phpfox::getService('yncaffiliate.helper')->getYearsBetweenToDay($iFromTimestamp,$iToTimestamp);
                break;
        }
        $aLineChartData = $aPieChartData = [];
        $aLabels = [];
        $fTotalDataPieChart = 0;
        if($sLabel == 'rules')
        {
            $aRules = Phpfox::getService('yncaffiliate.commissionrule.commissionrule')->getActiveRules();
            foreach ($aRules as $aRule)
            {
                $aChartData = Phpfox::getService('yncaffiliate.commission')->getDataForLineChart($iUserId,$sLabel,$iFromTimestamp,$iToTimestamp,$sStatus,$sData,$sGroupBy,$aRule['rule_id']);

                if(count($aChartData))
                {
                    $aLabels[$aRule['rule_id']] = _p($aRule['rule_title']);
                    foreach($aChartTicks as $key => $aChartTick)
                    {
                        $aLineChartData[$aRule['rule_id']][] = '['.$key.','.(isset($aChartData[$aChartTick]) ? $aChartData[$aChartTick] : 0 ). ']';
                    }
                }
                $aChartData2 = Phpfox::getService('yncaffiliate.commission')->getDataForPieChart($iUserId,$sLabel,$iFromTimestamp,$iToTimestamp,$sStatus,$sData,$aRule['rule_id']);
                if(count($aChartData2) && $aChartData2['total'])
                {
                    $fTotalDataPieChart = $fTotalDataPieChart + $aChartData2['total'];
                    $aPieChartData[$aRule['rule_id']] = $aChartData2['total'];
                }
            }
        }
        elseif($sLabel == 'levels')
        {
            $aAccounts = Phpfox::getService('yncaffiliate.affiliate.affiliate')->getAllClients($iUserId);
            foreach ($aAccounts as $ikey => $aAccount)
            {
                $aChartData = Phpfox::getService('yncaffiliate.commission')->getDataForLineChart($iUserId,$sLabel,$iFromTimestamp,$iToTimestamp,$sStatus,$sData,$sGroupBy,implode(',',$aAccount));
                if(count($aChartData))
                {
                    $aLabels[$ikey] = _p('level_l').' '.$ikey;
                    foreach($aChartTicks as $key => $aChartTick)
                    {
                        $aLineChartData[$ikey][] = '['.$key.','.(isset($aChartData[$aChartTick]) ? $aChartData[$aChartTick] : 0 ). ']';
                    }
                }
                $aChartData2 = Phpfox::getService('yncaffiliate.commission')->getDataForPieChart($iUserId,$sLabel,$iFromTimestamp,$iToTimestamp,$sStatus,$sData,implode(',',$aAccount));
                if(count($aChartData2) && $aChartData2['total'])
                {
                    $fTotalDataPieChart = $fTotalDataPieChart + $aChartData2['total'];
                    $aPieChartData[$ikey] = $aChartData2['total'];
                }
            }
        }
        list($sFinalData,$sChartTicks) = Phpfox::getService('yncaffiliate.helper')->getFinalLineChartData($aLabels,$aLineChartData,$aChartTicks);
        $sFinalPieData = Phpfox::getService('yncaffiliate.helper')->getFinalPieChartData($aLabels,$aPieChartData,$fTotalDataPieChart);
        $this->template()->assign([
            'sChartTicks' => $sChartTicks,
            'aLineChartFinalData' => $sFinalData,
            'aPieChartFinalData' => $sFinalPieData,
            'sChartName' => $sChartName,
            'yAxesName' => $sChartName
        ]);
        return 'block';
    }
}