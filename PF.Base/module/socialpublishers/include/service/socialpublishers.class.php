<?php

defined('PHPFOX') or exit('NO DICE!');

class SocialPublishers_Service_Socialpublishers extends Phpfox_Service {

    public function __construct()
    {
        $this->_sTable = Phpfox::getT('socialpublishers_services');
    }

    public function getUserConnected($iUserid = null, $iServiceId = null)
    {
        return Phpfox::getService('socialbridge.agents')->getUserConnected($iUserid, $iServiceId);
    }

    public function getUrlAuth($sService = "", $bRedirect = 0)
    {
        //USING SOCIAL BRIDGE TO GER AUTH URL
        return Phpfox::getService('socialbridge.libs')->getUrlAuth($sService, $bRedirect, 'publish_stream');
    }

    public function addToken($iUserId = null, $sService = 'facebook', $aParams, $aExtra)
    {
        //USING SOCIAL BRIDGE TO ADD USER TOKEN
        return Phpfox::getService('socialbridge.agents')->addToken($iUserId, $sService, $aParams, $aExtra);
    }

    public function getRealUser($iUserId = null)
    {
        if ($iUserId == null)
        {
            $iUserId = Phpfox::getUserId();
        }
        $id = (int) $this->database()->select('p.user_id')->from(Phpfox::getT('user'), 'u')->join(Phpfox::getT('pages'), 'p', 'u.profile_page_id = p.page_id')->where('u.user_id = ' . $iUserId)->execute('getField');
        return ($id == 0 ? $iUserId : $id);
    }

    public function getProfile($sService = "", $aParams = null)
    {
        //USING SOCIAL BRIDGE TO GET PROFILE INFO
        return Phpfox::getService('socialbridge.agents')->getProfile($sService, $aParams);
    }

    public function deleteToken($iUserId = null, $sService = 'facebook')
    {
        // USING SOCIAL BRIDGE TO DELETE USER TOKEN
        return Phpfox::getService('socialbridge.agents')->deleteToken($iUserId, $sService);
    }

    public function getToken($iUserId = null, $sService = 'facebook')
    {
        $iUserId = $this->getRealUser((int) $iUserId);
        // USING SOCIAL BRIDGE TO GET USER TOKEN
        return Phpfox::getService('socialbridge.agents')->getToken($iUserId, $sService);
    }

    function decodeEntities($text)
    {
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        return $text;
    }

    //show publisher
    public function showPublisher($sType = "", $iUserId = null, $aVals = array(), $bIsFrame = FALSE)
    {
        if (!Phpfox::isModule('socialbridge') || !Phpfox::isModule('feed') || Phpfox::isAdminPanel())
        {
            return FALSE;
        }
        $sProvides = Phpfox::getService('socialbridge')->getAllProviderData();
        if(isset($sProvides) && empty($sProvides))
        {
            return FALSE;
        }
        $sIdCache = Phpfox::getLib('cache')->set("socialpublishers_feed_" . $iUserId);
        if (!$sType || count($aVals) <= 0)
        {
            Phpfox::getLib('cache')->remove($sIdCache);
            return FALSE;
        }

        if ($sType == "user_status")
        {
            return FALSE;
        }

        if ($sType == "music_song" || $sType == "music_album")
        {
            $sType = "music";
            $aVals['content'] = '';
        }

        if ($sType == 'user_status')
        {
            $sType = 'status';
        }

        if ($sType == "video")
        {
            $aVals['content'] = '';
        }

        if (isset($aVals['text']) && !empty($aVals['text']))
        {
            $aVals['text'] = Phpfox::getLib('parse.input')->clean($aVals['text']);
        }

        if (isset($aVals['content']) && !empty($aVals['content']))
        {
            $aVals['content'] = Phpfox::getLib('parse.input')->clean($aVals['content']);
        }

        $aSupportedModule = Phpfox::getService('socialpublishers.modules')->getModule($sType);
        if (count($aSupportedModule) > 0 && $aSupportedModule['is_active'] == 0)
        {
            Phpfox::getLib('cache')->remove($sIdCache);
            return FALSE;
        }

        $aExistSettings = Phpfox::getService('socialpublishers.modules')->getUserModuleSettings($this->getRealUser($iUserId), $sType);

        $aShare['type'] = $sType;
        $aShare['user_id'] = $iUserId;
        $aShare['url'] = urlencode($aVals['url']);
        $aShare['title'] = isset($aVals['title']) ? $this->decodeEntities($aVals['title']) : "";
        $aShare['content'] = isset($aVals['content']) ? $this->decodeEntities($aVals['content']) : "";
        $aShare['item_id'] = isset($aVals['item_id']) ? $aVals['item_id'] : 0;

        $aModulePostContent = array(
            'status',
            'photo',
            'link',
            'music'
        );
        if (!in_array($sType, $aModulePostContent))
        {
            $aShare['content'] = $aShare['title'];
        }
        $aShare['content'] = $this->mbcf_truncate($aShare['content'], 300);

        $aFeed = Phpfox::getLib('cache')->get($sIdCache);

        if (!$aFeed)
        {
            return FALSE;
        }

        if (isset($aFeed['is_show']) && $aFeed['is_show'] == 1)
        {
            return FALSE;
        }

        if (count($aExistSettings) > 0 && $aExistSettings['no_ask'] == 1)
        {
            $aShare['url'] = urldecode($aShare['url']);
            $sPostMessage = Phpfox::getService('socialpublishers')->getPostMessage($aShare);
            $aShare['status'] = $sPostMessage;
            if ((int) $aExistSettings['facebook'] == 1 && isset($aSupportedModule['facebook']) && (int) $aSupportedModule['facebook'] == 1)
            {
                $sReponse = Phpfox::getService('socialbridge')->post('facebook', $aShare);
                if ($sReponse === true)
                {
                    Phpfox::getService('socialpublishers.statisticdate.process')->updateTotalPostByDate('facebook');
                    Phpfox::getService('socialpublishers.statisticuser.process')->updateTotalPostByUser('facebook');
                }
            }
            if ((int) $aExistSettings['twitter'] == 1 && isset($aSupportedModule['twitter']) && (int) $aSupportedModule['twitter'] == 1)
            {
                $sReponse = Phpfox::getService('socialbridge')->post('twitter', $aShare);
                if (isset($sReponse['id']) && !Phpfox::getLib('parse.format')->isEmpty($sReponse['id']))
                {
                    Phpfox::getService('socialpublishers.statisticdate.process')->updateTotalPostByDate('twitter');
                    Phpfox::getService('socialpublishers.statisticuser.process')->updateTotalPostByUser('twitter');
                }
            }
            if ((int) $aExistSettings['linkedin'] == 1 && isset($aSupportedModule['linkedin']) && (int) $aSupportedModule['linkedin'] == 1)
            {
                $aResponse = Phpfox::getService('socialbridge')->getProvider('linkedin')->getApi()->connections();
                if (isset($aResponse['success']) && $aResponse['success'])
                {
                    try
                    {
                        $sReponse = Phpfox::getService('socialbridge')->post('linkedin', $aShare);
                        if (isset($sReponse['success']) && $sReponse['success'] == 1)
                        {
                            Phpfox::getService('socialpublishers.statisticdate.process')->updateTotalPostByDate('linkedin');
                            Phpfox::getService('socialpublishers.statisticuser.process')->updateTotalPostByUser('linkedin');
                        }
                    }
                    catch (Exception $e)
                    {
                        // Do nothing.
                    }
                }
            }
            if (Phpfox::isModule('socialintegration'))
            {
                if ($bIsFrame == FALSE)
                {
                    echo "<script>\$.ajaxCall('socialintegration.showAfterPublisher');</script>";
                }
                else
                {
                    echo "\$.ajaxCall('socialintegration.showAfterPublisher');";
                }
            }

            $sIdCache = Phpfox::getLib('cache')->set("socialpublishers_feed_" . $iUserId);
            $aFeed['params'] = null;
            $aFeed['is_show'] = 1;
            Phpfox::getLib('cache')->save($sIdCache, $aFeed);

            return true;
        }

        $aFeed['params'] = $aShare;
        $sIdCache = Phpfox::getLib('cache')->set("socialpublishers_feed_" . $this->getRealUser((int) $iUserId));
        Phpfox::getLib('cache')->save($sIdCache, $aFeed);

        if ($bIsFrame === 2)
        {
            $sJsCall = "setTimeout(\"\$Core.box('socialpublishers.share', 500);\",2500);";
        }
        else
        if ($bIsFrame == true)
        {
            $sJsCall = "window.parent.\$Core.box('socialpublishers.share', 500);";
        }
        else
        {
             $sJsCall = "<script>var firstTimePublish = false;\$Behavior.showSocialPublishersPopup = (function(){if(firstTimePublish == false){\$Core.box('socialpublishers.share', 500); firstTimePublish = true};});</script>";
        }

        if ($bIsFrame === 3)
        {
            if (!isset($aExistSettings['no_ask']) || $aExistSettings['no_ask'] == 0)
            {
                Phpfox::getLib('ajax')->call("window.parent.\$Core.box('socialpublishers.share', 500);");
            }
        }
        elseif ($bIsFrame !== 4)
        {
            echo $sJsCall;
        }
    }

    public function showPublisher3rd($aParams)
    {
        if (Phpfox::hasCallback($aParams['type'], 'getPublisherInfo'))
        {
            $aParams = Phpfox::callback($aParams['type'] . '.getPublisherInfo', $aParams);
            $sType = $aParams['type'];
            $iUserId = $aParams['user_id'];
            $bIsFrame = $aParams['bIsFrame'];
            $aVals['url'] = $aParams['url'];
            $aVals['content'] = $aParams['content'];
            $aVals['title'] = $aParams['title'];
            Phpfox::getService('socialpublishers')->showPublisher($sType, $iUserId, $aVals, $bIsFrame);
        }
    }

    public function getPostMessage($aParams = array())
    {
        if (!isset($aParams['type']))
        {
            return _p('socialpublishers.post_on');
        }
        //for unsuportted module.
        if (Phpfox::hasCallback($aParams['type'], 'getPostMessage'))
        {
            $aPostMessage = Phpfox::callback($aParams['type'] . '.getPostMessage', $aParams);
            return $aPostMessage;
        }
        //end
        switch ($aParams['type']) {
            case 'blog' :
                //{full_name} posted a blog {title} on {site_name}
                return _p('socialpublishers.post_a_blog_title_on', array(
                            'full_name' => Phpfox::getUserBy('full_name'),
                            'title' => empty($aParams['title']) ? '' : ' "' . $aParams['title'] . '"',
                            'site_name' => Phpfox::getParam('core.site_title'),
                        ));
			case 'document' :
                //{full_name} posted a document {title} on {site_name}
                return _p('socialpublishers.post_a_document_title_on', array(
                            'full_name' => Phpfox::getUserBy('full_name'),
                            'title' => empty($aParams['title']) ? '' : ' "' . $aParams['title'] . '"',
                            'site_name' => Phpfox::getParam('core.site_title'),
                        ));
            case 'link' :
                return _p('socialpublishers.share_a_link_title_on', array(
                            'full_name' => Phpfox::getUserBy('full_name'),
                            'link' => ' "' . $aParams['url'] . '"',
                            'site_name' => Phpfox::getParam('core.site_title'),
                        ));
            case 'music' :
                return _p('socialpublishers.post_a_song_title_on', array(
                            'full_name' => Phpfox::getUserBy('full_name'),
                            'title' => empty($aParams['title']) ? '' : ' "' . $aParams['title'] . '"',
                            'site_name' => Phpfox::getParam('core.site_title'),
                        ));
            case 'photo' :
                return _p('socialpublishers.post_a_photo_title_on', array(
                            'full_name' => Phpfox::getUserBy('full_name'),
                            'title' => empty($aParams['title']) ? '' : ' "' . $aParams['title'] . '"',
                            'site_name' => Phpfox::getParam('core.site_title'),
                        ));
            case 'advancedphoto' :
                return _p('socialpublishers.post_a_photo_title_on', array(
                            'full_name' => Phpfox::getUserBy('full_name'),
                            'title' => empty($aParams['title']) ? '' : ' "' . $aParams['title'] . '"',
                            'site_name' => Phpfox::getParam('core.site_title'),
                        ));
            case 'poll' :
                return _p('socialpublishers.post_a_poll_title_on', array(
                            'full_name' => Phpfox::getUserBy('full_name'),
                            'title' => empty($aParams['title']) ? '' : ' "' . $aParams['title'] . '"',
                            'site_name' => Phpfox::getParam('core.site_title'),
                        ));
            case 'status' :
            case 'user_status' :
                return _p('socialpublishers.post_a_status_title_on', array(
                            'full_name' => Phpfox::getUserBy('full_name'),
                            'title' => empty($aParams['title']) ? '' : ' "' . $aParams['title'] . '"',
                            'site_name' => Phpfox::getParam('core.site_title'),
                        ));
            case 'video' :
            case 'ultimatevideo_video':
            case 'ultimatevideo_playlist':
                return _p('socialpublishers.post_a_video_title_on', array(
                            'full_name' => Phpfox::getUserBy('full_name'),
                            'title' => empty($aParams['title']) ? '' : ' "' . $aParams['title'] . '"',
                            'site_name' => Phpfox::getParam('core.site_title'),
                        ));
            case 'quiz' :
                return _p('socialpublishers.post_a_quiz_title_on', array(
                            'full_name' => Phpfox::getUserBy('full_name'),
                            'title' => empty($aParams['title']) ? '' : ' "' . $aParams['title'] . '"',
                            'site_name' => Phpfox::getParam('core.site_title'),
                        ));
            case 'event' :
                return _p('socialpublishers.post_a_event_title_on', array(
                            'full_name' => Phpfox::getUserBy('full_name'),
                            'title' => empty($aParams['title']) ? '' : ' "' . $aParams['title'] . '"',
                            'site_name' => Phpfox::getParam('core.site_title'),
                        ));
            case 'fevent' :
                return _p('socialpublishers.post_a_event_title_on', array(
                            'full_name' => Phpfox::getUserBy('full_name'),
                            'title' => empty($aParams['title']) ? '' : ' "' . $aParams['title'] . '"',
                            'site_name' => Phpfox::getParam('core.site_title'),
                        ));
            case 'marketplace' :
                return _p('socialpublishers.post_a_marketplace_title_on', array(
                            'full_name' => Phpfox::getUserBy('full_name'),
                            'title' => empty($aParams['title']) ? '' : ' "' . $aParams['title'] . '"',
                            'site_name' => Phpfox::getParam('core.site_title'),
                        ));
            case 'advancedmarketplace' :
                return _p('socialpublishers.post_a_marketplace_title_on', array(
                            'full_name' => Phpfox::getUserBy('full_name'),
                            'title' => empty($aParams['title']) ? '' : ' "' . $aParams['title'] . '"',
                            'site_name' => Phpfox::getParam('core.site_title'),
                        ));
            case 'karaoke_song' :
                return _p('socialpublishers.post_a_karaoke_title_on', array(
                            'full_name' => Phpfox::getUserBy('full_name'),
                            'title' => empty($aParams['title']) ? '' : ' "' . $aParams['title'] . '"',
                            'site_name' => Phpfox::getParam('core.site_title'),
                        ));
            case 'karaoke_recording' :
                return _p('socialpublishers.post_a_record_title_on', array(
                            'full_name' => Phpfox::getUserBy('full_name'),
                            'title' => empty($aParams['title']) ? '' : ' "' . $aParams['title'] . '"',
                            'site_name' => Phpfox::getParam('core.site_title'),
                        ));
            case 'petition' :
                return _p('socialpublishers.post_a_petition_title_on', array(
                            'full_name' => Phpfox::getUserBy('full_name'),
                            'title' => empty($aParams['title']) ? '' : ' "' . $aParams['title'] . '"',
                            'site_name' => Phpfox::getParam('core.site_title'),
                        ));
            case 'musicstore_album' :
                return _p('socialpublishers.post_a_album_title_on', array(
                            'full_name' => Phpfox::getUserBy('full_name'),
                            'title' => empty($aParams['title']) ? '' : ' "' . $aParams['title'] . '"',
                            'site_name' => Phpfox::getParam('core.site_title'),
                        ));
            case 'musicsharing_album' :
                return _p('socialpublishers.post_a_album_title_on', array(
                            'full_name' => Phpfox::getUserBy('full_name'),
                            'title' => empty($aParams['title']) ? '' : ' "' . $aParams['title'] . '"',
                            'site_name' => Phpfox::getParam('core.site_title'),
                        ));
            case 'musicstore_playlist' :
                return _p('socialpublishers.post_a_playlist_title_on', array(
                            'full_name' => Phpfox::getUserBy('full_name'),
                            'title' => empty($aParams['title']) ? '' : ' "' . $aParams['title'] . '"',
                            'site_name' => Phpfox::getParam('core.site_title'),
                        ));
            case 'musicsharing_playlist' :
                return _p('socialpublishers.post_a_playlist_title_on', array(
                            'full_name' => Phpfox::getUserBy('full_name'),
                            'title' => empty($aParams['title']) ? '' : ' "' . $aParams['title'] . '"',
                            'site_name' => Phpfox::getParam('core.site_title'),
                        ));
            default :
                return _p('socialpublishers.post_on_site', array(
                            'full_name' => Phpfox::getUserBy('full_name'),
                            'title' => empty($aParams['title']) ? '' : ' "' . $aParams['title'] . '"',
                            'site_name' => Phpfox::getParam('core.site_title'),
                        ));
        }
        return _p('socialpublishers.post_on');
    }

    public function post($sProvider = "", $aVals = array())
    {
        //USING SOCIAL BRIDGE TO POST CONTENT TO FACEBOOK
        return Phpfox::getService('socialbridge.libs')->post($sProvider, $aVals);
    }

    public function getShortBitlyUrl($sLongUrl)
    {
        try
        {
            $sLongUrl = urlencode($sLongUrl);
            $url = "http://api.bitly.com/v3/shorten?login=myshortlinkng&apiKey=R_0201be3efbcc7a1a0a0d1816802081d8&longUrl={$sLongUrl}&format=json";
            $result = @file_get_contents($url);
            $obj = json_decode($result, true);
            return ($obj['status_code'] == '200' ? $obj['data']['url'] : "");
        }
        catch (Exception $e)
        {
            return $sLongUrl;
        }
    }

    //get info from added
    public function getAddedInfo($aRecentAddedItem)//$sType,$iItemId,$bIsCallback,$aCallback)
    {
        $sType = $aRecentAddedItem['sType'];
        $aCallback = $aRecentAddedItem['aCallback'];
        $bIsCallback = $aRecentAddedItem['bIsCallback'];
        $iItemId = $aRecentAddedItem['iItemId'];
        $sModule = "";
        $sPrefix = "";

        if ($bIsCallback)
        {
            $sModule = $aCallback['module'];
            $sPrefix = $aCallback['table_prefix'];
        }

        $aFeed = $this->database()->select('feed.*,u.user_name,u.full_name,u.user_image,u.profile_page_id, u.gender')->from(Phpfox::getT($sPrefix . 'feed'), 'feed')->join(Phpfox::getT('user'), 'u', 'u.user_id = feed.user_id')->where('feed.item_id =' . (int) $iItemId . ' AND (feed.type_id = "' . $sType . '" )')->execute('getRow');
		
        if (count($aFeed) > 0 && $aFeed !== FALSE)
        {
            if ($bIsCallback)
            {
                $aFeedTmp = Phpfox::callback($aFeed['type_id'] . '.getActivityFeed', $aFeed, $sModule);
                return array_merge($aFeedTmp, $aFeed);
            }
            if (!Phpfox::hasCallback($aFeed['type_id'], 'getActivityFeed'))
            {
                return FALSE;
            }
            $aFeedTmp = Phpfox::callback($aFeed['type_id'] . '.getActivityFeed', $aFeed);
            return array_merge($aFeedTmp, $aFeed);
        }
        return array();
    }

    function mbcf_truncate($string, $length = 80, $etc = '...', $charset = 'UTF-8', $break_words = FALSE, $middle = FALSE)
    {
        if ($length == 0)
            return '';

        if (strlen($string) > $length)
        {
            $length -= min($length, strlen($etc));
            if (!$break_words && !$middle)
            {
                $string = preg_replace('/\s+?(\S+)?$/', '', substr($string, 0, $length + 1));
            }
            if (!$middle)
            {
                return substr($string, 0, $length) . $etc;
            }
            else
            {
                return substr($string, 0, $length / 2) . $etc . substr($string, -$length / 2);
            }
        }
        else
        {
            return $string;
        }
    }

    /**
     * Query the facebook query language.
     * @param string $sFql
     */
    public function query($sFql)
    {
        $aVals = array();
        $aVals['q'] = $sFql;

        return Phpfox::getService('socialbridge.provider.facebook')->getApi()->api('/fql', 'GET', $aVals);
    }

    public function getPrivacyOfApp()
    {
        $sFql = 'SELECT name, value, description, allow, deny, networks, friends FROM privacy_setting WHERE name = "default_stream_privacy"';

        $aResult = $this->query($sFql);
        $aFacebookPrivacy = array();
        if (isset($aResult['data']) && isset($aResult['data'][0]))
        {
            $aFacebookPrivacy = $aResult['data'][0];
        }
        return $aFacebookPrivacy;
    }

    /**
     * $aAllFacebookPrivacy = array(
      'EVERYONE' => 'Public',
      'FRIENDS_OF_FRIENDS' => 'Friends of Friends',
      'ALL_FRIENDS' => 'Friends',
      'SELF' => 'Only Me',
      'CUSTOM' => 'Name A, Name B, Name C',
      );
     * @param type $aFacebookPrivacy
     * @return boolean
     */
    public function convertPrivacyFromFacebookToPhpfox($aFacebookPrivacy)
    {
        if (!isset($aFacebookPrivacy['value']))
            return false;

        switch ($aFacebookPrivacy['value']) {
            case 'EVERYONE':
                return array(
                    'phrase' => _p('privacy.everyone'),
                    'value' => '0'
                );
                break;
            case 'FRIENDS_OF_FRIENDS':
                if (Phpfox::isModule('friend'))
                {
                    return array(
                        'phrase' => _p('privacy.friends'),
                        'value' => '1'
                    );
                }
                else
                {
                    return false;
                }
                break;
            case 'ALL_FRIENDS':
                if (Phpfox::isModule('friend'))
                {
                    return array(
                        'phrase' => _p('privacy.friends_of_friends'),
                        'value' => '2'
                    );
                }
                else
                {
                    return false;
                }
                break;
            case 'SELF':
                return array(
                    'phrase' => _p('privacy.only_me'),
                    'value' => '3'
                );
                break;
            case 'CUSTOM':
                if (Phpfox::isModule('friend'))
                {
                    return array(
                        'phrase' => preg_replace('/<span>(.*)<\/span>/i', '', _p('privacy.custom_span_click_to_edit_span')),
                        'value' => '4'
                    );
                }
                else
                {
                    return false;
                }
                break;

            default:
                return false;
                break;
        }
    }

    public function convertPrivacyFromPhpfoxToFacebook($iPhpfoxPrivacy)
    {
        switch ($iPhpfoxPrivacy) {
            case 0:
                return array('EVERYONE' => 'Public');
                break;
            case 1:
                if (Phpfox::isModule('friend'))
                {
                    return array('ALL_FRIENDS' => 'Friends');
                }
                else
                {
                    return false;
                }
                break;
            case 2:
                if (Phpfox::isModule('friend'))
                {
                    return array('FRIENDS_OF_FRIENDS' => 'Friends of Friends');
                }
                else
                {
                    return false;
                }
                break;
            case 3:
                return array('SELF' => 'Only Me');
                break;
            case 4:
                if (Phpfox::isModule('friend'))
                {
                    return array('CUSTOM' => 'Name A, Name B, Name C');
                }
                else
                {
                    return false;
                }
                break;
            default:
                return false;
                break;
        }
    }

    public function getFriendListOnFacebook()
    {
        $sFql = 'SELECT flid FROM friendlist WHERE owner = me()';
        $aResult = $this->query($sFql);
        $aData = array();
        if (isset($aResult['data']) && isset($aResult['data'][0]))
        {
            $aData = $aResult['data'][0];
        }
        return $aData;
    }

    public function makeDecision($iPhpfoxPrivacy)
    {
        $aFacebookPrivacy = $this->getPrivacyOfApp();

        $aPhpfoxPrivacy = $this->convertPrivacyFromFacebookToPhpfox($aFacebookPrivacy);
    }

}