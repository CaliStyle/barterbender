<?php

require_once '../../cli.php';
require_once 'libs/function.php';


$iId = $_GET['id'];

$aApplication = Phpfox::getService('jobposting.application')->get($iId);
if(!$aApplication)
{
    Phpfox::getLib('url')->send($_SERVER['HTTP_REFERER'], null, _p('jobposting.unable_to_find_the_resume_you_want_to_download'));
}

$bDelete = false; // delete file after download

if($aApplication['resume_type'] == 1)
{
    $bDelete = true;
    
    $sCss = Phpfox::getService('jobposting.resume')->getCss();
    
    $sHtml = Phpfox::getService('jobposting.resume')->buildHtml($aApplication['resume']);
    if(!$sHtml)
    {
        Phpfox::getLib('url')->send($_SERVER['HTTP_REFERER'], null, _p('jobposting.unable_to_find_the_resume_you_want_to_download'));
    }
    
    $sFileName = 'Resume_of_application_'.$iId.(!empty($aApplication['name']) ? '_'.$aApplication['name'] : '').'.pdf';
    $sFileName = clean($sFileName);
    $sFile = Phpfox::getParam('core.dir_pic').'jobposting'.PHPFOX_DS.md5($sFileName.PHPFOX_TIME.uniqid()).'.pdf';
    
    $postUrl = Phpfox::getParam('core.url_module').'jobposting/static/php/resume2pdf.php';
    $postField = http_build_query(array(
        'file' => $sFile,
        'html' => $sHtml,
        'css' => $sCss
    ));
    
    curlPost($postUrl, $postField);
}
else
{
    $sFile = Phpfox::getParam('core.dir_pic').'jobposting'.PHPFOX_DS.$aApplication['resume'];
    $ext = pathinfo($sFile, PATHINFO_EXTENSION);
    
    if (Phpfox::getParam('core.allow_cdn') && $aApplication['server_id'] > 0)
	{
		$sFile = Phpfox::getLib('cdn')->getUrl(str_replace(PHPFOX_DIR, Phpfox::getParam('core.path'), $sFile), $aApplication['server_id']); 
	}
    
    $sFileName = !empty($aApplication['file_name']) ? $aApplication['file_name'] : 'Resume_of_application_'.$iId.(!empty($aApplication['name']) ? '_'.$aApplication['name'] : '').'.'.$ext;
    $sFileName = clean($sFileName);
}

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
    Phpfox::getLib('url')->send($_SERVER['HTTP_REFERER'], null, _p('jobposting.unable_to_find_the_resume_you_want_to_download'));
}
