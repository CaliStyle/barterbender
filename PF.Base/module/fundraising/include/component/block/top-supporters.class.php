<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Component_Block_Top_Supporters extends Phpfox_Component
{


    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        // Must be not in homepage
        if (!$this->getParam('bInHomepageFr')) {
            return false;
        }

        // Limit
        $iLimit = $this->getParam('limit', 12);
        if (!$iLimit) {
            return false;
        }

        $aSupporters = Phpfox::getService('fundraising.user')->getTopSupporters($iLimit);
        if (count($aSupporters) == 0 || defined('PHPFOX_IS_USER_PROFILE') || defined('PHPFOX_IS_PAGES_VIEW')) {
            return false;
        }
        $this->template()->assign(array(
            'aSupporters' => $aSupporters,
            'sHeader' => _p('top_supporters')
        ));
        return 'block';

    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return [
            [
                'info' => _p('Number of Supporters Limit'),
                'description' => _p('Define the limit of how many supporters in top supporters block can be displayed when viewing the fundraising section. Set 0 will hide this block.'),
                'value' => 12,
                'type' => 'integer',
                'var_name' => 'limit',
            ]
        ];
    }

    public function getValidation()
    {
        return [
            'limit' => [
                'def' => 'int',
                'min' => 0,
                'title' => '"Number of Supporters Limit" must be greater than or equal to 0'
            ]
        ];
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        // Lets clear it from memory
        $this->template()->clean(array(
            'limit',
        ));

        (($sPlugin = Phpfox_Plugin::get('fundraising.component_block_top_supporters_clean')) ? eval($sPlugin) : false);
    }

}
