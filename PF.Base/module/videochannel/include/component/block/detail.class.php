<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Videochannel_Component_Block_Detail extends Phpfox_Component
{

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $aVideo = $this->getParam('aVideo');
        $sGroup = $this->getParam('sGroup', '');

        $aItems = array(
            _p('videochannel.added') => Phpfox::getTime(Phpfox::getParam('videochannel.video_time_stamp'), $aVideo['time_stamp'])
        );

        if (Phpfox::isModule('comment'))
        {
            $aItems[_p('videochannel.comments')] = $aVideo['total_comment'];
        }

        $this->template()->assign(array(
            'aVideoDetails' => $aItems,
            'sGroup' => $sGroup
                )
        );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('videochannel.component_block_detail_clean')) ? eval($sPlugin) : false);
    }

}

?>