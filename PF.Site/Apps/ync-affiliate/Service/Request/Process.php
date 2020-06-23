<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 2/27/17
 * Time: 15:13
 */
namespace Apps\YNC_Affiliate\Service\Request;

use Phpfox;

class Process extends \Phpfox_Service
{
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('yncaffiliate_requests');
    }
    public function add($aVals)
    {
        $sConvertRate = setting('yncaffiliate.ynaf_points_conversion_rate');
        if(!$sConvertRate)
        {
            $iConvertRate = 1;
        }
        else{
            $aConvertRate = json_decode($sConvertRate,true);
            if(!isset($aConvertRate[$aVals['currency']]))
            {
                $iConvertRate = 1;
            }
            else{
                $iConvertRate = $aConvertRate[$aVals['currency']];
            }
        }
        $fPoint= isset($aVals['amount']) ? (float) $aVals['amount'] : 0.00;
        $fAmount = $fPoint * $iConvertRate;
        $sReason = isset($aVals['reason']) ? $this->preParse()->clean($aVals['reason']) : '';
        if(!empty($aVals['method']) && $aVals['method'] != 'activitypoints')
        {
            $aGateway = $this->database()
                ->select('*')
                ->from(Phpfox::getT('api_gateway'))
                ->where('gateway_id = \'' . $aVals['method'] . '\' AND is_active = 1')
                ->execute('getSlaveRow');
            if(empty($aGateway))
            {
                return false;
            }
        }
        $iRequestId = db()->insert($this->_sTable,[
            'user_id' => (int)Phpfox::getUserId(),
            'request_points' => $fPoint,
            'request_amount' => $fAmount,
            'request_status' => 'waiting',
            'request_currency' => $aVals['currency'],
            'request_reason' => $sReason,
            'request_method' => $aVals['method'],
            'request_method_title' => ($aVals['method'] != 'activitypoints') ? $aGateway['title'] : _p('activity_points'),
            'time_stamp' => time()
        ]);
        return $iRequestId;
    }
    public function delete($iId)
    {
        return db()->delete($this->_sTable,'request_id ='.$iId);
    }
    public function updateStatus($iRequestId, $sStatus,$sResponse = NULL)
    {
        $aRequest = Phpfox::getService('yncaffiliate.request')->get($iRequestId);
        if(!$aRequest)
        {
            return false;
        }
        if(!empty($sResponse))
        {
            db()->update($this->_sTable, array('request_response' => $sResponse), "request_id =".$iRequestId);
        }

        $bResult = db()->update($this->_sTable, array('request_status' => $sStatus, 'modify_time' => PHPFOX_TIME ), "request_id =".$iRequestId);

        if(Phpfox::isModule('notification') && ($sStatus == 'completed' || $sStatus == 'denied')){
            Phpfox::getService("notification.process")->add("yncaffiliate_request",$aRequest['request_id'], $aRequest['user_id'], Phpfox::getUserId());
        }
        return $bResult;
    }
    public function updateRequest($iRequestId,$sMessage)
    {
        if(!$iRequestId)
        {
            return false;
        }
        return db()->update($this->_sTable,['request_response' => Phpfox::getLib('parse.input')->clean($sMessage),'modify_time' => time()],'request_id ='.$iRequestId);
    }
}