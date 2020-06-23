<?php

require_once 'cli.php';



require_once 'libs/function.php';


$iResumeId = $_GET['id'];

$aResume = Phpfox::getService('resume.basic')->getQuick($iResumeId);
if(!$aResume)
{
    Phpfox::getLib('url')->send($_SERVER['HTTP_REFERER'], null, _p('resume.unable_to_find_resume_file_to_download'));
}
/*
list($iCnt, $aApplications) = Phpfox::getService('jobposting.application')->getByJobId($iId, '', '');
if(!$aApplications)
{
    Phpfox::getLib('url')->send($_SERVER['HTTP_REFERER'], null, _p('jobposting.this_job_does_not_have_application'));
}*/

if (Phpfox::isModule('resume'))
{
    $sCss = Phpfox::getService('resume.pdfresume')->getCss();
}

$aFiles = array();
$sFile = '';

$sHtml = Phpfox::getService('resume.pdfresume')->buildHtml($aResume['resume_id']);



if($sHtml)
{
    $sFileName = 'Resume_'.$aResume['resume_id'].(!empty($aResume['headline']) ? '_'.$aResume['headline'] : '').'.pdf';
    $sFileName = clean($sFileName);
    $sFile = Phpfox::getParam('core.dir_pic').'resume'.PHPFOX_DS.md5($sFileName.PHPFOX_TIME.uniqid()).'.pdf';

    $postUrl = Phpfox::getParam('core.path_file').'module/resume/static/php/resume2pdf.php';

    $postField = http_build_query(array(
        'file' => $sFile,
        'html' => base64_encode($sHtml),
        'css' => $sCss,
        'core_path' => Phpfox::getParam('core.path')
    ));



    curlPost($postUrl, $postField);


    if(is_file($sFile))
    {
        $aFiles[] = array(
            'filename' => $sFile,
            'localname' => $sFileName,
            'bdelete' => true
        );
    }
}


if(count($aFiles)>0)
{
    $File = $aFiles[0];
    download($File['filename'], $File['localname']);
}
else
{
    //Phpfox::getLib('url')->send($_SERVER['HTTP_REFERER'], null, _p('resume.unable_to_find_resume_file_to_download'));
}
