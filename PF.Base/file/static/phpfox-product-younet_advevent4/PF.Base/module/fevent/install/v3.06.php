<?php
function ynfe_install306_getSameDayInNextMonth($day, $month, $year)
{
    //  if date is invalid, date will be become last date in month 
    if($month == 12){
        $month = 1;
        $year = $year + 1; 
    } else {
        $month = $month + 1;     
    }

    $exist = false; 
    while($exist == false){
        if (checkdate($month, $day, $year) == true) {
          $exist = true;
          break;
        } else {
          $exist = false;
          $day = $day - 1;
        }        
    }

    return array('day' => $day, 'month' => $month, 'year' => $year);
}

function ynfe_install306_migrate()
{
    $oDatabase = Phpfox::getLib('database');
    $curTime = PHPFOX_TIME;
    $repeatEvents = $oDatabase->select('e.*, txt.description, txt.description_parsed')
        ->from(Phpfox::getT('fevent'), 'e')
        ->leftJoin(Phpfox::getT('fevent_text'), 'txt', 'txt.event_id = e.event_id')
         ->where(' `isrepeat` > -1 ')
         // ->where(' `isrepeat` > -1 AND e.event_id = 1565')
         ->group('e.event_id')
         ->execute('getSlaveRows');

    foreach ($repeatEvents as $key => $aEvent) {
        // fevent_category_data
        $aCategories = $oDatabase->select('fcd.*')
            ->from(Phpfox::getT('fevent_category_data'), 'fcd')
             ->where('fcd.event_id = ' . (int) $aEvent['event_id'])
             ->execute('getSlaveRows');

        // fevent_admin
        $aAdmins = $oDatabase->select('fad.*')
            ->from(Phpfox::getT('fevent_admin'), 'fad')
             ->where('fad.event_id = ' . (int) $aEvent['event_id'])
             ->execute('getSlaveRows');

        // fevent_custom_value
        $aCustomValues = $oDatabase->select('fcv.*')
            ->from(Phpfox::getT('fevent_custom_value'), 'fcv')
             ->where('fcv.event_id = ' . (int) $aEvent['event_id'])
             ->execute('getSlaveRows');

        // fevent_feed
        $aFeeds = $oDatabase->select('ffd.*')
            ->from(Phpfox::getT('fevent_feed'), 'ffd')
             ->where('ffd.parent_user_id = ' . (int) $aEvent['event_id'])
             ->execute('getSlaveRows');

        // fevent_feed_comment
        $aFeedCommentId = array();
        foreach ($aFeeds as $keyaFeeds => $valueaFeeds) {
            if('fevent_comment' == $valueaFeeds['type_id']){
                $aFeedCommentId[] = $valueaFeeds['item_id'];
            }
        }

        $aFeedComments = array();
        if(count($aFeedCommentId) > 0){
            $sFeedCommentId = implode(",", $aFeedCommentId);
            $sFeedCommentId = trim($sFeedCommentId, ",");

            $aFeedComments = $oDatabase->select('ffc.*')
                ->from(Phpfox::getT('fevent_feed_comment'), 'ffc')
                 ->where('ffc.feed_comment_id IN ( ' . $sFeedCommentId . ' ) ')
                 ->execute('getSlaveRows');
        }

        foreach ($aFeedComments as $keyaFeedComments => $valueaFeedComments) {
            $aData = $oDatabase->select('cmt.*, ctt.text, ctt.text_parsed')
                ->from(Phpfox::getT('comment'), 'cmt')
                ->join(Phpfox::getT('comment_text'), 'ctt', 'ctt.comment_id = cmt.comment_id')
                 ->where('cmt.type_id = \'fevent\' AND cmt.item_id = ' . (int)$valueaFeedComments['feed_comment_id'])
                 ->execute('getSlaveRows');

             $aFeedComments[$keyaFeedComments]['comment'] = $aData;
        }

        // fevent_image
        $aImages = $oDatabase->select('fim.*')
            ->from(Phpfox::getT('fevent_image'), 'fim')
             ->where('fim.event_id = ' . (int) $aEvent['event_id'])
             ->execute('getSlaveRows');

        // fevent_invite
        $aInvites = $oDatabase->select('fin.*')
            ->from(Phpfox::getT('fevent_invite'), 'fin')
             ->where('fin.event_id = ' . (int) $aEvent['event_id'])
             ->execute('getSlaveRows');

        $aInstances = array();

        $iStartTime = (int)$aEvent['org_start_time'];
        $month = (int)Phpfox::getTime('n', $iStartTime, false);
        $day = (int)Phpfox::getTime('j', $iStartTime, false);
        $year = (int)Phpfox::getTime('Y', $iStartTime, false);
        $start_hour = (int)Phpfox::getTime('H', $iStartTime, false);
        $start_minute = (int)Phpfox::getTime('i', $iStartTime, false);
        $start_second = (int)Phpfox::getTime('s', $iStartTime, false);

        $iDuration = (int)$aEvent['end_time'] - (int)$aEvent['start_time'];
        $iEndTime = $iStartTime + $iDuration;

        if($iStartTime < $curTime && $iEndTime < $curTime){
            $type = 'past';
        } else if($iStartTime > $curTime){
            $type = 'upcoming';
        } else {
            $type = 'ongoing';
        }
        $aInstances[] = array(
            'start_time' => $iStartTime, 
            'end_time' => $iEndTime, 
            'type' => $type, 
            'existing_event_id' => 0, 
        );

        $iTimeRepeat = (int)$aEvent['timerepeat'];
        $len = 50;
        for($idx = 0; $idx < $len; $idx ++){
            if($aEvent['isrepeat'] == 0 ){
                //  daily
                $iStartTime = $iStartTime + (1 * 24 * 60 * 60);
            } else if($aEvent['isrepeat'] == 1 ){
                //  weekly
                $iStartTime = $iStartTime + (7 * 24 * 60 * 60);
            } else if($aEvent['isrepeat'] == 2 ){
                // monthly
                $next_start_time_obj = ynfe_install306_getSameDayInNextMonth($day, $month, $year);
                $month = $next_start_time_obj['month'];
                $year = $next_start_time_obj['year'];
                $iStartTime = Phpfox::getLib('date')->mktime($start_hour
                            , $start_minute
                            , $start_second
                            , $next_start_time_obj['month']
                            , $next_start_time_obj['day']
                            , $next_start_time_obj['year']
                            );
                if($day != $next_start_time_obj['day']){
                    continue;
                }
            }

            if($iStartTime > $iTimeRepeat){
                break;
            }

            $iEndTime = $iStartTime + $iDuration;
            $type = '';
            if($iStartTime < $curTime && $iEndTime < $curTime){
                $type = 'past';
            } else if($iStartTime > $curTime){
                $type = 'upcoming';
            } else {
                $type = 'ongoing';
            }
            $aInstances[] = array(
                'start_time' => $iStartTime, 
                'end_time' => $iEndTime, 
                'type' => $type, 
                'existing_event_id' => 0, 
            );
        }

        $find_existing_event_id = false;
        foreach ($aInstances as $keyaInstances => $valueaInstances) {
            if('ongoing' == $valueaInstances['type'] || 'upcoming' == $valueaInstances['type']){
                $find_existing_event_id = true;
                $aInstances[$keyaInstances]['existing_event_id'] = (int) $aEvent['event_id'];
                break; 
            }
        }

        if($find_existing_event_id === false && count($aInstances) > 0){
            $aInstances[0]['existing_event_id'] = (int) $aEvent['event_id'];
        }

        $org_event_id = 0;
        foreach ($aInstances as $keyaInstances => $valueaInstances) {
            $fevent_table = $aEvent;
            unset($fevent_table['event_id']);

            if($valueaInstances['existing_event_id'] == $aEvent['event_id']){
                if($org_event_id == 0){
                    $org_event_id = (int)$aEvent['event_id'];
                }
                // update start/end time 
                // update org_event_id
                $oDatabase->update(Phpfox::getT('fevent'), array(
                    'start_time' => (int) $valueaInstances['start_time'], 
                    'end_time' => (int) $valueaInstances['end_time'], 
                    'org_event_id' => $org_event_id, 
                ), 'event_id = ' . (int) $valueaInstances['existing_event_id']);

                // keep attendees, keep all data --> do nothing
            } else {
                $fevent_table['start_time'] = (int) $valueaInstances['start_time'];
                $fevent_table['end_time'] = (int) $valueaInstances['end_time'];
                // $fevent_table['org_event_id'] = (int) $valueaInstances['end_time'];
                switch ($valueaInstances['type']) {
                    case 'past':
                        unset($fevent_table['description']);
                        unset($fevent_table['description_parsed']);
                        unset($fevent_table['is_featured']);
                        unset($fevent_table['is_sponsor']);

                        $fevent_table['time_stamp'] = (int) $fevent_table['time_stamp'] - ( count($aInstances) - (int)$keyaInstances );

                        // create new instance 
                        $iId = $oDatabase->insert(Phpfox::getT('fevent'), $fevent_table);

                        if($org_event_id == 0){
                            $org_event_id = (int)$iId;
                        }
                        // update org_event_id
                        $oDatabase->update(Phpfox::getT('fevent'), array(
                            'org_event_id' => $org_event_id, 
                        ), 'event_id = ' . (int) $iId);

                        $oDatabase->insert(Phpfox::getT('fevent_text'), array(
                                'event_id' => $iId,
                                'description' => $aEvent['description'],
                                'description_parsed' => $aEvent['description_parsed'],
                            )
                        );

                        foreach ($aCategories as $objaCategories)
                        {
                            $objaCategories['event_id'] = $iId;
                            $oDatabase->insert(Phpfox::getT('fevent_category_data'), $objaCategories);
                        }       

                        // keep attendees, keep all data
                        foreach ($aAdmins as $objaAdmins)
                        {
                            $objaAdmins['event_id'] = $iId;
                            $oDatabase->insert(Phpfox::getT('fevent_admin'), $objaAdmins);
                        }       

                        foreach ($aCustomValues as $objaCustomValues)
                        {
                            $objaCustomValues['event_id'] = $iId;
                            $oDatabase->insert(Phpfox::getT('fevent_custom_value'), $objaCustomValues);
                        }       
                        
                        foreach ($aFeeds as $objaFeeds)
                        {
                            $objaFeeds['parent_user_id'] = $iId;
                            unset($objaFeeds['feed_id']);
                            if($objaFeeds['type_id'] == 'fevent_comment'){
                                foreach ($aFeedComments as $keyaFeedComments => $valueaFeedComments) {
                                    if($valueaFeedComments['feed_comment_id'] == $objaFeeds['item_id']){
                                        // fevent_feed_comment
                                        $fevent_feed_comment = $valueaFeedComments;
                                        unset($fevent_feed_comment['feed_comment_id']);
                                        unset($fevent_feed_comment['comment']);
                                        $feed_comment_id = $oDatabase->insert(Phpfox::getT('fevent_feed_comment'), $fevent_feed_comment);

                                        
                                        $objaFeeds['item_id'] = $feed_comment_id;
                                        $oDatabase->insert(Phpfox::getT('fevent_feed'), $objaFeeds);

                                        foreach ($valueaFeedComments['comment'] as $keycomment => $valuecomment) {
                                            // comment
                                            $comment = $valuecomment;
                                            unset($comment['comment_id']);
                                            unset($comment['text']);
                                            unset($comment['text_parsed']);
                                            $comment['item_id'] = $feed_comment_id;
                                            $comment_id = $oDatabase->insert(Phpfox::getT('comment'), $comment);

                                            // comment_text
                                            $comment_text = array(
                                                'comment_id' => $comment_id, 
                                                'text' => $valuecomment['text'], 
                                                'text_parsed' => $valuecomment['text_parsed'], 
                                            );
                                            $comment_id = $oDatabase->insert(Phpfox::getT('comment_text'), $comment_text);
                                            
                                        }
                                        break;
                                    }
                                }

                            } else {
                                $oDatabase->insert(Phpfox::getT('fevent_feed'), $objaFeeds);
                            }
                        }

                        foreach ($aImages as $objaImages)
                        {
                            $objaImages['event_id'] = $iId;
                            unset($objaImages['image_id']);
                            $oDatabase->insert(Phpfox::getT('fevent_image'), $objaImages);
                        }       

                        foreach ($aInvites as $objaInvites)
                        {
                            $objaInvites['event_id'] = $iId;
                            unset($objaInvites['invite_id']);
                            $oDatabase->insert(Phpfox::getT('fevent_invite'), $objaInvites);
                        }       

                        break;                      

                    case 'ongoing':
                        // do nothing because it is existing event 
                        break;                      

                    case 'upcoming':
                        unset($fevent_table['description']);
                        unset($fevent_table['description_parsed']);
                        unset($fevent_table['is_featured']);
                        unset($fevent_table['is_sponsor']);
                        unset($fevent_table['image_path']);
                        unset($fevent_table['server_id']);

                        // create new instance 
                        $iId = $oDatabase->insert(Phpfox::getT('fevent'), $fevent_table);
                        if($org_event_id == 0){
                            $org_event_id = (int)$iId;
                        }
                        // update org_event_id
                        $oDatabase->update(Phpfox::getT('fevent'), array(
                            'org_event_id' => $org_event_id, 
                        ), 'event_id = ' . (int) $iId);

                        $oDatabase->insert(Phpfox::getT('fevent_text'), array(
                                'event_id' => $iId,
                                'description' => $aEvent['description'],
                                'description_parsed' => $aEvent['description_parsed'],
                            )
                        );

                        // keep owner ONLY
                        foreach ($aInvites as $objaInvites)
                        {
                            if($objaInvites['user_id'] == $fevent_table['user_id']){
                                $objaInvites['event_id'] = $iId;
                                unset($objaInvites['invite_id']);
                                $oDatabase->insert(Phpfox::getT('fevent_invite'), $objaInvites);
                                break;
                            }
                        }       

                        break;                      
                }
            }
        }
    }
}

function ynfe_install306()
{
    $oDatabase = Phpfox::getLib('database');
    $is_migrate_data = false;

    // OLD DATABASE
    //      . start_time    : update by time
    //      . org_start_time : store orginal start time which inputing by user
    //      . end_time      : update by time
    //      . org_end_time  : store orginal end time which inputing by user or system calculate
    //      . ispreat       : -1, 0, 1, 2
    //      . timerepeat    : repeat until 
    //      . duration_days 
    //      . duration_hours
    //      . is_delete_user_past_repeat_event
    //      . is_update_warning
    //      
    // NEW DATABASE 
    //      . start_time    : NOT update if it is repeat event, update time when edting 
    //      . org_start_time : same old logic 
    //      . end_time      : NOT update if it is repeat event, update time when edting 
    //      . org_end_time  : same old logic 
    //      . ispreat       : -1, 0, 1, 2
    //      . timerepeat    : repeat until 
    //      . duration_days : specific 0
    //      . duration_hours : specific 0
    //      . is_delete_user_past_repeat_event : specific 0
    //      . is_update_warning : specific 0
    //      
    //      . org_event_id : first event which each instance belongs to it
    //      . after_number_event : number of instance has been created after create first event 
    if (!$oDatabase->isField(Phpfox::getT('fevent'), 'org_event_id'))
    {
        $oDatabase->query("ALTER TABLE  `".Phpfox::getT('fevent')."` ADD  `org_event_id` INT(10) NULL DEFAULT  '0' AFTER  `event_id`");
    }

    if (!$oDatabase->isField(Phpfox::getT('fevent'), 'after_number_event'))
    {
        $is_migrate_data = true;
        $oDatabase->query("ALTER TABLE  `".Phpfox::getT('fevent')."` ADD  `after_number_event` INT(10) NULL DEFAULT '0' AFTER  `timerepeat` ");
    }

    if($is_migrate_data == true){
        ynfe_install306_migrate();
    }


    $aLoadTinyMce = array();
        
    if(!in_array('fevent.add|#description', $aLoadTinyMce))
    {
        $aLoadTinyMce[] = 'fevent.add|#description';
        $aValue['value_actual'] = serialize(str_replace('array ', 'array', var_export($aLoadTinyMce, true)) . ';');
        
        Phpfox::getLib('database')->update(Phpfox::getT('setting'), $aValue,'var_name = "tinymce_load_on_pages"');
    }

    if(!in_array('fevent.edit|#description', $aLoadTinyMce))
    {
        $aLoadTinyMce[] = 'fevent.edit|#description';
        $aValue['value_actual'] = serialize(str_replace('array ', 'array', var_export($aLoadTinyMce, true)) . ';');
        
        Phpfox::getLib('database')->update(Phpfox::getT('setting'), $aValue,'var_name = "tinymce_load_on_pages"');
    }

    /*delete un-necessary setting*/
    $oDb = Phpfox::getLib('phpfox.database');
    
    $oDb->query("DELETE FROM `". Phpfox::getT('setting') ."`
        WHERE var_name IN ('fevent_event_item_width','fevent_event_gap','fevent_pages_item_width','fevent_pages_gap','fevent_profile_item_width','fevent_profile_gap') 
        and product_id = 'younetevent';");

    /*create new table for subscribe event*/
    $oDatabase->query("
    CREATE TABLE IF NOT EXISTS `". Phpfox::getT('fevent_subscribe_email') ."` (
            `subscribe_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `email` varchar(255) NOT NULL,
            `data` text DEFAULT NULL,
            PRIMARY KEY (`subscribe_id`) 
        )  AUTO_INCREMENT=1 ;
    ");

    $oDatabase->query("
        CREATE TABLE IF NOT EXISTS `". Phpfox::getT('fevent_cronlog') ."` (
          `cronlog_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `type` enum('default') DEFAULT 'default',
          `timestamp` int(10) unsigned NOT NULL,
          PRIMARY KEY (`cronlog_id`)
        ) AUTO_INCREMENT=1;
    ");


}

ynfe_install306();

?>