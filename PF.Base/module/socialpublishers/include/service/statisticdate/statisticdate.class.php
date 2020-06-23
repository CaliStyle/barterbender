<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class SocialPublishers_Service_Statisticdate_Statisticdate extends Phpfox_Service 
{
    public function adminGet($aConds, $sSort = 'd.statistic_date ASC', $iPage = '', $iLimit = '')
    {
        $iCount = $this->database()
                ->select('COUNT(d.id)')
                ->from(Phpfox::getT('socialpublishers_statistic_date'), 'd')
                ->where($aConds)
                ->order($sSort)
                ->execute('getSlaveField');
        
        $aItems = array();

        if ($iCount > 0)
        {
            $aItems = $this->database()
                    ->select('d.*')
                    ->from(Phpfox::getT('socialpublishers_statistic_date'), 'd')
                    ->where($aConds)
                    ->order($sSort)
                    ->limit($iPage, $iLimit, $iCount)
                    ->execute('getSlaveRows');
        }

        return array($iCount, $aItems);
    }
}

?>