<?php
/**
 * Created by PhpStorm.
 * User: dai
 * Date: 20/01/2017
 * Time: 18:24
 */

namespace Apps\YNC_Affiliate\Service\Materials;

use Phpfox;
use Phpfox_Error;
use Phpfox_File;
Class Process extends \Phpfox_Service
{
    public function __construct()
    {
        $this->_sTable = \Phpfox::getT('yncaffiliate_materials');
    }
    public function add($aVals)
    {
        $oFilter = Phpfox::getLib('parse.input');
        $oFile = \Phpfox_File::instance();
        if (isset($_FILES['image']['name']) && ($_FILES['image']['name'] != '')) {
            $aImage = $oFile->load('image', array('jpg', 'gif', 'png'));
            if (!Phpfox_Error::isPassed()) {
                return false;
            }

            if ($aImage === false) {
                return Phpfox_Error::set(_p('please_select_an_image_to_upload'));
            }
        }
        else{
            return Phpfox_Error::set(_p('please_select_an_image_to_upload'));
        }
        $aInsert = [
            'material_name' => $oFilter->clean($aVals['material_name'],255),
            'material_width' => $aVals['material_width'],
            'material_height' => $aVals['material_height'],
            'link' => $aVals['link'],
            'time_stamp' => PHPFOX_TIME
        ];
        $iId = db()->insert($this->_sTable,$aInsert);
        $this->upload($iId);
        return $iId;
    }
    public function update($aVals,$iId)
    {
        $oFilter = Phpfox::getLib('parse.input');
        $oFile = \Phpfox_File::instance();
        $aOldItem = $this->database()->select('*')
            ->from($this->_sTable)
            ->where('material_id = ' . (int) $iId)
            ->execute('getRow');
        $aUpdate = [
            'material_name' => $oFilter->clean($aVals['material_name'],255),
            'material_width' => $aVals['material_width'],
            'material_height' => $aVals['material_height'],
            'server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID'),
            'link' => $aVals['link'],
        ];
        db()->update($this->_sTable,$aUpdate,'material_id ='.(int)$iId);
        if (isset($_FILES['image']['name']) && ($_FILES['image']['name'] != '')) {
            $aImage = $oFile->load('image', array('jpg', 'gif', 'png'));
            if (!Phpfox_Error::isPassed()) {
                return false;
            }

            if ($aImage !== false) {
                $this->upload($iId);
            }
        }
        else{
            $sPicStorage = Phpfox::getParam('core.dir_pic') . 'yncaffiliate/';
            $oImage = \Phpfox_Image::instance();
            $sOldImage = $sPicStorage . sprintf($aOldItem['image_path'], '_'.$aOldItem['material_width'].'_'.$aOldItem['material_height']);
            $sOldOriginalImage = $sPicStorage . sprintf($aOldItem['image_path'],'');
            if (Phpfox::getParam('core.allow_cdn') && $aOldItem['server_id']) {
                \Phpfox_File::instance()->unlink($sOldOriginalImage);
                $sRealPath = Phpfox::getLib('cdn')->getUrl(str_replace(Phpfox::getParam('core.dir_pic'), Phpfox::getParam('core.url_pic'), $sOldImage), $aOldItem['server_id']);
                $sNewFile = $this->downloadImage($sRealPath,$aUpdate['material_width'],$aUpdate['material_height']);
                db()->update($this->_sTable,['image_path'=> $sNewFile],'material_id ='.(int)$iId);
                \Phpfox_File::instance()->unlink($sOldImage);
            }
            else{
                \Phpfox_File::instance()->unlink($sOldImage);
                $oImage->createThumbnail($sOldOriginalImage, $sPicStorage . sprintf($aOldItem['image_path'], '_'.$aUpdate['material_width'].'_'.$aUpdate['material_height']), $aUpdate['material_width'], $aUpdate['material_height'],false);
            }
        }
        return $iId;
    }
    public function upload($ItemId, $sFile = 'image')
    {

        $oFile = \Phpfox_File::instance();
        $oImage = \Phpfox_Image::instance();
        $sPicStorage = Phpfox::getParam('core.dir_pic') . 'yncaffiliate/';

        $aItem = $this->database()->select('*')
            ->from($this->_sTable)
            ->where('material_id = ' . (int) $ItemId)
            ->execute('getRow');
        if (!empty($aItem['image_path'])) {
            $oFile->unlink($sPicStorage . sprintf($aItem['image_path'],'_'.$aItem['material_width'].'_'.$aItem['material_height']));
            $oFile->unlink($sPicStorage . sprintf($aItem['image_path'],''));
        }

        if (!is_dir($sPicStorage)) {
            @mkdir($sPicStorage, 0777, 1);
            @chmod($sPicStorage, 0777);
        }

        $sFileName = $oFile->upload($sFile, $sPicStorage, rand());


        $oImage->createThumbnail($sPicStorage . sprintf($sFileName, ''), $sPicStorage . sprintf($sFileName, '_'.$aItem['material_width'].'_'.$aItem['material_height']), $aItem['material_width'], $aItem['material_height'],false);

//        @unlink($sPicStorage . sprintf($sFileName, ''));
        $this->database()->update($this->_sTable, array(
            'image_path' => $sFileName,
            'server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID'),
        ), 'material_id = '.$ItemId);
    }
    public function updateStatus($iId,$iStatus)
    {
        return db()->update($this->_sTable,['is_active' => $iStatus],'material_id ='.(int)$iId);
    }
    public function delete($iId)
    {
        $aItem = $this->database()->select('*')
            ->from($this->_sTable)
            ->where('material_id = ' . (int) $iId)
            ->execute('getRow');
        if($aItem)
        {
            $sPicStorage = Phpfox::getParam('core.dir_pic') . 'yncaffiliate/';
            if (!empty($aItem['image_path'])) {
                $sImagePath = $sPicStorage . sprintf($aItem['image_path'],'_'.$aItem['material_width'].'_'.$aItem['material_height']);
                \Phpfox_File::instance()->unlink($sImagePath);
                \Phpfox_File::instance()->unlink($sPicStorage . sprintf($aItem['image_path'],''));
            }
            if(db()->delete($this->_sTable,'material_id ='.(int)$iId))
            {
                return true;
            }
        }
        return false;
    }
    public function downloadImage($sImgUrl,$iWidth,$iHeight)
    {
        if (!$sImgUrl) {
            return '';
        }
        $pos = stripos($sImgUrl, ".bmp");
        if ($pos > 0) {
            return $sImgUrl;
        }
        //Check Folder Storage
        $sNewsPicStorage = Phpfox::getParam('core.dir_pic').'/yncaffiliate';
        if (!is_dir($sNewsPicStorage)) {
            @mkdir($sNewsPicStorage, 0777, 1);
            @chmod($sNewsPicStorage, 0777);
        }

        // Generate Image object and store image to the temp file
        $iToken = rand();
        $oImage = \Phpfox::getLib('request')->send($sImgUrl, array(), 'GET');

        if (empty($oImage) && (substr($sImgUrl, 0, 8) == 'https://')) {
            $sImgUrl = 'http://' . substr($sImgUrl, 8);
            $oImage = Phpfox::getLib('request')->send($sImgUrl, array(), 'GET');
        }
        $sTempImage = 'yncaffiliate_temp_thumbnail_' . $iToken . '_' . PHPFOX_TIME;
        \Phpfox::getLib('file')->writeToCache($sTempImage, $oImage);
        // Save image
        $ThumbNail = Phpfox::getLib('file')->getBuiltDir($sNewsPicStorage . PHPFOX_DS) . md5('image_' . $iToken . '_' . PHPFOX_TIME) . '%s.jpg';
        Phpfox::getLib('image')->createThumbnail(PHPFOX_DIR_CACHE . $sTempImage,sprintf($ThumbNail, '_' . $iWidth. '_' . $iHeight), $iWidth, $iHeight,true);
        @unlink(PHPFOX_DIR_CACHE . $sTempImage);

        $sFileName = str_replace(Phpfox::getParam('core.dir_pic'), "", $ThumbNail);
        $sFileName = str_replace("/yncaffiliate/", "", $sFileName);
        $sFileName = str_replace("\\", "/", $sFileName);
        // Return logo file
        return $sFileName;
    }
}