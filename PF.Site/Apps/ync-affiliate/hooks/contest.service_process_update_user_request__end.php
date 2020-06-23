<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 3/13/17
 * Time: 17:36
 */
if(Phpfox::isModule('yncaffiliate') && $iContestId && $iTransactionId)
{
    $aTransaction = $this->database()->select('*')
        ->from(Phpfox::getT('contest_transaction'))
        ->where('transaction_id = ' . $iTransactionId)
        ->execute('getSlaveRow');
    if($aTransaction)
    {
        foreach ($aService as $sService) {
            switch ($sService) {
                case 'publish':
                    $iPublishFee = Phpfox::getService('yncaffiliate.helper')->getUserParam('contest.contest_publish_fee',$aTransaction['user_id']);
                    if($iPublishFee)
                    {
                        Phpfox::getService('yncaffiliate.commission.process')->handlePayment($aTransaction['user_id'],['amount'=> $iPublishFee,'currency_id' => $aTransaction['currency']],'publish_contest','contest');
                    }
                    break;
                case 'premium':
                    $iPremiumFee = Phpfox::getService('yncaffiliate.helper')->getUserParam('contest.contest_premium_fee',$aTransaction['user_id']);
                    if($iPremiumFee)
                    {
                        Phpfox::getService('yncaffiliate.commission.process')->handlePayment($aTransaction['user_id'],['amount'=> $iPremiumFee,'currency_id' => $aTransaction['currency']],'premium_contest','contest');
                    }
                    break;
                case 'feature':
                    $iFeatureFee = Phpfox::getService('yncaffiliate.helper')->getUserParam('contest.contest_feature_fee',$aTransaction['user_id']);
                    if($iFeatureFee)
                    {
                        Phpfox::getService('yncaffiliate.commission.process')->handlePayment($aTransaction['user_id'],['amount'=> $iFeatureFee,'currency_id' => $aTransaction['currency']],'feature_contest','contest');
                    }
                    break;
                case 'ending_soon':
                    $iEndingSoonFee = Phpfox::getService('yncaffiliate.helper')->getUserParam('contest.contest_ending_soon_fee',$aTransaction['user_id']);
                    if($iEndingSoonFee)
                    {
                        Phpfox::getService('yncaffiliate.commission.process')->handlePayment($aTransaction['user_id'],['amount'=> $iEndingSoonFee,'currency_id' => $aTransaction['currency']],'ending_soon_contest','contest');
                    }
                    break;
                default:
                    break;
            }
        }
    }
}