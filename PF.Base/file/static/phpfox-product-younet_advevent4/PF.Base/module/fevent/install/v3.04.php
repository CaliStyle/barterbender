<?php

function ynfe_install304()
{
    Phpfox::getLib('cache')->remove();
    Phpfox::getLib('template.cache')->remove();
    Phpfox::getLib('cache')->removeStatic();
    Phpfox_Plugin::set();    
    
    $oDatabase = Phpfox::getLib('database');

    //  update url on menu
    $menuObj = $oDatabase->select("ppi.*")
                    ->from(Phpfox::getT('menu'), 'ppi')
                    ->where(' `m_connection` LIKE  \'main\' AND  `module_id` LIKE  \'fevent\' AND  `product_id` LIKE  \'younetevent\' AND  `var_name` LIKE  \'menu_fevent_events\' ')
                    ->execute('getSlaveRow');

    if(isset($menuObj) && isset($menuObj['menu_id'])){
        $oDatabase->query("UPDATE `".Phpfox::getT('menu')."` 
          SET `url_value` = 'fevent' WHERE menu_id = " . $menuObj['menu_id'] . " ;");
    }

    //  inactive Upcoming block
    $upcomingObj = $oDatabase->select("ppi.*")
                    ->from(Phpfox::getT('block'), 'ppi')
                    ->where(' `m_connection` LIKE  \'fevent.index\' AND  `module_id` LIKE  \'fevent\' AND  `product_id` LIKE  \'younetevent\' AND  `component` LIKE  \'upcoming\' ')
                    ->execute('getSlaveRow');
    if(isset($upcomingObj) && isset($upcomingObj['block_id'])){
        $oDatabase->query("UPDATE `".Phpfox::getT('block')."` 
          SET `is_active` = 0 WHERE block_id = " . $upcomingObj['block_id'] . " ;");
    }

    //  create cron database
    $oDatabase -> query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT('fevent_cron')."` (
          `cron_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `type_cron` varchar(20) NOT NULL,
          `time_stamp` int(10) unsigned NOT NULL,
          PRIMARY KEY (`cron_id`)
    );");

    //  add new fields in fevent table
    if (!$oDatabase->isField(Phpfox::getT('fevent'), 'org_start_time'))
    {
        $oDatabase->query("ALTER TABLE  `".Phpfox::getT('fevent')."` ADD  `org_start_time` INT(10) NULL AFTER  `start_time`");
    }
    if (!$oDatabase->isField(Phpfox::getT('fevent'), 'org_end_time'))
    {
        $oDatabase->query("ALTER TABLE  `".Phpfox::getT('fevent')."` ADD  `org_end_time` INT(10) NULL AFTER  `end_time`");
    }
    if (!$oDatabase->isField(Phpfox::getT('fevent'), 'duration_days'))
    {
        $oDatabase->query("ALTER TABLE  `".Phpfox::getT('fevent')."` ADD  `duration_days` INT(10) NULL");
    }
    if (!$oDatabase->isField(Phpfox::getT('fevent'), 'duration_hours'))
    {
        $oDatabase->query("ALTER TABLE  `".Phpfox::getT('fevent')."` ADD  `duration_hours` INT(10) NULL");
    }
    if (!$oDatabase->isField(Phpfox::getT('fevent'), 'is_delete_user_past_repeat_event'))
    {
        $oDatabase->query("ALTER TABLE  `".Phpfox::getT('fevent')."` ADD  `is_delete_user_past_repeat_event` tinyint(1) NOT NULL DEFAULT '0'");
    }

    //  REMOVE BLOCKS WHICH ARE NOT USED
    $oDatabase->delete(Phpfox::getT('component'), ' product_id="younetevent" AND module_id="fevent" AND component="upcoming" ');
    $oDatabase->delete(Phpfox::getT('block'), ' product_id="younetevent" AND module_id="fevent" AND component="upcoming" ');
    
    $oDatabase->delete(Phpfox::getT('component'), ' product_id="younetevent" AND module_id="fevent" AND component="featured" ');
    $oDatabase->delete(Phpfox::getT('block'), ' product_id="younetevent" AND module_id="fevent" AND component="featured" ');

    $oDatabase->delete(Phpfox::getT('component'), ' product_id="younetevent" AND module_id="fevent" AND component="search" ');
    $oDatabase->delete(Phpfox::getT('block'), ' product_id="younetevent" AND module_id="fevent" AND component="search" ');
    
    $oDatabase->query("CREATE TABLE IF NOT EXISTS `".Phpfox::getT("fevent_admin")."` (
      `event_id` int(10) unsigned NOT NULL,
      `user_id` int(10) unsigned NOT NULL
    );");

    //  update org_start_time = start_time
    $oDatabase->query("UPDATE `".Phpfox::getT('fevent')."` 
      SET `org_start_time` = `start_time` WHERE `isrepeat` > -1 ;");

    //  update org_end_time = end_time
    $oDatabase->query("UPDATE `".Phpfox::getT('fevent')."` 
      SET `org_end_time` = `end_time` WHERE `isrepeat` > -1 ;");
        
    //  CONVERT OLD DATA -> NEW DATA
    if (!$oDatabase->isField(Phpfox::getT('fevent'), 'is_update_warning'))
    {
        //  WHEN GO TO HERE WHICH MEAN WE NEED TO CONVERT OLD DATA -> NEW DATA
        //  field is used to check when show warning or not 
        $oDatabase->query("ALTER TABLE  `".Phpfox::getT('fevent')."` ADD  `is_update_warning` tinyint(1) NOT NULL DEFAULT '0'");

        //  get repeat event 
        $repeatEvents = $oDatabase->select('event_id, user_id, isrepeat')
                                ->from(Phpfox::getT('fevent'))
                                ->where(' `isrepeat` > -1 ')
                                ->execute('getSlaveRows');

        if(isset($repeatEvents) && is_array($repeatEvents) && count($repeatEvents) > 0)
        {
            $notifyUserID = array(); 
            foreach($repeatEvents as $event)
            {
                //  repeat event -> normal event
                //  show warning in My Events page 
                $oDatabase->query("UPDATE `".Phpfox::getT('fevent')."` 
                  SET `isrepeat` = -1
                        , `end_time` = `timerepeat` 
                        , `is_update_warning` = 1 
                  WHERE `event_id` = " . $event['event_id'] . " ;");

                $notifyUserID[$event['user_id']] = $event['user_id'];
            }

            //  notify for owner 
            if (count($notifyUserID) > 0 && Phpfox::isModule('notification'))
            {
                foreach($notifyUserID as $userID)
                {
                    if ($userID == Phpfox::getUserId()
                        || intval($userID) == intval(Phpfox::getUserId())
                        )
                    {
                        $userID = 'getUserId';
                    }

                    Phpfox::getService('notification.process')->add('fevent_repeattonormalwarning', -1, $userID);
                }
            }                

        }
    }


}

ynfe_install304();

?>