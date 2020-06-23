<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[YOUNET_COPPYRIGHT]
 * @author  		MinhTA
 * @package  		Module_socialad
 */

// Add and edit request both go here 
class Socialad_Component_Controller_Ad_Image extends Phpfox_Component 
{

	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		if (!Phpfox::isUser())
		{
			exit;
		}

		$sImageDir = Phpfox::getParam('core.dir_pic') . 'socialad' . PHPFOX_DS;

        if (!is_dir($sImageDir)) {
            @mkdir($sImageDir, 0777, 1);
            @chmod($sImageDir, 0777);
        }

        $aParams = Phpfox::getService('socialad.callback')->getUploadParams();
        $aParams['user_id'] = Phpfox::getUserId();
        $aParams['type'] = 'photo';
        $aImage = Phpfox::getService('user.file')->load('file', $aParams);
		if ($aImage === false)
		{
            echo json_encode([
                'error' => 1
            ]);
			exit;
		}			
		
		
		if ($sFileName = Phpfox::getLib('file')->upload('image', $sImageDir, Phpfox::getUserId() . uniqid()))
		{
			Phpfox::getService('socialad.ad.image')->renderThumbnail($sFileName);
            echo json_encode([
                'file_name' => $sFileName
            ]);
		}
		
		exit;


	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
	
	}

}

