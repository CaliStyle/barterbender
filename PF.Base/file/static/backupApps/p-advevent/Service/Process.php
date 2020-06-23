<?php

namespace Apps\P_AdvEvent\Service;

use Phpfox;
use Phpfox_Service;
use Phpfox_Error;
use Phpfox_Plugin;

class Process extends Phpfox_Service
{
    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('fevent');
    }

    public function addSendWish($aVals)
    {
        $aCommentVals = [
            'action' => 'upload_photo_via_share',
            'user_status' => $aVals['message'],
            'parent_user_id' => $aVals['target_user_id'],
            'method' => 'simple'
        ];
        Phpfox::getService('feed.process')->addComment($aCommentVals);
        return db()->insert(Phpfox::getT('fevent_birthday_wish'), $aVals);
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

        $oParseInput = Phpfox::getLib('parse.input');
        Phpfox::getService('ban')->checkAutomaticBan($aVals);
        $bHasAttachments = (!empty($aVals['attachment']) && Phpfox::getUserParam('fevent.can_attach_on_event'));

        list($iStartTime, $iEndTime) = Phpfox::getService('fevent.helper')->parseStartEndTime($aVals);
        if ($iStartTime > $iEndTime) {
            $iEndTime = $iStartTime + 3600;
        }

        $isRepeatEvent = $aVals['isrepeat'] != '-1';
        $timerepeat = 0;
        $after_number_event = 0;

        if ($isRepeatEvent) {
            switch ($aVals['repeat_section_end_repeat']) {
                case 'after_number_event':
                    $after_number_event = (int)$aVals['repeat_section_after_number_event'];
                    break;
                case 'repeat_until':
                    $timerepeat = Phpfox::getLib('date')->mktime(23, 59, 59, $aVals['repeat_section_repeatuntil_month'], $aVals['repeat_section_repeatuntil_day'], $aVals['repeat_section_repeatuntil_year']);
                    break;
            }
        }

        $range_value_real = $aVals['range_type'] * 1000;
        if ($range_value_real == 0) {
            $range_value_real = 1609;
        }

        $aSql = array(
            'view_id' => (Phpfox::getUserParam('fevent.event_must_be_approved') ? '1' : '0'),
            'privacy' => (isset($aVals['privacy']) ? $aVals['privacy'] : '0'),
            'privacy_comment' => (isset($aVals['privacy_comment']) ? $aVals['privacy_comment'] : '0'),
            'module_id' => $sModule,
            'isrepeat' => $aVals['isrepeat'],
            'timerepeat' => $timerepeat ? Phpfox::getLib('date')->convertToGmt($timerepeat) : '0',
            'after_number_event' => $after_number_event,
            'range_value' => (int)$aVals['range_value'],
            'range_type' => $aVals['range_type'],
            'range_value_real' => $aVals['range_value'] * $range_value_real,
            'duration_days' => (int)$aVals['daterepeat_dur_day'],
            'duration_hours' => (int)$aVals['daterepeat_dur_hour'],
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
            'has_ticket' => empty($aVals['has_ticket']) ? 0 : 1,
            'ticket_type' => $aVals['ticket_type'],
            'ticket_price' => $aVals['ticket_price'],
            'ticket_url' => $aVals['ticket_url'],
            'has_notification' => empty($aVals['has_notification']) ? 0 : 1,
            'notification_type' => empty($aVals['has_notification']) ? 'no_remind' : $aVals['notification_type'],
            'notification_value' => empty($aVals['has_notification']) ? 0 : $aVals['notification_value'],
            'total_comment' => 0,
            'total_like' => 0,
            'image_path' => empty($aVals['image_path']) ? '' : $aVals['image_path'],
            'server_id' => empty($aVals['server_id']) ? 0 : $aVals['server_id'],
        );
        $aSql['lat'] = $aSql['lng'] = 0;
        $aSql['notification_time'] = $this->getNotificationTime($aSql['start_time'], $aSql['notification_type'], $aSql['notification_value']);

        if (isset($aVals['gmap'])
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

        $this->addEventImage($iId, $aVals['image_path'], $aVals['server_id']);

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

        if (!empty($aVals['category']) && is_numeric($aVals['category'])) {
            $iCategoryId = $aVals['category'];
            $this->database()->insert(Phpfox::getT('fevent_category_data'), array('event_id' => $iId, 'category_id' => $iCategoryId));
        }

        $bAddFeed = Phpfox::getUserParam('fevent.event_must_be_approved') ? false : true;

        if ($bAddFeed === true) {
            if ($sModule == 'fevent') {
                (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->add('fevent', $iId, $aVals['privacy'], (isset($aVals['privacy_comment']) ? (int)$aVals['privacy_comment'] : 0)) : null);
            } else {
                (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->callback(Phpfox::callback($sModule . '.getFeedDetails', $iItem))->add('fevent', $iId, $aVals['privacy'], (isset($aVals['privacy_comment']) ? (int)$aVals['privacy_comment'] : 0), $iItem) : null);
            }

            Phpfox::getService('user.activity')->update(Phpfox::getUserId(), 'fevent');
        }

        $this->addRsvp($iId, 1, Phpfox::getUserId());

        if (Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support')) {
            Phpfox::getService('tag.process')->add('fevent', $iId, Phpfox::getUserId(), $aVals['description'], true);
        }

        // Plugin call
        if ($sPlugin = Phpfox_Plugin::get('fevent.service_process_add__end')) {
            eval($sPlugin);
        }
        // update 'org_event_id'
        // generate instance(s)
        if ($isRepeatEvent) {
            $this->database()->update(Phpfox::getT('fevent'), array(
                'org_event_id' => (int)$iId,
            ), 'event_id = ' . (int)$iId);

            $this->generateInstanceForRepeatEvent($iId, $aVals, $bHasAttachments);
        }

        return $iId;
    }

    public function getNotificationTime($startTime, $type, $value) {
        switch ($type) {
            case 'minute':
                $multiplier = MINUTE_IN_SECONDS;
                break;
            case 'hour':
                $multiplier = HOUR_IN_SECONDS;
                break;
            case 'day':
                $multiplier = DAY_IN_SECONDS;
                break;
        }

        return $startTime - $value * $multiplier;
    }

    public function addEventImage($eventId, $imagePath, $serverId)
    {
        $this->database()->insert(Phpfox::getT('fevent_image'), array(
            'event_id' => $eventId,
            'image_path' => $imagePath,
            'server_id' => $serverId
        ));
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

        $iTimeRepeat = 0;
        if ((int)$aEvent['after_number_event'] > 0) {
            $len = (int)$aEvent['after_number_event'];
        } else if (isset($aEvent['timerepeat']) && (int)$aEvent['timerepeat'] > 0) {
            $iTimeRepeat = (int)$aEvent['timerepeat'];
            $len = (int)Phpfox::getParam('fevent.fevent_max_instance_repeat_event');
        }

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

            if ($iTimeRepeat && $iStartTime > $iTimeRepeat) {
                break;
            }

            $iEndTime = $iStartTime + $iDuration;
            $fevent_table['start_time'] = $iStartTime;
            $fevent_table['end_time'] = $iEndTime;

            $iId = $this->database()->insert(Phpfox::getT('fevent'), $fevent_table);
            $copied_image_path = $this->copyRecurringImage(
                $iId,
                array(
                    'recurring_image' => array($aEvent['image_path']),
                    'server_id' => $aEvent['server_id'],
                ),
                true);

            if ($copied_image_path) {
                $this->database()->update(
                    $this->_sTable,
                    array(
                        'image_path' => $copied_image_path,
                        'server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID')
                    ),
                    'event_id = ' . $iId);
            }

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

            if (Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support')) {
                Phpfox::getService('tag.process')->add('fevent', $iId, Phpfox::getUserId(), $aVals['description'], true);
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

        $aUpdateEvent = Phpfox::getService('fevent')->getQuickEvent($iId);

        $oParseInput = Phpfox::getLib('parse.input');

        Phpfox::getService('ban')->checkAutomaticBan($aVals['title'] . ' ' . $aVals['description']);

        $bHasAttachments = (!empty($aVals['attachment']) && Phpfox::getUserParam('fevent.can_attach_on_event'));
        if ($bHasAttachments) {
            Phpfox::getService('attachment.process')->updateItemId($aVals['attachment'], Phpfox::getUserId(), $iId);
        }

        $one_time_to_repeat = false;
        $timerepeat = $aUpdateEvent['timerepeat'];
        $after_number_event = $aUpdateEvent['after_number_event'];

        if ($aVals['isrepeat'] != -1) {
            if ($aEventPost['isrepeat'] == -1) {
                $one_time_to_repeat = true;
                switch ($aVals['repeat_section_end_repeat']) {
                    case 'after_number_event':
                        $after_number_event = (int)$aVals['repeat_section_after_number_event'];
                        break;
                    case 'repeat_until':
                        $timerepeat = Phpfox::getLib('date')->mktime(23, 59, 59, $aVals['repeat_section_repeatuntil_month'], $aVals['repeat_section_repeatuntil_day'], $aVals['repeat_section_repeatuntil_year']);
                        break;
                }
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
            'isrepeat' => $aVals['isrepeat'],
            'range_value' => $aVals['range_value'],
            'range_type' => $aVals['range_type'],
            'range_value_real' => $aVals['range_value'] * $range_value_real,
            'country_child_id' => (isset($aVals['country_child_id']) ? Phpfox::getService('core.country')->getValidChildId($aVals['country_iso'], (int)$aVals['country_child_id']) : 0),
            'city' => (empty($aVals['city']) ? null : $oParseInput->clean($aVals['city'], 255)),
            'postal_code' => (empty($aVals['postal_code']) ? null : Phpfox::getLib('parse.input')->clean($aVals['postal_code'], 20)),
            'address' => (empty($aVals['address']) ? null : Phpfox::getLib('parse.input')->clean($aVals['address'])),
            'total_attachment' => (Phpfox::isModule('attachment') ? Phpfox::getService('attachment')->getCountForItem($iId, 'fevent') : '0'),
            'has_ticket' => empty($aVals['has_ticket']) ? 0 : 1,
            'ticket_type' => $aVals['ticket_type'],
            'ticket_price' => $aVals['ticket_price'],
            'ticket_url' => $aVals['ticket_url'],
            'has_notification' => empty($aVals['has_notification']) ? 0 : 1,
            'notification_type' => empty($aVals['has_notification']) ? 'no_remind' : $aVals['notification_type'],
            'notification_value' => empty($aVals['has_notification']) ? 0 : $aVals['notification_value']
        );

        if ($one_time_to_repeat) {
            $aSql['after_number_event'] = $after_number_event;
            $aSql['timerepeat'] = Phpfox::getLib('date')->convertToGmt($timerepeat);
        }

        //  HIDE WARNING UPDATE FOR OLD REPEAT DATA
        if ($this->database()->isField(Phpfox::getT('fevent'), 'is_update_warning')) {
            $aSql['is_update_warning'] = 0;
        }

        list($iStartTime, $iEndTime) = Phpfox::getService('fevent.helper')->parseStartEndTime($aVals);

        if ($iStartTime > $iEndTime) {
            $iEndTime = $iStartTime + 3600;
        }

        $aSql['start_time'] = Phpfox::getLib('date')->convertToGmt($iStartTime);
        $aVals['interval_start_date'] = Phpfox::getTime('j', $aSql['start_time'], false) - Phpfox::getTime('j', $aUpdateEvent['start_time'], false);
        $aSql['start_gmt_offset'] = Phpfox::getLib('date')->getGmtOffset($iStartTime);

        //  update start so that update end
        $aSql['end_time'] = Phpfox::getLib('date')->convertToGmt($iEndTime);
        $aVals['interval_end_date'] = Phpfox::getTime('j', $aSql['end_time'], false) - Phpfox::getTime('j', $aUpdateEvent['end_time'], false);
        $aSql['end_gmt_offset'] = Phpfox::getLib('date')->getGmtOffset($iEndTime);

        $aSql['notification_time'] = $this->getNotificationTime($aSql['start_time'], $aSql['notification_type'], $aSql['notification_value']);

        $this->notifyOnEventChange($aSql, $iId, $aUpdateEvent);

        $aSql['lat'] = $aSql['lng'] = 0;

        if (isset($aVals['gmap'])
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

        if ($sPlugin = Phpfox_Plugin::get('fevent.service_process_update__start')) {
            return eval($sPlugin);
        }

        $this->database()->update($this->_sTable, $aSql, 'event_id = ' . (int)$iId);

        $this->database()->update(Phpfox::getT('fevent_text'), array(
            'description' => (empty($aVals['description']) ? null : $oParseInput->clean($aVals['description'])),
            'description_parsed' => (empty($aVals['description']) ? null : $oParseInput->prepare($aVals['description']))
        ), 'event_id = ' . (int)$iId
        );

        // processing custom fileds
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

        $this->reInvite($iId, $aVals);

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

        if (!empty($aVals['category']) && is_numeric($aVals['category'])) {
            $this->database()->delete(Phpfox::getT('fevent_category_data'), 'event_id = ' . (int)$iId);
            $iCategoryId = $aVals['category'];
            $this->database()->insert(Phpfox::getT('fevent_category_data'), array('event_id' => $iId, 'category_id' => $iCategoryId));
        }

        if (empty($aEvent['module_id'])) {
            (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->update('fevent', $iId, $aVals['privacy'], $aVals['privacy_comment'], 0, $aEvent['user_id']) : null);
        }

        if (Phpfox::VERSION >= '3.7.0' && Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support')) {
            Phpfox::getService('tag.process')->update('fevent', $aEvent['event_id'], $aEvent['user_id'], $aVals['description'], true);
        }

        // update 'org_event_id'
        // generate instance(s)
        if ($one_time_to_repeat == true) {
            // one time type changes to repeat type
            $this->database()->update(Phpfox::getT('fevent'), array(
                'org_event_id' => (int)$iId,
            ), 'event_id = ' . (int)$iId);

            $this->generateInstanceForRepeatEvent($iId, $aVals, $bHasAttachments);

        } else if ($one_time_to_repeat == false && $aVals['isrepeat'] >= 0) {
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
            if (isset($aVals['category'])) {
                if (is_array($aVals['category'])) {
                    $categories = $aVals['category'];
                } else {
                    $categories = array($aVals['category']);
                }
                $this->database()->delete(Phpfox::getT('fevent_category_data'), 'event_id = ' . (int)$iId);
                foreach ($categories as $iCategoryId) {
                    $this->database()->insert(Phpfox::getT('fevent_category_data'), array('event_id' => $iId, 'category_id' => $iCategoryId));
                }
            }

            if (Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support')) {
                Phpfox::getService('tag.process')->update('fevent', $aEditedEvent['event_id'], $aEditedEvent['user_id'], $aVals['description'], true);
            }
        }
    }

    public function reInvite($iId, $aVals)
    {
        $oParseInput = Phpfox::getLib('parse.input');

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

                $iInviteId = $this->database()->insert(Phpfox::getT('fevent_invite'), array(
                    'event_id' => $iId,
                    'user_id' => Phpfox::getUserId(),
                    'invited_user_id' => $aUser['user_id'],
                    'time_stamp' => PHPFOX_TIME
                ));

                (Phpfox::isModule('request') ? Phpfox::getService('request.process')->add('fevent_invite', $iId, $aUser['user_id']) : null);

            }
        }
    }

    public function notifyOnEventChange($newData, $iId, $aUpdateEvent)
    {
        /*check change start time & end time,if true we will send notification and reset rsvp*/
        $change_start_time = $newData['start_time'] - $aUpdateEvent['start_time'];
        $change_end_time = $newData['end_time'] - $aUpdateEvent['end_time'];
        $change_location = $newData['location'] != $aUpdateEvent['location'];

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
        if (!PHPFOX_SAFE_MODE && !defined('PHPFOX_IS_HOSTED_SCRIPT')) {
            $aParts = explode('/', 'Y/m');
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
        $sImageDb = '';
        if ((isset($aVals['upload_photo']) && isset($_FILES['image']) || $bForce) && isset($aVals['recurring_image']) && count($aVals['recurring_image'])) {
            foreach ($aVals['recurring_image'] as $sFilename) {
                $aSizes = array('', 50, 120, 200);
                $sExt = $this->getFileExt($sFilename);
                $sFileDir = $this->_buildDir(Phpfox::getParam('event.dir_image'));

                $sFileNameCopy = $sFileDir . md5($iId . PHPFOX_TIME . uniqid()) . '%s.' . $sExt;

                foreach ($aSizes as $iSize) {
                    $sImage = Phpfox::getParam('event.dir_image') . sprintf($sFilename, (empty($iSize) ? '' : '_') . $iSize);
                    $sImageCopy = sprintf($sFileNameCopy, (empty($iSize) ? '' : '_') . $iSize);
                    if (file_exists($sImage)) {
                        if ($sFileDir !== false) {
                            if (Phpfox::getLib('file')->copy($sImage, $sImageCopy)) {
                                Phpfox::getLib('cdn')->put($sImageCopy);
                            };
                        }
                    } else {
                        if (!empty($aVals['server_id'])) {
                            $sActualFile = Phpfox::getLib('image.helper')->display(array(
                                    'server_id' => $aVals['server_id'],
                                    'path' => 'event.url_image',
                                    'file' => $sFilename,
                                    'suffix' => $iSize,
                                    'return_url' => true
                                )
                            );

                            if (filter_var($sActualFile, FILTER_VALIDATE_URL) !== false) {
                                file_put_contents($sImageCopy, fox_get_contents($sActualFile));
                            } else {
                                copy($sActualFile, $sImageCopy);
                            }
                            //Delete file in local server
                            register_shutdown_function(function () use ($sImageCopy) {
                                @unlink($sImageCopy);
                            });

                            if (file_exists($sImageCopy)) {
                                Phpfox::getLib('cdn')->put($sImageCopy);
                            }
                        }
                    }
                }

                if ($sFileDir !== false) {

                    $sImageDb = str_replace(Phpfox::getParam('event.dir_image'), "", $sFileNameCopy);

                    $this->database()->insert(
                        Phpfox::getT('fevent_image'),
                        array('event_id' => $iId, 'image_path' => $sImageDb, 'server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID'))
                    );
                }
            }
        }

        return $sImageDb;
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

        if (!Phpfox::getService('fevent.helper')->canEditEvent($aEvent)) {
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

        if (!Phpfox::getService('fevent.helper')->canEditEvent($aEvent)) {
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

            if ($aEvent['server_id'] > 0) {
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

        if (!Phpfox::getService('fevent.helper')->canEditEvent($aEvent)) {
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

                    if ($aEvent['server_id'] > 0) {
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
        (Phpfox::isAppActive('Core_BetterAds') ? Phpfox::getService('ad.process')->closeSponsorItem('fevent', (int)$iId) : null);

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

        if ($aEvent['view_id'] == 0) {
            return false;
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

        if(Phpfox::isModule('feed')) {
            if($aEvent['module_id'] == 'fevent') {
                Phpfox::getService('feed.process')->add('fevent', $aEvent['event_id'], $aEvent['privacy'], $aEvent['privacy_comment'], 0, $aEvent['user_id']);
            }
            else {
                Phpfox::getService('feed.process')->callback(Phpfox::callback($aEvent['module_id'] . '.getFeedDetails', $aEvent['item_id']))
                    ->add('fevent', $aEvent['event_id'], $aEvent['privacy'], $aEvent['privacy_comment'], $aEvent['item_id'], $aEvent['user_id']);
            }
        }

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
        //TODO: replace this format with core format
        $formatTime = 'F j, Y';
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
                    'code' => md5($aData['email']. uniqid())
                );

                $this->database()->insert(Phpfox::getT('fevent_subscribe_email'), $aSearch);
            }
            return true;
        }

        return false;
    }

    public function unsubscribe($subscribeId)
    {
        if(empty($subscribeId)) {
            return false;
        }
        return db()->delete(Phpfox::getT('fevent_subscribe_email'), 'subscribe_id = '. (int)$subscribeId);
    }
}