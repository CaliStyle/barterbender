<?php

if(!empty($aVals['value']['ynuv_video_s3_key']) && !empty($aVals['value']['ynuv_video_s3_secret']) && !empty($aVals['value']['ynuv_video_s3_bucket'])) {
    $region = 'us-east-2'; // default
    $oClient = new Aws\S3\S3Client([
        'region' => $region,
        'version' => 'latest',
        'credentials' => [
            'key' => $aVals['value']['ynuv_video_s3_bucket'],
            'secret' => $aVals['value']['ynuv_video_s3_bucket'],
        ],
    ]);
    $region = $oClient->determineBucketRegion($aVals['value']['ynuv_video_s3_bucket']);
    Phpfox::getLib('database')->update(':setting', ['value_actual' => $region], 'var_name="ynuv_video_s3_region" AND module_id="ultimatevideo"');

    Phpfox::getLib('cache')->remove('setting');
    Phpfox::getLib('cache')->remove('app_settings');

    $aNewSettings = Phpfox::getService('admincp.setting')->get($aCond);
    $this->template()
        ->assign(['aSettings' => $aNewSettings]);
}
