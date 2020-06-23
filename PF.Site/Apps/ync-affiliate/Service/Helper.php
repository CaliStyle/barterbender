<?php

/**
 * Created by PhpStorm.
 * User: dai
 * Date: 20/01/2017
 * Time: 10:44
 */

namespace Apps\YNC_Affiliate\Service;

use Phpfox;

Class Helper extends \Phpfox_Service
{
    public function buildMenu()
    {
        Phpfox::isUser(true);
        $iIsAffiliate =  Phpfox::getService('yncaffiliate.affiliate.affiliate')->checkIsAffiliate(Phpfox::getUserId());
        if (!defined('PHPFOX_IS_USER_PROFILE') && !defined('PHPFOX_IS_PAGES_VIEW')) {
            if(!$iIsAffiliate || $iIsAffiliate == 'pending' || $iIsAffiliate == 'denied')
            {
                $aFilterMenu = [
                    _p('join_affiliate_program') => '',
                    _p('commission_rules') => 'affiliate.commission-rules',
                    _p('faqs')  => 'affiliate.faqs'
                ];
            }
            else{
                $aFilterMenu = [
                    _p('commission_rules') => '',
                    _p('links') => 'affiliate.links',
                    _p('network_clients') => 'affiliate.network-clients',
                    _p('codes') => 'affiliate.codes',
                    _p('link_tracking') => 'affiliate.link-tracking',
                    _p('commissions_tracking') => 'affiliate.commission-tracking',
                    _p('statistics') => 'affiliate.statistics',
                    _p('my_requests') => 'affiliate.my-request',
                    _p('faqs') => 'affiliate.faqs'
                ];
            }
        }
        \Phpfox_Template::instance()->buildSectionMenu('affiliate', $aFilterMenu, false);
    }
    public function getMonthsBetweenTwoDays($iFromTimestamp, $iToTimestamp)
    {
        $iFromTimestamp = (int) $iFromTimestamp;
        if ($iFromTimestamp == 0)
        {
            return array();
        }

        $iToTimestamp = (int) $iToTimestamp;
        if ($iToTimestamp == 0)
        {
            return array();
        }

        $i = date("Ym", $iFromTimestamp);

        $aMonths = array();

        while ($i <= date("Ym", $iToTimestamp))
        {
            $iCurrentTimestamp = strtotime($i . "01");

            $iYear = (int) substr($i, 0, -2);
            $iMonth = (int) substr($i, 4);

//            $sMonth = date("M", $iCurrentTimestamp);

            $aMonths[] = $iMonth.'/'.$iYear;

            if (substr($i, 4, 2) == "12")
            {
                $i = (date("Y", strtotime($i . "01")) + 1) . "01";
            }
            else
            {
                $i++;
            }
        }

        return $aMonths;
    }
    public function getDaysBetweenTwoDays($iFromTimeStamp, $iToTimeStamp)
    {
        $strDateFrom = date('Y-m-d',$iFromTimeStamp);
        $strDateTo = date('Y-m-d',$iToTimeStamp);
        $aryRange=array();

        $iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2),substr($strDateFrom,8,2),substr($strDateFrom,0,4));
        $iDateTo=mktime(1,0,0,substr($strDateTo,5,2),substr($strDateTo,8,2),substr($strDateTo,0,4));

        if ($iDateTo>=$iDateFrom)
        {
            array_push($aryRange,date('n/j/Y',$iDateFrom)); // first entry
            while ($iDateFrom<$iDateTo)
            {
                $iDateFrom+=86400; // add 24 hours
                array_push($aryRange,date('n/j/Y',$iDateFrom));
            }
        }
        return $aryRange;
    }
    public function getWeeksBetweenToDay($iFromTimeStamp,$iToTimeStamp)
    {
        while ($iFromTimeStamp < $iToTimeStamp) {
            $aWeeks[] = _p('week').' '.date('W', $iFromTimeStamp);
            $iFromTimeStamp += strtotime('+1 week', 0);
        }
        return $aWeeks;
    }
    public function getYearsBetweenToDay($iFromTimeStamp,$iToTimeStamp)
    {
        $sStartYear = date('Y', $iFromTimeStamp);
        $sEndYear = date('Y',$iToTimeStamp);
        while ($sStartYear <= $sEndYear) {
            $aYears[] = $sStartYear;
            $sStartYear++;
        }
        return $aYears;
    }
    public function getFinalLineChartData($aLabels,$aLineChartData,$aChartTicks)
    {
        $sFinalData = '[';
        $i = 1;
        $iCnt = count($aLabels);
        foreach ($aLabels as $key => $aLabel)
        {
            if($i == $iCnt)
            {
                $sFinalData .= '{label:\''.$aLabel.'\',data: ['.implode(',',$aLineChartData[$key]).'] }';
            }
            else{
                $sFinalData .= '{label:\''.$aLabel.'\',data: ['.implode(',',$aLineChartData[$key]).'] },';
            }
            $i++;
        }
        $sFinalData .= ']';
        if($sFinalData == '[]')
        {
            $sFinalData = '[0,0]';
        }
        $sChartTicks = '[';
        $iCnt = count($aChartTicks);
        foreach ($aChartTicks as $key => $aChartTick) {
            if($key == $iCnt - 1)
            {
                $sChartTicks .= '['.$key.',\''.$aChartTick.'\']';
            }
            else{
                $sChartTicks .= '['.$key.',\''.$aChartTick.'\'],';
            }
        }
        $sChartTicks .= ']';
        return [$sFinalData,$sChartTicks];
    }

    public function getFinalPieChartData($aLabels,$aPieChartData,$fTotalValue)
    {
        $sFinalData = '[';
        $i = 1;
        $iCnt = count($aPieChartData);
        $fCurrentPercent = 0;
        foreach($aPieChartData as $key => $aData)
        {
            if($i < $iCnt)
            {
                $iPercent = round($aData*100/$fTotalValue);
                $fCurrentPercent = $fCurrentPercent + $iPercent;
                $aPieChartData[$key] = $iPercent;
            }
            else{
                $aPieChartData[$key] = 100 - $fCurrentPercent;
            }
            $i++;
        }
        $i = 1;
        $iCnt = count($aLabels);
        foreach ($aLabels as $key => $aLabel)
        {
            if($i == $iCnt)
            {
                $sFinalData .= '{label: \''.$aLabel.'\', data: '.$aPieChartData[$key].'}';
            }
            else
            {
                $sFinalData .= '{label: \''.$aLabel.'\', data: '.$aPieChartData[$key].'},';
            }
            $i++;
        }
        $sFinalData .= ']';
        if($sFinalData == '[]')
        {
            $sFinalData = '[0,0]';
        }
        return $sFinalData;
    }
    public function sendMail($sEmail,$sText,$sSubject)
    {
        return Phpfox::getLib('mail')->to($sEmail)
            ->subject($sSubject)
            ->message($sText)
            ->send();
    }
    public function getUserParam($sParam, $iUserId) {

        $aUser = Phpfox::getService('user')->getUser($iUserId);
        if($aUser)
        {
            $iGroupId = $aUser['user_group_id'];
            return Phpfox::getService('user.group.setting')->getGroupParam($iGroupId, $sParam);
        }
        return null;

    }
}