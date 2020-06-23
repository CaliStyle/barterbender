<?php

require_once 'cli.php';
require_once 'google-api-php-client/src/Google_Client.php';
require_once 'google-api-php-client/src/contrib/Google_CalendarService.php';
/*
@ini_set('display_startup_errors',1);
@ini_set('display_errors',1);
@ini_set('error_reporting',-1);
*/
//session_start();

$client = new Google_Client();
$cal = new Google_CalendarService($client);
$salt = Phpfox::getParam('core.salt');

$client->setApplicationName("phpFox Advanced Event Application");

if (isset($_GET['error']) && $_GET['error']=='access_denied')
{
    if (isset($_SESSION[$salt . 'fevent_event_id']))
    {
        $event_id = $_SESSION[$salt . 'fevent_event_id'];
        $title = Phpfox::getLib('database')->select('title')->from(Phpfox::getT('fevent'))->where('event_id = '.$event_id)->execute('getSlaveField');
        $title = Phpfox::getLib('parse.input')->cleanTitle($title);
        Phpfox::getLib('url')->send('fevent', array($event_id, $title));
    }
    else
    {
        Phpfox::getLib('url')->send('fevent.when_upcoming');
    }
}

if (isset($_GET['logout']))
{
    unset($_SESSION[$salt . 'fevent_token']);
}

if (isset($_GET['event_id']))
{
    $_SESSION[$salt . 'fevent_event_id'] = $_GET['event_id'];
}

if (isset($_GET['code']))
{
    $client->authenticate($_GET['code']);
    $_SESSION[$salt . 'fevent_token'] = $client->getAccessToken();
    header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
}

if (isset($_SESSION[$salt . 'fevent_token']))
{
    $client->setAccessToken($_SESSION[$salt . 'fevent_token']);
}

if ($client->getAccessToken())
{
    $event_id = $_SESSION[$salt . 'fevent_event_id'];

    $aEvent = Phpfox::getService('fevent')->getEvent($event_id);

    #Create Google Event
    $event = new Google_Event();

    #Set Summary
    $event->setSummary(html_entity_decode($aEvent['title'], ENT_QUOTES, 'UTF-8'));

    #Set Description
    $sDescription = strip_tags(html_entity_decode($aEvent['description'], ENT_QUOTES, 'UTF-8'));
    $sEventUrl = Phpfox::getLib('url')->makeUrl('fevent', array($event_id, Phpfox::getLib('parse.input')->cleanTitle($aEvent['title'])));
    $sDescription .= "\n\n" . _p('link_of_event_link', array('link' => $sEventUrl));
    $event->setDescription($sDescription);

    #Set Location
    $sLocation = html_entity_decode($aEvent['location'], ENT_QUOTES, 'UTF-8');
    if ($aEvent['address'] != '')
    {
        $sLocation .= ', ' . html_entity_decode($aEvent['address'], ENT_QUOTES, 'UTF-8');
    }
    if ($aEvent['city'] != '')
    {
        $sLocation .= ', ' . html_entity_decode($aEvent['city'], ENT_QUOTES, 'UTF-8');
    }
    $event->setLocation($sLocation);

    #Check repeat
    $start_time = $aEvent['start_time'];
    $isrepeat = $aEvent['isrepeat'];
    if ($isrepeat == '-1')
    {
        $end_time = $aEvent['end_time'];
    }
    else
    {
        $end_time = $start_time + 3600;
    }

    #Set Start time
    $start_gmt_offset = $aEvent['start_gmt_offset'];
    $cStartTime = convertCDate($start_time + ($start_gmt_offset*3600), $start_gmt_offset);
    $start_gmt_offset = convertOffset($start_gmt_offset);

    $start = new Google_EventDateTime();
    $start->setDateTime($cStartTime);
    $start->setTimeZone($start_gmt_offset);
    $event->setStart($start);

    #Set End time
    $end_gmt_offset = $aEvent['end_gmt_offset'];
    $cEndTime = convertCDate($end_time + ($end_gmt_offset*3600), $end_gmt_offset);
    $end_gmt_offset = convertOffset($end_gmt_offset);

    $end = new Google_EventDateTime();
    $end->setDateTime($cEndTime);
    $end->setTimeZone($end_gmt_offset);
    $event->setEnd($end);

    #Build RRULE for Recurrence Event
    if ($isrepeat == 0 || $isrepeat == 1 || $isrepeat == 2)
    {
        $rRule = Phpfox::getService('fevent')->buildRRule($aEvent);
        $event->setRecurrence(array($rRule));
    }

    #Get Calendar list
    $calendarList = $cal->calendarList->listCalendarList();
    
    #Check main Calendar
    if (count($calendarList))
    {
        foreach ($calendarList['items'] as $calendar)
        {
            if ($calendar['accessRole'] == 'owner')
            {
                #Post event to Google Calendar
                $cal->events->insert($calendar['id'], $event);
            }
        }
    }

    #Redirect to event
    $title = Phpfox::getLib('parse.input')->cleanTitle($aEvent['title']);
    Phpfox::getLib('url')->send('fevent',array($event_id, $title,'gsuccess'),_p('event_has_been_successfully_added_to_your_google_calendar'));

    $_SESSION[$salt . 'fevent_token'] = $client->getAccessToken();
}
else
{
    $authUrl = $client->createAuthUrl();
    header("Location: " . $authUrl);
    die;
}

function convertCDate($time_stamp, $offset)
{
    //2013-03-05T14:04:00+07:00
    return substr(date('c', $time_stamp), 0, 19).convertOffset($offset);
}

function convertOffset($offset)
{
    $offset_minutes = ($offset - (int)$offset)*60;

    if((int)$offset_minutes == 0)
    {
        $offset_minutes = "00";
    }
    $offset = (int)$offset;
    if($offset >= 0)
    {
        $result = ($offset < 10) ? '+0'.$offset.':'.$offset_minutes : '+'.$offset.':'.$offset_minutes;
    }
    else
    {
        $result = ($offset > -10) ? '-0'.abs($offset).':'.$offset_minutes : $offset.':'.$offset_minutes;
    }
    return $result;
}
