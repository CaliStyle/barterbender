<?php

/**
 *
 *
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_ScheduledMail
 * @version          2.01
 */
defined('PHPFOX') or exit('NO DICE!');
if (!defined("TIME_DEV")) {
    define("TIME_DEV", 60 * 60);
}

class gettingstarted_service_gettingstarted extends Phpfox_Service {

    public function getMails($title_search, $category_search, $iPage, $iLimit, $iCnt) {
        $aRows = $this->database()->select('schmail.*,category.scheduledmail_name')
                ->from(phpfox::getT('gettingstarted'), 'schmail')
                ->join(phpfox::getT('gettingstarted_category'), 'category', 'category.scheduledmail_id=schmail.scheduledmail_category_id')
                ->where("schmail.name like '%" . $title_search . "%' and (schmail.scheduledmail_category_id = " . $category_search . " or '" . $category_search . "'='0')")
                ->group('category.scheduledmail_id,schmail.time')
                ->limit($iPage, $iLimit, $iCnt)
                ->execute('getSlaveRows');
        return $aRows;
    }

    public function getCountMails($title_search, $category_search) {
        $iCount = (int) $this->database()->select('count(*)')
                        ->from(phpfox::getT('gettingstarted'), 'schmail')
                        ->join(phpfox::getT('gettingstarted_category'), 'category', 'category.scheduledmail_id = schmail.scheduledmail_category_id')
                        ->where("schmail.name like '%" . $title_search . "%' and (schmail.scheduledmail_category_id = " . $category_search . " or '" . $category_search . "'='0')")
                        ->execute('getSlaveField');
        return $iCount;
    }

    public function getCategory($iPage, $iLimit, $iCnt) {
        $aRows = $this->database()->select('*')
                ->from(phpfox::getT('gettingstarted_category'))
                ->limit($iPage, $iLimit, $iCnt)
                ->execute('getSlaveRows');
        return $aRows;
    }

    public function getCountCategory() {
        $iCount = (int) $this->database()->select('count(*)')
                        ->from(phpfox::getT('gettingstarted_category'))
                        ->execute('getSlaveField');
        return $iCount;
    }

    public function getScheduledMail() {
        $aRows = $this->database()->select('schmail.*,category.scheduledmail_name')
                ->from(phpfox::getT('gettingstarted'), 'schmail')
                ->leftjoin(phpfox::getT('gettingstarted_category'), 'category', 'category.scheduledmail_id=schmail.scheduledmail_category_id')
                ->execute('getSlaveRows');
        return $aRows;
    }

    public function deleteScheduledMail($scheduledmail_id) {
        $this->database()->delete(phpfox::getT('gettingstarted'), "scheduledmail_id=" . $scheduledmail_id);
    }

    public function getUsers() {
        $aRows = $this->database()->select('*')
                ->from(phpfox::getT('user'), 'user')
                ->execute('getSlaveRows');
        return $aRows;
    }

    public function getUsersIp($user_id) {
        $aRows = $this->database()->select('*')
                ->from(phpfox::getT('user_ip'), 'user_ip')
                ->where('user_ip.user_id=' . $user_id)
                ->execute('getSlaveRows');
        return $aRows;
    }

    public function getUsersIp_type($user_id, $type) {
        $aRow = $this->database()->select('*')
                ->from(phpfox::getT('user_ip'), 'user_ip')
                ->where('user_ip.user_id = ' . $user_id . ' and type_id="' . $type . '"')
                ->execute('getSlaveRow');
        return $aRow;
    }

    public function add_historysend($aVals) {

        $aInsert = array();
        $aInsert['user_id'] = $aVals['user_id'];
        $aInsert['time_stamp'] = $aVals['time_stamp'];
        $aInsert['scheduledmail_id'] = $aVals['scheduledmail_id'];
        $aInsert['is_sent'] = $aVals['is_sent'];
        $this->database()->insert(phpfox::getT('gettingstarted_issend'), $aInsert);
    }

    public function get_historysend($aVals) {
        $aRow = $this->database()->select('*')
                ->from(phpfox::getT('gettingstarted_issend'), 'issend')
                ->where('issend.user_id = ' . $aVals['user_id'] . ' and issend.scheduledmail_id = ' . $aVals['scheduledmail_id'])
                ->order('issend.time_stamp desc')
                ->limit(1)
                ->execute('getSlaveRow');
        return $aRow;
    }

    public function delete_historysend($aVals) {
        $this->database()->delete(phpfox::getT('gettingstarted_issend'), "user_id=" . $aVals['user_id'] . " and scheduledmail_id=" . $aVals['scheduledmail_id']);
    }

    public function delete_scheduledmail_issend($iId) {
        $this->database()->delete(phpfox::getT('gettingstarted_issend'), "scheduledmail_id=" . $iId);
    }

    public function delete_schedulemail_letter($iId) {
        $this->database()->delete(phpfox::getT('gettingstarted_letter'), "scheduledmail_id = " . (int) $iId);
    }

    public function getAllCategoryMail() {
    	
		$where = 'scheduledmail_name<>"video"';
		if (Phpfox::isModule("video"))
		{
			$where = "1=1";
		}
		
        $aRows = $this->database()->select('*')
                ->from(phpfox::getT('gettingstarted_category'))
				->where($where)
                ->execute('getSlaveRows');

        return $aRows;
    }

    public function getAllCategoryArticle() {
        $aRows = $this->database()->select('*')
                ->from(phpfox::getT('gettingstarted_article_category'))
                ->execute('getSlaveRows');
        return $aRows;
    }

    public function getAllCategoryMailId($scheduledmail_id) {
        $aRow = $this->database()->select('*')
                ->from(phpfox::getT('gettingstarted_category'))
                ->where('scheduledmail_id=' . $scheduledmail_id)
                ->execute('getSlaveRow');
        return $aRow;
    }

    public function getScheduledMailId($id) {
        $aRow = $this->database()->select('mail.*,category.scheduledmail_name')
                ->from(phpfox::getT('gettingstarted'), 'mail')
                ->leftJoin(phpfox::getT('gettingstarted_category'), 'category', 'category.scheduledmail_id=mail.scheduledmail_category_id')
                ->where('mail.scheduledmail_id = ' . $id)
                ->execute('getSlaveRow');
        return $aRow;
    }

    public function addScheduledMail($aVals) {
        $aVals['time'] = $this->sortTimeString($aVals['time']);
        $aInserts = array();
        $oFilter = phpfox::getLib('parse.input');
        $aInserts['time'] = $aVals['time'];
        $aInserts['message'] = $aVals['message'];
        $aInserts['message_parsed'] = $oFilter->prepare($aVals['message']);
        $aInserts['scheduledmail_category_id'] = $aVals['scheduledmail_id'];
        $aInserts['active'] = $aVals['active'];
        $aInserts['unsubscribe_email'] = $aVals['unsubscribe'];
        $aInserts['name'] = $oFilter->clean($aVals['name']);
        $aInserts['time_stamp'] = PHPFOX_TIME;
        if ($this->getIsExist($aVals) == 0) {
            $this->database()->insert(phpfox::getT('gettingstarted'), $aInserts);
            return true;
        }
        else
            return false;
    }

    private function sortTimeString($sTime) {
        $aTime = explode(" ", $sTime);
        $aTimeResult = array();
        $sTimeResult = "";
        $iTime = "";
        if (count($aTime) > 1) {
            foreach ($aTime as $k => $v) {
                if (strpos($v, "m") != false) {
                    $aTimeResult["m"] = substr($v, 0, strlen($v) - 1);
                }
                if (strpos($v, "w") != false) {
                    $aTimeResult["w"] = substr($v, 0, strlen($v) - 1);
                }
                if (strpos($v, "d") != false) {
                    $aTimeResult["d"] = substr($v, 0, strlen($v) - 1);
                }
                if (strpos($v, "h") != false) {
                    $aTimeResult["h"] = substr($v, 0, strlen($v) - 1);
                }
            }
            if (isset($aTimeResult['m']) && $aTimeResult['m'] != null) {
                $sTimeResult .= $aTimeResult['m'] . 'm ';
            }
            if (isset($aTimeResult['w']) && $aTimeResult['w'] != null) {
                $sTimeResult .= $aTimeResult['w'] . 'w ';
            }
            if (isset($aTimeResult['d']) && $aTimeResult['d'] != null) {
                $sTimeResult .= $aTimeResult['d'] . 'd ';
            }
            if (isset($aTimeResult['h']) && $aTimeResult['h'] != null) {
                $sTimeResult .= $aTimeResult['h'] . 'h';
            }
            return $sTimeResult;
        }
        else
            return $sTime;
    }

    public function updateScheduledMail($aVals) {
        $aUpdates = array();
        $oFilter = phpfox::getLib('parse.input');
        $aUpdates['time'] = $aVals['time'];
        $aUpdates['message'] = $aVals['message'];
        $aUpdates['scheduledmail_category_id'] = $aVals['scheduledmail_category_id'];
        $aUpdates['message_parsed'] = $oFilter->prepare($aVals['message']);
        $aUpdates['name'] = $oFilter->clean($aVals['name']);
        $aUpdates['unsubscribe_email'] = (isset($aVals['unsubscribe_email']) ? $aVals['unsubscribe_email'] : '0');

        $this->database()->update(phpfox::getT('gettingstarted'), $aUpdates, 'scheduledmail_id=' . $aVals['scheduledmail_id']);
    }

    public function updateCategoryMail($aVals) {
        $aUpdates = array();
        $aUpdates['description'] = $aVals['description'];
        $aUpdates['time_stamp'] = PHPFOX_TIME;
        $this->database()->update(phpfox::getT('gettingstarted_category'), $aUpdates, 'scheduledmail_id=' . $aVals['scheduledmail_id']);
    }

    public function getIsExist($aVals) {
        $iCount = $this->database()->select('count(*) as count')
                ->from(phpfox::getT('gettingstarted'), 'mail')
                ->where('mail.scheduledmail_category_id=' . $aVals['scheduledmail_id'] . ' and mail.time="' . $aVals['time'] . '"')
                ->execute('getSlaveField');
        return $iCount;
    }

    public function getLastestTypeId($user_id, $type_id) {
        $aRow = $this->database()->select('*')
                ->from(phpfox::getT('user_ip'), 'ip')
                ->where('ip.type_id="' . $type_id . '" and ip.user_id=' . $user_id)
                ->order('ip.time_stamp desc')
                ->limit(1)
                ->execute('getSlaveRow');
        return $aRow;
    }

    public function addQueryLetter($aVals, $aTimes = PHPFOX_TIME) {
        $bIsExisLetter = $this->isExistScheduleMail($aVals['scheduledmail_id'], $aVals['to']);
        if ($bIsExisLetter == false) {
            $aInserts = array();
            $aInserts['user_id'] = $aVals['user_id'];
            $aInserts['message'] = $aVals['message'];
            $aInserts['subject'] = $aVals['subject'];
            $aInserts['time_stamp'] = $aTimes;
            $aInserts['scheduledmail_id'] = $aVals['scheduledmail_id'];
            $bCanUnsubscribe = $this->canUnsubscribe($aVals['scheduledmail_id']);
            $bUnsubscribe = Phpfox::getService('gettingstarted.unsubscribe')->isUnsubscribe($aVals['user_id']);
            if ($bCanUnsubscribe && $bUnsubscribe)
            {
                $aInserts['email_status'] = '-1';
            }
            Phpfox::getLib('database')->insert(phpfox::getT('gettingstarted_letter'), $aInserts);
        }
    }

    /*
     * Check the letter which must be sent to user is existing or not
     * return false if not, return true if exist
     */

    public function isExistScheduleMail($scheduledmail_id, $user_id) {
        $aRows = Phpfox::getLib('database')->select('scheduledmail_id, user_id')
                ->from(phpfox::getT('gettingstarted_letter'), 'letter')
                ->where('letter.scheduledmail_id = ' . (int) $scheduledmail_id . ' and letter.user_id = ' . $user_id)
                ->execute('getSlaveRows');
        if (empty($aRows))
            return false;
        return true;
    }
    
    public function canUnsubscribe($iScheduledMailId)
    {
        $iField = $this->database()->select('unsubscribe_email')
        ->from(Phpfox::getT('gettingstarted'))
        ->where('scheduledmail_id = '.$iScheduledMailId)
        ->execute('getSlaveField');
        
        if (!empty($iField) && $iField == 1)
        {
            return true;
        }
        
        return false;
    }

    public function getArticleCategoryforEdit($iId) {
        $aLanguage = PHPFOX::getLib('locale')->getLang();
        $language_id = $aLanguage['language_id'];
        if ($iId == -1) {
            $aRow = array(
                'article_category_id' => -1,
                'article_category_name' => _p('gettingstarted.uncategorized'),
            );
        } else {
            $aRow = Phpfox::getLib('database')->select('*')
                    ->from(Phpfox::getT('gettingstarted_article_category'), 'ac')
                    ->where("language_id = '" . $language_id . "' AND ac.article_category_id = " . $iId)
                    ->execute('getSlaveRow');
        }

        return $aRow;
    }

    public function getQueryLetter($iLimit) {
        $aRows = $this->database()->select('letter.*, g.unsubscribe_email')
                ->from(phpfox::getT('gettingstarted_letter'), 'letter')
                ->leftjoin(phpfox::getT('gettingstarted'), 'g', 'g.scheduledmail_id = letter.scheduledmail_id')
                ->order('letter.email_status asc, letter.time_stamp asc')
                ->where('letter.email_status <> -1')
                ->limit($iLimit)
                ->execute('getSlaveRows');
        return $aRows;
    }

    private function checkRegisterLetter($schedulemail_id) {
        $aRow = Phpfox::getLib('database')->select('gc.scheduledmail_name, g.scheduledmail_id')
                ->from(Phpfox::getT('gettingstarted'), 'g')
                ->leftjoin(phpfox::getT('gettingstarted_category'), 'gc', 'gc.scheduledmail_id = g.scheduledmail_category_id')
                ->where('g.scheduledmail_id = ' . $schedulemail_id)
                ->limit(1)
                ->execute('getSlaveRow');
        if (isset($aRow['scheduledmail_name']) && ($aRow['scheduledmail_name'] == 'register')) {
            return true;
        }
        return false;
    }

    public function updateSchedueLetter($aTimes, $schedulemail_id, $user_id) {
        $aRow = phpfox::getLib('database')->select('g.time,gl.email_status, gl.scheduledmail_id')
                ->from(phpfox::getT('gettingstarted'), 'g')
                ->leftjoin(phpfox::getT('gettingstarted_letter'), 'gl', 'gl.scheduledmail_id = g.scheduledmail_id AND gl.user_id = ' . $user_id)
                ->where('g.scheduledmail_id = ' . (int) $schedulemail_id)
                ->limit(1)
                ->execute('getRow');
        if (count($aRow) > 0) {
            $aRow['time']=(int)Phpfox::getService('gettingstarted.process')->CalTime($aRow['time']);
            $aUpdate['time_stamp'] = $aTimes + (TIME_DEV * $aRow['time']);
            $t = 0;
            if (isset($aRow['email_status']) && $this->checkRegisterLetter($aRow['scheduledmail_id'])) {
                $t = -1;
            }
            if (isset($aRow['email_status']) && $aRow['email_status'] == 0 && $t != -1) {
                $t = 2;
            }
            if (isset($aRow['email_status']) && $aRow['email_status'] >= 2 && $t != -1) {
                $t = $aRow['email_status'] + 1;
            }

            $aUpdate['email_status'] = $t;
        }

        Phpfox::getLib('database')->update(phpfox::getT('gettingstarted_letter'), $aUpdate, 'scheduledmail_id = ' . (int) $schedulemail_id . ' and user_id = ' . (int) $user_id);
    }

    public function updateUnsubscribeLetter($user_id, $scheduledmail_id)
    {
        $this->database()->query('UPDATE  `'.Phpfox::getT('gettingstarted_letter').'` gl JOIN  `'.Phpfox::getT('gettingstarted').'` g ON ( g.scheduledmail_id = gl.scheduledmail_id ) SET  gl.email_status = -1 WHERE g.unsubscribe_email = 1 AND gl.user_id = '.$user_id);
        
        Phpfox::getService('gettingstarted.unsubscribe')->add($user_id);
    }

    public function deleteQueryLetter($scheduledmail_letter_id) {
        $this->database()->delete(phpfox::getT('gettingstarted_letter'), "scheduledmail_letter_id= " . $scheduledmail_letter_id);
    }

    public function getModule($user_id, $type_id) {
        $aRow = $this->database()->select('*')
                ->from(phpfox::getT($type_id))
                ->where('user_id=' . $user_id)
                ->limit(1)
                ->execute('getSlaveRow');
        return $aRow;
    }

    public function updateMenuProfile($is_active) {
        $this->database()->update(phpfox::getT('menu'), array('is_active' => $is_active), 'm_connection="' . 'main' . '" and module_id="' . "gettingstarted" . '"');
    }

    //get top most view articles 
    public function getArticleMostView($limit) {
        $aLanguage = PHPFOX::getLib("locale")->getLang();
        $language_id = $aLanguage['language_id'];
        $aRows = $this->database()->select('article_id,title')
                ->from(PhpFox::getT('gettingstarted_article'))
                ->where("language_id ='" . $language_id . "'")
                ->order('total_view DESC, time_stamp DESC')
                ->limit($limit)
                ->execute('getSlaveRows');
        return $aRows;
    }

    //get top featured articles
    public function getArticleFeatured($limit) {
        $aLanguage = PHPFOX::getLib("locale")->getLang();
        $language_id = $aLanguage['language_id'];
        $aRows = $this->database()->select('article_id,title')
                ->from(PhpFox::getT('gettingstarted_article'))
                ->where('is_featured = 1' . " AND language_id ='" . $language_id . "'")
                ->order('time_stamp DESC')
                ->limit($limit)
                ->execute('getSlaveRows');
        return $aRows;
    }

    //get relate entries in same category 
    public function getRelatedEntries($iId, $iLimit) {
        $aLanguage = PHPFOX::getLib('locale')->getLang();
        $language_id = $aLanguage['language_id'];
        $aCategory = $this->database()->select('article_category_id')
                ->from(PhpFox::getT('gettingstarted_article'))
                ->where("language_id ='" . $language_id . "' AND article_id =" . $iId)
                ->execute('getSlaveRow');


        if (count($aCategory) == 0) {
            $aCategory = -1;
        } else {
            $aCategory = $aCategory['article_category_id'];
        }
        $aRows = $this->database()->select('article_id,title,total_view')
                ->from(PhpFox::getT('gettingstarted_article'))
                ->where("language_id ='" . $language_id . "' AND article_category_id =" . $aCategory . ' AND article_id <>' . $iId)
                ->order('total_view DESC, time_stamp DESC')
                ->limit($iLimit)
                ->execute('getSlaveRows');
        return $aRows;
    }

}

?>
