<?php

namespace Apps\YNC_Member\Installation\Data;

use Phpfox;

class YnMemberv401
{
    private function database()
    {
        return Phpfox::getLib('phpfox.database');
    }

    public function process()
    {
        if(!$this->database()->isField(Phpfox::getT('user_activity'), 'activity_ynmember_review'))
        {
            $this->database()->query("ALTER TABLE  `".Phpfox::getT('user_activity')."` ADD `activity_ynmember_review` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0'");
        }
        if(!$this->database()->isField(Phpfox::getT('user_field'),'ynmember_rating'))
        {
            $this->database()->query("ALTER TABLE  `".Phpfox::getT('user_field')."` ADD  `ynmember_rating` DECIMAL(4,2) NOT NULL DEFAULT '0.00'");
        }
        if(!$this->database()->isField(Phpfox::getT('user_field'),'ynmember_total_review'))
        {
            $this->database()->query("ALTER TABLE  `".Phpfox::getT('user_field')."` ADD  `ynmember_total_review` INT(10) UNSIGNED NOT NULL DEFAULT '0'");
        }
    }
}