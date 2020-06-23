<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_Business_Extra_Info extends Phpfox_Component
{

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {


        $sType = $this->getParam('sType');
        $iBusinessId = $this->getParam('iBusinessId');

        $aInfo = array();
        switch ($sType) {
            case 'phone':
                $aPhones = Phpfox::getService('directory')->getBusinessPhone($iBusinessId);
                foreach ($aPhones as $key =>$aPhone) {
                    $aInfo[$key]['text'] = $aPhone['phone_number'];
                    $aInfo[$key]['link'] = '';
                    $aInfo[$key]['type'] = 'phone';
                }
                break;
            case 'website':
                $aWebsites = Phpfox::getService('directory')->getBusinessWebsite($iBusinessId);                
                foreach ($aWebsites as $key =>$aWebsite) {
                    $aInfo[$key]['text'] = $aWebsite['website_text'];
                    $url = $aWebsite['website_text'];
                    // to clarify, this shouldn't be === false, but rather !== 0
                    if (false === strpos($url, 'http://') && false === strpos($url, 'https://')) {
                       $url = "//{$url}";
                    } 
                    $aInfo[$key]['link'] = $url;
                    $aInfo[$key]['type'] = 'website';
                }
                break;
            case 'location':
                $aLocations = Phpfox::getService('directory')->getBusinessLocation($iBusinessId);                
                foreach ($aLocations as $key =>$aLocation) {
                    if($aLocation['location_title'] != ''){
                        $aInfo[$key]['text'] = $aLocation['location_title'];
                        $aInfo[$key]['text_location'] = $aLocation['location_address'];
                        $aInfo[$key]['lat'] = $aLocation['location_latitude'];
                        $aInfo[$key]['lng'] = $aLocation['location_longitude'];
                        $aInfo[$key]['link'] = '';
                        $aInfo[$key]['type'] = 'location';
                    }
                }
                break;
            default:
                break;
        }

        $this->template()->assign(array(
                'sHeader'  => $sType,
                'aInfo' => $aInfo,
                'sCustomClassName' => 'ync-block'
            )
        );

        return 'block';
    }

}

?>