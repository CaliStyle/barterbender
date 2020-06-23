<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 *
 * @copyright       [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Event
 */
class Fevent_Service_Process extends Phpfox_Service
{
    private $_bHasImage = false;

    private $_aInvited = array();

    private $_aCategories = array();

    private $_bIsEndingInThePast = false;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('fevent');
    }

    public function getStartWeekCode($startWeek)
    {
        switch ($startWeek) {
            case 'monday':
                $code = 1;
                break;
            case 'tuesday':
                $code = 2;
                break;
            case 'wednesday':
                $code = 3;
                break;
            case 'thursday':
                $code = 4;
                break;
            case 'friday':
                $code = 5;
                break;
            case 'saturday':
                $code = 6;
                break;
            case 'sunday':
                $code = 0;
                break;
            default:
                $code = 1;
                break;
        }
        return $code;
    }

    public function add($aVals, $sModule = 'fevent', $iItem = 0)
    {
        $this->_buildDir(Phpfox::getParam('event.dir_image'));

        if (!$this->_verify($aVals)) {
            return false;
        }

        if (!isset($aVals['privacy'])) {
            $aVals['privacy'] = 0;
        }

        $oParseInput = Phpfox::getLib('parse.input');
        Phpfox::getService('ban')->checkAutomaticBan($aVals);

        $iStartTime = Phpfox::getLib('date')->mktime($aVals['start_hour'], $aVals['start_minute'], 0, $aVals['start_month'], $aVals['start_day'], $aVals['start_year']);
        $iEndTime = Phpfox::getLib('date')->mktime($aVals['end_hour'], $aVals['end_minute'], 0, $aVals['end_month'], $aVals['end_day'], $aVals['end_year']);
        if ($this->_bIsEndingInThePast === true) {
            $iEndTime = $iStartTime + 3600;
        }
        if ($iEndTime <= $iStartTime && $iEndTime) {
            return Phpfox_Error::set(_p('end_event_time_has_to_be_greater_than_start_event_time'));
        }

        $bHasAttachments = (!empty($aVals['attachment']) && Phpfox::getUserParam('fevent.can_attach_on_event'));

        $aVals['txtrepeat'] = -1;
        $timerepeat = 0;
        $after_number_event = 0;
        if ($aVals['event_type'] == 'repeat') {
            $iStartTime = Phpfox::getLib('date')->mktime($aVals['repeat_section_start_hour'], $aVals['repeat_section_start_minute'], 0, $aVals['repeat_section_start_month'], $aVals['repeat_section_start_day'], $aVals['repeat_section_start_year']);
            $iEndTime = Phpfox::getLib('date')->mktime($aVals['repeat_section_end_hour'], $aVals['repeat_section_end_minute'], 0, $aVals['repeat_section_end_month'], $aVals['repeat_section_end_day'], $aVals['repeat_section_end_year']);

            $timerepeat = 0;
            $after_number_event = 0;
            switch ($aVals['repeat_section_end_repeat']) {
                case 'after_number_event':
                    $after_number_event = (int)$aVals['repeat_section_after_number_event'];
                    break;

                case 'repeat_until':
                    $timerepeat = Phpfox::getLib('date')->mktime(23, 59, 59, $aVals['repeat_section_repeatuntil_month'], $aVals['repeat_section_repeatuntil_day'], $aVals['repeat_section_repeatuntil_year']);
                    break;
            }

            switch ($aVals['repeat_section_type']) {
                case 'daily':
                    $aVals['txtrepeat'] = 0;
                    break;

                case 'weekly':
                    $aVals['txtrepeat'] = 1;
                    break;

                case 'monthly':
                    $aVals['txtrepeat'] = 2;
                    break;
            }
        }

        $range_value_real = $aVals['range_type'] * 1000;
        if ($range_value_real == 0) {
            $range_value_real = 1609;
        }

        $aSql = array(
            'view_id' => (($sModule == 'fevent' && Phpfox::getUserParam('fevent.event_must_be_approved')) ? '1' : '0'),
            'privacy' => (isset($aVals['privacy']) ? $aVals['privacy'] : '0'),
            'privacy_comment' => (isset($aVals['privacy_comment']) ? $aVals['privacy_comment'] : '0'),
            'module_id' => $sModule,
            'isrepeat' => $aVals['txtrepeat'],
            'timerepeat' => Phpfox::getLib('date')->convertToGmt($timerepeat),
            'after_number_event' => $after_number_event,
            'range_value' => (int)$aVals['range_value'],
            'range_type' => $aVals['range_type'],
            'range_value_real' => $aVals['range_value'] * $range_value_real,
            'duration_days' => (int)$aVals['daterepeat_dur_day'],
            'duration_hours' => (int)$aVals['daterepeat_dur_hour'],
            'is_delete_user_past_repeat_event' => ((!isset($aVals['deleteAllAttendees']) || empty($aVals['deleteAllAttendees'])) ? 0 : 1),
            'item_id' => $iItem,
            'user_id' => Phpfox::getUserId(),
            'title' => $oParseInput->clean($aVals['title'], 255),
            'location' => $oParseInput->clean($aVals['location'], 255),
            'country_iso' => (empty($aVals['country_iso']) ? Phpfox::getUserBy('country_iso') : $aVals['country_iso']),
            'country_child_id' => (isset($aVals['country_child_id']) ? (int)$aVals['country_child_id'] : 0),
            'postal_code' => (empty($aVals['postal_code']) ? null : Phpfox::getLib('parse.input')->clean($aVals['postal_code'], 20)),
            'city' => (empty($aVals['city']) ? null : $oParseInput->clean($aVals['city'], 255)),
            'time_stamp' => PHPFOX_TIME,
            'start_time' => Phpfox::getLib('date')->convertToGmt($iStartTime),
            'org_start_time' => Phpfox::getLib('date')->convertToGmt($iStartTime),
            'end_time' => Phpfox::getLib('date')->convertToGmt($iEndTime),
            'org_end_time' => Phpfox::getLib('date')->convertToGmt($iEndTime),
            'start_gmt_offset' => Phpfox::getLib('date')->getGmtOffset($iStartTime),
            'end_gmt_offset' => Phpfox::getLib('date')->getGmtOffset($iEndTime),
            'address' => (empty($aVals['address']) ? null : Phpfox::getLib('parse.input')->clean($aVals['address'])),
            'total_attachment' => ($bHasAttachments ? Phpfox::getService('attachment')->getCount($aVals['attachment']) : 0),
            'total_comment' => 0,
            'total_like' => 0,
            'total_view' => 0,
        );
        $aSql['lat'] = $aSql['lng'] = 0;

        if (Phpfox::getUserParam('fevent.can_add_gmap') && isset($aVals['gmap'])
            && is_array($aVals['gmap']) && isset($aVals['gmap']['latitude'])
            && isset($aVals['gmap']['longitude'])) {

            $aSql['gmap'] = serialize($aVals['gmap']);
            $aSql['lat'] = (isset($aVals['gmap']['latitude']) && !empty($aVals['gmap']['latitude'])) ? $aVals['gmap']['latitude'] : 0;
            $aSql['lng'] = (isset($aVals['gmap']['longitude']) && !empty($aVals['gmap']['longitude'])) ? $aVals['gmap']['longitude'] : 0;
        }
        $sFullAddress = $aSql["location"] . " " . $aSql["address"] . " " . $aSql["city"] . " " . $aSql["country_iso"];
        list($aCoordinates, $sGmapAddress) = $this->address2coordinates($sFullAddress);
        if (!empty($aCoordinates[1])) {
            $aSql['lat'] = $aCoordinates[1];
            $aSql['lng'] = $aCoordinates[0];
            $aSql['gmap_address'] = $oParseInput->prepare($sGmapAddress);
        }

        if ($sPlugin = Phpfox_Plugin::get('fevent.service_process_add__start')) {
            return eval($sPlugin);
        }

        if (!Phpfox_Error::isPassed()) {
            return false;
        }

        $iId = $this->database()->insert($this->_sTable, $aSql);

        if (!$iId) {
            return false;
        }

        // If we uploaded any attachments make sure we update the 'item_id'
        if ($bHasAttachments) {
            Phpfox::getService('attachment.process')->updateItemId($aVals['attachment'], Phpfox::getUserId(), $iId);
        }

        $this->database()->insert(Phpfox::getT('fevent_text'), array(
                'event_id' => $iId,
                'description' => (empty($aVals['description']) ? null : $oParseInput->clean($aVals['description'])),
                'description_parsed' => (empty($aVals['description']) ? null : $oParseInput->prepare($aVals['description']))
            )
        );

        if (isset($aVals['custom'])) {
            $this->database()->delete(Phpfox::getT('fevent_custom_value'), 'event_id = ' . $iId);

            foreach ($aVals['custom'] as $iFieldId => $sValue) {
                $this->database()->insert(Phpfox::getT('fevent_custom_value'), array(
                        'event_id' => $iId,
                        'field_id' => $iFieldId,
                        'value' => is_array($sValue) ? json_encode($sValue) : $sValue
                    )
                );
            }
        }

        foreach ($this->_aCategories as $iCategoryId) {
            $this->database()->insert(Phpfox::getT('fevent_category_data'), array('event_id' => $iId, 'category_id' => $iCategoryId));
        }

        $bAddFeed = ($sModule == 'fevent' ? (Phpfox::getUserParam('fevent.event_must_be_approved') ? false : true) : true);

        if ($bAddFeed === true) {
            if ($sModule == 'fevent') {
                (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->add('fevent', $iId, $aVals['privacy'], (isset($aVals['privacy_comment']) ? (int)$aVals['privacy_comment'] : 0)) : null);
            } else {
                (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->callback(Phpfox::callback($sModule . '.getFeedDetails', $iItem))->add('fevent', $iId, $aVals['privacy'], (isset($aVals['privacy_comment']) ? (int)$aVals['privacy_comment'] : 0), $iItem) : null);
            }

            Phpfox::getService('user.activity')->update(Phpfox::getUserId(), 'fevent');
        }

        $this->addRsvp($iId, 1, Phpfox::getUserId());

        if (Phpfox::VERSION >= '3.7.0' && Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support')) {
            Phpfox::getService('tag.process')->add('fevent', $iId, Phpfox::getUserId(), $aVals['description'], true);
        }

        // Plugin call
        if ($sPlugin = Phpfox_Plugin::get('fevent.service_process_add__end')) {
            eval($sPlugin);
        }

        // update 'org_event_id'
        // generate instance(s)
        if ($aVals['event_type'] == 'repeat') {
            $this->database()->update(Phpfox::getT('fevent'), array(
                'org_event_id' => (int)$iId,
            ), 'event_id = ' . (int)$iId);

            $this->generateInstanceForRepeatEvent($iId, $aVals, $bHasAttachments);
        }

        return $iId;
    }

    public function generateInstanceForRepeatEvent($event_id, $aVals, $bHasAttachments)
    {
        $aEvent = Phpfox::getService('fevent')->getAllDataEventById($event_id);
        if (isset($aEvent['event_id']) == false) {
            return false;
        }
        if ((int)$aEvent['isrepeat'] < 0) {
            return false;
        }

        // tables: fevent, fevent_text, fevent_category_data
        $fevent_table = $aEvent;
        unset($fevent_table['event_id']);
        unset($fevent_table['description']);
        unset($fevent_table['description_parsed']);
        unset($fevent_table['list_category_id']);
        unset($fevent_table['is_featured']);
        unset($fevent_table['is_sponsor']);

        $iStartTime = (int)$aEvent['start_time'];
        $month = (int)Phpfox::getTime('n', $iStartTime, false);
        $day = (int)Phpfox::getTime('j', $iStartTime, false);
        $year = (int)Phpfox::getTime('Y', $iStartTime, false);
        $start_hour = (int)Phpfox::getTime('H', $iStartTime, false);
        $start_minute = (int)Phpfox::getTime('i', $iStartTime, false);
        $start_second = (int)Phpfox::getTime('s', $iStartTime, false);

        $iEndTime = (int)$aEvent['end_time'];
        $iDuration = $iEndTime - $iStartTime;

        if ((int)$aEvent['after_number_event'] > 0) {
            $len = (int)$aEvent['after_number_event'];
            for ($idx = 0; $idx < $len; $idx++) {
                if ($aEvent['isrepeat'] == 0) {
                    //  daily
                    $iStartTime = $iStartTime + (1 * 24 * 60 * 60);
                } else if ($aEvent['isrepeat'] == 1) {
                    //  weekly
                    $iStartTime = $iStartTime + (7 * 24 * 60 * 60);
                } else if ($aEvent['isrepeat'] == 2) {
                    // monthly
                    $next_start_time_obj = Phpfox::getService('fevent.helper')->getSameDayInNextMonth($day, $month, $year);
                    $month = $next_start_time_obj['month'];
                    $year = $next_start_time_obj['year'];
                    $iStartTime = Phpfox::getLib('date')->mktime($start_hour
                        , $start_minute
                        , $start_second
                        , $next_start_time_obj['month']
                        , $next_start_time_obj['day']
                        , $next_start_time_obj['year']
                    );
                    if ($day != $next_start_time_obj['day']) {
                        continue;
                    }
                }
                $iEndTime = $iStartTime + $iDuration;
                $fevent_table['start_time'] = $iStartTime;
                $fevent_table['end_time'] = $iEndTime;

                $iId = $this->database()->insert(Phpfox::getT('fevent'), $fevent_table);

                $this->database()->insert(Phpfox::getT('fevent_text'), array(
                        'event_id' => $iId,
                        'description' => $aEvent['description'],
                        'description_parsed' => $aEvent['description_parsed'],
                    )
                );

                $aCategories = explode(',', $aEvent['list_category_id']);
                foreach ($aCategories as $iCategoryId) {
                    $this->database()->insert(Phpfox::getT('fevent_category_data'), array('event_id' => $iId, 'category_id' => $iCategoryId));
                }

                // If we uploaded any attachments make sure we update the 'item_id'
                if ($bHasAttachments) {
                    Phpfox::getService('attachment.process')->updateItemId($aVals['attachment'], Phpfox::getUserId(), $iId);
                }

                if (isset($aVals['custom'])) {
                    foreach ($aVals['custom'] as $iFieldId => $sValue) {
                        $this->database()->insert(Phpfox::getT('fevent_custom_value'), array(
                                'event_id' => $iId,
                                'field_id' => $iFieldId,
                                'value' => is_array($sValue) ? json_encode($sValue) : $sValue
                            )
                        );
                    }
                }

                $this->addRsvp($iId, 1, Phpfox::getUserId());

                if (Phpfox::VERSION >= '3.7.0' && Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support')) {
                    Phpfox::getService('tag.process')->add('fevent', $iId, Phpfox::getUserId(), $aVals['description'], true);
                }
            }
        } else if (isset($aEvent['timerepeat']) && (int)$aEvent['timerepeat'] > 0) {
            $iTimeRepeat = (int)$aEvent['timerepeat'];
            $len = (int)Phpfox::getParam('fevent.fevent_max_instance_repeat_event');
            for ($idx = 0; $idx < $len; $idx++) {
                if ($aEvent['isrepeat'] == 0) {
                    //  daily
                    $iStartTime = $iStartTime + (1 * 24 * 60 * 60);
                } else if ($aEvent['isrepeat'] == 1) {
                    //  weekly
                    $iStartTime = $iStartTime + (7 * 24 * 60 * 60);
                } else if ($aEvent['isrepeat'] == 2) {
                    // monthly
                    $next_start_time_obj = Phpfox::getService('fevent.helper')->getSameDayInNextMonth($day, $month, $year);
                    $month = $next_start_time_obj['month'];
                    $year = $next_start_time_obj['year'];
                    $iStartTime = Phpfox::getLib('date')->mktime($start_hour
                        , $start_minute
                        , $start_second
                        , $next_start_time_obj['month']
                        , $next_start_time_obj['day']
                        , $next_start_time_obj['year']
                    );
                    if ($day != $next_start_time_obj['day']) {
                        continue;
                    }
                }
                if ($iStartTime > $iTimeRepeat) {
                    break;
                }

                $iEndTime = $iStartTime + $iDuration;
                $fevent_table['start_time'] = $iStartTime;
                $fevent_table['end_time'] = $iEndTime;

                $iId = $this->database()->insert(Phpfox::getT('fevent'), $fevent_table);

                $this->database()->insert(Phpfox::getT('fevent_text'), array(
                        'event_id' => $iId,
                        'description' => $aEvent['description'],
                        'description_parsed' => $aEvent['description_parsed'],
                    )
                );

                $aCategories = explode(',', $aEvent['list_category_id']);
                foreach ($aCategories as $iCategoryId) {
                    $this->database()->insert(Phpfox::getT('fevent_category_data'), array('event_id' => $iId, 'category_id' => $iCategoryId));
                }

                // If we uploaded any attachments make sure we update the 'item_id'
                if ($bHasAttachments) {
                    Phpfox::getService('attachment.process')->updateItemId($aVals['attachment'], Phpfox::getUserId(), $iId);
                }

                if (isset($aVals['custom'])) {
                    foreach ($aVals['custom'] as $iFieldId => $sValue) {
                        $this->database()->insert(Phpfox::getT('fevent_custom_value'), array(
                                'event_id' => $iId,
                                'field_id' => $iFieldId,
                                'value' => is_array($sValue) ? json_encode($sValue) : $sValue
                            )
                        );
                    }
                }

                $this->addRsvp($iId, 1, Phpfox::getUserId());

                if (Phpfox::VERSION >= '3.7.0' && Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support')) {
                    Phpfox::getService('tag.process')->add('fevent', $iId, Phpfox::getUserId(), $aVals['description'], true);
                }
            }
        }

        return true;
    }

    public function updateView($iId)
    {
        $this->database()->query("
            UPDATE " . $this->_sTable . "
            SET total_view = total_view + 1
            WHERE event_id = " . (int)$iId . "
        ");

        return true;
    }

    public function update($iId, $aVals, $aEventPost = null)
    {
        $this->_buildDir(Phpfox::getParam('event.dir_image'));

        $oHelper = Phpfox::getService('fevent.helper');

        $aUpdateEvent = Phpfox::getService('fevent')->getQuickEvent($iId);

        if (!$this->_verify($aVals, true, $iId)) {
            return false;
        }

        if (!isset($aVals['privacy'])) {
            $aVals['privacy'] = 0;
        }

        if (!isset($aVals['privacy_comment'])) {
            $aVals['privacy_comment'] = 0;
        }

        $oParseInput = Phpfox::getLib('parse.input');

        Phpfox::getService('ban')->checkAutomaticBan($aVals['title'] . ' ' . $aVals['description']);

        //  get permission
        $canEditStartTime = $oHelper->canEditStartTimeByEventID($iId);
        $canEditEndTime = $oHelper->canEditEndTimeByEventID($iId);
        $canEditDuration = $oHelper->canEditDurationByEventID($iId);

        $iStartTime = 0;
        if (isset($aVals['start_day'])) {
            $iStartTime = Phpfox::getLib('date')->mktime($aVals['start_hour'], $aVals['start_minute'], 0, $aVals['start_month'], $aVals['start_day'], $aVals['start_year']);
        }

        $iEndTime = 0;
        if (isset($aVals['end_day'])) {
            $iEndTime = Phpfox::getLib('date')->mktime($aVals['end_hour'], $aVals['end_minute'], 0, $aVals['end_month'], $aVals['end_day'], $aVals['end_year']);
        }
        if ($this->_bIsEndingInThePast === true) {
            $iEndTime = $iStartTime + 3600;
        }
        if ($iEndTime <= $iStartTime && $iEndTime) {
            return Phpfox_Error::set(_p('end_event_time_has_to_be_greater_than_start_event_time'));
        }

        if ($iStartTime > $iEndTime) {
            $iEndTime = $iStartTime;
        }

        $bHasAttachments = (!empty($aVals['attachment']) && Phpfox::getUserParam('fevent.can_attach_on_event'));
        if ($bHasAttachments) {
            Phpfox::getService('attachment.process')->updateItemId($aVals['attachment'], Phpfox::getUserId(), $iId);
        }

        $aVals['txtrepeat'] = -1;
        $one_time_to_repeat = false;
        $timerepeat = 0;
        $after_number_event = 0;

        if ($aVals['event_type'] == 'repeat') {
            if ($aEventPost['isrepeat'] == -1) {
                $one_time_to_repeat = true;
            }
            $iStartTime = Phpfox::getLib('date')->mktime($aVals['repeat_section_start_hour'], $aVals['repeat_section_start_minute'], 0, $aVals['repeat_section_start_month'], $aVals['repeat_section_start_day'], $aVals['repeat_section_start_year']);
            $iEndTime = Phpfox::getLib('date')->mktime($aVals['repeat_section_end_hour'], $aVals['repeat_section_end_minute'], 0, $aVals['repeat_section_end_month'], $aVals['repeat_section_end_day'], $aVals['repeat_section_end_year']);

            switch ($aVals['repeat_section_end_repeat']) {
                case 'after_number_event':
                    $after_number_event = (int)$aVals['repeat_section_after_number_event'];
                    break;

                case 'repeat_until':
                    $timerepeat = Phpfox::getLib('date')->mktime(23, 59, 59, $aVals['repeat_section_repeatuntil_month'], $aVals['repeat_section_repeatuntil_day'], $aVals['repeat_section_repeatuntil_year']);
                    break;
            }

            switch ($aVals['repeat_section_type']) {
                case 'daily':
                    $aVals['txtrepeat'] = 0;
                    break;

                case 'weekly':
                    $aVals['txtrepeat'] = 1;
                    break;

                case 'monthly':
                    $aVals['txtrepeat'] = 2;
                    break;
            }
        }

        $range_value_real = $aVals['range_type'] * 1000;
        if ($range_value_real == 0) {
            $range_value_real = 1609;
        }

        $aSql = array(
            'privacy' => (isset($aVals['privacy']) ? $aVals['privacy'] : '0'),
            'privacy_comment' => (isset($aVals['privacy_comment']) ? $aVals['privacy_comment'] : '0'),
            'title' => $oParseInput->clean($aVals['title'], 255),
            'location' => $oParseInput->clean($aVals['location'], 255),
            'country_iso' => $aVals['country_iso'],
            'isrepeat' => $aVals['txtrepeat'],
            'after_number_event' => $after_number_event,
            'range_value' => $aVals['range_value'],
            'range_type' => $aVals['range_type'],
            'range_value_real' => $aVals['range_value'] * $range_value_real,
            'country_child_id' => (isset($aVals['country_child_id']) ? Phpfox::getService('core.country')->getValidChildId($aVals['country_iso'], (int)$aVals['country_child_id']) : 0),
            'city' => (empty($aVals['city']) ? null : $oParseInput->clean($aVals['city'], 255)),
            'postal_code' => (empty($aVals['postal_code']) ? null : Phpfox::getLib('parse.input')->clean($aVals['postal_code'], 20)),
            'is_delete_user_past_repeat_event' => ((!isset($aVals['deleteAllAttendees']) || empty($aVals['deleteAllAttendees'])) ? 0 : 1),
            'address' => (empty($aVals['address']) ? null : Phpfox::getLib('parse.input')->clean($aVals['address'])),
            'total_attachment' => (Phpfox::isModule('attachment') ? Phpfox::getService('attachment')->getCountForItem($iId, 'fevent') : '0')
        );

        //  HIDE WARNING UPDATE FOR OLD REPEAT DATA 
        if ($this->database()->isField(Phpfox::getT('fevent'), 'is_update_warning')) {
            $aSql['is_update_warning'] = 0;
        }

        //  CHECK FOR EDIT      
        if ($canEditStartTime == true) {

            $aSql['start_time'] = Phpfox::getLib('date')->convertToGmt($iStartTime);
            $aVals['interval_start_date'] = Phpfox::getTime('j', $aSql['start_time'], false) - Phpfox::getTime('j', $aUpdateEvent['start_time'], false);
            $aSql['start_gmt_offset'] = Phpfox::getLib('date')->getGmtOffset($iStartTime);

            if ($canEditEndTime == false) {
                //  update start so that update end
                $aSql['end_time'] = Phpfox::getLib('date')->convertToGmt($iEndTime);
                $aSql['end_gmt_offset'] = Phpfox::getLib('date')->getGmtOffset($iEndTime);
            }
        }
        if ($canEditEndTime == true) {

            $aSql['end_time'] = Phpfox::getLib('date')->convertToGmt($iEndTime);
            $aVals['interval_end_date'] = Phpfox::getTime('j', $aSql['end_time'], false) - Phpfox::getTime('j', $aUpdateEvent['end_time'], false);
            $aSql['end_gmt_offset'] = Phpfox::getLib('date')->getGmtOffset($iEndTime);
            if (isset($timerepeat)) {
                $aSql['timerepeat'] = Phpfox::getLib('date')->convertToGmt($timerepeat);
            }
        }
        if ($canEditDuration == true && $aVals['txtrepeat'] > -1) {
            if ($canEditEndTime == false) {
                //  update duration so that update end
                $aSql['end_time'] = Phpfox::getLib('date')->convertToGmt($iEndTime);
                $aSql['end_gmt_offset'] = Phpfox::getLib('date')->getGmtOffset($iEndTime);
            }
        }

        /*check change start time & end time,if true we will send notification and reset rsvp*/
        $change_start_time = $aSql['start_time'] - $aUpdateEvent['start_time'];
        $change_end_time = $aSql['end_time'] - $aUpdateEvent['end_time'];
        $change_location = $aSql['location'] != $aUpdateEvent['location'];

        if (abs($change_start_time) > 0 || abs($change_end_time)) {
            $aInvites = Phpfox::getService('fevent')->getInviteAdvancedEvent($iId);

            if (count($aInvites)) {
                foreach ($aInvites as $key => $aInvite) {
                    Phpfox::getService('notification.process')->add('fevent_remindchangetime', $iId, $aInvite['user_id']);
                }
            }

            $this->database()->delete(Phpfox::getT('fevent_invite'), 'event_id = ' . $iId . ' AND user_id != ' . (int)$aUpdateEvent['user_id']);
        }

        if (abs($change_start_time) || abs($change_end_time) || $change_location) {
            /* notify Attending and Maybe attending users on event changing of time and location */
            list($attendingCnt, $aAttendings) = Phpfox::getService('fevent')->getInvites($iId, 1);
            list($maybeAttendingCnt, $aMaybeAttendings) = Phpfox::getService('fevent')->getInvites($iId, 2);
            $aReceiverIds = array_unique(array_map(function ($user) {
                return $user['user_id'];
            }, array_merge($aAttendings, $aMaybeAttendings)));
            $aReceiverIds = array_diff($aReceiverIds, array(Phpfox::getUserId()));

            if (abs($change_start_time) > 0 || abs($change_end_time)) {
                foreach ($aReceiverIds as $iReceiverId) {
                    Phpfox::getService('notification.process')->add('fevent_remindchangetime', $iId, $iReceiverId);
                }
            }
            if ($change_location) {
                foreach ($aReceiverIds as $iReceiverId) {
                    Phpfox::getService('notification.process')->add('fevent_remindchangelocation', $iId, $iReceiverId);
                }
            }
        }

        $aSql['lat'] = $aSql['lng'] = 0;

        if (Phpfox::getUserParam('fevent.can_add_gmap') && isset($aVals['gmap'])
            && is_array($aVals['gmap']) && isset($aVals['gmap']['latitude'])
            && isset($aVals['gmap']['longitude'])) {
            $aSql['gmap'] = serialize($aVals['gmap']);
            $aSql['lat'] = (double)$aVals['gmap']['latitude'];
            $aSql['lng'] = (double)$aVals['gmap']['longitude'];
        }
        if (empty($aSql['gmap'])) {
            $sFullAddress = $aSql["location"] . " " . $aSql["address"] . " " . $aSql["city"] . " " . $aSql["country_iso"];
            list($aCoordinates, $sGmapAddress) = $this->address2coordinates($sFullAddress);
            if (!empty($aCoordinates[1])) {
                $aSql['lat'] = (double)$aCoordinates[1];
                $aSql['lng'] = (double)$aCoordinates[0];
                $aSql['gmap_address'] = $oParseInput->prepare($sGmapAddress);
            }
        }

        // Multi-upload
        if ($this->_bHasImage) {
            $oImage = Phpfox::getLib('image');
            $oFile = Phpfox::getLib('file');

            $aSizes = array(50, 120, 200);

            $iFileSizes = 0;

            foreach ($_FILES['image']['error'] as $iKey => $sError) {
                if ($sError == UPLOAD_ERR_OK) {
                    if ($aImage = $oFile->load('image[' . $iKey . ']', array(
                        'jpg',
                        'gif',
                        'png'
                    ), (Phpfox::getUserParam('fevent.max_upload_size_event') === 0 ? null : (Phpfox::getUserParam('fevent.max_upload_size_event') / 1024))
                    )
                    ) {
                        $sFileName = $oFile->upload('image[' . $iKey . ']', Phpfox::getParam('event.dir_image'), $iId);
                        $aVals['recurring_image'][] = $sFileName;
                        $iFileSizes += filesize(Phpfox::getParam('event.dir_image') . sprintf($sFileName, ''));
                        $this->database()->insert(Phpfox::getT('fevent_image'), array('event_id' => $iId, 'image_path' => $sFileName, 'server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID')));
                        $a = Phpfox::getParam('event.dir_image') . sprintf($sFileName, '');
                        list($width, $height, $type, $attr) = getimagesize($a);
                        foreach ($aSizes as $iSize) {
                            if ($iSize == 120) {
                                if ($width < 120 || $height < 120) {
                                    $this->resizeImage($sFileName, $width > 120 ? 120 : $width, $height > 120 ? 120 : $height, "_120");
                                } else
                                    $this->resizeImage($sFileName, 120, 120, "_120");
                            } elseif ($iSize == 200) {
                                if ($width < 200 || $height < 200) {
                                    $this->resizeImage($sFileName, $width > 200 ? 200 : $width, $height > 200 ? 200 : $height, "_200");
                                } else {
                                    $this->resizeImage($sFileName, 160, 200, "_200");
                                }
                            } elseif ($iSize == 50) {
                                if ($width < 50 || $height < 50) {
                                    $this->resizeImage($sFileName, $width > 50 ? 50 : $width, $height > 50 ? 50 : $height, "_50");
                                } else
                                    $this->resizeImage($sFileName, 50, 50, "_50");
                            } else {
                                $oImage->createThumbnail(Phpfox::getParam('event.dir_image') . sprintf($sFileName, ''), Phpfox::getParam('event.dir_image') . sprintf($sFileName, '_' . $iSize), $iSize, $iSize);
                                $oImage->createThumbnail(Phpfox::getParam('event.dir_image') . sprintf($sFileName, ''), Phpfox::getParam('event.dir_image') . sprintf($sFileName, '_' . $iSize . '_square'), $iSize, $iSize, false);
                            }
                            $iFileSizes += filesize(Phpfox::getParam('event.dir_image') . sprintf($sFileName, '_' . $iSize));
                        }

                        if ($width > 800) {
                            if ($height > 500) {
                                $this->resizeImage($sFileName, 800, 500, "");
                            }
                        }
                    }
                }
            }
            if ($iFileSizes === 0) {
                return false;
            }
            // Update user space usage
            Phpfox::getService('user.space')->update(Phpfox::getUserId(), 'fevent', $iFileSizes);
            $aSql['image_path'] = $sFileName;
            $aSql['server_id'] = Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID');
        }

        if ($sPlugin = Phpfox_Plugin::get('fevent.service_process_update__start')) {
            return eval($sPlugin);
        }

        $this->database()->update($this->_sTable, $aSql, 'event_id = ' . (int)$iId);

        $this->database()->update(Phpfox::getT('fevent_text'), array(
            'description' => (empty($aVals['description']) ? null : $oParseInput->clean($aVals['description'])),
            'description_parsed' => (empty($aVals['description']) ? null : $oParseInput->prepare($aVals['description']))
        ), 'event_id = ' . (int)$iId
        );
        $this->database()->delete(Phpfox::getT('fevent_custom_value'), 'event_id = ' . $iId);
        if (isset($aVals['custom'])) {
            foreach ($aVals['custom'] as $iFieldId => $sValue) {

                $this->database()->insert(Phpfox::getT('fevent_custom_value'), array(
                        'event_id' => $iId,
                        'field_id' => $iFieldId,
                        'value' => is_array($sValue) ? json_encode($sValue) : $sValue
                    )
                );
            }
        }

        $aEvent = $this->database()->select('event_id, user_id, title, module_id')
            ->from($this->_sTable)
            ->where('event_id = ' . (int)$iId)
            ->execute('getSlaveRow');

        if (isset($aVals['emails']) || isset($aVals['invite'])) {
            $aInvites = $this->database()->select('invited_user_id, invited_email')
                ->from(Phpfox::getT('fevent_invite'))
                ->where('event_id = ' . (int)$iId)
                ->execute('getRows');
            $aInvited = array();
            foreach ($aInvites as $aInvite) {
                $aInvited[(empty($aInvite['invited_email']) ? 'user' : 'email')][(empty($aInvite['invited_email']) ? $aInvite['invited_user_id'] : $aInvite['invited_email'])] = true;
            }
        }

        if (isset($aVals['emails'])) {
            // if (strpos($aVals['emails'], ','))
            {
                $aEmails = explode(',', $aVals['emails']);
                $aCachedEmails = array();
                foreach ($aEmails as $sEmail) {
                    $sEmail = trim($sEmail);
                    if (!Phpfox::getLib('mail')->checkEmail($sEmail)) {
                        continue;
                    }

                    if (isset($aInvited['email'][$sEmail])) {
                        continue;
                    }

                    $sLink = Phpfox::getLib('url')->permalink('fevent', $aEvent['event_id'], $aEvent['title']);

                    $sMessage = _p('full_name_invited_you_to_the_title', array(
                            'full_name' => Phpfox::getUserBy('full_name'),
                            'title' => $oParseInput->clean($aVals['title'], 255),
                            'link' => $sLink
                        )
                    );
                    if (!empty($aVals['personal_message'])) {
                        $sMessage .= _p('full_name_added_the_following_personal_message', array(
                                    'full_name' => Phpfox::getUserBy('full_name')
                                )
                            ) . "\n";
                        $sMessage .= $aVals['personal_message'];
                    }
                    $oMail = Phpfox::getLib('mail');
                    if (isset($aVals['invite_from']) && $aVals['invite_from'] == 1) {
                        $oMail->fromEmail(Phpfox::getUserBy('email'))
                            ->fromName(Phpfox::getUserBy('full_name'));
                    }
                    $bSent = $oMail->to($sEmail)
                        ->subject(array('fevent.full_name_invited_you_to_the_event_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $oParseInput->clean($aVals['title'], 255))))
                        ->message($sMessage)
                        ->send();

                    if ($bSent) {
                        $this->_aInvited[] = array('email' => $sEmail);

                        $aCachedEmails[$sEmail] = true;

                        $this->database()->insert(Phpfox::getT('fevent_invite'), array(
                                'event_id' => $iId,
                                'type_id' => 1,
                                'user_id' => Phpfox::getUserId(),
                                'invited_email' => $sEmail,
                                'time_stamp' => PHPFOX_TIME
                            )
                        );
                    }
                }
            }
        }

        if (isset($aVals['invite']) && is_array($aVals['invite'])) {
            $sUserIds = '';
            foreach ($aVals['invite'] as $iUserId) {
                if (!is_numeric($iUserId)) {
                    continue;
                }
                $sUserIds .= $iUserId . ',';
            }
            $sUserIds = rtrim($sUserIds, ',');

            $aUsers = $this->database()->select('user_id, email, language_id, full_name')
                ->from(Phpfox::getT('user'))
                ->where('user_id IN(' . $sUserIds . ')')
                ->execute('getSlaveRows');

            foreach ($aUsers as $aUser) {
                if (isset($aCachedEmails[$aUser['email']])) {
                    continue;
                }

                if (isset($aInvited['user'][$aUser['user_id']])) {
                    continue;
                }

                $sLink = Phpfox::getLib('url')->permalink('fevent', $aEvent['event_id'], $aEvent['title']);

                $sMessage = _p('full_name_invited_you_to_the_title', array(
                    'full_name' => Phpfox::getUserBy('full_name'),
                    'title' => $oParseInput->clean($aVals['title'], 255),
                    'link' => $sLink
                ), false, null, $aUser['language_id']);
                if (!empty($aVals['personal_message'])) {
                    $sMessage .= _p('full_name_added_the_following_personal_message', array(
                            'full_name' => Phpfox::getUserBy('full_name')
                        ), false, null, $aUser['language_id']
                        ) . ":\n" . $aVals['personal_message'];
                }
                $bSent = Phpfox::getLib('mail')->to($aUser['user_id'])
                    ->subject(array('fevent.full_name_invited_you_to_the_event_title', array('full_name' => Phpfox::getUserBy('full_name'), 'title' => $oParseInput->clean($aVals['title'], 255))))
                    ->message($sMessage)
                    ->notification('fevent.invite_to_event')
                    ->send();

                $this->_aInvited[] = array('user' => $aUser['full_name']);

                $iInviteId = $this->database()->insert(Phpfox::getT('fevent_invite'), array(
                        'event_id' => $iId,
                        'user_id' => Phpfox::getUserId(),
                        'invited_user_id' => $aUser['user_id'],
                        'time_stamp' => PHPFOX_TIME
                    )
                );

                (Phpfox::isModule('request') ? Phpfox::getService('request.process')->add('fevent_invite', $iId, $aUser['user_id']) : null);

            }
        }

        #Admins
        $aUserCache = array();
        $this->database()->delete(Phpfox::getT('fevent_admin'), 'event_id = ' . (int)$iId);
        $aAdmins = Phpfox::getLib('request')->getArray('admins');
        if (count($aAdmins)) {
            foreach ($aAdmins as $iAdmin) {
                if (isset($aUserCache[$iAdmin])) {
                    continue;
                }

                $aUserCache[$iAdmin] = true;
                $this->database()->insert(Phpfox::getT('fevent_admin'), array('event_id' => $iId, 'user_id' => $iAdmin));
                if (Phpfox::isModule('notification')) {
                    Phpfox::getService('notification.process')->add('fevent_admins', $iId, $iAdmin);
                }
            }
        }


        $this->database()->delete(Phpfox::getT('fevent_category_data'), 'event_id = ' . (int)$iId);

        foreach ($this->_aCategories as $iCategoryId) {
            if ((int)$iCategoryId > 0) {
                $this->database()->insert(Phpfox::getT('fevent_category_data'), array('event_id' => $iId, 'category_id' => $iCategoryId));
            }
        }

        if (empty($aEvent['module_id'])) {
            (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->update('fevent', $iId, $aVals['privacy'], $aVals['privacy_comment'], 0, $aEvent['user_id']) : null);
        }

        if (Phpfox::VERSION >= '3.7.0' && Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support')) {
            Phpfox::getService('tag.process')->update('fevent', $aEvent['event_id'], $aEvent['user_id'], $aVals['description'], true);
        }

        // update 'org_event_id'
        // generate instance(s)
        if ($one_time_to_repeat == true && $aVals['event_type'] == 'repeat') {
            // one time type changes to repeat type 
            $this->database()->update(Phpfox::getT('fevent'), array(
                'org_event_id' => (int)$iId,
            ), 'event_id = ' . (int)$iId);

            $this->generateInstanceForRepeatEvent($iId, $aVals, $bHasAttachments);
        } else if ($one_time_to_repeat == false && $aVals['event_type'] == 'repeat') {
            if ((int)$aEventPost['org_event_id'] > 0) {
                // edit repeat type, check affecting to other brother events 
                switch ($aVals['ynfevent_editconfirmboxoption_value']) {
                    case 'only_this_event':
                        // do nothing 
                        break;

                    case 'all_events_uppercase':
                        // get all brother events
                        $aEventId = Phpfox::getService('fevent')->getBrotherEventByEventId($iId, $aEventPost['org_event_id']);
                        $this->updateBrotherEvent($iId, $aEventId, $aVals);
                        break;

                    case 'following_events':
                        // get younger brother events
                        $aConds = array();
                        $aConds[] = ' AND e.event_id > ' . (int)$iId;
                        $aEventId = Phpfox::getService('fevent')->getBrotherEventByEventId($iId, $aEventPost['org_event_id'], $aConds);
                        $this->updateBrotherEvent($iId, $aEventId, $aVals);
                        break;
                }
            }
        }

        $this->cache()->remove();

        return true;
    }

    public function updateBrotherEvent($event_id, $aEventId, $aVals, $start_time = null, $end_time = null)
    {
        $aEvent = Phpfox::getService('fevent')->getAllDataEventById($event_id);
        if (isset($aEvent['event_id']) == false) {
            return false;
        }
        if ((int)$aEvent['isrepeat'] < 0) {
            return false;
        }

        if (null == $start_time) {
            $start_hour = (int)Phpfox::getTime('H', $aEvent['start_time'], false);
            $start_minute = (int)Phpfox::getTime('i', $aEvent['start_time'], false);
        }
        if (null == $end_time) {
            $end_hour = (int)Phpfox::getTime('H', $aEvent['end_time'], false);
            $end_minute = (int)Phpfox::getTime('i', $aEvent['end_time'], false);
        }

        // tables: fevent, fevent_text, fevent_category_data

        $fevent_table = $aEvent;

        unset($fevent_table['event_id']);
        unset($fevent_table['description']);
        unset($fevent_table['description_parsed']);
        unset($fevent_table['list_category_id']);

        unset($fevent_table['is_featured']);
        unset($fevent_table['is_sponsor']);
        unset($fevent_table['start_time']);
        unset($fevent_table['org_start_time']);
        unset($fevent_table['end_time']);
        unset($fevent_table['org_end_time']);
        unset($fevent_table['image_path']);
        unset($fevent_table['server_id']);
        unset($fevent_table['total_comment']);
        unset($fevent_table['total_like']);
        unset($fevent_table['total_view']);
        unset($fevent_table['mass_email']);
        unset($fevent_table['isrepeat']);
        unset($fevent_table['timerepeat']);
        unset($fevent_table['after_number_event']);

        foreach ($aEventId as $key => $value) {
            $iId = $value['event_id'];

            $aEditedEvent = Phpfox::getService('fevent')->getEventByID($iId);
            $start_month = (int)Phpfox::getTime('n', $aEditedEvent['start_time'], false);
            $start_day = (int)Phpfox::getTime('j', $aEditedEvent['start_time'], false);
            $start_year = (int)Phpfox::getTime('Y', $aEditedEvent['start_time'], false);

            $end_month = (int)Phpfox::getTime('n', $aEditedEvent['end_time'], false);
            $end_day = (int)Phpfox::getTime('j', $aEditedEvent['end_time'], false);
            $end_year = (int)Phpfox::getTime('Y', $aEditedEvent['end_time'], false);

            $iStartTime = Phpfox::getLib('date')->mktime($start_hour, $start_minute, 0, $start_month, $start_day, $start_year);
            $iEndTime = Phpfox::getLib('date')->mktime($end_hour, $end_minute, 0, $end_month, $end_day, $end_year);

            /*update if change start time and end time*/
            $iStartTime += $aVals['interval_start_date'] * 3600 * 24;
            $iEndTime += $aVals['interval_end_date'] * 3600 * 24;

            $fevent_table['start_time'] = ($iStartTime);
            $fevent_table['end_time'] = ($iEndTime);


            /*check change start time & end time,if true we will send notification and reset rsvp*/
            $change_start_time = $fevent_table['start_time'] - $aEditedEvent['start_time'];
            $change_end_time = $fevent_table['end_time'] - $aEditedEvent['end_time'];

            if (abs($change_start_time) > 0 || abs($change_end_time)) {

                $aInvites = Phpfox::getService('fevent')->getInviteAdvancedEvent($iId);

                if (count($aInvites)) {
                    foreach ($aInvites as $key => $aInvite) {
                        Phpfox::getService('notification.process')->add('fevent_remindchangetime', $iId, $aInvite['user_id']);
                    }
                }

                $this->database()->delete(Phpfox::getT('fevent_invite'), 'event_id = ' . $iId . ' AND user_id != ' . (int)$aEditedEvent['user_id']);
            }

            $this->database()->update(Phpfox::getT('fevent'), $fevent_table, 'event_id = ' . (int)$iId);

            $this->database()->update(Phpfox::getT('fevent_text'), array(
                'description' => $aEvent['description'],
                'description_parsed' => $aEvent['description_parsed'],
            ), 'event_id = ' . (int)$iId
            );

            $this->copyRecurringImage($iId, $aVals);

            if (isset($aVals['custom'])) {
                foreach ($aVals['custom'] as $iFieldId => $sValue) {
                    $this->database()->delete(Phpfox::getT('fevent_custom_value'), 'event_id = ' . $iId . ' AND field_id = ' . $iFieldId);

                    $this->database()->insert(Phpfox::getT('fevent_custom_value'), array(
                            'event_id' => $iId,
                            'field_id' => $iFieldId,
                            'value' => is_array($sValue) ? json_encode($sValue) : $sValue
                        )
                    );
                }
            }

            $this->database()->delete(Phpfox::getT('fevent_category_data'), 'event_id = ' . (int)$iId);

            foreach ($this->_aCategories as $iCategoryId) {
                if ((int)$iCategoryId > 0) {
                    $this->database()->insert(Phpfox::getT('fevent_category_data'), array('event_id' => $iId, 'category_id' => $iCategoryId));
                }
            }
            if (Phpfox::VERSION >= '3.7.0' && Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support')) {
                Phpfox::getService('tag.process')->update('fevent', $aEditedEvent['event_id'], $aEditedEvent['user_id'], $aVals['description'], true);
            }
        }
    }

    public function getFileExt($sFileName)
    {
        $sFilename = strtolower($sFileName);
        $aExts = preg_split("/[\/\\.]/", $sFileName);
        $iCnt = count($aExts) - 1;

        return strtolower($aExts[$iCnt]);
    }


    private function _buildDir($sDestination)
    {

        if (!PHPFOX_SAFE_MODE && Phpfox::getParam('core.build_file_dir') && !defined('PHPFOX_IS_HOSTED_SCRIPT')) {
            $aParts = explode('/', Phpfox::getParam('core.build_format'));
            foreach ($aParts as $sPart) {
                $sDate = date($sPart) . PHPFOX_DS;
                $sDestination .= $sDate;

                if (!file_exists($sDestination)) {
                    @mkdir($sDestination, 0777, true);
                    @chmod($sDestination, 0777);
                }

            }

            // Make sure the directory was actually created, if not we use the default dir we know is working
            if (is_dir($sDestination)) {
                return $sDestination;
            } else {
                return false;
            }
        }

    }

    public function copyRecurringImage($iId, $aVals, $bForce = false)
    {
        if ((isset($aVals['upload_photo']) && isset($_FILES['image']) || $bForce) && isset($aVals['recurring_image']) && count($aVals['recurring_image'])) {
            $sImageDb = '';
            foreach ($aVals['recurring_image'] as $sFilename) {
                $aSizes = array('', 50, 120, 200);
                $sExt = $this->getFileExt($sFilename);
                $sFileDir = $this->_buildDir(Phpfox::getParam('event.dir_image'));

                $sFileNameCopy = $sFileDir . md5($iId . PHPFOX_TIME . uniqid()) . '%s.' . $sExt;

                foreach ($aSizes as $iSize) {
                    $sImage = Phpfox::getParam('event.dir_image') . sprintf($sFilename, (empty($iSize) ? '' : '_') . $iSize);
                    if (file_exists($sImage)) {

                        if ($sFileDir !== false) {
                            $sImageCopy = sprintf($sFileNameCopy, (empty($iSize) ? '' : '_') . $iSize);
                            Phpfox::getLib('file')->copy($sImage, $sImageCopy);

                        }
                    }
                }

                if ($sFileDir !== false) {

                    $sImageDb = str_replace(Phpfox::getParam('event.dir_image'), "", $sFileNameCopy);

                    $this->database()->insert(Phpfox::getT('fevent_image'), array('event_id' => $iId, 'image_path' => $sImageDb, 'server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID')));

                }
            }

            if ($sImageDb != '') {
                $this->database()->update($this->_sTable, array('image_path' => $sImageDb, 'server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID')), 'event_id = ' . $iId);
            }

        }
    }

    public function setDefault($iImageId)
    {
        $aEvent = $this->database()->select('fei.image_path, fei.server_id, fe.event_id, fe.user_id')
            ->from(Phpfox::getT('fevent_image'), 'fei')
            ->join($this->_sTable, 'fe', 'fe.event_id = fei.event_id')
            ->where('fei.image_id = ' . (int)$iImageId)
            ->execute('getSlaveRow');

        if (!isset($aEvent['user_id'])) {
            return Phpfox_Error::set('Unable to find the image.');
        }

        if (Phpfox::getService('fevent.helper')->canEditEvent($aEvent['event_id'], Phpfox::getUserId(), $aEvent['user_id']) == false) {
            return Phpfox_Error::set(_p('you_do_not_have_sufficient_permission_to_modify_this_event'));
        }

        $this->database()->update($this->_sTable, array('image_path' => $aEvent['image_path'], 'server_id' => $aEvent['server_id']), 'event_id = ' . $aEvent['event_id']);

        return true;
    }

    public function deleteImage($iImageId)
    {
        $aEvent = $this->database()->select('fei.image_id, fei.image_path, fei.server_id, fe.user_id, fe.event_id, fe.image_path AS default_image_path')
            ->from(Phpfox::getT('fevent_image'), 'fei')
            ->join($this->_sTable, 'fe', 'fe.event_id = fei.event_id')
            ->where('fei.image_id = ' . (int)$iImageId)
            ->execute('getSlaveRow');

        if (!isset($aEvent['user_id'])) {
            return Phpfox_Error::set('Unable to find the image.');
        }

        if (Phpfox::getService('fevent.helper')->canEditEvent($aEvent['event_id'], Phpfox::getUserId(), $aEvent['user_id']) == false) {
            return Phpfox_Error::set(_p('you_do_not_have_sufficient_permission_to_modify_this_event'));
        }

        if ($aEvent['default_image_path'] == $aEvent['image_path']) {
            $aImages = $this->database()->select('image_path, server_id')
                ->from(Phpfox::getT('fevent_image'))
                ->where('event_id = ' . $aEvent['event_id'])
                ->execute('getSlaveRows');

            for ($i = 0; $i < count($aImages); $i++) {
                $this->database()->update($this->_sTable, array('image_path' => (isset($aImages[$i + 1]['image_path']) ? $aImages[$i + 1]['image_path'] : null), 'server_id' => (isset($aImages[$i + 1]['server_id']) ? $aImages[$i + 1]['server_id'] : null)), 'event_id = ' . $aEvent['event_id']);
                break;
            }

        }

        $iFileSizes = 0;
        $aSizes = array('', 50, 120, 200);
        foreach ($aSizes as $iSize) {
            $sImage = Phpfox::getParam('event.dir_image') . sprintf($aEvent['image_path'], (empty($iSize) ? '' : '_') . $iSize);
            if (file_exists($sImage)) {
                $iFileSizes += filesize($sImage);

                @unlink($sImage);
            }

            if (Phpfox::getParam('core.allow_cdn') && $aEvent['server_id'] > 0) {
                // Get the file size stored when the photo was uploaded
                $sTempUrl = Phpfox::getLib('cdn')->getUrl(str_replace(Phpfox::getParam('event.dir_image'), Phpfox::getParam('event.url_image'), $sImage));

                $aHeaders = get_headers($sTempUrl, true);
                if (preg_match('/200 OK/i', $aHeaders[0])) {
                    $iFileSizes += (int)$aHeaders["Content-Length"];
                }

                Phpfox::getLib('cdn')->remove($sImage);
            }
        }

        if ($iFileSizes > 0) {
            Phpfox::getService('user.space')->update($aEvent['user_id'], 'fevent', $iFileSizes, '-');
        }

        $this->database()->delete(Phpfox::getT('fevent_image'), 'image_id = ' . $aEvent['image_id']);

        return true;
    }

    public function addRsvp($iEvent, $iRsvp, $iUserId)
    {
        if (!Phpfox::isUser()) {
            return false;
        }

        if (($iInviteId = $this->database()->select('invite_id')
            ->from(Phpfox::getT('fevent_invite'))
            ->where('event_id = ' . (int)$iEvent . ' AND invited_user_id = ' . (int)$iUserId)
            ->execute('getField'))) {
            $this->database()->update(Phpfox::getT('fevent_invite'), array(
                'rsvp_id' => $iRsvp,
                'invited_user_id' => $iUserId,
                'time_stamp' => PHPFOX_TIME
            ), 'invite_id = ' . $iInviteId
            );

            (Phpfox::isModule('request') ? Phpfox::getService('request.process')->delete('fevent_invite', $iEvent, $iUserId) : false);
        } else {
            $this->database()->insert(Phpfox::getT('fevent_invite'), array(
                    'event_id' => $iEvent,
                    'rsvp_id' => $iRsvp,
                    'user_id' => $iUserId,
                    'invited_user_id' => $iUserId,
                    'time_stamp' => PHPFOX_TIME
                )
            );

            if ($iRsvp == 1) {
                $aEvent = Phpfox::getService('fevent')->getQuickEvent($iEvent);
                $sModule = $aEvent['module_id'];
                if ($sModule == 'directory') {
                    (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->callback(Phpfox::callback($sModule . '.getFeedDetails', $aEvent['item_id']))->add('fevent_attendevent', $iEvent, $aEvent['privacy'], (isset($aEvent['privacy_comment']) ? (int)$aEvent['privacy_comment'] : 0), $iUserId) : null);
                }
            }
        }

        return true;
    }

    public function addRsvpForRecurrentEvent($iEvent, $iRsvp, $iUserId, $typeEvent)
    {
        if (!Phpfox::isUser()) {
            return false;
        }

        if (($iInviteId = $this->database()->select('invite_id')
            ->from(Phpfox::getT('fevent_invite'))
            ->where('event_id = ' . (int)$iEvent . ' AND invited_user_id = ' . (int)$iUserId)
            ->execute('getField'))) {
            $this->database()->update(Phpfox::getT('fevent_invite'), array(
                'rsvp_id' => $iRsvp,
                'invited_user_id' => $iUserId,
                'time_stamp' => PHPFOX_TIME
            ), 'invite_id = ' . $iInviteId
            );

            (Phpfox::isModule('request') ? Phpfox::getService('request.process')->delete('fevent_invite', $iEvent, $iUserId) : false);
        } else {
            $this->database()->insert(Phpfox::getT('fevent_invite'), array(
                    'event_id' => $iEvent,
                    'rsvp_id' => $iRsvp,
                    'user_id' => $iUserId,
                    'invited_user_id' => $iUserId,
                    'time_stamp' => PHPFOX_TIME
                )
            );

            if ($iRsvp == 1) {
                $aEvent = Phpfox::getService('fevent')->getQuickEvent($iEvent);
                $sModule = $aEvent['module_id'];
                if ($sModule == 'directory') {
                    (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->callback(Phpfox::callback($sModule . '.getFeedDetails', $aEvent['item_id']))->add('fevent_attendevent', $iEvent, $aEvent['privacy'], (isset($aEvent['privacy_comment']) ? (int)$aEvent['privacy_comment'] : 0), $iUserId) : null);
                }
            }
        }

        if ($typeEvent == 'only_this_event') {

            return true;
        }

        if ($typeEvent == 'following_events') {

            /*get following events*/
            $aQuickEvent = Phpfox::getService('fevent')->getEventByID($iEvent);

            $aFollowingEvents = Phpfox::getService('fevent')->getFollowingEventByEventId($aQuickEvent['event_id'], $aQuickEvent['org_event_id']);

            if (count($aFollowingEvents)) {
                foreach ($aFollowingEvents as $key => $aEvent) {

                    $iEvent = $aEvent['event_id'];

                    if (($iInviteId = $this->database()->select('invite_id')
                        ->from(Phpfox::getT('fevent_invite'))
                        ->where('event_id = ' . (int)$iEvent . ' AND invited_user_id = ' . (int)$iUserId)
                        ->execute('getField'))) {
                        $this->database()->update(Phpfox::getT('fevent_invite'), array(
                            'rsvp_id' => $iRsvp,
                            'invited_user_id' => $iUserId,
                            'time_stamp' => PHPFOX_TIME
                        ), 'invite_id = ' . $iInviteId
                        );

                        (Phpfox::isModule('request') ? Phpfox::getService('request.process')->delete('fevent_invite', $iEvent, $iUserId) : false);
                    } else {
                        $this->database()->insert(Phpfox::getT('fevent_invite'), array(
                                'event_id' => $iEvent,
                                'rsvp_id' => $iRsvp,
                                'user_id' => $iUserId,
                                'invited_user_id' => $iUserId,
                                'time_stamp' => PHPFOX_TIME
                            )
                        );
                    }


                }
            }

            return true;
        }
    }


    public function deleteGuest($iInviteId)
    {
        $aEvent = $this->database()->select('e.event_id, e.user_id')
            ->from(Phpfox::getT('fevent_invite'), 'ei')
            ->join($this->_sTable, 'e', 'e.event_id = ei.event_id')
            ->where('ei.invite_id = ' . (int)$iInviteId)
            ->execute('getRow');

        if (!isset($aEvent['user_id'])) {
            return Phpfox_Error::set(_p('unable_to_find_the_event'));
        }

        if (Phpfox::getService('fevent.helper')->canEditEvent($aEvent['event_id'], Phpfox::getUserId(), $aEvent['user_id']) == false) {
            return Phpfox_Error::set(_p('you_do_not_have_sufficient_permission_to_modify_this_event'));
        }

        $this->database()->delete(Phpfox::getT('fevent_invite'), 'invite_id = ' . (int)$iInviteId);

        return true;
    }

    public function delete($iId, &$aEvent = null)
    {
        if ($sPlugin = Phpfox_Plugin::get('fevent.service_process_delete__start')) {
            return eval($sPlugin);
        }

        $mReturn = true;
        if ($aEvent === null) {
            $aEvent = $this->database()->select('user_id, module_id, item_id, image_path, is_sponsor,server_id')
                ->from($this->_sTable)
                ->where('event_id = ' . (int)$iId)
                ->execute('getRow');

            if ($aEvent['module_id'] == 'pages' && Phpfox::getService('pages')->isAdmin($aEvent['item_id'])) {
                $mReturn = Phpfox::getService('pages')->getUrl($aEvent['item_id']) . 'fevent/';
            } else {
                if (!isset($aEvent['user_id'])) {
                    return Phpfox_Error::set(_p('unable_to_find_the_event_you_want_to_delete'));
                }

                if (!Phpfox::getService('user.auth')->hasAccess('fevent', 'event_id', $iId, 'fevent.can_delete_own_event', 'fevent.can_delete_other_event', $aEvent['user_id'])) {
                    return Phpfox_Error::set(_p('you_do_not_have_sufficient_permission_to_delete_this_event'));
                }
            }
        }

        $aTemp = $this->database()->select("image_path")->from(Phpfox::getT('fevent_image'))->where("event_id = '$iId'")->execute("getRows");
        $this->database()->delete(Phpfox::getT('fevent_image'), "event_id = '$iId'");
        $aThumbs = array();
        foreach ($aTemp as $aRow) {
            $aThumbs[] = $aRow["image_path"];
        }
        $aThumbs[] = $aEvent['image_path'];
        foreach ($aThumbs as $sImagePath) {
            $aEvent['image_path'] = $sImagePath;
            if (!empty($aEvent['image_path'])) {
                $aImages = array(
                    Phpfox::getParam('event.dir_image') . sprintf($aEvent['image_path'], ''),
                    Phpfox::getParam('event.dir_image') . sprintf($aEvent['image_path'], '_50'),
                    Phpfox::getParam('event.dir_image') . sprintf($aEvent['image_path'], '_120'),
                    Phpfox::getParam('event.dir_image') . sprintf($aEvent['image_path'], '_200'),
                    Phpfox::getParam('event.dir_image') . sprintf($aEvent['image_path'], '_50_square')
                );
                //var_dump($aImages);

                $iFileSizes = 0;
                foreach ($aImages as $sImage) {
                    if (file_exists($sImage)) {
                        $iFileSizes += filesize($sImage);
                        if ($sPlugin = Phpfox_Plugin::get('fevent.service_process_delete__pre_unlink')) {
                            return eval($sPlugin);
                        }
                        @unlink($sImage);
                    }

                    if (Phpfox::getParam('core.allow_cdn') && $aEvent['server_id'] > 0) {
                        // Get the file size stored when the photo was uploaded
                        $sTempUrl = Phpfox::getLib('cdn')->getUrl(str_replace(Phpfox::getParam('event.dir_image'), Phpfox::getParam('event.url_image'), $sImage));

                        $aHeaders = get_headers($sTempUrl, true);
                        if (preg_match('/200 OK/i', $aHeaders[0])) {
                            $iFileSizes += (int)$aHeaders["Content-Length"];
                        }

                        Phpfox::getLib('cdn')->remove($sImage);

                    }
                }

                if ($iFileSizes > 0) {
                    if ($sPlugin = Phpfox_Plugin::get('fevent.service_process_delete__pre_space_update')) {
                        return eval($sPlugin);
                    }
                    Phpfox::getService('user.space')->update($aEvent['user_id'], 'fevent', $iFileSizes, '-');
                }
            }
        }

        if ($sPlugin = Phpfox_Plugin::get('fevent.service_process_delete__pre_deletes')) {
            return eval($sPlugin);
        }

        (Phpfox::isModule('comment') ? Phpfox::getService('comment.process')->deleteForItem(null, $iId, 'fevent') : null);
        (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->delete('fevent', $iId) : null);
        (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->delete('comment_event', $iId) : null);

        $aInvites = $this->database()->select('invite_id, invited_user_id')
            ->from(Phpfox::getT('fevent_invite'))
            ->where('event_id = ' . (int)$iId)
            ->execute('getSlaveRows');
        foreach ($aInvites as $aInvite) {
            (Phpfox::isModule('request') ? Phpfox::getService('request.process')->delete('fevent_invite', $aInvite['invite_id'], $aInvite['invited_user_id']) : false);
        }

        $this->database()->delete($this->_sTable, 'event_id = ' . (int)$iId);
        $this->cache()->remove();
        $this->database()->delete(Phpfox::getT('fevent_text'), 'event_id = ' . (int)$iId);
        $this->database()->delete(Phpfox::getT('fevent_category_data'), 'event_id = ' . (int)$iId);
        $this->database()->delete(Phpfox::getT('fevent_invite'), 'event_id = ' . (int)$iId);
        $iTotalEvent = $this->database()
            ->select('total_fevent')
            ->from(Phpfox::getT('user_field'))
            ->where('user_id =' . (int)$aEvent['user_id'])->execute('getSlaveField');
        $iTotalEvent = $iTotalEvent - 1;

        if ($iTotalEvent > 0) {
            $this->database()->update(Phpfox::getT('user_field'),
                array('total_fevent' => $iTotalEvent),
                'user_id = ' . (int)$aEvent['user_id']);
        }

        if (isset($aEvent['is_sponsor']) && $aEvent['is_sponsor'] == 1) {
            $this->cache()->remove('fevent_sponsored');
        }
        //close all sponsorships
        (Phpfox::isModule('ad') ? Phpfox::getService('ad.process')->closeSponsorItem('fevent', (int)$iId) : null);

        Phpfox::getService('user.activity')->update(Phpfox::getUserId(), 'fevent', '-');
        if ($sPlugin = Phpfox_Plugin::get('fevent.service_process_delete__end')) {
            return eval($sPlugin);
        }

        return $mReturn;
    }

    public function actionMultiple($aIds, $sAction)
    {
        switch ($sAction) {
            case 'delete':
                foreach ($aIds as $iId) {
                    $this->delete($iId);
                }
                break;
            case 'feature':
                foreach ($aIds as $iId) {
                    $this->feature($iId, 1);
                }
                break;
            case 'un-feature':
                foreach ($aIds as $iId) {
                    $this->feature($iId, 0);
                }
                break;
            case 'sponsor':
                foreach ($aIds as $iId) {
                    if ($this->sponsor($iId, 1)) {
                        $aEvent = Phpfox::getService('fevent')->getEventByID($iId);
                        Phpfox::getService('ad.process')->addSponsor(array(
                            'module' => 'fevent',
                            'item_id' => $iId,
                            'name' => _p('default_campaign_custom_name', ['module' => _p('event'), 'name' => $aEvent['title']])
                        ));
                    }
                }
                break;
            case 'un-sponsor':
                foreach ($aIds as $iId) {
                    if ($this->sponsor($iId, 0)) {
                        Phpfox::getService('ad.process')->deleteAdminSponsor('fevent', $iId);
                    }
                }
                break;
        }
        return true;
    }

    public function feature($iId, $iType)
    {
        Phpfox::isUser(true);
        Phpfox::getUserParam('fevent.can_feature_events', true);

        $this->database()->update($this->_sTable, array('is_featured' => ($iType ? '1' : '0')), 'event_id = ' . (int)$iId);

        $this->cache()->remove();

        return true;
    }

    public function sponsor($iId, $iType)
    {
        if (!Phpfox::getUserParam('fevent.can_sponsor_fevent') && !Phpfox::getUserParam('fevent.can_purchase_sponsor') && !defined('PHPFOX_API_CALLBACK')) {
            return Phpfox_Error::set('Hack attempt?');
        }

        $iType = (int)$iType;
        if ($iType != 1 && $iType != 0) {
            return false;
        }

        $this->database()->update($this->_sTable, array('is_sponsor' => $iType), 'event_id = ' . (int)$iId);


        if ($sPlugin = Phpfox_Plugin::get('fevent.service_process_sponsor__end')) {
            return eval($sPlugin);
        }

        $this->cache()->remove();

        return true;
    }

    public function approve($iId)
    {
        Phpfox::isUser(true);
        Phpfox::getUserParam('fevent.can_approve_events', true);

        $aEvent = $this->database()->select('v.*, ' . Phpfox::getUserField())
            ->from($this->_sTable, 'v')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = v.user_id')
            ->where('v.event_id = ' . (int)$iId)
            ->execute('getRow');

        if (!isset($aEvent['event_id'])) {
            return Phpfox_Error::set(_p('unable_to_find_the_event_you_want_to_approve'));
        }

        $this->database()->update($this->_sTable, array('view_id' => '0'), 'event_id = ' . $aEvent['event_id']);

        if (Phpfox::isModule('notification')) {
            Phpfox::getService('notification.process')->add('fevent_approved', $aEvent['event_id'], $aEvent['user_id']);
        }

        // Send the user an email
        $sLink = Phpfox::getLib('url')->permalink('fevent', $aEvent['event_id'], $aEvent['title']);

        Phpfox::getLib('mail')->to($aEvent['user_id'])
            ->subject(array('fevent.your_event_has_been_approved_on_site_title', array('site_title' => Phpfox::getParam('core.site_title'))))
            ->message(array('fevent.your_event_has_been_approved_on_site_title_link', array('site_title' => Phpfox::getParam('core.site_title'), 'link' => $sLink)))
            ->notification('fevent.event_is_approved')
            ->send();

        (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->add('fevent', $aEvent['event_id'], $aEvent['privacy'], $aEvent['privacy_comment'], 0, $aEvent['user_id']) : null);

        return true;
    }

    public function massEmail($iId, $iPage, $sSubject, $sText)
    {
        Phpfox::isUser(true);
        Phpfox::getUserParam('fevent.can_mass_mail_own_members', true);

        $aEvent = Phpfox::getService('fevent')->getEvent($iId, true);

        if (!isset($aEvent['event_id'])) {
            return false;
        }

        if ($aEvent['user_id'] != Phpfox::getUserId()) {
            return false;
        }
        if ($sPlugin = Phpfox_Plugin::get('fevent.service_process_massemail__start')) {
            return eval($sPlugin);
        }
        Phpfox::getService('ban')->checkAutomaticBan($sText);
        list($iCnt, $aGuests) = Phpfox::getService('fevent')->getInvites($iId, 1, $iPage, 20);

        $sLink = Phpfox::getLib('url')->permalink('fevent', $aEvent['event_id'], $aEvent['title']);

        $sText = '##<br />
        ' . _p('notice_this_is_a_newsletter_sent_from_the_event') . ': ' . $aEvent['title'] . '<br />
        <a href="' . $sLink . '">' . $sLink . '</a>
        ##<br />
        ' . $sText;

        foreach ($aGuests as $aGuest) {
            if ($aGuest['user_id'] == Phpfox::getUserId()) {
                continue;
            }

            Phpfox::getLib('mail')->to($aGuest['user_id'])
                ->subject($sSubject)
                ->message($sText)
                ->notification('fevent.mass_emails')
                ->send();
        }
        if ($sPlugin = Phpfox_Plugin::get('fevent.service_process_massemail__end')) {
            return eval($sPlugin);
        }
        $this->database()->update($this->_sTable, array('mass_email' => PHPFOX_TIME), 'event_id = ' . $aEvent['event_id']);

        return $iCnt;
    }

    public function removeInvite($iId)
    {
        $this->database()->delete(Phpfox::getT('fevent_invite'), 'event_id = ' . (int)$iId . ' AND invited_user_id = ' . Phpfox::getUserId());

        (Phpfox::isModule('request') ? Phpfox::getService('request.process')->delete('fevent_invite', $iId, Phpfox::getUserId()) : false);

        return true;
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
        if ($sPlugin = Phpfox_Plugin::get('fevent.service_process__call')) {
            return eval($sPlugin);
        }

        /**
         * No method or plug-in found we must throw a error.
         */
        Phpfox_Error::trigger('Call to undefined method ' . __CLASS__ . '::' . $sMethod . '()', E_USER_ERROR);
    }

    private function _verify(&$aVals, $bIsUpdate = false, $iId = null)
    {
        if (isset($aVals['category']) && is_array($aVals['category'])) {
            foreach ($aVals['category'] as $iCategory) {
                if (empty($iCategory)) {
                    continue;
                }

                if (!is_numeric($iCategory)) {
                    continue;
                }

                $this->_aCategories[] = $iCategory;
            }
        }


        if (isset($_FILES['image'])) {
            foreach ($_FILES['image']['error'] as $iKey => $sError) {
                if ($sError == UPLOAD_ERR_OK) {
                    $aImage = Phpfox::getLib('file')->load('image[' . $iKey . ']', array(
                            'jpg',
                            'gif',
                            'png'
                        )
                    );

                    if ($aImage === false) {
                        continue;
                    }

                    $this->_bHasImage = true;
                }
            }
        }

        //if ($bIsUpdate === false)
        if (isset($aVals['start_day']) && isset($aVals['end_day'])) {
            $iStartTime = Phpfox::getLib('date')->mktime($aVals['start_hour'], $aVals['start_minute'], 0, $aVals['start_month'], $aVals['start_day'], $aVals['start_year']);
            $iEndTime = Phpfox::getLib('date')->mktime($aVals['end_hour'], $aVals['end_minute'], 0, $aVals['end_month'], $aVals['end_day'], $aVals['end_year']);

            if ($iEndTime < $iStartTime) {
                return Phpfox_Error::set(_p('end_event_time_has_to_be_greater_than_start_event_time'));
                $this->_bIsEndingInThePast = true;
            }
        }

        return true;
    }

    public function resizeImage($sFilePath, $iThumbWidth, $iThumbHeight, $sSubfix)
    {
        $sRealPath = Phpfox::getParam('event.dir_image');
        // Resize to Width/Height
        list($iWidth, $iHeight, $sType, $sAttr) = getimagesize($sRealPath . sprintf($sFilePath, ""));
        $fSourceRatio = $iWidth / $iHeight;
        $fThumbRatio = $iThumbWidth / $iThumbHeight;
        if ($fSourceRatio > $fThumbRatio) {
            $iNewHeight = $iThumbHeight;
            $fRatio = $iNewHeight / $iHeight;
            $iNewWidth = $iWidth * $fRatio;
        } else {
            $iNewWidth = $iThumbWidth;
            $fRatio = $iNewWidth / $iWidth;
            $iNewHeight = $iHeight * $fRatio;
        }

        Phpfox::getLib("image")->createThumbnail($sRealPath . sprintf($sFilePath, ""), $sRealPath . sprintf($sFilePath, $sSubfix), $iNewWidth, $iNewHeight, true, false);

        // Crop the resized image
        if ($iNewWidth > $iThumbWidth) {
            $iX = ceil(($iNewWidth - $iThumbWidth) / 2);
            Phpfox::getLib("image")->cropImage($sRealPath . sprintf($sFilePath, $sSubfix), $sRealPath . sprintf($sFilePath, '_temp'), $iThumbWidth, $iThumbHeight, $iX, 0, $iThumbWidth);
            copy($sRealPath . sprintf($sFilePath, '_temp'), $sRealPath . sprintf($sFilePath, $sSubfix));
            unlink($sRealPath . sprintf($sFilePath, '_temp'));
        }
        if ($iNewHeight > $iThumbHeight) {
            $iY = ceil(($iNewHeight - $iThumbHeight) / 2);
            Phpfox::getLib("image")->cropImage($sRealPath . sprintf($sFilePath, $sSubfix), $sRealPath . sprintf($sFilePath, '_temp'), $iThumbWidth, $iThumbHeight, 0, $iY, $iThumbWidth);
            copy($sRealPath . sprintf($sFilePath, '_temp'), $sRealPath . sprintf($sFilePath, $sSubfix));
            unlink($sRealPath . sprintf($sFilePath, '_temp'));
        }
    }

    public function address2coordinates($sAddress)
    {
        $apiaddress = "https://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($sAddress) . "&sensor=true&key=" . Phpfox::getParam('core.google_api_key');
        $aResponse = json_decode(Phpfox::getLib('request')->send($apiaddress, array(), 'GET', $_SERVER['HTTP_USER_AGENT']), true);
        if ($aResponse['results']) {
            $tmpaCoordinates = $aResponse['results'][0]['geometry']['location'];
            $aCoordinates[1] = $tmpaCoordinates['lat'];
            $aCoordinates[0] = $tmpaCoordinates['lng'];
            $sGmapAddress = $aResponse['results'][0]['formatted_address'];
        } else {
            $aCoordinates[1] = 0;
            $aCoordinates[0] = 0;
            $sGmapAddress = '';
        }
        return array($aCoordinates, $sGmapAddress);
    }

    public function deleteAttendees($eventID, $iRsvp = 1, $deleteAll = false)
    {
        if (isset($deleteAll) && $deleteAll === true) {
            $this->database()->delete(Phpfox::getT('fevent_invite'), 'event_id = ' . (int)$eventID);
        } else {
            $this->database()->delete(Phpfox::getT('fevent_invite'), 'event_id = ' . (int)$eventID . ' AND rsvp_id = ' . (int)$iRsvp);
        }

        return true;
    }

    public function updateStartEndTimeByDurationOfRepeatEvent($eventID, $startTime, $endTime)
    {
        $this->database()->update(Phpfox::getT('fevent'), array(
            'start_time' => (int)$startTime,
            'end_time' => (int)$endTime
        ), 'event_id = ' . (int)$eventID
        );

        return true;
    }

    public function prePareDataForMap(&$aEvents)
    {
        $oHelper = Phpfox::getService('fevent.helper');
        $formatTime = Phpfox::getParam('fevent.fevent_browse_time_stamp');
        $data = $dataID = array();
        if (count($aEvents)) {
            foreach ($aEvents as $i => $aEvent) {
                $aEvents[$i]['lat'] = round($aEvents[$i]['lat'], 7);
                $aEvents[$i]['lng'] = round($aEvents[$i]['lng'], 7);
            }

            $aEventsData = $aEvents;
            $aEventsCompares = $aEvents;
            foreach ($aEventsData as $key => $aEvent) {
                if ($aEvent['lat'] != '' && $aEvent['lng'] != '') {
                    $keyLatLog = implode(",", array($aEvent['lat'], $aEvent['lng']));
                    if (in_array($key, $dataID[$keyLatLog])) {
                        continue;
                    }
                    $event = array();
                    $event['title'] = $aEvent['title'];
                    $event['location'] = $aEvent['location'];
                    $event['latitude'] = round($aEvent['lat'], 7);
                    $event['longitude'] = round($aEvent['lng'], 7);
                    $event['url_image'] = ($aEvent['image_path'] != '') ? Phpfox::getLib('image.helper')->display(array(
                        'server_id' => $aEvent['server_id'],
                        'path' => 'core.url_pic',
                        'file' => 'event' . PHPFOX_DS . $aEvent['image_path'],
                        'suffix' => '_120',
                        'return_url' => true
                    )) : Phpfox::getService('fevent')->getDefaultPhoto();

                    $event['d_start_time'] = $oHelper->displayTimeByFormat($formatTime, $aEvent['start_time']);
                    $event['url_detail'] = Phpfox::getLib('url')->permalink('fevent', $aEvent['event_id'], $aEvent['title']);
                    $data[$keyLatLog][] = $event;

                    /*check duplicate*/
                    foreach ($aEventsCompares as $keycp => $aEventsCompare) {
                        if ($key != $keycp) {
                            if ($aEvent['lat'] == $aEventsCompare['lat'] &&
                                $aEvent['lng'] == $aEventsCompare['lng']) {

                                $eventsame = array();
                                $eventsame['title'] = $aEventsCompare['title'];
                                $eventsame['location'] = $aEventsCompare['location'];
                                $eventsame['latitude'] = round($aEventsCompare['lat'], 7);
                                $eventsame['longitude'] = round($aEventsCompare['lng'], 7);
                                $eventsame['url_image'] = ($aEventsCompare['image_path'] != '') ? Phpfox::getLib('image.helper')->display(array(
                                    'server_id' => $aEventsCompare['server_id'],
                                    'path' => 'core.url_pic',
                                    'file' => 'event' . PHPFOX_DS . $aEventsCompare['image_path'],
                                    'suffix' => '_120',
                                    'return_url' => true
                                )) : Phpfox::getService('fevent')->getDefaultPhoto();

                                $eventsame['url_detail'] = Phpfox::getLib('url')->permalink('fevent', $aEventsCompare['event_id'], $aEventsCompare['title']);
                                $eventsame['d_start_time'] = $oHelper->displayTimeByFormat($formatTime, $aEventsCompare['start_time']);

                                $data[$keyLatLog][] = $eventsame;
                                $dataID[$keyLatLog][] = $keycp;
                            }
                        }
                    }

                }
            }
        }
        return $data;

    }


    public function drawCalendar($month, $year, $events = array())
    {

        $oHelper = Phpfox::getService('fevent.helper');

        /* draw table */
        $calendar = '<table cellpadding="0" cellspacing="0" class="calendar">';

        /* table headings */

        $sun = _p('sunday');
        $mon = _p('monday');
        $tue = _p('tuesday');
        $wed = _p('wednesday');
        $thu = _p('thursday');
        $fri = _p('friday');
        $sat = _p('saturday');

        $headings = array(
            '#',
            $mon,
            $tue,
            $wed,
            $thu,
            $fri,
            $sat,
            $sun
        );

        $aDayOfWeek = array(
            'monday' => 0,
            'tuesday' => 1,
            'wednesday' => 2,
            'thursday' => 3,
            'friday' => 4,
            'saturday' => 5,
            'sunday' => 6
        );

        $aRevertDayOfWeek = array(
            _p('monday'),
            _p('tuesday'),
            _p('wednesday'),
            _p('thursday'),
            _p('friday'),
            _p('saturday'),
            _p('sunday')
        );

        $aSortDayOfWeek = array();
        $countDayOfWeek = 0;
        $iIndexDate = $aDayOfWeek[Phpfox::getParam('fevent.fevent_start_week')];
        $iIndexMonday = $aDayOfWeek['monday'];
        $iOffSetDayInWeek = $iIndexMonday - $iIndexDate;
        while ($countDayOfWeek < 7) {
            $aSortDayOfWeek[] = ucfirst($aRevertDayOfWeek[$iIndexDate]);
            $iIndexDate++;
            if ($iIndexDate > 6) {
                $iIndexDate = 0;
            }
            $countDayOfWeek++;
        }

        array_unshift($aSortDayOfWeek, "#");
        $headings = $aSortDayOfWeek;

        /*echo '<pre>';
        print_r($headings);
        die;*/
        $calendar .= '<tr class="calendar-row"><td class="calendar-day-head">' . implode('</td><td class="calendar-day-head">', $headings) . '</td></tr>';
        $to_day = date('d');
        $cur_month = date('m');
        $cur_year = date('Y');
        /* days and weeks vars now ... */
        $running_day = date('w', mktime(0, 0, 0, $month, 1, $year)) + $iOffSetDayInWeek;
        $running_day = ($running_day < 0) ? ($running_day + 7) : $running_day;
        $days_in_month = date('t', mktime(0, 0, 0, $month, 1, $year));
        $days_in_this_week = 1;

        $iFirstDateInMonth = mktime(0, 0, 0, $month, 1, $year);
        $iLastDateInMonth = mktime(23, 59, 59, $month, (int)date("t", $iFirstDateInMonth), $year);
        // return date("t", mktime(0, 0, 0, $month, 1, $year));


        $day_counter = 0;
        $dates_array = array();
        if ($running_day == 0) {
            $running_day = 6;
        } else {
            $running_day = $running_day - 1;
        }

        /* row for week one */
        $calendar .= '<tr class="calendar-row">';
        $week_number_of_year = date("W", $iFirstDateInMonth);
        $calendar .= '<td class="calendar-day-np"><div>' . $week_number_of_year . '</div></td>';

        /* print "blank" days until the first of the current week */
        for ($x = 0; $x < $running_day; $x++) {
            $calendar .= '<td class="calendar-day-np"><div><span>' . (date('d', strtotime('-' . ($running_day - $x) . " days", $iFirstDateInMonth))) . '</span></div></td>';
            $days_in_this_week++;
        }

        /* keep going with days.... */
        for ($list_day = 1; $list_day <= $days_in_month; $list_day++) {
            $month1 = $month;
            $list_day1 = $list_day;
            if ($month < 10) {
                $month1 = '0' . $month;
            }
            if ($list_day < 10) {
                $list_day1 = '0' . $list_day;
            }

            $event_day = $year . '-' . $month1 . '-' . $list_day1;

            $showedViewMoreButton = false;
            $oldDay = $event_day;

            $href = '';
            $class_today = "";
            if ($month == $cur_month && $list_day == $to_day && $year == $cur_year) {
                $class_today = 'today';
            }

            $day_events = array();

            if (count($events)) {

                foreach ($events as $event) {

                    $startDate = Phpfox::getTime('Y-m-d', $oHelper->convertToUserTimeZone($event['start_time'])); //start date of event

                    if ((strcmp($startDate, $event_day) == 0)) {
                        $day_events[] = $event;
                    }

                    if (isset($event['bday'])) {
                        if (strcmp($event['bday'], $event_day) == 0) {
                            $day_events[] = $event;
                        }
                    }
                }
            }
            $calendar .= '<td class="calendar-day ' . $class_today . ' ' . ((count($day_events)) ? "calendar-event" : '') . '"><div>';

            $count = 0;

            if (count($day_events)) {

                $calendar .= '<div class="ynfevent-page-calendar-day-content">';
                foreach ($day_events as $event) {
                    $numberEvent = count($day_events);
                    $count++;
                    if ($count <= 2) {
                        $formatTime = Phpfox::getParam('fevent.fevent_browse_time_stamp');
                        if (isset($event['start_time'])) {
                            $dateTitle = $oHelper->displayTimeByFormat($formatTime, (int)$event['start_time']);
                        } else {
                            $dateTitle = $event['birthdate1'];
                        }


                        $url_event = Phpfox::getLib('url')->makeUrl('fevent', array($event['event_id']));
                        if (!empty($event['bday'])) {
                            $url_bday = Phpfox::getLib('url')->makeUrl($event['user_name']);
                        }
                        if (!empty($event) && $event['isrepeat'] >= 0) {
                            $calendar .= '<a id="fevent_calendar_' . $event['event_id'] . '" href="' . $url_event . '" class="fevent_calendar one-time"><span>' . Phpfox::getTime('h:i A',
                                    $event['start_time']) . "</span> " . '- ' . Phpfox::getLib('parse.output')->shorten($event['title'],
                                    15, '...') . '</a>';
                        } elseif ($event['isrepeat'] == -1) {
                            $calendar .= '<a id="fevent_calendar_' . $event['event_id'] . '" href="' . $url_event . '" class="fevent_calendar repeat"><span>' . Phpfox::getTime('h:i A',
                                    $event['start_time']) . "</span> " . '- ' . Phpfox::getLib('parse.output')->shorten($event['title'],
                                    15, '...') . '</a>';
                        }

                        if (!empty($event['bday'])) {
                            $calendar .= '<a href="' . $url_bday . '" class="fevent_calendar birthday">' . Phpfox::getLib('parse.output')->shorten($event['full_name'],
                                    15, '...') . '</a>';
                        }
                    }
                }
                if (count($day_events) > 2) {
                    $titleBox = _p('fevent.event_on') . ' ' . $dateTitle;
                    $calendar .= '<div class="viewmore_event" onClick="tb_show(\'' . $titleBox . '\', $.ajaxBox(\'fevent.loadRestEvent\',\'month=' . $month1 . '&year=' . $year . '&day=' . $list_day1 . '&width=600\'));">' . ($numberEvent - 2) . ' ' . _p('more') . '</div>';
                }
                $calendar .= '</div>';

            }

            /* add in the day number */
            $href = Phpfox::getLib('url')->makeUrl('fevent') . '?date=' . $year . '-' . $month1 . '-' . $list_day . '&when=all-time&view=all';
            $calendar .= '<div class="day-number"><a href = "' . $href . '"><span>' . $list_day . '</span></a></div>';

            $calendar .= '</div></td>';

            if ($running_day == 6) {
                $calendar .= '</tr>';
                if (($day_counter + 1) != $days_in_month) {
                    $calendar .= '<tr class="calendar-row">';
                    if ((date("N", mktime(0, 0, 0, $month, 1, $year)) - 1) == $iIndexDate) {
                        $week_number_of_year = date("W", mktime(0, 0, 0, $month, ($list_day + 1), $year));
                    } else {
                        $week_number_of_year = date("W", mktime(0, 0, 0, $month, ($list_day + 1 + $iIndexDate), $year));
                    }
                    $calendar .= '<td class="calendar-day-np"><div><span>' . $week_number_of_year . '</span></div></td>';
                }
                $running_day = -1;
                $days_in_this_week = 0;
            }
            $days_in_this_week++;
            $running_day++;
            $day_counter++;
        }

        /* finish the rest of the days in the week */
        if ($days_in_this_week < 8 && $days_in_this_week > 1) {
            for ($x = 1; $x <= (8 - $days_in_this_week); $x++) {
                $calendar .= '<td class="calendar-day-np">' . (date('d', strtotime('+' . $x . " days", $iLastDateInMonth))) . '</td>';
                // $calendar .= '<td class="calendar-day-np">&nbsp;</td>';
            }
        }

        /* final row */
        $calendar .= '</tr>';

        /* end the table */
        $calendar .= '</table>';

        /** DEBUG * */
        $calendar = str_replace('</td>', '</td>' . "\n", $calendar);
        $calendar = str_replace('</tr>', '</tr>' . "\n", $calendar);

        /* all done, return result */
        return $calendar;

    }

    public function subscribeEvent($aData)
    {

        if (count($aData) && $aData['email'] != '') {

            $aSearch = $this->database()->select('dbs.email')
                ->from(Phpfox::getT('fevent_subscribe_email'), 'dbs')
                ->where('dbs.email = \'' . $aData['email'] . '\'')
                ->execute('getSlaveRow');

            if (count($aSearch)) {
                $this->database()->update(Phpfox::getT('fevent_subscribe_email'), array('data' => json_encode($aData)), 'email = \'' . $aData['email'] . '\'');
            } else {

                $aSearch = array(
                    'email' => $aData['email'],
                    'data' => json_encode($aData),
                );

                $this->database()->insert(Phpfox::getT('fevent_subscribe_email'), $aSearch);
            }
            return true;
        }

        return false;
    }

}

?>
