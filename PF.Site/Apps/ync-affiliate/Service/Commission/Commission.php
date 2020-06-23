<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 2/27/17
 * Time: 15:13
 */
namespace Apps\YNC_Affiliate\Service\Commission;

use Phpfox;

class Commission extends \Phpfox_Service
{
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('yncaffiliate_commissions');
        $this->_activeModule  = implode('\',\'',Phpfox::getLib('module')->getModules());
    }
    public function getManageCommissions($aConds = array(), $iPage = 0, $iLimit = NULL, $bGetMore = false)
    {
        $sWhere = '1=1';
        if (count($aConds) > 0) {
            $sCond = implode('  ', $aConds);
            $sWhere .= ' ' . $sCond;
        }
        $iCount = db()->select('COUNT(*)')
                        ->from($this->_sTable,'yc')
                        ->join(Phpfox::getT('user'),'u1','u1.user_id = yc.user_id')
                        ->join(Phpfox::getT('user'),'u2','u2.user_id = yc.from_user_id')
                        ->where($sWhere)
                        ->execute('getSlaveField');
        $aItems = [];
        if($iCount)
        {
            $aItems = db()->select('yc.*,yr.*,u1.full_name as affiliate_name, u1.user_name as affiliate_username, u2.full_name as client_name,u2.user_name as client_username')
                            ->from($this->_sTable,'yc')
                            ->join(Phpfox::getT('user'),'u1','u1.user_id = yc.user_id')
                            ->join(Phpfox::getT('user'),'u2','u2.user_id = yc.from_user_id')
                            ->join(Phpfox::getT('yncaffiliate_rules'),'yr','yr.rule_id = yc.rule_id')
                            ->where($sWhere)
                            ->limit($iPage,$iLimit,$iCount)
                            ->order('yc.time_stamp DESC')
                            ->execute('getRows');
            foreach ($aItems as $key => $aItem) {
                if(isset($aItem['purchase_currency']))
                {
                    $aItems[$key]['purchase_symbol'] = Phpfox::getService('core.currency')->getSymbol($aItem['purchase_currency']);
                }
                else{
                    $aItems[$key]['purchase_symbol'] = '';
                }
                if($bGetMore)
                {
                    $iUserId = $aItem['user_id'];
                    $iFromUserId = $aItem['from_user_id'];
                    $aItems[$key]['relation'] = '';
                    $aRelation = [];
                    while ($iUserId != $iFromUserId) {
                        // insert first user id as client id, next is middle assoc
                        $aUser = Phpfox::getService('user')->getUser($iFromUserId);
                        $aRelation[] = $iFromUserId;
                        if($aUser){
                            $aItems[$key]['relation'] .= '<a href="'.Phpfox::getLib('url')->makeUrl($aUser['user_name']).'" title="'.$aUser['full_name'].'">'.Phpfox::getLib('parse.output')->shorten($aUser['full_name'],10, '...').'</a> > ';
                        }
                        else{
                            $aItems[$key]['relation'] .= _p('anonymous_user'). ' > ';
                        }
                        $aAssoc = Phpfox::getService('yncaffiliate.affiliate.affiliate')->getAssoc($iFromUserId);
                        if($aAssoc){
                            $iFromUserId = $aAssoc['user_id'];
                        }
                        else{
                            break;
                        }
                    }
                    $aItems[$key]['relation'] .= _p('me').' (Level '.count($aRelation).')';
                }
            }
        }
        return [$iCount,$aItems];
    }
    public function countCommission($sWhere)
    {
        return db()->select('COUNT(*)')
                    ->from($this->_sTable)
                    ->where($sWhere)
                    ->execute('getField');
    }
    public function getTotalCommissionPoints($iUserId = null,$sStatus = null,$noStatus = null,$iRuleId = null)
    {
        $sWhere = '1=1';
        if($sStatus)
        {
            $sWhere .= ' AND status = \''.$sStatus.'\'';
        }
        if($iUserId)
        {
            $sWhere .= ' AND user_id ='.$iUserId;
        }
        if($noStatus)
        {
            $sWhere .= ' AND status != \''.$noStatus.'\'';
        }
        if($iRuleId)
        {
            $sWhere .= ' AND rule_id = '.$iRuleId;
        }
        return db()->select('SUM(commission_points)')
                    ->from($this->_sTable)
                    ->where($sWhere)
                    ->execute('getField');
    }
    public function getTotalCommissionAmount($iUserId = null)
    {
        if(!$iUserId)
        {
            $iUserId = Phpfox::getUserId();
        }
        return db()->select('SUM(commission_amount)')
            ->from($this->_sTable)
            ->where('status = \'approved\' AND user_id ='.$iUserId)
            ->execute('getField');
    }

    public function getDataForLineChart($iUserId = null,$sLabel, $iFromTimestamp,$iToTimestamp,$sStatus,$sData,$sGroupBy,$sLabelData)
    {
        if($sLabel == 'levels')
        {
            $sWhere = 'yc.from_user_id IN ('.$sLabelData.') AND yc.time_stamp >='.$iFromTimestamp.' AND yc.time_stamp <='.$iToTimestamp;
            $sGroup = '';
        }
        elseif($sLabel == 'rules')
        {
            $sWhere = 'yr.rule_id = '.$sLabelData.' AND yc.time_stamp >='.$iFromTimestamp.' AND yc.time_stamp <='.$iToTimestamp;
            $sGroup = 'yc.rule_id,';
        }
        if($iUserId)
        {
            $sWhere .=' AND yc.user_id = '.$iUserId;
        }
        if($sStatus != 'all')
        {
            $sWhere .=' AND yc.status = \''.$sStatus.'\'';
        }
        else{
            $sWhere .= ' AND yc.status IN (\'approved\',\'delaying\',\'waiting\')';
        }
        $sSelect = 'yr.rule_title,yr.rule_name,';
        if($sData == 'number_transaction')
        {
            $sSelect .= 'COUNT(yc.commission_id) as total';
        }
        elseif($sData == 'earning')
        {
            $sSelect .= 'SUM(yc.commission_points) as total';
        }

        $sOrder = 'yc.commission_id';
        switch ($sGroupBy)
        {
            case 'day':
                $sSelect .= ',DAY(FROM_UNIXTIME(yc.time_stamp)) as day,MONTH(FROM_UNIXTIME(yc.time_stamp)) as month,YEAR(FROM_UNIXTIME(yc.time_stamp)) as year';
                $sGroup .= 'DAY(FROM_UNIXTIME(yc.time_stamp)),MONTH(FROM_UNIXTIME(yc.time_stamp)),YEAR(FROM_UNIXTIME(yc.time_stamp))';
                $sOrder = 'YEAR(FROM_UNIXTIME(yc.time_stamp)),MONTH(FROM_UNIXTIME(yc.time_stamp)),DAY(FROM_UNIXTIME(yc.time_stamp))';
                break;
            case 'week':
                $sSelect .= ',WEEKOFYEAR(FROM_UNIXTIME(yc.time_stamp)) as week,YEAR(FROM_UNIXTIME(yc.time_stamp)) as year';
                $sGroup .= 'WEEKOFYEAR(FROM_UNIXTIME(yc.time_stamp)),YEAR(FROM_UNIXTIME(yc.time_stamp))';
                $sOrder = 'YEAR(FROM_UNIXTIME(yc.time_stamp)),WEEKOFYEAR(FROM_UNIXTIME(yc.time_stamp))';

                break;
            case 'month':
                $sSelect .= ',MONTH(FROM_UNIXTIME(yc.time_stamp)) as month,YEAR(FROM_UNIXTIME(yc.time_stamp)) as year';
                $sGroup .= 'MONTH(FROM_UNIXTIME(yc.time_stamp)),YEAR(FROM_UNIXTIME(yc.time_stamp))';
                $sOrder = 'YEAR(FROM_UNIXTIME(yc.time_stamp)),MONTH(FROM_UNIXTIME(yc.time_stamp))';
                break;
            case 'year':
                $sSelect .= ',YEAR(FROM_UNIXTIME(yc.time_stamp)) as year';
                $sGroup .= 'YEAR(FROM_UNIXTIME(yc.time_stamp))';
                $sOrder = 'YEAR(FROM_UNIXTIME(yc.time_stamp))';
                break;
        }
        $aDatas = db()->select($sSelect)
                    ->from($this->_sTable,'yc')
                    ->join(Phpfox::getT('yncaffiliate_rules'),'yr','yr.rule_id = yc.rule_id')
                    ->where($sWhere)
                    ->group($sGroup)
                    ->order($sOrder)
                    ->execute('getRows');
        $aNewData = [];
        if(count($aDatas))
        {


                switch ($sGroupBy) {
                    case 'day':
                        foreach($aDatas as $aData) {
                            $aNewData[$aData['month'] . '/' . $aData['day'] . '/' . $aData['year']] = $aData['total'];
                        }
                        break;
                    case 'week':
                        foreach($aDatas as $aData) {
                            $aNewData[_p('week').' '.$aData['week']] = $aData['total'];
                        }
                        break;
                    case 'month':
                        foreach($aDatas as $aData) {
                            $aNewData[$aData['month'] . '/' . $aData['year']] = $aData['total'];
                        }
                        break;
                    case 'year':
                        foreach($aDatas as $aData) {
                            $aNewData[$aData['year']] = $aData['total'];
                        }
                        break;

            }
        }
        return $aNewData;
    }
    public function getDataForPieChart($iUserId = null,$sLabel, $iFromTimestamp,$iToTimestamp,$sStatus,$sData,$sLabelData)
    {
        if($sLabel == 'levels')
        {
            $sWhere = 'yc.from_user_id IN ('.$sLabelData.') AND yc.time_stamp >='.$iFromTimestamp.' AND yc.time_stamp <='.$iToTimestamp;
        }
        elseif($sLabel == 'rules')
        {
            $sWhere = 'yr.rule_id = '.$sLabelData.' AND yc.time_stamp >='.$iFromTimestamp.' AND yc.time_stamp <='.$iToTimestamp;
        }
        if($iUserId)
        {
            $sWhere .=' AND yc.user_id = '.$iUserId;
        }
        if($sStatus != 'all')
        {
            $sWhere .=' AND yc.status = \''.$sStatus.'\'';
        }
        else{
            $sWhere .= ' AND yc.status IN (\'approved\',\'delaying\',\'waiting\')';
        }
        $sSelect = 'yr.rule_title,yr.rule_name,';
        if($sData == 'number_transaction')
        {
            $sSelect .= 'COUNT(yc.commission_id) as total';
        }
        elseif($sData == 'earning')
        {
            $sSelect .= 'SUM(yc.commission_points) as total';
        }

        $sOrder = 'yc.commission_id';
        $aDatas = db()->select($sSelect)
            ->from($this->_sTable,'yc')
            ->join(Phpfox::getT('yncaffiliate_rules'),'yr','yr.rule_id = yc.rule_id')
            ->where($sWhere)
            ->order($sOrder)
            ->execute('getRow');
        return $aDatas;
    }
}