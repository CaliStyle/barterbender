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
class Module_Event
{
	public static $aTables = array(
		'fevent',
		'event_category',
		'event_category_data',
		'event_feed',
		'event_feed_comment',
		'event_invite',		
		'event_text'
	);
	
	public static $aInstallWritable = array(
		'file/pic/event/'
	);		
}

?>