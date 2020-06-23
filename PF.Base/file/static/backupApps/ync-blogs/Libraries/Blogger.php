<?php

namespace Apps\YNC_Blogs\Libraries;

use DOMDocument;
use Phpfox_Error;

class Blogger
{
    private $_blXML;

    function __construct($_blXML)
    {
        $this->_blXML = $_blXML;
    }

    public function getPosts()
    {
        $oDoc = new DOMDocument();
        @$oDoc->load($this->_blXML);

        $aPosts = [];
        $bFlag = false;
        $eEntrys = $oDoc->getElementsByTagName("entry");
        foreach ($eEntrys as $eEntry) {
            $eCategories = $eEntry->getElementsByTagName("category");
            $bFlag = true;
            $sTerm = $eCategories->item(0)->getAttribute('term');
            if (strpos($sTerm, 'kind#post')) {
                // Get content
                $eContents = $eEntry->getElementsByTagName("content");
                $sContent = $eContents->item(0)->nodeValue;

                if ($sContent != "") {
                    $aPost['text'] = $sContent;

                    // Get title
                    $eTitles = $eEntry->getElementsByTagName("title");
                    $aPost['title'] = $eTitles->item(0)->nodeValue;

                    // Get public time
                    $eDate = $eEntry->getElementsByTagName("published");
                    $aPost['time_stamp'] = strtotime($eDate->item(0)->nodeValue);

                    // Get the first image in content
                    if (preg_match('/< *img[^>]*src *= *["\']?([^"\']*)/i', $sContent, $matches)) {
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
