<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class SocialPublishers_Service_Statisticuser_Process extends Phpfox_Service 
{   
    public function updateTotalPostByUser($sType = 'facebook', $iUserId = 0)
    {
        if ($iUserId <= 0)
        {
            $iUserId = Phpfox::getUserId();
        }
        
        $aStatisticDate = $this->database()
                ->select('*')
                ->from(Phpfox::getT('socialpublishers_statistic_user'))
                ->where('user_id = ' . (int) $iUserId)
                ->execute('getRow');
        
        if (!isset($aStatisticDate['id']))
        {
            $aValues = array();
            $aValues['user_id'] = (int) $iUserId;
            $aValues['total_facebook_post'] = 0;
            $aValues['total_twitter_post'] = 0;
            $aValues['total_linkedin_post'] = 0;
            
            $iStatisticDateId = $this->database()->insert(Phpfox::getT('socialpublishers_statistic_user'), $aValues);
        }
        else
        {
            $iStatisticDateId = $aStatisticDate['id'];
        }
        
        $sSql = '';
        switch ($sType) {
            case 'facebook':
                $sSql = "UPDATE `" . Phpfox::getT('socialpublishers_statistic_user') .  "` SET `total_facebook_post` = total_facebook_post + 1 WHERE  `id`= " . (int) $iStatisticDateId . " LIMIT 1;";
                break;

            case 'twitter':
                $sSql = "UPDATE `" . Phpfox::getT('socialpublishers_statistic_user') .  "` SET `total_twitter_post` = total_twitter_post + 1 WHERE  `id`= " . (int) $iStatisticDateId . " LIMIT 1;";
                break;
            
            case 'linkedin':
                $sSql = "UPDATE `" . Phpfox::getT('socialpublishers_statistic_user') .  "` SET `total_linkedin_post` = total_linkedin_post + 1 WHERE  `id`= " . (int) $iStatisticDateId . " LIMIT 1;";
                break;
            default:
                return false;
                break;
        }
        
        return $this->database()->query($sSql); 
    }
    
    public function deleteAll()
    {
        return Phpfox::getLib('database')->delete(Phpfox::getT('socialpublishers_statistic_user'), TRUE);
    }
}

?>