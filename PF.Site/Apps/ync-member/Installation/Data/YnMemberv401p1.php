<?php

namespace Apps\YNC_Member\Installation\Data;

use Phpfox;

class YnMemberv401p1
{
    public function process()
    {
        // Update old settings
        db()->update(':block', array(
            'params' => json_encode(array(
                'limit' => Phpfox::getParam('ynmember.ynmember_max_featured_member', 20),
            ))
        ), 'component = \'featured_members\' AND module_id = \'ynmember\' AND params IS NULL');

        // Update old settings
        db()->update(':block', array(
            'params' => json_encode(array(
                'limit' => Phpfox::getParam('ynmember.ynmember_max_most_reviewed', 4),
            ))
        ), 'component = \'most_reviewed\' AND module_id = \'ynmember\' AND params IS NULL');

        // Update old settings
        db()->update(':block', array(
            'params' => json_encode(array(
                'limit' => Phpfox::getParam('ynmember.ynmember_max_top_rated', 4),
            ))
        ), 'component = \'top_rated\' AND module_id = \'ynmember\' AND params IS NULL');

        // Update old settings
        db()->update(':block', array(
            'params' => json_encode(array(
                'limit' => Phpfox::getParam('ynmember.ynmember_max_people_you_may_know', 4),
            ))
        ), 'component = \'people_you_may_know\' AND module_id = \'ynmember\' AND params IS NULL');

        // Update old settings
        db()->update(':block', array(
            'params' => json_encode(array(
                'limit' => Phpfox::getParam('ynmember.ynmember_max_upcoming_birthday', 4),
            ))
        ), 'component = \'birthday_calendar\' AND module_id = \'ynmember\' AND params IS NULL');

        db()->delete(':setting', 'module_id=\'ynmember\' AND var_name=\'ynmember_max_featured_member\'');
        db()->delete(':setting', 'module_id=\'ynmember\' AND var_name=\'ynmember_max_most_reviewed\'');
        db()->delete(':setting', 'module_id=\'ynmember\' AND var_name=\'ynmember_max_top_rated\'');
        db()->delete(':setting', 'module_id=\'ynmember\' AND var_name=\'ynmember_max_people_you_may_know\'');
        db()->delete(':setting', 'module_id=\'ynmember\' AND var_name=\'ynmember_max_upcoming_birthday\'');
    }
}