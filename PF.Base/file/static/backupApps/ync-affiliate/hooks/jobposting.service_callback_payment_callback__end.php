<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 3/14/17
 * Time: 10:23
 */
if(Phpfox::isModule('yncaffiliate') && $aTransaction)
{
    $sCurrency = (isset($aTransaction['currency'])) ? $aTransaction['currency'] : 'USD';
    switch($aTransaction['payment_type'])
    {
        case 1:
            $iFee = Phpfox::getParam('jobposting.jobposting_fee_to_sponsor_company');
            if($iFee)
            {
                $aPurchase = [
                    'amount' => $iFee,
                    'currency_id' => $sCurrency
                ];
                Phpfox::getService('yncaffiliate.commission.process')->handlePayment($aTransaction['user_id'],$aPurchase,'sponsor_company','jobposting');
            }
            break;
        case 2:
        case 3:
            if(isset($aTransaction['invoice']['package_data']) && count($aTransaction['invoice']['package_data'])) {
                foreach ($aTransaction['invoice']['package_data'] as $iDataId) {
                    $aPackage = Phpfox::getService('jobposting.package')->getPackageByDataId($iDataId);
                    if($aPackage['fee'])
                    {
                        $aPurchase = [
                            'amount' => $aPackage['fee'],
                            'currency_id' => $sCurrency
                        ];
                        Phpfox::getService('yncaffiliate.commission.process')->handlePayment($aTransaction['user_id'],$aPurchase,'buy_job_package','jobposting');
                    }
                }
            }
            break;
        case 4:
            if(isset($aTransaction['invoice']['package_data']) && count($aTransaction['invoice']['package_data'])) {
                foreach ($aTransaction['invoice']['package_data'] as $iDataId) {
                    $aPackage = Phpfox::getService('jobposting.package')->getPackageByDataId($iDataId);
                    if($aPackage['fee'])
                    {
                        $aPurchase = [
                            'amount' => $aPackage['fee'],
                            'currency_id' => $sCurrency
                        ];
                        Phpfox::getService('yncaffiliate.commission.process')->handlePayment($aTransaction['user_id'],$aPurchase,'buy_job_package','jobposting');
                    }
                }
            }
            $iFeatureFee = Phpfox::getParam('jobposting.fee_feature_job');
            if($iFeatureFee)
            {
                $aPurchase = [
                    'amount' => $iFeatureFee,
                    'currency_id' => $sCurrency
                ];
                Phpfox::getService('yncaffiliate.commission.process')->handlePayment($aTransaction['user_id'],$aPurchase,'feature_job','jobposting');
            }
            break;
        case 5:
            $iFeatureFee = Phpfox::getParam('jobposting.fee_feature_job');
            if($iFeatureFee)
            {
                $aPurchase = [
                    'amount' => $iFeatureFee,
                    'currency_id' => $sCurrency
                ];
                Phpfox::getService('yncaffiliate.commission.process')->handlePayment($aTransaction['user_id'],$aPurchase,'feature_job','jobposting');
            }
            break;
        case 6:
            break;
        case 7:
            if(isset($aTransaction['invoice']['package_data']))
            {
                $aPackage = Phpfox::getService('jobposting.applyjobpackage')->getPackageByDataId($aTransaction['invoice']['package_data']);
                if($aPackage['fee'])
                {
                    $aPurchase = [
                        'amount' => $aPackage['fee'],
                        'currency_id' => $sCurrency
                    ];
                    Phpfox::getService('yncaffiliate.commission.process')->handlePayment($aTransaction['user_id'],$aPurchase,'buy_apply_job_package','jobposting');

                }
            }
            break;
    }

}
?>