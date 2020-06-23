<?php

/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 * @copyright      YouNet Company
 * @author         VuDP, TienNPL
 * @package        Module_Resume
 * @version        3.01
 * 
 */
class Contest_Service_Browse extends Phpfox_Service
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('contest');
    }

    public function query()
    {
        if ($this->_isEntryBrowse())
        {
            return Phpfox::getService("contest.entry.browse")->query();
        }
    }

    public function getQueryJoins($bIsCount = false, $bNoQueryFriend = false)
    {
        if ($this->_isEntryBrowse())
        {
            return Phpfox::getService("contest.entry.browse")->getQueryJoins($bIsCount, $bNoQueryFriend);
        }
		$this->database()->innerJoin(Phpfox::getT('user'), 'userDelete', 'userDelete.user_id = ct.user_id');
        if (Phpfox::isModule('friend') && Phpfox::getService('friend')->queryJoin($bNoQueryFriend))
        {
            $this->database()->join(Phpfox::getT('friend'), 'friends', 'friends.user_id = ct.user_id AND friends.friend_user_id = '.Phpfox::getUserId());
        }

        // Category Filter
        if ($this->request()->get((defined('PHPFOX_IS_USER_PROFILE') ? 'req3' : 'req2')) == 'category')
        {
            $this->database()->innerJoin(Phpfox::getT('contest_category_data'), 'rcd', 'rcd.contest_id = ct.contest_id')->innerJoin(Phpfox::getT('contest_category'), 'rc', 'rc.category_id = rcd.category_id');
        }
        
        $sView = $this->request()->get('view');
        if ($sView == 'my_following' || $sView == 'my_favorite')
        {
            $this->database()->leftJoin(Phpfox::getT('contest_participant'),'pa','pa.contest_id=ct.contest_id and pa.user_id = '.Phpfox::getUserId());
        }
    }

    private function _isEntryBrowse()
    {
        $sView = $this->request()->get('view', false);
        if ($sView == 'my_entries' || $sView == 'pending_entries')
        {
            return true;
        }

        $sController = Phpfox::getLib("module")->getFullControllerName();
        if ($sController == 'contest.entry/index' || $sController == 'contest.view')
        {
            return true;
        }

        return false;
    }

    /**
     * If a call is made to an unknown method attempt to connect
     * it to a specific plug-in with the same name thus allowing 
     * plug-in developers the ability to extend classes.
     *
     * @param string $sMethod is the name of the method
     * @param array $aArguments is the array of arguments of being passed
     */
    public function __call($sMethod, $aArguments)
    {
        /**
         * Check if such a plug-in exists and if it does call it.
         */
        if ($sPlugin = Phpfox_Plugin::get('contest.service_browse__call'))
        {
            eval($sPlugin);
            return;
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method '.__class__.'::'.$sMethod.'()', E_USER_ERROR);
    }
}

?>