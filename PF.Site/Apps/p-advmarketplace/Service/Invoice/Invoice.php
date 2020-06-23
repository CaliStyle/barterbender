<?php
namespace Apps\P_AdvMarketplace\Service\Invoice;

use Phpfox_Service;
use Phpfox;

class Invoice extends Phpfox_Service
{
    public function getInvoicesForSeller($userId = null, $conditions = [], $page = 1, $size = 6)
    {
        if(empty($userId)) {
            $userId = Phpfox::getUserId();
        }

        $aRows = [];

        $iCnt = $this->database()->select('COUNT(*)')
            ->from(Phpfox::getT('advancedmarketplace_invoice'),'mi')
            ->join(Phpfox::getT('advancedmarketplace'), 't', 't.listing_id = mi.listing_id AND t.user_id = '. (int)$userId)
            ->where($conditions)
            ->execute('getSlaveField');
        if($iCnt)
        {
            $aRows = $this->database()->select('mi.*, t.title, ' . Phpfox::getUserField())
                ->from(Phpfox::getT('advancedmarketplace_invoice'),'mi')
                ->join(Phpfox::getT('advancedmarketplace'), 't', 't.listing_id = mi.listing_id AND t.user_id = '. (int)$userId)
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = mi.user_id')
                ->where($conditions)
                ->order('mi.time_stamp DESC')
                ->limit($page, $size)
                ->execute('getSlaveRows');

            foreach ($aRows as $iKey => $aRow) {
                switch ($aRow['status']) {
                    case 'completed':
                        $aRows[$iKey]['status_phrase'] = _p('advancedmarketplace.paid');
                        break;
                    case 'cancel':
                        $aRows[$iKey]['status_phrase'] = _p('advancedmarketplace.cancelled');
                        break;
                    case 'pending':
                        $aRows[$iKey]['status_phrase'] = _p('advancedmarketplace.pending_payment');
                        break;
                    default:
                        $aRows[$iKey]['status_phrase'] = _p('advancedmarketplace_pending');
                        break;
                }
                $aRows[$iKey]['listing_price'] = Phpfox::getService('core.currency')->getCurrency($aRow['price'], $aRow['currency_id']);
                $aRows[$iKey]['link'] = Phpfox::permalink('advancedmarketplace.detail',$aRow['listing_id'], $aRow['title']);
            }
        }

        return array($iCnt, $aRows);
    }
}