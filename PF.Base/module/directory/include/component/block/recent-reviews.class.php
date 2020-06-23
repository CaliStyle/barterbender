<?php

defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_Recent_Reviews extends Phpfox_Component
{

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $iLimit = $this->getParam('limit', 3);
        if (!$iLimit) {
            return false;
        }

        $aRecentReviews = Phpfox::getService('directory')->getRecentReviews($iLimit);

        foreach ($aRecentReviews as $key_review => $aReview) {
            $aRecentReviews[$key_review]['business_link'] = Phpfox::getLib('url')->permalink('directory.detail',
                $aReview['business_id'], $aReview['name']);

        }
        if (!count($aRecentReviews)) {
            return false;
        }
        $this->template()->assign(array(
                'aRecentReviews' => $aRecentReviews,
                'sHeader' => _p('directory.recent_reviews'),
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
                'info' => _p('Recent Reviews Businesses Limit'),
                'description' => _p('Define the limit of how many recent reviews businesses can be displayed when viewing the directory section. Set 0 will hide this block.'),
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
                'title' => '"Recent Reviews Businesses Limit" must be greater than or equal to 0'
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
                'aRecentReviews',
                'sHeader',
                'limit',
            )
        );

        (($sPlugin = Phpfox_Plugin::get('directory.component_block_recent_reviews_clean')) ? eval($sPlugin) : false);
    }

}
