<?php
defined('PHPFOX') or exit('NO DICE!');
class Ynsocialstore_Component_Block_Store_DetailActivities extends Phpfox_Component
{
    public function process()
    {
        $this->setParam('allowTagFriends', false);
    }
}