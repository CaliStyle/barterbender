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

class Socialad_Service_Ad_Image extends Phpfox_Service
{
	private $_aImageSizes = array( 
		'html' => array(
			"width" => 100,
			"height" => 70,
			"suffix" => "_html",
		), 
		'feed' => array(
			"width" => 500,
			"height" => 500,
			"suffix" => "_feed",
		), 
		'block1' => array(
			"width" => 0,
			"height" => 0,
			"suffix" => "_block1",
		), 
		'block2' => array(
			"width" => 0,
			"height" => 0,
			"suffix" => "_block2",
		), 
		'block3' => array(
			"width" => 0,
			"height" => 0,
			"suffix" => "_block3",
		), 
		'block4' => array(
			"width" => 0,
			"height" => 0,
			"suffix" => "_block4",
		), 
		'block5' => array(
			"width" => 0,
			"height" => 0,
			"suffix" => "_block5",
		), 
		'block6' => array(
			"width" => 0,
			"height" => 0,
			"suffix" => "_block6",
		), 
		'block7' => array(
			"width" => 0,
			"height" => 0,
			"suffix" => "_block7",
		), 
		'block8' => array(
			"width" => 0,
			"height" => 0,
			"suffix" => "_block8",
		), 
		'block9' => array(
			"width" => 0,
			"height" => 0,
			"suffix" => "_block9",
		), 
		'block10' => array(
			"width" => 0,
			"height" => 0,
			"suffix" => "_block10",
		), 
		'block11' => array(
			"width" => 0,
			"height" => 0,
			"suffix" => "_block11",
		), 
		'block12' => array(
			"width" => 0,
			"height" => 0,
			"suffix" => "_block12",
		), 
		// banner keeps original size
	);

    public function __construct()
    {
    	$this->updateWidthHeightBlock();
		$this->_sImageDir = Phpfox::getParam('core.dir_pic'). 'socialad' . PHPFOX_DS;
		$this->_sImageUrl = Phpfox::getParam('core.url_pic'). 'socialad' . PHPFOX_DS;
		$this->_sImageTable = Phpfox::getT('socialad_image');
		$this->_sTempDir = $this->_sImageDir;


    }

    public function updateWidthHeightBlock(){
		$aBlocks = array(
			//1, 2, 3, 4, 5, 6, 7, 8, 9 ,10, 11, 12
			3
		);

		foreach($aBlocks as $idx){
			$block = null;
	    	$block = Phpfox::getParam('socialad.banner_image_width_height_block_' . $idx);
	    	$block = explode("|", $block);
	    	if(!isset($block[0])){
	    		$block[0] = 0;
	    	}
	    	if(!isset($block[1])){
	    		$block[1] = 0;
	    	}

	    	$this->_aImageSizes['block' . $idx]['width'] = $block[0];
	    	$this->_aImageSizes['block' . $idx]['height'] = $block[1];

		}
    }

	public function getImageSizes() {
		return $this->_aImageSizes;
	}


	public function getNoImageUrlOfHtml() {
		return  Phpfox::getParam('core.path'). 'module/socialad/static/image/html_noImage.jpg';
	}

	public function getNoImageUrlOfFeed() {
		return  Phpfox::getParam('core.path'). 'module/socialad/static/image/feed_noImage.jpg';
	}
	public function remove($sName) { 
		$sFullName = $this->_sImageDir .  $sName;
        Phpfox::getLib('file')->unlink($sFullName);
		return true;
	}

	public function getTempDir() {
		if(!is_dir($this->_sTempDir)) {
			mkdir($this->_sTempDir);
	   	}
		return $this->_sTempDir;
	}

	public function getImageDir()
	{
		if(!is_dir($this->_sImageDir)) {
			mkdir($this->_sImageDir);
	   	}
		return $this->_sImageDir;
	}

	public function getCoreImageUrl()
	{
	    $iServerId = Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID');
		 if (Phpfox::getParam('core.allow_cdn')  && $iServerId > 0)
		 {
			 return Phpfox::getLib('cdn')->getUrl($this->_sImageUrl,$iServerId);
		 }
		 else{
			 return $this->_sImageUrl;
		 }
		
		return $this->_sImageUrl;
	}

	public function getImagesByAdId($iAdId) {
		$aRows = $this->database()->select('*')
			->from($this->_sImageTable)
			->where('image_ad_id  = ' . $iAdId)
			->execute('getRows');

		return $aRows;
	}
	
	public function removeImageByAdId($iAdId) {
		$aImages = $this->getImagesByAdId($iAdId);
		foreach($aImages as $aImage) {
			foreach($this->_aImageSizes as $aSize) {
				$sImagePath = sprintf($aImages['image_path'], $aSize['suffix']);
				$this->remove($sImagePath);
			}
		}

		$this->database()->delete($this->_sImageTable, 'image_ad_id = ' . $iAdId);
		return true;
	}

	public function addImageUrl($aVals) {
		$this->removeImageByAdId($aVals['ad_id']);
		$aInsert = array(
			'image_ad_id' => $aVals['ad_id'],
			'image_path' => $aVals['image_path'],
			'image_user_id' => Phpfox::getUserId(),
            'image_server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID')
		);

		$this->database()->insert($this->_sImageTable, $aInsert);
	}

	public function getFullUrlFromPath($sPath,$sSuffix = '',$iServerId) {
        $sImageFullPath = Phpfox::getLib('image.helper')->display([
            'server_id' => $iServerId,
            'path' => 'core.url_pic',
            'file' => 'socialad'. PHPFOX_DS . $sPath,
            'suffix' => $sSuffix,
            'return_url' => true
        ]);
        return $sImageFullPath;

	}

	public function renderThumbnail($sImagePath) {
		// @todo change it later
		foreach($this->_aImageSizes as $aSize) {
			if((int)$aSize['width'] <= 0 || (int)$aSize['height'] <= 0){
		
				if (Phpfox::getParam('core.allow_cdn'))
				{
                    $aWidthHeight = getimagesize($this->_sImageDir . sprintf($sImagePath, ''));
					Phpfox::getLib('image')->createThumbnail($this->_sImageDir . sprintf($sImagePath, ''), $this->_sImageDir . sprintf($sImagePath, $aSize['suffix'] ), $aWidthHeight[0], $aWidthHeight[1]);
				}else{
					Phpfox::getLib('file')->copy($this->_sImageDir . sprintf($sImagePath, ''), $this->_sImageDir . sprintf($sImagePath, $aSize['suffix'] ));
				}		

			} else {
				Phpfox::getLib('image')->createThumbnail($this->_sImageDir . sprintf($sImagePath, ''), $this->_sImageDir . sprintf($sImagePath, $aSize['suffix'] ), $aSize['width'], $aSize['height']);
			}
		}
		return $sImagePath;
	}

	public function createAdImageFromItemImage($sItemImageFullPath) {
		$sDirPath = $this->_sTempDir;
		$sNewImageName = md5(Phpfox::getUserId() . PHPFOX_TIME . uniqid()) . '%s.' . Phpfox::getLib('file')->getFileExt($sItemImageFullPath);
		$sNewImageFullPath = $sDirPath . $sNewImageName;
		// cpy original image to ad image folder
		Phpfox::getLib('file')->copy($sItemImageFullPath, sprintf($sNewImageFullPath, ""));


		// get path to new copied file
		$sNewImagePath = str_replace($this->_sImageDir, "", $sNewImageFullPath);

		// send path to create thumb 
		$this->renderThumbnail($sNewImagePath);
		return $sNewImagePath;

	}

	public function copyItemFromTempToRealFolder($sTempPath) {
		$sTempImageFullPath = $sTempImageFullPath1 = $this->_sImageDir . $sTempPath;
		$sExt = Phpfox::getLib('file')->getFileExt($sTempImageFullPath);
		$iServerId = Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID');
        if(Phpfox::getParam('core.allow_cdn') && $iServerId > 0)
        {
            $sImgUrl = Phpfox::getLib('cdn')->getUrl(Phpfox::getParam('core.url_pic').'socialad'. PHPFOX_DS .sprintf($sTempPath,''),$iServerId);
            // Generate Image object and store image to the temp file
            $iToken = rand();
            $oImage = \Phpfox::getLib('request')->send($sImgUrl, array(), 'GET');

            if (empty($oImage) && (substr($sImgUrl, 0, 8) == 'https://')) {
                $sImgUrl = 'http://' . substr($sImgUrl, 8);
                $oImage = Phpfox::getLib('request')->send($sImgUrl, array(), 'GET');
            }
            $sTempImage = 'social_ads_temp_thumbnail_' . $iToken . '_' . PHPFOX_TIME;
            \Phpfox::getLib('file')->writeToCache($sTempImage, $oImage);
            $sTempImageFullPath = PHPFOX_DIR_CACHE . $sTempImage;
        }
		$sDirPath = Phpfox::getLib('file')->getBuiltDir($this->_sImageDir);
		$sNewImageName = md5(Phpfox::getUserId() . PHPFOX_TIME . uniqid()) . '%s.' . $sExt;
		$sNewImageFullPath = $sDirPath . $sNewImageName;


		$bReturn = Phpfox::getLib('file')->copy(sprintf($sTempImageFullPath, ''), sprintf($sNewImageFullPath, ""));
		$sNewImagePath = str_replace($this->_sImageDir, "", $sNewImageFullPath);
		$this->renderThumbnail($sNewImagePath);
		
		// remove all temporary images
		Phpfox::getLib('file')->unlink(sprintf($sTempImageFullPath1, ''));
        @unlink($sTempImageFullPath);
		foreach($this->_aImageSizes as $aSize) {
			Phpfox::getLib('file')->unlink(sprintf($sTempImageFullPath1, $aSize['suffix']));
		}
				
		
		return $sNewImagePath;
	}

	public function clearTempFolder() {
		$patterns = $this->_sTempDir . '*';
		$files = glob($patterns); // get all file names
		foreach($files as $file){ // iterate files
			  if(is_file($file))
				      unlink($file); // delete file
		}
	}
}



