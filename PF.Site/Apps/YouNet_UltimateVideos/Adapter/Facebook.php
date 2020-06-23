<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */

namespace Apps\YouNet_UltimateVideos\Adapter;

use Phpfox;
use Phpfox_Url;
use Apps\YouNet_UltimateVideos\Adapter\Abstracts;

class Facebook extends Abstracts
{
    public function extractCode()
    {
        $link = $this->_params['link'];
        $regex = "/http(?:s?):\/\/(?:www\.|web\.|m\.)?facebook\.com\/([A-z0-9\.]+)\/videos(?:\/[0-9A-z].+)?\/(\d+)(?:.+)?$/";
        preg_match($regex, $link, $matches);
        $code = $matches[2];
        return $code;
    }

    /**
     *
     * @return : false if the link is invalid, otherwise return an SimpleXMLElement object containing the video information
     */
    public function isValid()
    {
        if (array_key_exists('code', $this->_params)) {
            $code = $this->_params['code'];
        }
        if (empty($code) && array_key_exists('link', $this->_params)) {
            $code = $this->extractCode();
            $this->_params['code'] = $code;
        }
        if ($code) {
            $url = 'https://www.facebook.com/video/embed?video_id=' . $code;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            $data = curl_exec($ch);
            curl_close($ch);
            if ($data)
                return $code;
        }
        return false;
    }

    public function fetchLink()
    {
        $code = $this->isValid();
        $aData = Phpfox::getService('link')->getLink($this->_params['link']);
        if (!$code) {
            return false;
        } else {
            $this->_information = array();
            $this->_information['code'] = $code;
            $this->_information['title'] = $aData['title'];
            $this->_information['large-thumbnail'] = $aData['default_image'];
            $this->_information['description'] = $aData['description'];
            $this->_information['duration'] = "https://graph.facebook.com/$code?fields=length";
        }
        return true;
    }

    public function getVideoLargeImage()
    {
        if (empty($this->_information)) {
            $this->fetchLink();
        }
        if (array_key_exists('large-thumbnail', $this->_information)) {
            return $this->_fetchImage($this->_information['large-thumbnail']);
        }

        return null;
    }

    public function _fetchImage($photo_url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $photo_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        $data = curl_exec($ch);
        curl_close($ch);
        $iToken = rand();
        $sNewsPicStorage = Phpfox::getParam('core.dir_pic') . 'ynultimatevideo';
        if (!is_dir($sNewsPicStorage)) {
            @mkdir($sNewsPicStorage, 0777, 1);
            @chmod($sNewsPicStorage, 0777);
        }
        $sTempImage = 'ynultimatevideo_temp_thumbnail_' . $iToken . '_' . PHPFOX_TIME;
        \Phpfox::getLib('file')->writeToCache($sTempImage, $data);
        // Save image
        $ThumbNail = Phpfox::getLib('file')->getBuiltDir($sNewsPicStorage . PHPFOX_DS) . md5('image_' . $iToken . '_' . PHPFOX_TIME) . '%s.jpg';
        Phpfox::getLib('image')->createThumbnail(PHPFOX_DIR_CACHE . $sTempImage, sprintf($ThumbNail, ''), 1024, 1024);
        Phpfox::getLib('image')->createThumbnail(PHPFOX_DIR_CACHE . $sTempImage, sprintf($ThumbNail, '_' . 1024), 1024, 1024);
        Phpfox::getLib('image')->createThumbnail(PHPFOX_DIR_CACHE . $sTempImage, sprintf($ThumbNail, '_' . 500), 500, 500);
        Phpfox::getLib('image')->createThumbnail(PHPFOX_DIR_CACHE . $sTempImage, sprintf($ThumbNail, '_' . 250), 250, 250);
        @unlink(PHPFOX_DIR_CACHE . $sTempImage);

        $sFileName = str_replace(Phpfox::getParam('core.dir_pic'), "", $ThumbNail);
        $sFileName = str_replace("\\", "/", $sFileName);
        // Return logo file
        return $sFileName;
    }

    public function getVideoDuration()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->_information['duration']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        $data = curl_exec($ch);
        curl_close($ch);
        $aData = json_decode($data, true);
        if (isset($aData['length'])) {
            return sprintf("%s", $aData['length']);
        }
        return "";
    }

    public function getVideoTitle()
    {
        return $this->_information['title'];
    }

    public function getVideoDescription()
    {
        return empty($this->_information['description']) ? '' : $this->_information['description'];
    }

    public function getEmbededCode($code = null)
    {

    }

    public function compileVideo($params)
    {
        $video_id = $params['video_id'];
        $code = $params['code'];
        $videoFrame = $video_id . "_" . $params['count_video'];
        $embedded = '<iframe title="Facebook video player" id="videoFrame' . $videoFrame .
            '" class="facebook_iframe" src="//www.facebook.com/video/embed?video_id=' . $code
            . '"frameborder="0" allowfullscreen="" scrolling="no"></iframe>';
        return $embedded;
    }

    public function extractVideo($params)
    {
        $video_id = $params['video_id'];
        $code = $params['code'];
        $embedded = '
            <video id="player_' . $video_id . '" class="ynultimatevideo-player" data-type="1" width="764" height="426">
                <source type="video/facebook" src="//www.facebook.com/video/embed?video_id=' . $code . '" />
            </video>';

        return $embedded;
    }
}
