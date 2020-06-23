<?php

namespace Apps\YNC_Member\Block;

use \Phpfox;

class MostReviewed extends \Phpfox_Component
{
    public function process()
    {
        if ($this->request()->get('view') == 'my')
            return false;

        $iLimit = $this->getParam('limit', 4);
    
        $aUsers = Phpfox::getService('ynmember.browse')->getMostReviewed($iLimit);


        if (!count($aUsers)) {
            return false;
        }

        $aFields = [
            'Places',
            'MutualFriends',
            'FriendStatus',
            'Pages',
            'Groups',
            'Review',
        ];
        
        Phpfox::getService('ynmember.browse')->processRows($aUsers, $aFields);

        if(count($aUsers) >= $iLimit)
        {
            $this->template()->assign([
                'aFooter' => [
                    _p('more') => [
                        'link' => phpfox::getParam('core.path').'ynmember/review/?s=1&sort=most_viewed',
                    ]
                ]
            ]);
        }
        $this->template()->assign([
            'sHeader' => _p('Most Reviewed'),
            'aUsers' => $aUsers,
            'popularMode' => 'most_reviewed'
        ]);

        return 'block';
    }

    public function getSettings()
    {
        return [
            [
                'info' => _p('Most Reviewed Limit'),
                'description' => _p('Maximum number of items on block "Most Reviewed"'),
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
                'title' => _p('"Most Reviewed Limit" must be greater than or equal to 0')
            ],
        ];
    }
}