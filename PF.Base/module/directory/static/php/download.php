<?php

require_once '../../cli.php';
require_once 'libs/function.php';

$type = $_GET['type'];
switch ($type) {
    case 'business':
        $iBusinessId = $_GET['id'];
        $aBusiness = Phpfox::getService('directory')->getQuickBusinessById($iBusinessId);
        if(isset($aBusiness['business_id']) == false)
        {
            Phpfox::getLib('url')->send($_SERVER['HTTP_REFERER'], null, _p('directory.unable_to_find_the_business_you_wan_to_download'));
        }

        $bDelete = true; // delete file after download

        $sCss = Phpfox::getService('directory')->getCss();

        Phpfox::getBlock('directory.businesstodownload', array(
            'iBusinessId' => $iBusinessId, 
        ));

        $sHtml = Phpfox::getLib('ajax')->getContent();
        $sHtml = str_replace(array('[img]','[/img]'), array('<img src="','" />'), $sHtml);

        if(!$sHtml)
        {
            Phpfox::getLib('url')->send($_SERVER['HTTP_REFERER'], null, _p('directory.unable_to_find_the_business_you_wan_to_download'));
        }
        $sHtml =  stripslashes($sHtml);
        $sFileName = 'Business_' . $iBusinessId . (!empty($aBusiness['name']) ? '_' . $aBusiness['name'] : '') . '.pdf';
        $sFileName = clean($sFileName);
        $sFile = Phpfox::getParam('core.dir_pic') . 'yndirectory' . PHPFOX_DS . md5($sFileName . PHPFOX_TIME . uniqid()) . '.pdf';

        $postUrl = Phpfox::getService('directory') -> getStaticPath() .'module/directory/static/php/businesstopdf.php';
        $postField = http_build_query(array(
            'file' => $sFile,
            'html' => base64_encode($sHtml),
            'css' => $sCss
        ));
        
        curlPost($postUrl, $postField);

        if(is_file($sFile) || Phpfox::getParam('core.allow_cdn'))
        {
            download($sFile, $sFileName);
            if ($bDelete)
            {
                @unlink($sFile);
            }
        }
        else
        {
            Phpfox::getLib('url')->send($_SERVER['HTTP_REFERER'], null, _p('directory.unable_to_find_the_business_you_wan_to_download'));
        }

        break;
}

