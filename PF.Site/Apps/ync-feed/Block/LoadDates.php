<?php
/**
 * [PHPFOX_HEADER]
 */
namespace Apps\YNC_Feed\Block;

use Phpfox;
use Phpfox_Parse_Output;
use Core;
use Phpfox_Component;
use Phpfox_Plugin;
use Phpfox_Request;
defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox_Component
 * @version 		$Id: loaddates.class.php 5521 2013-03-19 12:58:06Z Raymond_Benc $
 */
class LoadDates extends Phpfox_Component
{
	/**
	 * Controller
	 */
	public function process()
	{
		
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('feed.component_block_loaddates_clean')) ? eval($sPlugin) : false);
	}
}