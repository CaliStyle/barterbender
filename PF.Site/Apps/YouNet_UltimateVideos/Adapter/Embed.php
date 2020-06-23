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

class Embed extends Abstracts
{
    public function compileVideo($params)
    {
        $video_id = $params['video_id'];
        $code = $params['code'];
        $view = $params['view'];
        $videoEmbedded = "";
        $mobile = $params['mobile'];
        $autoplay = !$mobile && $view;
        $slide = (isset($params['slide'])) ? $params['slide'] : false;
        if ($code) {
            // check facebook url
            $regex = "/http(?:s?)%3A\%2F\%2F(?:www\.|web\.|m\.)?facebook\.com\%2F([A-z0-9\.]+)\%2Fvideos(?:\%2F[0-9A-z].+)?\%2F(\d+)(?:.+)?$/";
            preg_match($regex, $code, $matches);
            if (count($matches) > 2) {
                $code = $matches[2];
                if ($code) {
                    $code = '//www.facebook.com/video/embed?video_id=' . $code;
                }
            }
            $videoFrame = $video_id . "_" . $params['count_video'];
            $videoEmbedded = '<iframe
                title="Embed video player"
                id="' . ($slide ? "player_" . $video_id : "videoFrame" . $videoFrame) . '"
                class="' . (!$slide ? 'vimeo_iframe' . ($view ? "_big" : "_small") : "") . ($slide ? " ynultimatevideo-player" : "") . '"' .
                'src="' . $code . '"' . ($autoplay ? "&autoplay=1" : "") . '
                frameborder="0"
                allowfullscreen=""
                scrolling="no"' . ($slide ? " style=\"display:none;\"" : "") . '>
               </iframe>';
        }
        return $videoEmbedded;
    }

    public function isValid()
    {
        if (array_key_exists('link', $this->_params)) {
            preg_match('/(<iframe.*? src=(\"|\'))(.*?)((\"|\').*)/', $this->_params['link'], $matches);
            if (count($matches) > 2) {
                return true;
            }
        }
        return false;
    }


    public function extractVideo($params)
    {
        return $this->compileVideo($params);
    }

    public function getVideoLargeImage()
    {
    }

    public function getVideoDuration()
    {
    }

    public function getVideoTitle()
    {
    }

    public function getVideoDescription()
    {
    }

    public function fetchLink()
    {
    }
}