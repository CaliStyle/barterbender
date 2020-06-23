<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Videochannel_Component_Block_Channel_Videolist extends Phpfox_Component {

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        Phpfox::isUser(true);
        $sUrl = base64_decode($this->getParam('sUrl'));

        if (empty($sUrl))
        {
            return Phpfox_Error::display(_p('videochannel.invalid_channel_link'));
        }

        //Limit videos per page
        $iLimit = 6;

        //How many video can grab by user
        $iMaxNum = Phpfox::getUserParam('videochannel.channel_add_videos_limit');

        //Grab videos
        $aVideos = array();
        $aVideos = Phpfox::getService('videochannel.channel.process')->getVideos($sUrl, $iMaxNum, false);

        $this->template()->assign(array(
            'aVideos' => $aVideos,
            'iVideoCount' => count($aVideos),
            'iLimit' => $iLimit
                )
        );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('videochannel.component_block_channel_videolist_clean')) ? eval($sPlugin) : false);
    }

}

?>