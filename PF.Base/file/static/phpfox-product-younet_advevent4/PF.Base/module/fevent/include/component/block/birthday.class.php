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
class Fevent_Component_Block_Birthday extends Phpfox_Component
{
    public function process()
    {
        if (!Phpfox::getParam('friend.enable_birthday_notices'))
        {
            return false;
        }
        
        if (!Phpfox::isUser())
        {
            return false;
        }

        $aBirthdays = Phpfox::getService('friend')->getBirthdays(Phpfox::getuserId());

        if (empty($aBirthdays) && (Phpfox::getParam('friend.show_empty_birthdays') == false))
        {
            return false;
        }

        $this->template()->assign(array(
                'aBirthdays' => $aBirthdays,
                'sHeader' => _p('friend.birthdays'),
                'sCustomClassName' => 'ync-block'
            )
        );

        return 'block';
    }
}