<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_HomePage extends Phpfox_Component
{

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $bIsHomePage = $this->getParam('bInHomepageFr');
        if(!$bIsHomePage) {
            return false;
        }

        $iPage = $this->getParam('page', 0);
        $aItem = $this->getParam('aItem', false);
        $iCnt = $this->getParam('iCnt', false);

        $viewType = 'listview';
        if($this->getParam('viewType')){
            $viewType  = $this->getParam('viewType');
        }
        // $getAjax = $this->getParam('getAjax', false);

        $iLimit = 12;

        if($aItem === false){
            list($iCnt,$aBusinessHomepage) = Phpfox::getService('directory')->getBusiness('', $iLimit, $iPage);
        } else {
            $aBusinessHomepage = $aItem;
        }

        foreach ($aBusinessHomepage as $key_hompage => $aBusiness) {
            $aChildCategory = Phpfox::getService('directory')->isHaveChildCategory($aBusiness['business_id'],$aBusiness['category_id']);   
            $aBusinessHomepage[$key_hompage]['childCategory'] = $aChildCategory;
            $aBusinessHomepage[$key_hompage]['phone_number'] = Phpfox::getService('directory')->getBusinessPhone($aBusiness['business_id']);
            $aBusinessHomepage[$key_hompage]['phone_number'] = $aBusinessHomepage[$key_hompage]['phone_number'][0]['phone_number'];

            $aCoverPhotos = Phpfox::getService('directory')->getImages($aBusiness['business_id'], 1);
            $sPathCoverPhoto = "";
            if(count($aCoverPhotos)){

                $aBusinessHomepage[$key_hompage]['default_cover'] =  false;
                $sPathCoverPhoto = 'yndirectory/'.$aCoverPhotos[0]['image_path'];
            }
            else{
                $aBusinessHomepage[$key_hompage]['default_cover'] =  true;
                $sPathCoverPhoto = Phpfox::getParam('core.path').'module/directory/static/image/default_cover.png';
            }

            $aBusinessHomepage[$key_hompage]['cover_photo'] = $sPathCoverPhoto;
        }
      
    // Phpfox::getLib('pager')->set(array('ajax' => 'directory.getBusHomepageAjax', 'page' => $iPage, 'size' => $iLimit, 'count' => $iCnt, 'aParams' => $aParams));
       Phpfox::getLib('pager')->set(array('page' => $iPage, 'size' =>$iLimit, 'count' => $iCnt));
        $this->template()->assign(array(
                'aBusinessHomepage' => $aBusinessHomepage,
                'sCorePath' => Phpfox::getParam('core.path'),
                'viewType' => $viewType,
                'sCustomClassName' => 'ync-block',
                'sHeader' => _p('newest_businesses')
            )
        );

        return 'block';
    }

}

?>