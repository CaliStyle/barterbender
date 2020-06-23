<?php

namespace Apps\YNC_Blogs\Libraries;

use Phpfox_Error;

class Tumblr
{
    private $_tbUserName;

    function __construct($_tbUserName)
    {
        $this->_tbUserName = $_tbUserName;
    }

    public function getPosts()
    {
        $aTumblrPosts = array();
        $aPosts = array();
        $aType = [];
        $i = 0;
        $userName = str_replace(".tumblr.com", "", $this->_tbUserName);
        if (!preg_match('/^[\w-]*$/', $userName)) {
            return Phpfox_Error::display('invalid_tumblr_blog_name');
        }

        do {
            $sUrl = 'http://' . $userName . '.tumblr.com/api/read?start=' . $i . '&num=50';

            // Fetch data from remote user
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $sUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
            $myXMLData = curl_exec($ch);
            curl_close($ch);

            if ($myXMLData) {
                $sFeed = @simplexml_load_string($myXMLData);
                if ($sFeed) {
                    $aTumblrPosts[] = array_merge($aTumblrPosts, $sFeed->xpath('posts//post'));
                    $i = ( int )$sFeed->posts->attributes()->start + 50;
                }
            } else {
                return Phpfox_Error::display('username_does_not_exist');
            }
        } while ($i <= (int)@$sFeed->posts["total"]);
        foreach ($aTumblrPosts[0] as $ePost) {
            $bFlag = true;
            $aPost = [];
            $aType[] = $ePost->attributes()->type;
            switch ($ePost->attributes()->type) {
                case "regular" :
                    $aPost['title'] = htmlspecialchars($ePost->{'regular-title'});
                    $sTxt = $ePost->{'regular-body'};
                    $sTxt = str_replace(array(
                        '<br/>',
                        '<br>',
                        '<br />'
                    ), array(
                        '<p ></p>',
                        '<p></p>',
                        '<p></p>'
                    ), $sTxt);
                    $aPost['text'] = $sTxt;

                    // Get the first image in content
                    if (preg_match('/< *img[^>]*src *= *["\']?([^"\']*)/i', $sTxt, $matches)) {
                        $aPost['image_path'] = strtok($matches[1], '?');
                    } else {
                        $aPost['image_path'] = "";
                    }
                    break;
                case "photo" :
                    $aPost['title'] = "Photos";
                    // Get the first image in content
                    $aPost['image_path'] = (string)$ePost->{'photo-url'};
                    $aPost['text'] = '';
                    if(isset($ePost->{'photoset'})) {
                        foreach ($ePost->{'photoset'}[0] as $sPhotoUrl) {
                            $aPhotoUrl = (array)$sPhotoUrl->{'photo-url'};
                            $aPost['text'] .= "<img src=" . (string)$aPhotoUrl[0] . " alt=''/><br/><br/>";
                        }
                    }
                    $aPost['text'] .= $ePost->{'photo-caption'};
                    break;
                case "quote" :
                    $aPost['title'] = htmlspecialchars(strip_tags($ePost->{'quote-text'}));
                    $aPost['text'] = $ePost->{'quote-text'} . "<br/>" . $ePost->{'quote-source'};
                    $aPost['image_path'] = "";
                    break;
                case "link" :
                    $aPost['title'] = htmlspecialchars(strip_tags($ePost->{'link-text'}));
                    $aPost['text'] = "<a href='" . $ePost->{'link-url'} . "'>" . $ePost->{'link-text'} . "</a><br/>" . $ePost->{'link-description'};

                    // Get Image from link
                    $url = (string)$ePost->{'link-url'};
                    $content = file_get_contents($url);
                    // Get the first image in content
                    if (preg_match_all('/< *img[^>]*src *= *["\']?([^"\']*)/i', $content, $matches)) {
                        $cur_size = 0;
                        $cur_img = "";
                        foreach ($matches[1] as $match) {
                            if (!empty($match) && strpos('http', $match) !== false) {
                                $new_size = getimagesize($match)[0] * getimagesize($match)[1];
                                if ($new_size > $cur_size) {
                                    $cur_size = $new_size;
                                    $cur_img = $match;
                                }
                            }
                        }
                        $aPost['image_path'] = $cur_img;
                    } else {
                        $aPost['image_path'] = "";
                    }
                    break;
                case "conversation" :
                    $title = htmlspecialchars(strip_tags($ePost->{'conversation-title'}));
                    if ($title != "")
                        $aPost['title'] = $title;
                    else
                        $aTitles[] = "Conversation";
                    $sTemp = '';
                    foreach ($ePost->{'conversation'}[0] as $line) {
                        $sTemp .= "<strong>" . $line->attributes()->label . "</strong>" . $line . "<br/>";
                    }
                    $aPost['text'] = $sTemp;
                    $aPost['image_path'] = "";
                    break;
                case "audio":
                    $aPost['title'] = htmlspecialchars(strip_tags($ePost->{'id3-title'}));
                    $aPost['text'] = $ePost->{'audio-embed'} . "<br/>" . $ePost->{'audio-caption'};
                    $aPost['image_path'] = "";
                    break;
                case "video":
                    $aPost['title'] = htmlspecialchars(strip_tags($ePost->{'video-caption'}));
                    $aPost['text'] = $ePost->{'video-player'};
                    $aPost['image_path'] = "";
                    break;
            }
            if (!empty($aPost)) {
                foreach ((array)$ePost->{'tag'} as $tag) {
                    $aPost['list_tags'][] = $tag;
                }

                $oTimeStamp = (array)$ePost->attributes()->{'unix-timestamp'};
                $aPost['time_stamp'] = $oTimeStamp[0];

                $aPosts[] = $aPost;
            }
        }
        if ($bFlag == false) {
            return Phpfox_Error::display('import_error_or_no_entry_was_gotten');
        }
        return $aPosts;
    }
}
