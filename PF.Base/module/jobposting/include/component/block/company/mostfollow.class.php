<?php

defined('PHPFOX') or exit('NO DICE!');

class jobposting_component_block_Company_mostfollow extends Phpfox_Component
{

    public function process()
    {
        $iLimit = $this->getParam('limit', 4);
        if (!$iLimit) {
            return false;
        }

        $order = 'ca.total_follow desc';
        $aBlockCompanies = Phpfox::getService('jobposting.company')->getBlockCompany(null, $order, $iLimit);
        if (empty($aBlockCompanies)) {
            return false;
        }

        $this->template()->assign(array(
                'sHeader' => _p('most_followed_employers'),
                'aBlockCompanies' => $aBlockCompanies,
                'type_id' => 2,
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
                'info' => _p('Most Followed Companies Limit'),
                'description' => _p('Define the limit of how many most followed companies can be displayed when viewing the social store section. Set 0 will hide this block.'),
                'value' => 4,
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
                'title' => '"Most Followed Companies Limit" must be greater than or equal to 0'
            ]
        ];
    }

}