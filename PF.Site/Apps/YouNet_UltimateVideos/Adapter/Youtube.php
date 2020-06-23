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
use DateTime;
use DateInterval;
use Apps\YouNet_UltimateVideos\Adapter\Abstracts;

class Youtube extends Abstracts
{
    public function extractCode()
    {
        $link = $this->_params['link'];
        $new_code = @pathinfo($link);
        $link = preg_replace("/#!/", "?", $link);

        // get v variable from the url
        $arr = array();
        $arr = @parse_url($link);
        $code = "code";
        $parameters = $arr["query"];
        parse_str($parameters, $data);
        $code = $data['v'];
        if ($code == "") {
            $code = $new_code['basename'];
        }

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
            $api_key = setting('ynuv_youtube_api_key', 'AIzaSyDpUPT_nafV_MFSAlc-8AH4e1Gy578iK0M');
            $url = "https://www.googleapis.com/youtube/v3/videos?id=$code&key=$api_key&part=snippet,contentDetails";
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
            $data = json_decode($data);
            if (empty($data->items)) {
                return false;
            } else {
                $jsonData = $data->items[0];
                return $jsonData;
            }
        }
        return false;
    }

    public function fetchLink()
    {
        $data = $this->isValid();
        if ($data === false) {
            return false;
        } else {
            $this->_information = array();
            $this->_information['title'] = sprintf("%s", $data->snippet->title);
            $this->_information['content'] = sprintf("%s", $data->snippet->description);
            $start = new DateTime('@0'); // Unix epoch
            $start->add(new DateInterval($data->contentDetails->duration));
            $duration = (int) $start->format('H') * 60 * 60 + (int) $start->format('i') * 60 + (int) $start->format('s');
            $this->_information['duration'] = sprintf("%s", $duration);
            $this->_information['description'] = sprintf("%s", $data->snippet->description);
            $thumbnails = end($data->snippet->thumbnails);
            $this->_information['large-thumbnail'] = sprintf("%s", $thumbnails->url);
        }
        return true;
    }

    public function getVideoLargeImage()
    {
        if (empty($this->_information)) {
            $this->fetchLink();
        }
        if (array_key_exists('large-thumbnail', $this->_information)) {
            return $this->_information['large-thumbnail'];
        }

        return null;
    }

    public function getVideoDuration()
    {
        return $this->_information['duration'];
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
        if (!$code) {
            if (array_key_exists('code', $this->_params)) {
                $code = $this->_params['code'];
            } else if (array_key_exists('link', $this->_params)) {
                $code = $this->extractCode();
            }
        }

        if ($code) {
            $url = "https://www.youtube.com/share_ajax?action_get_embed=1&video_id=$code";

            ## HTTPS url that you are targeting.
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Opera/9.23 (Windows NT 5.1; U; en)');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            ## Below two option will enable the HTTPS option.
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            $data = json_decode(curl_exec($ch));
            if (!isset($data->error)) {
                var_dump($data);
            }
        }
    }

    public function compileVideo($params)
    {
        $video_id = $params['video_id'];
        $code = $params['code'];
        $view = $params['view'];
        $mobile = empty($params['mobile']) ? false : $params['mobile'];

        $autoplay = !$mobile && $view;
        $videoFrame = $video_id . "_" . $params['count_video'];
        $embedded = '<iframe title="YouTube video player" id="videoFrame'
            . $videoFrame . '" class="youtube_iframe' . ($view ? "_big" : "_small")
            . '" src="//www.youtube.com/embed/' . $code . '?wmode=opaque' . ($autoplay ? "&autoplay=1" : "")
            . '"frameborder="0" allowfullscreen="" scrolling="no"></iframe>';
        return $embedded;
    }

    public function extractVideo($params)
    {
        $video_id = $params['video_id'];
        $code = $params['code'];
        $view = $params['view'];
        $mobile = empty($params['mobile']) ? false : $params['mobile'];

        $embedded = '
            <video id="player_' . $video_id . '" class="ynultimatevideo-player" data-type="1" style="width:100%;height:100%;top:0 !important;">
                <source type="video/youtube" src="//www.youtube.com/watch?v=' . $code . '" />
            </video>';

        return $embedded;
    }
}
