<?php
namespace Apps\P_AdvEvent\Service;

use Phpfox;
use Phpfox_Service;
use Phpfox_Error;
use Phpfox_Plugin;

class Browse extends Phpfox_Service
{
    private $_sCategory = null;

    private $_iAttending = null;

    private $_aCallback = false;

    private $_bFull = false;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('fevent');
    }

    public function category($sCategory)
    {
        $this->_sCategory = $sCategory;

        return $this;
    }

    public function attending($iAttending)
    {
        $this->_iAttending = $iAttending;

        return $this;
    }

    public function callback($aCallback)
    {
        $this->_aCallback = $aCallback;

        return $this;
    }

    public function full($bFull)
    {
        $this->_bFull = $bFull;

        return $this;
    }

    public function query()
    {
        $this->database()->select('m.server_id as event_server_id, ');

        if ($this->_iAttending !== null)
        {
            $this->database()->group('m.event_id');
        }
    }

    public function processRows(&$aRows)
    {
        $oHelper = Phpfox::getService('fevent.helper');
        $len = count($aRows);

        $iCurYear = Phpfox::getTime('Y');

        for($i = 0; $i < $len; $i ++){
            $aRows[$i]['d_type'] = $oHelper->getTimeLineStatus($aRows[$i]['start_time'], $aRows[$i]['end_time']);
            if('upcoming' == $aRows[$i]['d_type']){
                $aRows[$i]['d_start_in'] = $oHelper->timestampToCountdownString($aRows[$i]['start_time'], 'upcoming');
            } if('ongoing' == $aRows[$i]['d_type']){
                $aRows[$i]['d_left'] = $oHelper->timestampToCountdownString($aRows[$i]['end_time'], 'ongoing');
            }

            if((int)$aRows[$i]['isrepeat'] >= 0)
            {
                if($aRows[$i]['after_number_event'] > 0){
                    $aLastInstance = Phpfox::getService('fevent')->getLastInstanceEvent($aRows[$i]['org_event_id']);
                    if(!empty($aLastInstance)){
                        $aRows[$i]['timerepeat'] = $aLastInstance['start_time'] ;
                    }
                }

                $aRows[$i]['d_repeat_time'] = $oHelper->displayRepeatTime((int)$aRows[$i]['isrepeat'], (int)$aRows[$i]['timerepeat']);
            }

            //  any status event (upcoming, ongoing, past) has start time
            //  with: upcoming event: start time at this time is next start time
            $aRows[$i]['d_next_start_time'] = $aRows[$i]['d_start_time'];

            $aRows[$i]['M_start_time'] = $oHelper->displayTimeByFormat('M', (int)$aRows[$i]['start_time']); //month

            $aRows[$i]['date_end_time1'] = $oHelper->displayTimeByFormat('M j', (int)$aRows[$i]['end_time']);
            $aRows[$i]['year'] = $oHelper->displayTimeByFormat('Y', (int)$aRows[$i]['end_time']);
            $aRows[$i]['check'] = abs($iCurYear - $aRows[$i]['year']);
            $aRows[$i] = $oHelper->retrieveEventPermissions($aRows[$i]);
            $oHelper->getImageDefault($aRows[$i],'home');
            $location = '';
            if(!empty($aRows[$i]['location'])) {
                $location .= $aRows[$i]['location'] .' - ';
            }
            if(!empty($aRows[$i]['address'])) {
                $location .= $aRows[$i]['address'] . ' - ';
            }
            if(!empty($aRows[$i]['city'])) {
                $location .= $aRows[$i]['city'];
            }
            $aRows[$i]['location_parsed'] = trim($location, ' - ');

            if($aRows[$i]['isrepeat'] != -1) {
                if ($aRows[$i]['isrepeat'] == 0) {
                    $aRows[$i]['repeat_title'] = _p('daily');
                } else if ($aRows[$i]['isrepeat'] == 1) {
                    $aRows[$i]['repeat_title'] = _p('weekly');
                } else if ($aRows[$i]['isrepeat'] == 2) {
                    $aRows[$i]['repeat_title'] = _p('monthly');
                }
            }
            $aRows[$i]['is_invited'] = !empty($aRows[$i]['invitee_id']) && !empty($aRows[$i]['inviter_id']) ? ($aRows[$i]['inviter_id'] != $aRows[$i]['invitee_id'] ? true : ($aRows[$i]['user_id'] == $aRows[$i]['invitee_id'] ? true : false)) : false;

            $timeNeedToFormatted = in_array($aRows[$i]['d_type'], ['ongoing', 'past']) ? $aRows[$i]['end_time'] : $aRows[$i]['start_time'];
            $aRows[$i]['date_formatted'] = Phpfox::getService('fevent.helper')->formatTimeToDate($aRows[$i]['d_type'], $timeNeedToFormatted);

            Phpfox::getService('fevent')->getMoreInfoForEventItem($aRows[$i]);
        }
    }


    /**
     * Convert seconds to string
     * @param int $timeInSeconds
     * @return string
     */
    public function seconds2string($timeInSeconds)
    {
        static $phrases = null;

        $seeks = array(
            31536000,
            2592000,
            86400,
            3600,
            60);

        if (null == $phrases)
        {
            $phrases = array(array(
                _p('year'),
                _p('month'),
                _p('day'),
                _p('hour'),
                _p('minute')), array(
                _p('years'),
                _p('months'),
                _p('days'),
                _p('hours'),
                _p('minutes')));
        }

        $result = [];

        $remain = $timeInSeconds;

        foreach ($seeks as $index => $seek)
        {
            $check = intval($remain / $seek);
            $remain = $remain % $seek;

            if ($check > 0)
            {
                $result[] = $check . ' ' . $phrases[($check > 1) ? 1 : 0][$index];
            }
            else
            {
                continue;
            }

            if (count($result) > 1)
            {
                break;
            }
        }

        return implode(' ', $result);
    }

    public function getQueryJoins($bIsCount = false, $bNoQueryFriend = false)
    {
        $this->database()->innerJoin(Phpfox::getT('fevent_text'), 'ft', 'ft.event_id = m.event_id');
        if (Phpfox::isModule('friend') && Phpfox::getService('friend')->queryJoin($bNoQueryFriend))
        {
            $this->database()->join(Phpfox::getT('friend'), 'friends', 'friends.user_id = m.user_id AND friends.friend_user_id = ' . Phpfox::getUserId());
        }

        if ($this->_sCategory !== null)
        {
            $this->database()->innerJoin(Phpfox::getT('fevent_category_data'), 'mcd', 'mcd.event_id = m.event_id');

            if (!$bIsCount)
            {
                $this->database()->group('m.event_id');
            }
        }

        if ($this->_iAttending !== null)
        {
            $this->database()->innerJoin(Phpfox::getT('fevent_invite'), 'ei', 'ei.event_id = m.event_id AND ei.rsvp_id = ' . (int)$this->_iAttending . ' AND ei.invited_user_id = ' . Phpfox::getUserId());

            if (!$bIsCount)
            {
                $this->database()->select('ei.rsvp_id, ei.user_id AS inviter_id, ei.invited_user_id AS invitee_id, ');
                $this->database()->group('m.event_id');
            }
        }
        else
        {
            if (Phpfox::isUser())
            {
                $this->database()->leftJoin(Phpfox::getT('fevent_invite'), 'ei', 'ei.event_id = m.event_id AND ei.invited_user_id = ' . Phpfox::getUserId());

                if (!$bIsCount)
                {
                    $this->database()->select('ei.rsvp_id, ei.user_id AS inviter_id, ei.invited_user_id AS invitee_id, ');
                    $this->database()->group('m.event_id');
                }
            }
        }
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
        if ($sPlugin = Phpfox_Plugin::get('fevent.service_browse__call'))
        {
            return eval($sPlugin);
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __class__ . '::' . $sMethod . '()', E_USER_ERROR);
    }
}