<?php

require_once '../../cli.php';
require_once 'libs/function.php';

$iId = $_GET['id'];
$aJob = Phpfox::getService('jobposting.job')->getGeneralInfo($iId);
if(!$aJob)
{
    Phpfox::getLib('url')->send($_SERVER['HTTP_REFERER'], null, _p('unable_to_find_the_job_you_want_to_download_resumes'));
}

list($iCnt, $aApplications) = Phpfox::getService('jobposting.application')->getByJobId($iId, '', '');
if(!$aApplications)
{
    Phpfox::getLib('url')->send($_SERVER['HTTP_REFERER'], null, _p('this_job_does_not_have_application'));
}
$sCss = '';
if (Phpfox::isModule('resume'))
{
    $sCss = Phpfox::getService('jobposting.resume')->getCss();
}

$aFiles = array();
$sFile = '';
foreach($aApplications as $k=>$aApplication)
{
    if($aApplication['resume_type'] == 1 && !empty($aApplication['resume']))
    {
        $sHtml = Phpfox::getService('jobposting.resume')->buildHtml($aApplication['resume']);
        if(!$sHtml)
        {
            continue;
        }
        
        $sFileName = 'Resume_of_application_'.$aApplication['application_id'].(!empty($aApplication['name']) ? '_'.$aApplication['name'] : '').'.pdf';
        $sFileName = clean($sFileName);
        $sFile = Phpfox::getParam('core.dir_pic').'jobposting'.PHPFOX_DS.md5($sFileName.PHPFOX_TIME.uniqid()).'.pdf';
        $postUrl = Phpfox::getParam('core.path_file').'module/jobposting/static/php/resume2pdf.php';

        $postField = http_build_query(array(
            'file' => $sFile,
            'html' => $sHtml,
            'css' => $sCss
        ));
        
        curlPost($postUrl, $postField);
    }
    else if(!empty($aApplication['resume']))
    {
        $sFile = Phpfox::getParam('core.dir_pic').'jobposting'.PHPFOX_DS.$aApplication['resume'];
        $ext = pathinfo($sFile, PATHINFO_EXTENSION);
        
        if (Phpfox::getParam('core.allow_cdn') && $aApplication['server_id'] > 0)
        {
            $sFileUrl = Phpfox::getLib('cdn')->getUrl(str_replace(PHPFOX_DIR, Phpfox::getParam('core.path'), $sFile), $aApplication['server_id']);
            $sFile = Phpfox::getParam('core.dir_pic').'jobposting'.PHPFOX_DS.md5($sFile.PHPFOX_TIME.uniqid()).'_temp.'.$ext;
            file_put_contents($sFile, file_get_contents($sFileUrl));
        }
        
        $sFileName = !empty($aApplication['file_name']) ? $aApplication['file_name'] : 'Resume_of_application_'.$aApplication['application_id'].(!empty($aApplication['name']) ? '_'.$aApplication['name'] : '').'.'.$ext;
        $sFileName = clean($sFileName);
    }
    
    if(is_file($sFile) || Phpfox::getParam('core.allow_cdn'))
    {
        $aFiles[] = array(
            'filename' => $sFile,
            'localname' => $sFileName,
            'bdelete' => ($aApplication['resume_type'] == 1 || strpos($sFile, '_temp') !== false) ? true : false
        );
    }
}
$sZipFileName = 'Resumes_of_job_'.$iId.(!empty($aJob['title']) ? '_'.$aJob['title'] : '').'.zip';
$sZipFileName = clean($sZipFileName);
$sZipFile = Phpfox::getParam('core.dir_pic').'jobposting'.PHPFOX_DS.md5($sZipFileName.PHPFOX_TIME.uniqid()).'.zip';
if(create_zip($aFiles, $sZipFile, false))
{
    foreach($aFiles as $aFile)
    {
        if ($aFile['bdelete'])
        {
            @unlink($aFile['filename']);
        }
    }

    download($sZipFile, $sZipFileName);
    @unlink($sZipFile);
}
else
{
    Phpfox::getLib('url')->send($_SERVER['HTTP_REFERER'], null, _p('unable_to_find_any_resume_for_download'));
}
