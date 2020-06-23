<?php
namespace Apps\YNC_Feed\Block;

use Phpfox;
use Phpfox_Component;
use Phpfox_Parse_Output;
use Core;
use Phpfox_Plugin;
use Phpfox_Request;
defined('PHPFOX') or exit('NO DICE!');

/**
 * Class Feed_Component_Block_Form
 */
class Form extends Phpfox_Component {

	public function process() {
		$bLoadCheckIn = false;
		if (!defined('PHPFOX_IS_PAGES_VIEW') && Phpfox::getParam('feed.enable_check_in') && (Phpfox::getParam('core.ip_infodb_api_key') || Phpfox::getParam('core.google_api_key') ) )
		{
			$bLoadCheckIn = true;
		}
		$this->template()->assign([
			'aFeedStatusLinks' => Phpfox::getService('ynfeed')->getShareLinks(),
			'bLoadCheckIn' => $bLoadCheckIn
		]);
	}
}