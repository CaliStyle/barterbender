<?php

namespace Apps\YNC_Member\Block;

use \Phpfox;

class TopRated extends \Phpfox_Component
{
    public function process()
    {
        if ($this->request()->get('view') == 'my')
            return false;

        $iLimit = $this->getParam('limit', 4);

        $aUsers = Phpfox::getService('ynmember.browse')->getTopRated($iLimit);

        if (empty($aUsers)) {
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
                        'link' => phpfox::getParam('core.path').'ynmember/review/?s=1&sort=highest_rated',
                    ]
                ]
            ]);
        }
        $this->template()->assign([
            'sHeader' => _p('Top Rated'),
            'aUsers' => $aUsers,
            'popularMode' => 'top_rated'
        ]);

        return 'block';
    }
    
    public function getSettings()
    {
        return [
            [
                'info' => _p('Top Rated Limit'),
                'description' => _p('Maximum number of items on block "Top Rated"'),
                'value' => 4,
                'type' => 'integer',
                'var_name' => 'limit',
            ],
        ];
    }

    /**
     * @return array
     */
    public function getValidation()
    {
        return [
            'limit' => [
                'def' => 'int',
                'min' => 0,
                'title' => _p('"Top Rated Limit" must be greater than or equal to 0')
            ],
        ];
    }
}