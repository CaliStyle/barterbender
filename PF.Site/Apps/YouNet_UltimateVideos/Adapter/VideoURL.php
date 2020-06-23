<?php

namespace Apps\YouNet_UltimateVideos\Adapter;

use Apps\YouNet_UltimateVideos\Adapter\Abstracts;
use Phpfox;
use Phpfox_Url;

class VideoURL extends Abstracts
{

    protected $_params;

    public function compileVideo($params)
    {
        $video_id = $params['video_id'];
        $location = $params['location'];
        $view = $params['view'];
        $mobile = $params['mobile'];
        $class = "";
        if ($mobile) {
            $class = "video-js vjs-default-skin";
        }
        $video = Phpfox::getService('ultimatevideo')->getVideoForEdit($params['video_id']);
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
        $embedded = '
		 <video id="player_' . $video_id . '" class="ultimatevideo-player ' . $class . '" controls
			 preload="none" poster="' . $imagePath . '"
			 data-setup="{}">
			 <source src="' . $location . '" type="video/mp4">
			</video>';
        return $embedded;
    }

    public function extractVideo($params)
    {
        $video_id = $params['video_id'];
        $location = $params['location'];
        $view = $params['view'];
        $mobile = $params['mobile'];
        $class = "";
        if ($mobile) {
            $class = "video-js vjs-default-skin";
        }
        $video = Phpfox::getService('ultimatevideo')->getVideoForEdit($params['video_id']);
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
        $embedded = '
		 <video id="player_' . $video_id . '" class="ynultimatevideo-player ' . $class . '" controls
			 preload="none" poster="' . $imagePath . '"
			 data-setup="{}" style="display:none;" data-type="5" width="766" height="426">
			 <source src="' . $location . '" type="video/mp4" >
			</video>';
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
        if (isset($this->_params) && array_key_exists('code', $this->_params)) {

            return $this->getVideoImage();
        }

        return null;
    }

    public function getVideoImage()
    {
        $iToken = rand();
        $duration = $this->getVideoDuration();
        $thumb_splice = $duration / 2;
        $tmpDir = PHPFOX_DIR_CACHE . 'ynultimatevideo';
        $thumbPathLarge = $tmpDir . DIRECTORY_SEPARATOR . $iToken . '_' . PHPFOX_TIME . '_vthumb_large.jpg';
        $ffmpeg_path = setting('ynuv_ffmpeg_path');

        if (!@file_exists($ffmpeg_path) || !@is_executable($ffmpeg_path)) {
            $output = null;
            $return = null;
            exec($ffmpeg_path . ' -version', $output, $return);
            if ($return > 0) {
                return 0;
            }
        }

        // Prepare output header
        $output = PHP_EOL;
        $output .= $this->_params['code'] . PHP_EOL;
        $output .= $thumbPathLarge . PHP_EOL;

        $thumbCommand = $ffmpeg_path . ' ' . '-i ' . escapeshellarg($this->_params['code']) . ' ' . '-f image2' . ' ' . '-ss ' . $thumb_splice . ' ' . '-vframes ' . '1' . ' ' . '-v 2' . ' ' . '-y ' . escapeshellarg($thumbPathLarge) . ' ' . '2>&1';
        // Process thumbnail
        $thumbOutput = $output . $thumbCommand . PHP_EOL . shell_exec($thumbCommand);
        // Check output message for success
        $thumbSuccess = true;
        if (preg_match('/video:0kB/i', $thumbOutput)) {
            $thumbSuccess = false;
        }

        // Resize thumbnail
        if ($thumbSuccess && is_file($thumbPathLarge)) {
            try {
                $iToken = rand();
                $sNewsPicStorage = Phpfox::getParam('core.dir_pic') . 'ynultimatevideo';
                if (!is_dir($sNewsPicStorage)) {
                    @mkdir($sNewsPicStorage, 0777, 1);
                    @chmod($sNewsPicStorage, 0777);
                }
                $ThumbNail = Phpfox::getLib('file')->getBuiltDir($sNewsPicStorage . PHPFOX_DS) . md5('image_' . $iToken . '_' . PHPFOX_TIME) . '%s.jpg';
                Phpfox::getLib('image')->createThumbnail($thumbPathLarge, sprintf($ThumbNail, ''), 250, 250);
                Phpfox::getLib('image')->createThumbnail($thumbPathLarge, sprintf($ThumbNail, '_' . 500), 500, 500);
                $sFileName = str_replace(Phpfox::getParam('core.dir_pic'), "", $ThumbNail);
                $sFileName = str_replace("\\", "/", $sFileName);
                unlink($thumbPathLarge);

                return $sFileName;
            } catch (\Exception $e) {
                unlink($thumbPathLarge);
            }
        }

        return null;
    }

    public function getVideoDuration()
    {
        $ffmpeg_path = setting('ynuv_ffmpeg_path');

        if (!@file_exists($ffmpeg_path) || !@is_executable($ffmpeg_path)) {
            $output = null;
            $return = null;
            exec($ffmpeg_path . ' -version', $output, $return);
            if ($return > 0) {
                return 0;
            }
        }

        // Prepare output header
        $fileCommand = $ffmpeg_path . ' ' . '-i ' . escapeshellarg($this->_params['code']) . ' ' . '2>&1';
        // Process thumbnail
        $fileOutput = shell_exec($fileCommand);
        // Check output message for success
        $infoSuccess = true;
        if (preg_match('/video:0kB/i', $fileOutput)) {
            $infoSuccess = false;
        }

        $duration = 0;
        // Resize thumbnail
        if ($infoSuccess) {
            // Get duration of the video to caculate where to get the thumbnail
            if (preg_match('/Duration:\s+(.*?)[.]/i', $fileOutput, $matches)) {
                list($hours, $minutes, $seconds) = preg_split('[:]', $matches[1]);
                $duration = ceil($seconds + ($minutes * 60) + ($hours * 3600));
            }
        }
        return $duration;
    }

    public function isValid()
    {
        if (isset($this->_params['link'])) {
            $valid = preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $this->_params['link']);
            $params = @pathinfo($this->_params['link']);
            if (isset($params['extension']) && (strtoupper($params['extension']) == 'FLV' || strtoupper($params['extension']) == 'MP4')) {
                return true;
            }
        }
        return false;
    }

    public static function getDefaultTitle()
    {
        return _p('Untitled video');
    }

    public function fetchLink()
    {
    }

    public function getVideoTitle()
    {
    }

    public function getVideoDescription()
    {
    }

}
