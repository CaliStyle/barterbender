<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Videochannel_Component_Block_Related extends Phpfox_Component
{

    private function getServiceVideochannel()
    {
        $oObject = Phpfox::getService('videochannel');
        $oObject instanceof Videochannel_Service_Videochannel;
        return $oObject;
    }

    private function pager()
    {
        $oObject = Phpfox::getLib('pager');
        $oObject instanceof Phpfox_Pager;
        return $oObject;
    }

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $aVideo = $this->getParam('aVideo');

        $iCategoryId = 0;
        if (count($aVideo['breadcrumb']))
        {
            foreach($aVideo['breadcrumb'] as $aCategory)
            {
                $iCategoryId = $aCategory[2];
            }
        }

        list(,$aVideos) = $this->getServiceVideochannel()->getRelatedVideosSuggestions($aVideo['video_id'], '', true, false, $iCategoryId);

        if (!count($aVideos))
        {
            return false;
        }

        $arAssign = array(
            'sHeader' => _p('related_videos'),
            'aRelatedVideos' => $aVideos
        );
        $this->template()->assign($arAssign);

        return 'block';
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('videochannel.component_block_related_clean')) ? eval($sPlugin) : false);
    }

}

?>