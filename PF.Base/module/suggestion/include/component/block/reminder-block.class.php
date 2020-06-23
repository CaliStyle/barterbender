<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');


class Suggestion_Component_Block_Reminder_Block extends Phpfox_Component
{
	/**
	 * Class process method which is used to execute this component.
	 */
	public function process()
	{
        $aReminders = phpfox::getService("suggestion.reminder")->getAllReminder(Phpfox::getUserId(),Phpfox::getParam('suggestion.number_item_on_other_block'));
        if(!count($aReminders))
        {
            return false;
        }
        $this->template()->assign(array(
            'sFullUrl' => Phpfox::getParam('core.path'),
            'viewMoreUrl' => $this->url()->makeUrl('suggestion.reminder'),
            'aReminders' => $aReminders             
        ));
        return 'block';  
	}

	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{

	}
}

?>