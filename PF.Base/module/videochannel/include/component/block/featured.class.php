<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Videochannel_Component_Block_Featured extends Phpfox_Component
{
    /**
     *
     * @var Videochannel_Service_Videochannel 
     */
    public $oSerVideoChannel;
    
    public function __construct($aParams)
    {
        parent::__construct($aParams);
        
        $this->oSerVideoChannel = Phpfox::getService('videochannel');
    }
    
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {



        $aParentModule = $this->getParam('aParentModule');
        $aFeatured = $this->oSerVideoChannel->getFeaturedVideos(5, isset($aParentModule['module_id']) ? $aParentModule['module_id'] : null, isset($aParentModule['item_id']) ? $aParentModule['item_id'] : null);

        $sView = $this->request()->get('view');


        if (count($aFeatured) == 0 || defined('PHPFOX_IS_USER_PROFILE') || $sView == 'channels'
                || $sView == 'all_channels')
        {
            return false;
        }

        $sLink = isset($aParentModule['module_id']) ? ($aParentModule['module_id'] . '/' . $aParentModule['item_id'] . '/videochannel' ) : 'videochannel';

        $this->template()->assign(array(
            'sHeader' => _p('videochannel.featured_videos'),
            'aFeatured' => $aFeatured,
            'bViewMore' => (count($aFeatured) == 5) ? true : false,
            'sLink' => isset($aParentModule['module_id']) ? ($aParentModule['module_id'] . '/' . $aParentModule['item_id'] . '/videochannel' ) : 'videochannel'
                )
        );

        if (count($aFeatured) >= 5) {
            $this->template()->assign(array(
                'aFooter' => array(
                    _p('view_more') => $this->url()->makeUrl($sLink) . '?sort=featured'
                ),
            ));
        }

        return 'block';
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('videochannel.component_block_featured_clean')) ? eval($sPlugin) : false);
    }

}

?>