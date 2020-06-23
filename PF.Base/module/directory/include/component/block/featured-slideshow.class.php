<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_Featured_Slideshow extends Phpfox_Component
{

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $bIsInHomePage = $this->getParam('bInHomepageFr');
        if (!$bIsInHomePage) {
            return false;
        }

        $iLimit = 5;

        $aFeaturedBusinesses = Phpfox::getService('directory')->getBusiness($sType = 'featured', $iLimit);

        if(empty($aFeaturedBusinesses))
        {
            return false;
        }

        /*load cover photo instead of logo photo*/
        foreach ($aFeaturedBusinesses as $key => $aFeaturedBusiness) {
            $aCoverPhotos = Phpfox::getService('directory')->getImages($aFeaturedBusiness['business_id'], 1);
            $sPathCoverPhoto = "";
            if(count($aCoverPhotos)){
                
                $aFeaturedBusinesses[$key]['default_cover'] =  false;
                $sPathCoverPhoto = 'yndirectory/'.$aCoverPhotos[0]['image_path'];
            }
            else{
                $aFeaturedBusinesses[$key]['default_cover'] =  true;
                $sPathCoverPhoto = Phpfox::getParam('core.path').'module/directory/static/image/default_cover.png';
            }

            $aFeaturedBusinesses[$key]['cover_photo'] = $sPathCoverPhoto;
            $aFeaturedBusinesses[$key]['total_score'] = (int)$aFeaturedBusinesses[$key]['total_score']/2;
            if (empty($aFeaturedBusinesses[$key]['logo_path'])) {
                $aFeaturedBusinesses[$key]['default_logo_path'] = Phpfox::getService('directory')->getStaticPath() . 'module/directory/static/image/default_ava.png';
            }
        }

        $this->template()->assign(array(
                'aFeaturedBusinesses' => $aFeaturedBusinesses,
                'sCorePath' => Phpfox::getParam('core.path'),
                'sCustomClassName' => 'ync-block',
                'sHeader' => ''
            )
        );

        return 'block';
    }

}

?>