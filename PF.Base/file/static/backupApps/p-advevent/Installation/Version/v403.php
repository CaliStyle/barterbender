<?php
namespace Apps\P_AdvEvent\Installation\Version;

use Phpfox;

class v403
{
    private $_db;

    public function __construct()
    {
        $this->_db = db();
    }

    public function process()
    {
        $this->insertAndUpdateDefault();
        $this->migrateDataForRepeatEvent();
        $this->processSettings();
        $this->processBlocks();
        $this->removeOldControllers();
        $this->replaceOldBlocks();
        $this->updateModuleToApp();
        $this->addCron();
        $this->processComponentBlocks();
        $this->migrateSubscribeTable();
    }

    private function migrateSubscribeTable()
    {
        $subscribeTable = Phpfox::getT('fevent_subscribe_email');
        $subscribers = db()->select('subscribe_id, email')
                        ->from(Phpfox::getT('fevent_subscribe_email'))
                        ->where('code IS NULL OR code = ""')
                        ->execute('getSlaveRows');
        if(!empty($subscribers)) {
            foreach($subscribers as $subscriber) {
                db()->update($subscribeTable, ['code' => md5($subscriber['email']. uniqid())], 'subscribe_id = '. (int)$subscriber['subscribe_id']);
            }
        }
    }

    private function insertAndUpdateDefault()
    {
        if($this->_db->tableExists(Phpfox::getT('fevent_category'))) {
            $count = $this->_db->select('COUNT(*)')
                        ->from(Phpfox::getT('fevent_category'))
                        ->execute('getSlaveField');
            if(empty($count)) {
                $this->_db->query("INSERT IGNORE INTO `" . Phpfox::getT('fevent_category') . "` VALUES 
                ('1', '0', '1', 'Arts', null, '0', '0', '1'),
                ('2', '0', '1', 'Party', null, '0', '0', '2'),
                ('3', '0', '1', 'Comedy', null, '0', '0', '3'),
                ('4', '0', '1', 'Sports', null, '0', '0', '4'),
                ('5', '0', '1', 'Music', null, '0', '0', '5'),
                ('6', '0', '1', 'TV', null, '0', '0', '6'),
                ('7', '0', '1', 'Movies', null, '0', '0', '7'),
                ('8', '0', '1', 'Other', null, '0', '0', '8');");
            }

        }
        if(!$this->_db->isField(Phpfox::getT('user_activity'),'activity_fevent'))
        {
            $this->_db->query("ALTER TABLE `" . Phpfox::getT('user_activity') . "`ADD `activity_fevent` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `activity_event` ");
        }
        if(!$this->_db->isField(Phpfox::getT('user_field'),'total_fevent'))
        {
            $this->_db->query("ALTER TABLE `" . Phpfox::getT('user_field') . "`ADD `total_fevent` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `total_event` ");
        }
        if(!$this->_db->isField(Phpfox::getT('user_space'),'space_fevent'))
        {
            $this->_db->query("ALTER TABLE `" . Phpfox::getT('user_space') . "`ADD `space_fevent` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `space_event` ");
        }
        if(!$this->_db->isField(Phpfox::getT('user_count'),'fevent_invite'))
        {
            $this->_db->query("ALTER TABLE `" . Phpfox::getT('user_count') . "`ADD `fevent_invite` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `event_invite` ");
        }
        if (Phpfox::isModule('socialpublisherss'))
        {
            $aRow = $this->_db->select('*')
                ->from(Phpfox::getT('socialpublishers_modules'))
                ->where('product_id = "younetevent" AND module_id = "fevent"')
                ->execute('getRow');

            if(empty($aRow))
            {
                $this->_db->insert(Phpfox::getT('socialpublishers_modules'),
                    [
                        'product_id' => 'younetevent',
                        'module_id' => 'fevent',
                        'title' => 'fevent.publishers_advancedevent',
                        'is_active' => 1,
                        'facebook' => 1,
                        'twitter' => 1,
                        'linkedin' => 1,
                    ]
                );
            }
        }

    }

    private function migrateDataForRepeatEvent()
    {
        $repeatEvents = $this->_db->select('e.*, txt.description, txt.description_parsed')
            ->from(Phpfox::getT('fevent'), 'e')
            ->leftJoin(Phpfox::getT('fevent_text'), 'txt', 'txt.event_id = e.event_id')
            ->where(' `isrepeat` > -1 ')
            ->group('e.event_id')
            ->execute('getSlaveRows');
        if(!empty($repeatEvents)) {
            $isNotificationActive = Phpfox::isModule('notification');
            $curTime = PHPFOX_TIME;
            foreach($repeatEvents as $event) {
                $userID = $event['user_id'];
                $this->_db->query("UPDATE `".Phpfox::getT('fevent')."` 
                  SET `isrepeat` = -1
                        , `end_time` = `timerepeat` 
                        , `is_update_warning` = 1 
                  WHERE `event_id` = " . $event['event_id'] . " ;");

                if($isNotificationActive) {
                    if ($userID == Phpfox::getUserId() || intval($userID) == intval(Phpfox::getUserId()))
                    {
                        $userID = 'getUserId';
                    }
                    Phpfox::getService('notification.process')->add('fevent_repeattonormalwarning', -1, $userID);
                }

                // fevent_category_data
                $aCategories = $this->_db->select('fcd.*')
                    ->from(Phpfox::getT('fevent_category_data'), 'fcd')
                    ->where('fcd.event_id = ' . (int) $event['event_id'])
                    ->execute('getSlaveRows');

                // fevent_admin
                $aAdmins = $this->_db->select('fad.*')
                    ->from(Phpfox::getT('fevent_admin'), 'fad')
                    ->where('fad.event_id = ' . (int) $event['event_id'])
                    ->execute('getSlaveRows');

                // fevent_custom_value
                $aCustomValues = $this->_db->select('fcv.*')
                    ->from(Phpfox::getT('fevent_custom_value'), 'fcv')
                    ->where('fcv.event_id = ' . (int) $event['event_id'])
                    ->execute('getSlaveRows');

                // fevent_feed
                $aFeeds = $this->_db->select('ffd.*')
                    ->from(Phpfox::getT('fevent_feed'), 'ffd')
                    ->where('ffd.parent_user_id = ' . (int) $event['event_id'])
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

                    $aFeedComments = $this->_db->select('ffc.*')
                        ->from(Phpfox::getT('fevent_feed_comment'), 'ffc')
                        ->where('ffc.feed_comment_id IN ( ' . $sFeedCommentId . ' ) ')
                        ->execute('getSlaveRows');
                }

                foreach ($aFeedComments as $keyaFeedComments => $valueaFeedComments) {
                    $aData = $this->_db->select('cmt.*, ctt.text, ctt.text_parsed')
                        ->from(Phpfox::getT('comment'), 'cmt')
                        ->join(Phpfox::getT('comment_text'), 'ctt', 'ctt.comment_id = cmt.comment_id')
                        ->where('cmt.type_id = \'fevent\' AND cmt.item_id = ' . (int)$valueaFeedComments['feed_comment_id'])
                        ->execute('getSlaveRows');

                    $aFeedComments[$keyaFeedComments]['comment'] = $aData;
                }

                // fevent_image
                $aImages = $this->_db->select('fim.*')
                    ->from(Phpfox::getT('fevent_image'), 'fim')
                    ->where('fim.event_id = ' . (int) $event['event_id'])
                    ->execute('getSlaveRows');

                // fevent_invite
                $aInvites = $this->_db->select('fin.*')
                    ->from(Phpfox::getT('fevent_invite'), 'fin')
                    ->where('fin.event_id = ' . (int) $event['event_id'])
                    ->execute('getSlaveRows');

                $aInstances = array();

                $iStartTime = (int)$event['org_start_time'];
                $month = (int)Phpfox::getTime('n', $iStartTime, false);
                $day = (int)Phpfox::getTime('j', $iStartTime, false);
                $year = (int)Phpfox::getTime('Y', $iStartTime, false);
                $start_hour = (int)Phpfox::getTime('H', $iStartTime, false);
                $start_minute = (int)Phpfox::getTime('i', $iStartTime, false);
                $start_second = (int)Phpfox::getTime('s', $iStartTime, false);

                $iDuration = (int)$event['end_time'] - (int)$event['start_time'];
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

                $iTimeRepeat = (int)$event['timerepeat'];
                $len = 50;
                for($idx = 0; $idx < $len; $idx ++){
                    if($event['isrepeat'] == 0 ){
                        //  daily
                        $iStartTime = $iStartTime + (1 * 24 * 60 * 60);
                    } else if($event['isrepeat'] == 1 ){
                        //  weekly
                        $iStartTime = $iStartTime + (7 * 24 * 60 * 60);
                    } else if($event['isrepeat'] == 2 ){
                        // monthly
                        $next_start_time_obj = $this->_getSameDayInNextMonth($day, $month, $year);
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
                        $aInstances[$keyaInstances]['existing_event_id'] = (int) $event['event_id'];
                        break;
                    }
                }

                if($find_existing_event_id === false && count($aInstances) > 0){
                    $aInstances[0]['existing_event_id'] = (int) $event['event_id'];
                }

                $org_event_id = 0;
                foreach ($aInstances as $keyaInstances => $valueaInstances) {
                    $fevent_table = $event;
                    unset($fevent_table['event_id']);

                    if($valueaInstances['existing_event_id'] == $event['event_id']){
                        if($org_event_id == 0){
                            $org_event_id = (int)$event['event_id'];
                        }
                        // update start/end time
                        // update org_event_id
                        $this->_db->update(Phpfox::getT('fevent'), array(
                            'start_time' => (int) $valueaInstances['start_time'],
                            'end_time' => (int) $valueaInstances['end_time'],
                            'org_event_id' => $org_event_id,
                        ), 'event_id = ' . (int) $valueaInstances['existing_event_id']);

                        // keep attendees, keep all data --> do nothing
                    } else {
                        $fevent_table['start_time'] = (int) $valueaInstances['start_time'];
                        $fevent_table['end_time'] = (int) $valueaInstances['end_time'];
                        switch ($valueaInstances['type']) {
                            case 'past':
                                unset($fevent_table['description']);
                                unset($fevent_table['description_parsed']);
                                unset($fevent_table['is_featured']);
                                unset($fevent_table['is_sponsor']);

                                $fevent_table['time_stamp'] = (int) $fevent_table['time_stamp'] - ( count($aInstances) - (int)$keyaInstances );

                                // create new instance
                                $iId = $this->_db->insert(Phpfox::getT('fevent'), $fevent_table);

                                if($org_event_id == 0){
                                    $org_event_id = (int)$iId;
                                }
                                // update org_event_id
                                $this->_db->update(Phpfox::getT('fevent'), array(
                                    'org_event_id' => $org_event_id,
                                ), 'event_id = ' . (int) $iId);

                                $this->_db->insert(Phpfox::getT('fevent_text'), array(
                                        'event_id' => $iId,
                                        'description' => $event['description'],
                                        'description_parsed' => $event['description_parsed'],
                                    )
                                );

                                foreach ($aCategories as $objaCategories)
                                {
                                    $objaCategories['event_id'] = $iId;
                                    $this->_db->insert(Phpfox::getT('fevent_category_data'), $objaCategories);
                                }

                                // keep attendees, keep all data
                                foreach ($aAdmins as $objaAdmins)
                                {
                                    $objaAdmins['event_id'] = $iId;
                                    $this->_db->insert(Phpfox::getT('fevent_admin'), $objaAdmins);
                                }

                                foreach ($aCustomValues as $objaCustomValues)
                                {
                                    $objaCustomValues['event_id'] = $iId;
                                    $this->_db->insert(Phpfox::getT('fevent_custom_value'), $objaCustomValues);
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
                                                $feed_comment_id = $this->_db->insert(Phpfox::getT('fevent_feed_comment'), $fevent_feed_comment);


                                                $objaFeeds['item_id'] = $feed_comment_id;
                                                $this->_db->insert(Phpfox::getT('fevent_feed'), $objaFeeds);

                                                foreach ($valueaFeedComments['comment'] as $keycomment => $valuecomment) {
                                                    // comment
                                                    $comment = $valuecomment;
                                                    unset($comment['comment_id']);
                                                    unset($comment['text']);
                                                    unset($comment['text_parsed']);
                                                    $comment['item_id'] = $feed_comment_id;
                                                    $comment_id = $this->_db->insert(Phpfox::getT('comment'), $comment);

                                                    // comment_text
                                                    $comment_text = array(
                                                        'comment_id' => $comment_id,
                                                        'text' => $valuecomment['text'],
                                                        'text_parsed' => $valuecomment['text_parsed'],
                                                    );
                                                    $comment_id = $this->_db->insert(Phpfox::getT('comment_text'), $comment_text);

                                                }
                                                break;
                                            }
                                        }

                                    } else {
                                        $this->_db->insert(Phpfox::getT('fevent_feed'), $objaFeeds);
                                    }
                                }

                                foreach ($aImages as $objaImages)
                                {
                                    $objaImages['event_id'] = $iId;
                                    unset($objaImages['image_id']);
                                    $this->_db->insert(Phpfox::getT('fevent_image'), $objaImages);
                                }

                                foreach ($aInvites as $objaInvites)
                                {
                                    $objaInvites['event_id'] = $iId;
                                    unset($objaInvites['invite_id']);
                                    $this->_db->insert(Phpfox::getT('fevent_invite'), $objaInvites);
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
                                $iId = $this->_db->insert(Phpfox::getT('fevent'), $fevent_table);
                                if($org_event_id == 0){
                                    $org_event_id = (int)$iId;
                                }
                                // update org_event_id
                                $this->_db->update(Phpfox::getT('fevent'), array(
                                    'org_event_id' => $org_event_id,
                                ), 'event_id = ' . (int) $iId);

                                $this->_db->insert(Phpfox::getT('fevent_text'), array(
                                        'event_id' => $iId,
                                        'description' => $event['description'],
                                        'description_parsed' => $event['description_parsed'],
                                    )
                                );

                                // keep owner ONLY
                                foreach ($aInvites as $objaInvites)
                                {
                                    if($objaInvites['user_id'] == $fevent_table['user_id']){
                                        $objaInvites['event_id'] = $iId;
                                        unset($objaInvites['invite_id']);
                                        $this->_db->insert(Phpfox::getT('fevent_invite'), $objaInvites);
                                        break;
                                    }
                                }

                                break;
                        }
                    }
                }
            }
        }
    }

    private function processSettings()
    {
        $varNames = [
            'fevent_event_item_width',
            'fevent_event_gap',
            'fevent_pages_item_width',
            'fevent_pages_gap',
            'fevent_profile_item_width',
            'fevent_profile_gap',
            'google_api_keys_location',
            'fevent_number_of_event_most_liked_viewed_discussed_block',
            'fevent_number_of_event_upcoming_past_block_right_side',
            'fevent_view_time_stamp_profile',
            'fevent_start_week',
            'fevent_number_of_event_upcoming_ongoing_block_home_page',
            'fevent_browse_time_stamp',
            'fevent_basic_information_time_short',
            'fevent_basic_information_time'
        ];
        $this->_db->delete(Phpfox::getT('setting'),"var_name IN ('" . implode("','", $varNames) ."') AND (`product_id` = 'younetevent' OR `module_id` = 'fevent')");

        $userGroupSettingVarName = [
            'can_use_editor_on_event',
            'can_view_gmap'
        ];
        $this->_db->delete(Phpfox::getT('user_group_setting'),"`name` IN ('" . implode("','", $userGroupSettingVarName) ."') AND ((`product_id` = 'younet_advevent4' OR `product_id` = 'P_AdvEvent') AND `module_id` = 'fevent')");
    }

    private function processBlocks()
    {
        $deleteBlocks = [
            ['component' => 'applyforrepeatevent', 'product_id' => 'younet_advevent4'],
            ['component' => 'rsvp', 'product_id' => 'younet_advevent4'],
            ['component' => 'image', 'product_id' => 'younet_advevent4'],
            ['component' => 'upcoming', 'product_id' => 'younet_advevent4'],
            ['component' => 'awaitingreply', 'product_id' => 'younet_advevent4']
        ];
        foreach($deleteBlocks as $deleteBlock) {
            $this->_db->delete(':block', $deleteBlock);
        }

        $this->_db->update(':block', ['ordering' => 10], ['module_id' => 'fevent', 'm_connection' => 'fevent.view', 'component' => 'category', 'location' => 1, 'ordering' => 7]);
    }

    private function processComponentBlocks()
    {
        $this->_db->delete(':block', 'module_id = "ynfeed" AND component = "display" AND m_connection = "fevent.view"');

        $newComponentBlocks = [
            [
                'condition' => ['component' => 'display', 'module_id' => 'feed', 'm_connection' => 'fevent.view'],
                'insert_data' => [
                    'Activity Feed', '0', 'fevent.view', 'feed', 'phpfox', 'display', '4', '1', '1', null, '0', null, null,
                ]
            ],
            [
                'condition' => ['component' => 'event-list', 'module_id' => 'fevent', 'm_connection' => 'fevent.view'],
                'insert_data' => [
                    'Related Events', '0', 'fevent.view', 'fevent', 'phpfox', 'event-list', '3', '1', '2', null, '0', null, json_encode(
                        [
                            'data_source' => 'related',
                            'limit' => '3',
                            'display_view_more' => '0',
                            'is_slider' => '0',
                            'view_modes' =>
                                [
                                    0 => 'grid',
                                ],
                        ]
                    ),
                ]
            ]
        ];

        $InsertData = [];
        foreach ($newComponentBlocks as $value) {
            $count = $this->_db->select('COUNT(block_id)')
                            ->from(':block')
                            ->where($value['condition'])
                            ->execute('getSlaveField');
            if ($count) {
                continue;
            }
            $InsertData[] = $value['insert_data'];
        }
        if (count($InsertData)) {
            db()->multiInsert(Phpfox::getT('block'), [
                'title',
                'type_id',
                'm_connection',
                'module_id',
                'product_id',
                'component',
                'location',
                'is_active',
                'ordering',
                'disallow_access',
                'can_move',
                'version_id',
                'params'
            ], $InsertData);
        }
    }

    private function updateModuleToApp()
    {
        db()->delete(':product', '`product_id` = "younet_advevent4"');

        // update module is app
        db()->update(':module', ['product_id' => 'phpfox', 'phrase_var_name' => 'module_apps', 'is_active' => 1], ['module_id' => 'fevent']);
    }

    private function removeOldControllers()
    {
        //TODO: implement
    }

    private function replaceOldBlocks()
    {
        // replace blocks of previous version
        $aReplacedBlocks = array(
            'attending' => array(),
            'awatingreply' => array(),
            'info' => array(),
            'display' => array(),
            'extramenuinview' => array(),
            'image' => array(),
            'invite' => array(),
            'map' => array(),
            'maybeattending' => array(),
            'menu' => array(),
            'filter' => array(),
            'more-recurrent-event' => array(),
            'notattending' => array(),
            'parent' => array(),
            'pic' => array(),
            'profile' => array(),
            'status-time-event' => array(),
            'viewmore-events' => array(),

            'homepage.featured' => array(
                'new_component' => 'event-list',
                'old_params' => array(
                    'data_source' => 'featured',
                    'display_view_more' => '0',
                    'limit' => '6',
                    'is_slider' => '1',
                )
            ),
            'homepage.ongoing' => array(
                'new_component' => 'event-list',
                'old_params' => array(
                    'data_source' => 'ongoing',
                    'display_view_more' => '1',
                    'limit' => '3',
                    'is_slider' => '0',
                )
            ),
            'homepage.upcoming' => array(
                'new_component' => 'event-list',
                'old_params' => array(
                    'data_source' => 'upcoming',
                    'display_view_more' => '1',
                    'limit' => '3',
                    'is_slider' => '0',
                )
            ),
            'past' => array(
                'new_component' => 'event-list',
                'old_params' => array(
                    'data_source' => 'past',
                    'display_view_more' => '1',
                    'limit' => '3',
                    'is_slider' => '0',
                )
            ),
            'mostliked' => array(
                'new_component' => 'event-list',
                'old_params' => array(
                    'data_source' => 'most-liked',
                    'display_view_more' => '1',
                    'limit' => '3',
                    'is_slider' => '0',
                )
            ),
            'mostviewed' => array(
                'new_component' => 'event-list',
                'old_params' => array(
                    'data_source' => 'most-viewed',
                    'display_view_more' => '1',
                    'limit' => '3',
                    'is_slider' => '0',
                )
            ),
            'mostdiscussed' => array(
                'new_component' => 'event-list',
                'old_params' => array(
                    'data_source' => 'most-discussed',
                    'display_view_more' => '1',
                    'limit' => '3',
                    'is_slider' => '0',
                )
            ),
            'sponsored' => array(
                'new_component' => 'event-list',
                'old_params' => array(
                    'data_source' => 'sponsored',
                    'display_view_more' => '1',
                    'limit' => '1',
                    'is_slider' => '0',
                )
            ),
        );

        $aOldBlocks = db()->select('*')
            ->from(':block')
            ->where('module_id = "fevent"')
            ->executeRows();

        foreach ($aOldBlocks as $aOldBlock) {
            $sComponent = $aOldBlock['component'];
            if (isset($aReplacedBlocks[$sComponent])) {
                if (empty($aReplacedBlocks[$sComponent])) {
                    db()->delete(':block', '`module_id` = "fevent" AND `component` = "' . $sComponent . '"');
                } else {
                    if (!empty($aOldBlock['params'])) {
                        $aOldParams = json_decode($aOldBlock['params'], true);
                        $aParams = array_merge($aReplacedBlocks[$sComponent]['old_params'], $aOldParams);
                    } else {
                        $aParams = array_merge($aReplacedBlocks[$sComponent]['old_params']);
                    }

                    db()->update(':block',
                        array(
                            'component' => $aReplacedBlocks[$sComponent]['new_component'],
                            'params' => json_encode($aParams)
                        ),
                        array(
                            'block_id' => $aOldBlock['block_id']
                        )
                    );
                }
            }
        }

        foreach ($aReplacedBlocks as $key => $aReplacedBlock) {
            db()->delete(':component', '`module_id` = "fevent" AND `is_controller` = 0 AND `component` = "' . $key . '"');
        }
    }

    private function _getSameDayInNextMonth($day, $month, $year) {
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
        return ['day' => $day, 'month' => $month, 'year' => $year];
    }

    private function addCron() {
        $cron = db()->select('*')->from(':cron')->where(['module_id' => 'fevent'])->executeRow();
        if (!$cron) {
            db()->insert(':cron', [
                'module_id' => 'fevent',
                'type_id' => 1,
                'every' => 5,
                'is_active' => 1,
                'php_code' => '(new \Apps\P_AdvEvent\Service\FEvent)->executeCron();'
            ]);
        }
    }
}