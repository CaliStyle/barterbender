<?php

namespace Apps\YNC_Member\Block;

use \Phpfox;

class PeopleYouMayKnow extends \Phpfox_Component
{
    public function process()
    {
        if (!Phpfox::isModule('suggestion') && !Phpfox::isModule('userconnect')) {

            $iLimit = $this->getParam('limit', 4);
            
            if ($iLimit <= 0) {
                return false;
            }

            if (Phpfox::isModule('friend')){
                $aUsers = Phpfox::getService('friend.suggestion')->get();
            } else {
                return false;
            }

            if (empty($aUsers)) {
                return false;
            }

            $aUsers = array_slice($aUsers, 0, $iLimit);

            Phpfox::getService('ynmember.browse')->processRows($aUsers);

            $this->template()->assign([
                'sHeader' => _p('People You May Know'),
                'aUsers' => $aUsers
            ]);

            return 'block';
        }
    }

    public function getSettings()
    {
        return [
            [
                'info' => _p('People You May Know Limit'),
                'description' => _p('Maximum number of items on block "People You May Know"'),
                'value' => 4,
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
                'title' => _p('"People You May Know Limit" must be greater than or equal to 0')
            ],
        ];
    }
}