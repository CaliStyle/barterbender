<?php

namespace Apps\YNC_Member\Service;

use Phpfox;
use Phpfox_Service;
use Phpfox_Plugin;

defined('PHPFOX') or exit('NO DICE!');

class Feature extends Phpfox_Service
{
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('user_featured');
    }

    public function get()
    {
        if ($sPlugin = Phpfox_Plugin::get('user.service_featured_get_1'))
        {
            eval($sPlugin);
            if (isset($mPluginReturn)){ return $mPluginReturn; }
        }
        $iTotal = Phpfox::getParam('user.how_many_featured_members');

        $aUsers = $this->database()->select(Phpfox::getUserField() . ', uf.ordering')
            ->from(Phpfox::getT('user'), 'u')
            ->join($this->_sTable, 'uf', 'uf.user_id = u.user_id')
            ->where(Phpfox::getService('ynmember.browse')->excludeBlockedConds())
            ->order('ordering DESC')
            ->limit(100)
            ->execute('getSlaveRows');


        if (!is_array($aUsers)) return array(array(), 0);
        $aOut = array();
        shuffle($aUsers);

        $iCount = count($aUsers); // using count instead of $this->database()->limit to measure the real value
        for ($i = 0; $i <= $iTotal; $i++)
        {
            if (!isset($aUsers[$iCount -$i])) continue; // availability check
            $aOut[] = $aUsers[$iCount - $i];
        }

        return array($aOut, count($aUsers));
    }
}