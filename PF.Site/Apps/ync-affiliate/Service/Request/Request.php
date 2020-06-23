<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 2/27/17
 * Time: 15:13
 */
namespace Apps\YNC_Affiliate\Service\Request;

use Phpfox;

class Request extends \Phpfox_Service
{
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('yncaffiliate_requests');
    }

    public function getTotalRequestPoints($sStatus = 'completed',$iUserId = null)
    {
        return db()->select('SUM(request_points)')
                    ->from($this->_sTable)
                    ->where('request_status IN ('.$sStatus.')'.(($iUserId) ? ' AND user_id = '.$iUserId : ''))
                    ->execute('getField');
    }
    public function getTotalRequestAmount($sStatus = 'completed',$iUserId = null)
    {
        if(!$iUserId)
        {
            $iUserId = Phpfox::getUserId();
        }
        return db()->select('SUM(request_amount)')
            ->from($this->_sTable)
            ->where('request_status IN ('.$sStatus.') AND user_id = '.$iUserId)
            ->execute('getField');
    }
    public function getRequest($aConds = [],$sSort = 'yr.request_id DESC',$iPage,$iLimit)
    {
        $sWhere = '1=1';
        if (count($aConds) > 0) {
            $sCond = implode('  ', $aConds);
            $sWhere .= ' ' . $sCond;
        }
        $iCount = db()->select('COUNT(*)')
                        ->from($this->_sTable,'yr')
                        ->join(Phpfox::getT('user'),'u','u.user_id = yr.user_id')
                        ->where($sWhere)
                        ->execute('getField');

        $aRequests = [];
        if($iCount)
        {
            $aRequests = db()->select('yr.*,'.Phpfox::getUserField())
                            ->from($this->_sTable,'yr')
                            ->join(Phpfox::getT('user'),'u','u.user_id = yr.user_id')
                            ->where($sWhere)
                            ->limit($iPage,$iLimit,$iCount)
                            ->order($sSort)
                            ->execute('getRows');
            foreach($aRequests as $key => $aRequest)
            {
                $aRequests[$key]['currency_symbol'] = Phpfox::getService('core.currency')->getSymbol($aRequest['request_currency']);
            }
        }
        return [$iCount,$aRequests];
    }
    public function get($iRequestId)
    {
        return db()->select('*')
                    ->from($this->_sTable)
                    ->where('request_id ='.$iRequestId)
                    ->execute('getRow');
    }
}