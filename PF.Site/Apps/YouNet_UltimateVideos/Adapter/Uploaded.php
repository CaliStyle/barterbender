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
use Phpfox_Service;

class Uploaded extends Abstracts
{
    protected $_params;

    public function compileVideo($params)
    {
        $video_id = $params['video_id'];
        $location = $params['location'];
        $location1 = $params['location1'];
        $view = $params['view'];
        $duration = $params['duration'];
        $mobile = $params['mobile'];
        $video = Phpfox::getService('ultimatevideo')->getVideoForEdit($params['video_id']);
        $class = "";
        if ($video['image_path']) {
            $imagePath = $video['image_server_id'] == -1 ? (Phpfox::getParam('ultimatevideo.ynuv_video_s3_url') . $video['image_path'])  : Phpfox::getLib('image.helper')->display(array(
                'server_id' => $video['image_server_id'],
                'path' => 'core.url_pic',
                'file' => $video['image_path'],
                'suffix' => '_500',
                'max_width' => 500,
                'max_height' => 500,
                'return_url' => true
            ));
        } else {
            $imagePath = Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/YouNet_UltimateVideos/assets/image/noimg_video.jpg';
        }
        if ($location1) {
            if ($mobile) {
                $class = "video-js vjs-default-skin";
            }
            $embedded = '
			 <video id="player_' . $video_id . '" class="ultimatevideo-player ' . $class . '" controls
				 preload="auto" poster="' . $imagePath . '"
				 data-setup="{}">
				  <source src="' . $location1 . '" type="video/mp4">
				</video>';
        } else {
            $embedded = "
		  <div id='videoFrame" . $video_id . "'></div>
		  <script type='text/javascript'></script>";
        }
        return $embedded;
    }

    public function extractVideo($params)
    {
        $video_id = $params['video_id'];
        $location = $params['location'];
        $location1 = $params['location1'];
        $view = $params['view'];
        $duration = $params['duration'];
        $mobile = $params['mobile'];
        $video = Phpfox::getService('ultimatevideo')->getVideoForEdit($params['video_id']);
        $class = "";
        if ($video['image_path']) {
            $imagePath = Phpfox::getLib('image.helper')->display(array(
                'server_id' => $video['image_server_id'],
                'path' => 'core.url_pic',
                'file' => $video['image_path'],
                'suffix' => '_500',
                'max_width' => 500,
                'max_height' => 500,
                'return_url' => true
            ));
        } else {
            $imagePath = Phpfox::getParam('core.path_actual') . 'PF.Site/Apps/YouNet_UltimateVideos/assets/image/noimg_video.jpg';
        }
        if ($location1) {
            if ($mobile) {
                $class = "video-js vjs-default-skin";
            }
            $embedded = '
			 <video id="player_' . $video_id . '" class="ynultimatevideo-player ' . $class . '" controls
				 preload="auto" poster="' . $imagePath . '"
				 data-setup="{}" style="display:none;" data-type="3" width="766" height="426">
				 <source src="' . $location1 . '" type="video/mp4">
				</video>';
        } else {
            $embedded = '
			 <video id="player_' . $video_id . '" class="ynultimatevideo-player ' . $class . '" controls
				 preload="auto" poster="' . $imagePath . '"
				 data-setup="{}" style="display:none;" data-type="3" width="766" height="426">
				 <source src="' . $location . '" type="video/mp4">
				</video>';
        }
        return $embedded;
    }

    public function setParams($options)
    {
        foreach ($options as $key => $value) {
            $this->_params[$key] = $value;
        }
    }

    public function getVideoLargeImage()
    {
    }

    public function getVideoDescription()
    {
    }

    public function getVideoTitle()
    {
    }

    public function getVideoDuration()
    {
    }

    public function isValid()
    {
    }

    public function fetchLink()
    {
    }
}
