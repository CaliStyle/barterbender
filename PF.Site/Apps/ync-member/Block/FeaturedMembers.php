<?php

namespace Apps\YNC_Member\Block;

use \Phpfox;
use \Phpfox_Search;

class FeaturedMembers extends \Phpfox_Component
{
    public function process()
    {

        $iLimit = $this->getParam('limit', 20);

        if ($iLimit <= 0) {
            return false;
        }

        if ($this->request()->get('view') || $this->request()->get('s'))
            return false;

        if(Phpfox_Search::instance()->get('form_flag') == 1)
            return false;

        $limit = $this->getParam('limit', 8);

        list($aUsers, $iTotal) = Phpfox::getService('ynmember.feature')->get($limit);

        if (!$iTotal)
            return false;
        $aUsers = array_slice($aUsers, 0, $iLimit);

        Phpfox::getService('ynmember.browse')->processRows($aUsers, ['FriendStatus', 'Review']);

        $this->template()->assign([
            'aUsers' => $aUsers,
            'iTotal' => $iTotal,
        ]);

        return 'block';
    }

    public function getSettings()
    {
        return [
            [
                'info' => _p('Featured Members Limit'),
                'description' => _p('Maximum number of items on block "Featured Members"'),
                'value' => 20,
                'type' => 'integer',
                'var_name' => 'limit',
            ],
        ];
    }
    public function getValidation()
    {
        return [
            'limit' => [
                'def' => 'int',
                'min' => 0,
                'title' => _p('"Featured Members Limit" must be greater than or equal to 0')
            ],
        ];
    }

}