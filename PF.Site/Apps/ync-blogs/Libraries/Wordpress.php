<?php

namespace Apps\YNC_Blogs\Libraries;

use DOMDocument;
use Phpfox_Error;

class Wordpress
{
    private $_wpXML;

    function __construct($_wpXML)
    {
        $this->_wpXML = $_wpXML;
    }

    public function getPosts()
    {
        $oDoc = new DOMDocument();
        @$oDoc->load($this->_wpXML);

        $eChanels = $oDoc->getElementsByTagName("channel");
        $aPosts = [];
        $bFlag = false;
        foreach ($eChanels as $eChannel) {
            $eItemss = $eChannel->getElementsByTagName("item");
            $bFlag = true;
            foreach ($eItemss as $eItems) {
                // Get content
                $eContents = $eItems->getElementsByTagName("encoded");
                $sContent = $eContents->item(0)->nodeValue;

                if ($sContent != "") {
                    $aPost['text'] = $sContent;

                    // Get title
                    $eTitles = $eItems->getElementsByTagName("title");
                    $aPost['title'] = $eTitles->item(0)->nodeValue;

                    // Get public day
                    $eDate = $eItems->getElementsByTagName("post_date");
                    $aPost['time_stamp'] = strtotime($eDate->item(0)->nodeValue);

                    // Get the first image in content
                    if (preg_match('/< *img[^>]*src *= *["\']?([^"\']*)/i', $sContent, $matches)) {
                        // echo $matches[1];die;
                        $aPost['image_path'] = strtok($matches[1], '?');
                    } else {
                        $aPost['image_path'] = "";
                    }

                    $aPosts[] = $aPost;
                }
            }
        }

        if ($bFlag == false) {
            return Phpfox_Error::display('import_error_or_no_entry_was_gotten');
        }

        return $aPosts;
    }
}
