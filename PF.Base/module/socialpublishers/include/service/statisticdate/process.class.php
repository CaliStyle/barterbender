<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class SocialPublishers_Service_Statisticdate_Process extends Phpfox_Service {

    public function updateTotalPostByDate($sType = 'facebook', $iStatisticDate = 0)
    {
        if ($iStatisticDate <= 0)
        {
            $iStatisticDate = strtotime(gmdate("M d Y", time()));
        }

        $aStatisticDate = $this->database()
                ->select('id')
                ->from(Phpfox::getT('socialpublishers_statistic_date'))
                ->where('statistic_date = ' . (int) $iStatisticDate)
                ->execute('getRow');

        if (!isset($aStatisticDate['id']))
        {
            $aValues = array();
            $aValues['statistic_date'] = (int) $iStatisticDate;
            $aValues['total_facebook_post'] = 0;
            $aValues['total_twitter_post'] = 0;
            $aValues['total_linkedin_post'] = 0;

            $iStatisticDateId = $this->database()->insert(Phpfox::getT('socialpublishers_statistic_date'), $aValues);
        }
        else
        {
            $iStatisticDateId = $aStatisticDate['id'];
        }

        $sSql = '';
        switch ($sType) {
            case 'facebook':
                $sSql = "UPDATE `" . Phpfox::getT('socialpublishers_statistic_date') . "` SET `total_facebook_post` = total_facebook_post + 1 WHERE  `id`= " . (int) $iStatisticDateId . " LIMIT 1;";
                break;

            case 'twitter':
                $sSql = "UPDATE `" . Phpfox::getT('socialpublishers_statistic_date') . "` SET `total_twitter_post` = total_twitter_post + 1 WHERE  `id`= " . (int) $iStatisticDateId . " LIMIT 1;";
                break;

            case 'linkedin':
                $sSql = "UPDATE `" . Phpfox::getT('socialpublishers_statistic_date') . "` SET `total_linkedin_post` = total_linkedin_post + 1 WHERE  `id`= " . (int) $iStatisticDateId . " LIMIT 1;";
                break;
            default:
                return false;
                break;
        }

        return $this->database()->query($sSql);
    }

    public function deleteAll()
    {
        return Phpfox::getLib('database')->delete(Phpfox::getT('socialpublishers_statistic_date'), TRUE);
    }

}

?>