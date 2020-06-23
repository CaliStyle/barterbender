<?php

defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_Most_Comment extends Phpfox_Component
{

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        if (!$this->getParam('bInHomepageFr')) {
            return false;
        }

        $iLimit = $this->getParam('limit', 3);
        if (!$iLimit) {
            return false;
        }

        $aMostDiscussBusinesses = Phpfox::getService('directory')->getBusiness($sType = 'most-comment', $iLimit);

        if (!$aMostDiscussBusinesses) {
            return false;
        }

        $this->template()->assign(array(
                'aMostDiscussBusinesses' => $aMostDiscussBusinesses,
                'sHeader' => _p('directory.most_discussed_businesses'),
                'sCustomClassName' => 'ync-block'
            )
        );
        return 'block';
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return [
            [
                'info' => _p('Most Discussed Businesses Limit'),
                'description' => _p('Define the limit of how many most discussed businesses can be displayed when viewing the directory section. Set 0 will hide this block.'),
                'value' => 3,
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
                'title' => '"Most Discussed Businesses Limit" must be greater than or equal to 0'
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
                'aMostDiscussBusinesses',
                'sHeader',
                'limit',
            )
        );

        (($sPlugin = Phpfox_Plugin::get('directory.component_block_most_discussed_clean')) ? eval($sPlugin) : false);
    }

}
