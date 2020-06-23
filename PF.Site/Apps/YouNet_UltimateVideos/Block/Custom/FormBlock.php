<?php
/**
 * [PHPFOX_HEADER]
 */
namespace Apps\YouNet_UltimateVideos\Block\Custom;

defined('PHPFOX') or exit('NO DICE!');

class FormBlock extends \Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $aCustomFields = $this->getParam('aCustomFields');
        $this->template()->assign(array(
            'aCustomFields' => $aCustomFields,
        ));
    }
}
