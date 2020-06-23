<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

require_once (PHPFOX_DIR . 'module' . PHPFOX_DS . 'videochannel' . PHPFOX_DS . 'include' . PHPFOX_DS . 'service' . PHPFOX_DS . 'libs.class.php');


/**
 *
 *
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Development Group
 * @package          Module_VideoChannel
 * @version          2.02
 */
class Videochannel_Service_Channel_Grab extends Phpfox_Service
{

    public $_parseStats;
    private $_aSites = array(
        'youtube' => 'YouTube',
        'myspace' => 'MySpace Video',
        'break' => 'Break',
        'metacafe' => 'Metacafe'
    );
    private $_aData = array(
        'site_id' => 0,
        'url' => '',
        'html' => '',
        'lines' => array()
    );
    private $_bHasImage = false;

    /**
     * Class constructor
     */
    public function __construct()
    {
        (($sPlugin = Phpfox_Plugin::get('videochannel.service_channel_grab_1')) ? eval($sPlugin) : false);
    }

    public function getGdataUrl($sUrl)
    {
        $sUrl = trim($sUrl);
		$pattern = "/((http|https):\/\/|)(www\.|)youtube\.com\/(channel\/|user\/)[a-zA-Z0-9]{1,}/";
		if(preg_match($pattern, $sUrl))
        {
            return true;
        }

        return Phpfox_Error::display(_p('videochannel.please_provide_a_valid_url_for_your_channel'));
    }

    public function viewData()
    {
        $data = array();
        $data['parseStats'] = $this->_parseStats;
        $data['aSite'] = $this->_aSites;
        $data['aData'] = $this->_aData;
        $data['bHasImage'] = $this->_bHasImage;
        return $data;
    }

    public function getSites()
    {
        $aSites = array();
        foreach ($this->_aSites as $sSite => $sName)
        {
            $aSites[] = $sName;
        }

        return implode(', ', $aSites);
    }

    public function get($sUrl)
    {
        $sUrl = trim($sUrl);
        if (preg_match('/http:\/\/youtu\.be\/(.*)/i', $sUrl, $aMatches) && isset($aMatches[1]))
        {
            $sUrl = 'https://www.youtube.com/watch?v=' . $aMatches[1];
        }

        if (substr($sUrl, 0, 7) == 'http://' || substr($sUrl, 0, 8) == 'https://')
        {
            $aSites = array();
            foreach ($this->_aSites as $sSite => $sName)
            {
                if (strpos($sUrl, $sSite))
                {
                    $this->_aData['url'] = $sUrl;
                    $this->_aData['site_id'] = $sSite;
                }

                $aSites[] = $sName;
            }

            if (!$this->_aData['site_id'])
            {
                // 'Not a valid site. Valid sites: ' . implode(', ', $aSites)
                return Phpfox_Error::set(_p('videochannel.not_a_valid_site_valid_sites_asites', array('aSites' => implode(', ', $aSites))));
            }

            return true;
        }

        return Phpfox_Error::set(_p('videochannel.please_provide_a_valid_url_for_your_video'));
    }

    public function parse()
    {
        static $bSent = false;
        // Plugin call
        if ($sPlugin = Phpfox_Plugin::get('videochannel.service_channel_grab_parse__start'))
        {
            eval($sPlugin);
        }
        if ($bSent === false)
        {
            switch ($this->_aData['site_id'])
            {
                case 'youtube':
                    $aUrl = parse_url($this->_aData['url']);
                    if (!isset($aUrl['query']) && isset($aUrl['path']))
                    {
                        $aFix = explode('/', $aUrl['path']);
                        $aUrl['query'] = 'v=' . $aFix[2];
                    }
                    parse_str($aUrl['query'], $aStr);
					$api_key = 'AIzaSyDpUPT_nafV_MFSAlc-8AH4e1Gy578iK0M';
					$code = $aStr['v'];
            		$url = "https://www.googleapis.com/youtube/v3/videos?id=$code&key=$api_key&part=snippet,contentDetails";
		            $xVideo = Phpfox::getLib('request')->send($url, array(), 'GET');
					$data = json_decode($xVideo);
					if (empty($data->items)) {
		                return false;
		            }
                    $this->_aData['html'] = $data->items[0];
                    break;
                case 'myspace':
                case 'break':
                case 'metacafe':
                    // metacafe video pages need the trailing slash to be fetched
                    if (strrpos($this->_aData['url'], '/') != strlen($this->_aData['url']))
                    {
                        $this->_aData['url'] .= '/';
                    }
                    $this->_aData['html'] = Phpfox::getLib('request')->send($this->_aData['url'], array(), 'GET');
                    break;
                default:

                    break;
            }
        }
        if ($sPlugin = Phpfox_Plugin::get('videochannel.service_channel_grab_parse__end'))
        {
            eval($sPlugin);
        }
        return $this;
    }

    public function title()
    {
        switch ($this->_aData['site_id'])
        {
            // YouTube
            case 'youtube':
                if (isset($this->_aData['html']-> snippet-> title))
                {

                    $sTitle = $this->_aData['html']-> snippet-> title;


                }
                break;
            case 'myspace':
                preg_match('/<title>(.*?)<\/title>/is', $this->_aData['html'], $aMatches);
                if (isset($aMatches[1]))
                {
                    $sTitle = str_replace('- MySpace Video', '', trim($aMatches[1]));
                }
                break;
            case 'break':
                // fixes importing video title
                if (preg_match('/<meta name=["|\']embed_video_title["|\'] id=["|\']vid_title["|\'] content=["|\'](.*?)["|\']>/i', $this->_aData['html'], $aMatches))
                {
                    $sTitle = trim($aMatches[1]);
                }
                break;
            case 'metacafe':
                preg_match('/<title>(.*?)<\/title>/is', $this->_aData['html'], $aMatches);
                if (isset($aMatches[1]))
                {
                    $sTitle = str_replace('- Video', '', trim($aMatches[1]));
                }
                break;
            default:

                break;
        }
        (($sPlugin = Phpfox_Plugin::get('videochannel.service_channel_grab_title')) ? eval($sPlugin) : false);
        if (!isset($sTitle))
        {
            return false;
        }

        return $sTitle;
    }

    public function image($iId, $sModule = 'videochannel')
    {
        switch ($this->_aData['site_id'])
        {
            // YouTube
            case 'youtube':
                $sImage = $this -> _aData['html'] -> snippet->  thumbnails -> medium -> url;
				$thumbnails = end($this -> _aData['html']-> snippet-> thumbnails);
                $sImageForSlide = $thumbnails -> url;
                break;
            case 'myspace':
                preg_match('/<link rel="image_src" href="(.*?)" \/>/i', $this->_aData['html'], $aMatches);
                if (isset($aMatches[1]))
                {
                    $sImage = trim($aMatches[1]);
                }
                break;
            case 'break':
                // Fixes fetching the thumbnail
                if (preg_match('/<meta name=["|\']embed_video_thumb_url["|\'] content=["|\'](.*?)["|\']>/i', $this->_aData['html'], $aMatches))
                {
                    $sImage = trim($aMatches[1]);
                }
                break;
            case 'metacafe':
                preg_match('/<link rel="image_src" href="(.*?)" \/>/i', $this->_aData['html'], $aMatches);
                if (isset($aMatches[1]))
                {
                    $sImage = trim($aMatches[1]);
                }
                break;
            default:

                break;
        }
        (($sPlugin = Phpfox_Plugin::get('videochannel.service_channel_image_1')) ? eval($sPlugin) : false);

        if (isset($sImage))
        {
            $sImageLocation = Phpfox::getLib('file')->getBuiltDir(Phpfox::getParam('core.dir_pic')) . md5($iId . 'videochannel') . '%s.jpg';

            if ($sModule == 'videochannel')
            {
                $oImage1 = Phpfox::getLib('request')->send(((isset($sImageForSlide)) ? $sImageForSlide : $sImage), array(), 'GET');
                $sTempImage1 = 'video_temporal_image1_' . $iId;
                Phpfox::getLib('file')->writeToCache($sTempImage1, $oImage1);
                @copy(PHPFOX_DIR_CACHE . $sTempImage1, sprintf($sImageLocation, '_480'));
                unlink(PHPFOX_DIR_CACHE . $sTempImage1);
            }

            $oImage = Phpfox::getLib('request')->send($sImage, array(), 'GET');
            $sTempImage = 'video_temporal_image_' . $iId;
            Phpfox::getLib('file')->writeToCache($sTempImage, $oImage);
            @copy(PHPFOX_DIR_CACHE . $sTempImage, sprintf($sImageLocation, '_120'));
            unlink(PHPFOX_DIR_CACHE . $sTempImage);

            $this->_bHasImage = true;

            Phpfox::getLib('cdn')->put(sprintf($sImageLocation, '_120'));
            Phpfox::getLib('cdn')->put(sprintf($sImageLocation, '_480'));

            return true;
        }

        return false;
    }

    public function description()
    {
        switch ($this->_aData['site_id'])
        {

            // YouTube
            case 'youtube':
                if (isset($this->_aData['html']-> snippet-> description))
                {
                    $sDescription = $this->_aData['html']-> snippet-> description;
                }
                break;
            case 'myspace':
                preg_match('/<b id="tv_vid_vd_fulldesc_text">(.*?)<\/b>/is', $this->_aData['html'], $aMatches);
                if (isset($aMatches[1]))
                {
                    $sDescription = Phpfox::getLib('parse.format')->unhtmlspecialchars(trim($aMatches[1]));
                }
                break;
            case 'break':
                if (preg_match('/<meta name="embed_video_description" id="vid_desc" content="(.*?)" \/>/i', $this->_aData['html'], $aMatches))
                {
                    $sDescription = Phpfox::getLib('parse.format')->unhtmlspecialchars(trim($aMatches[1]));
                }
                break;
            case 'metacafe':
                if (preg_match('/<meta name="description" content="(.*?)" \/>/i', $this->_aData['html'], $aMatches))
                {
                    $sDescription = Phpfox::getLib('parse.format')->unhtmlspecialchars(trim($aMatches[1]));
                }
                break;
            default:

                break;
        }
        (($sPlugin = Phpfox_Plugin::get('videochannel.service_channel_description_1')) ? eval($sPlugin) : false);
        if (!isset($sDescription))
        {
            return false;
        }

        return $sDescription;
    }

    public function duration()
    {
        switch ($this->_aData['site_id'])
        {
            // YouTube
            case 'youtube':
                if (isset($this->_aData['html'] -> contentDetails))
                {
                	$start = new DateTime('@0'); // Unix epoch
		    		$start->add(new DateInterval($this->_aData['html']->contentDetails->duration));
					$sDuration = '';
					if((int)$start->format('H') > 0)
						$sDuration.= $start->format('H') . ':';
		    		$sDuration .= $start->format('i').':'.$start->format('s');
                }
                break;
            default:

                break;
        }
        (($sPlugin = Phpfox_Plugin::get('videochannel.service_channel_duration_1')) ? eval($sPlugin) : false);

        if (!isset($sDuration))
        {
            return false;
        }

        return $sDuration;
    }

    public function getTimeStamp()
    {
        switch ($this->_aData['site_id'])
        {
            // YouTube
            case 'youtube':
                if (isset($this->_aData['html'] -> snippet -> publishedAt))
                {
                    $sTimeStamp = strtotime($this->_aData['html'] -> snippet -> publishedAt);
                }
                break;
            default:

                break;
        }
        (($sPlugin = Phpfox_Plugin::get('videochannel.service_channel_time_stamp')) ? eval($sPlugin) : false);

        if (!isset($sTimeStamp))
        {
            return false;
        }

        return $sTimeStamp;
    }

    public function embed()
    {
        switch ($this->_aData['site_id'])
        {
            // YouTube
            case 'youtube':
                $aUrl = parse_url($this->_aData['url']);
                if (!isset($aUrl['query']) && isset($aUrl['path']))
                {
                    $aFix = explode('/', $aUrl['path']);
                    $aUrl['query'] = 'v=' . $aFix[2];
                }
                parse_str($aUrl['query'], $aStr);
                $sEmbed = '<object width="425" height="344"><param name="wmode" value="transparent"></param><param name="movie" value="https://www.youtube.com/v/' . $aStr['v'] . (Phpfox::getParam('videochannel.embed_auto_play') ? '&amp;autoplay=1' : '') . (Phpfox::getParam('videochannel.full_screen_with_youtube') ? '&amp;fs=1' : '') . (Phpfox::getParam('videochannel.disable_youtube_related_videos') ? '&amp;rel=0' : '') . '"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed wmode="transparent" src="https://www.youtube.com/v/' . $aStr['v'] . (Phpfox::getParam('videochannel.embed_auto_play') ? '&amp;autoplay=1' : '') . (Phpfox::getParam('videochannel.full_screen_with_youtube') ? '&amp;fs=1' : '') . '" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="425" height="344"></embed></object>';
                break;
            case 'myspace':
                if (preg_match('/id="mainVideoPlayer"/i', $this->_aData['html']))
                {
                    $this->_aData['lines'] = explode("\n", $this->_aData['html']);
                    foreach ($this->_aData['lines'] as $sLine)
                    {
                        if (preg_match('/id="msVideoPlayer"/i', $sLine))
                        {
                            if (isset($sLine))
                            {
                                $aParts = explode('<object', Phpfox::getLib('parse.format')->unhtmlspecialchars($sLine));
                                if (isset($aParts[1]))
                                {
                                    $sEmbed = '<object' . $aParts[1];
                                }
                            }
                            break;
                        }
                    }
                }
                break;
            case 'break':
                $mediaSearch = preg_match('/http:\/\/[www\.]?embed.break.com\/[0-9]*/i', $this->_aData['html'], $aMatches);
                //return '<embed src="'.$aMatches[0].'" width="'.Phpfox::getParam('videochannel.player_width').'" height="'.Phpfox::getParam('videochannel.player_height').'"></embed>';
                return '<embed src="' . $aMatches[0] . '"></embed>';


                /*
                  if (preg_match('/name="EmbedTextBox"/i', $this->_aData['html']))
                  {
                  $this->_aData['lines'] = explode("\n", $this->_aData['html']);
                  foreach ($this->_aData['lines'] as $sLine)
                  {
                  if (preg_match('/name="EmbedTextBox"/i', $sLine))
                  {
                  preg_match('/value="&lt;object(.*?)"/i', $sLine, $aMatches);
                  $aParts = explode('</object>', Phpfox::getLib('parse.format')->unhtmlspecialchars($aMatches[1]));
                  if (isset($aParts[0]))
                  {
                  $sEmbed = '<object' . $aParts[0] . '<param name="wmode" value="transparent"></param></object>';
                  }
                  $aEmbed = explode('<embed ', $sEmbed);
                  $sEmbed = $aEmbed[0] . '<embed wmode="transparent" ' . $aEmbed[1];
                  break;
                  }
                  }
                  } */
                break;
            case 'metacafe':
                // get the list of swfs
                preg_match_all('/http:\/\/(.*swf)/i', $this->_aData['html'], $aMatches);
                // get the last word from the URL
                $bVidName = preg_match('/\/[0-9]+\/(.*[^\/])/', $this->_aData['url'], $aVidName);
                if (!$bVidName)
                {
                    return Phpfox_Error::display('Could not identify video name.');
                }
                foreach ($aMatches[0] as $sMatch)
                {
                    // find the video
                    if (strpos($sMatch, $aVidName[1]) !== false)
                    {
                        /* First way of fixing it */
                        /** @TODO need to add 2 settings to control the height */
                        //$sEmbed = '<embed flashVars="playerVars=showStats=no|autoPlay='.(Phpfox::getParam('videochannel.embed_auto_play') ? 'yes' : 'no').'" src="'.$sMatch.'" width="'.Phpfox::getParam('videochannel.player_width').'" height="'.Phpfox::getParam('videochannel.player_height').'" wmode="transparent" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash"> </embed>';
                        $sEmbed = '<embed class="metacafe_video_player" flashVars="playerVars=showStats=no|autoPlay=' . (Phpfox::getParam('videochannel.embed_auto_play') ? 'yes' : 'no') . '" src="' . $sMatch . '" width="400" height="348" wmode="transparent" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash"> </embed>';
                        /* Second way to fix it */
                        //$sEmbed = '<iframe class="sEmbed" width="400" height="300"    src="'.$sMatch.'" /></iframe>';

                        return $sEmbed;
                        break;
                    }
                }
                // left here as a plan B
                if (preg_match('/name="embedCode"/i', $this->_aData['html']))
                {
                    $this->_aData['lines'] = explode("\n", $this->_aData['html']);
                    foreach ($this->_aData['lines'] as $sLine)
                    {
                        if (preg_match('/name="embedCode"/i', $sLine))
                        {
                            preg_match('/value="(.*?)"/i', $sLine, $aMatches);
                            if (isset($aMatches[1]))
                            {
                                $aParts = explode('</embed>', Phpfox::getLib('parse.format')->unhtmlspecialchars($aMatches[1]));
                                if (isset($aParts[0]))
                                {
                                    $sEmbed = $aParts[0] . '</embed>';
                                }
                            }
                            break;
                        }
                    }
                }
                break;
            default:

                break;
        }

        (($sPlugin = Phpfox_Plugin::get('videochannel.service_channel_embed_1')) ? eval($sPlugin) : false);
        if (!isset($sEmbed))
        {
            return false;
        }

        return $sEmbed;
    }

    public function hasImage()
    {
        return $this->_bHasImage;
    }

    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod is the name of the method
     * @param array $aArguments is the array of arguments of being passed
     */
    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get('videochannel.service_channel_grab__call'))
        {
            return eval($sPlugin);
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }

}

?>
