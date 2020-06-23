<?php
defined('PHPFOX') or exit('NO DICE!');

class Auction_Component_Block_Featured_Slideshow extends Phpfox_Component
{

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $iLimit = 5;

        $aFeaturedBusinesses = Phpfox::getService('auction')->getFeaturedProduct($sType = 'featured', $iLimit);

        if(empty($aFeaturedBusinesses))
        {
            return false;
        }

        /*load cover photo instead of logo photo*/
        foreach ($aFeaturedBusinesses as $key => $aFeaturedBusiness) {
            $aCoverPhotos = Phpfox::getService('directory')->getImages($aFeaturedBusiness['business_id']);
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

        }


        $this->template()->assign(array(
                'aFeaturedBusinesses' => $aFeaturedBusinesses,
                'sCorePath' => Phpfox::getParam('core.path'),
            )
        );

        return 'block';
    }

}

?>