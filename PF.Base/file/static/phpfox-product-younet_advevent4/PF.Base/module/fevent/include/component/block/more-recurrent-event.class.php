<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright       [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Event
 */
class Fevent_Component_Block_More_Recurrent_Event extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $iEventId = $this->getParam('iEventId');
        $iOrginId = $this->getParam('iOrginId');
        $iPage = $this->getParam('iPage');
        $iPageSize = $this->getParam('iPageSize');

        list(, $aCurrentEvents) = Phpfox::getService('fevent')->getAjaxBrotherEventByEventId($iEventId, $iOrginId, $iPage, $iPageSize);

        $this->template()->assign(array(
            'aCurrentEvents' => $aCurrentEvents
        ));
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('fevent.component_block_info_clean')) ? eval($sPlugin) : false);
    }
}

?>