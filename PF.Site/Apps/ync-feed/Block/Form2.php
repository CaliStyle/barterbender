<?php
namespace Apps\YNC_Feed\Block;

use Phpfox;
use Phpfox_Parse_Output;
use Core;
use Phpfox_Component;
use Phpfox_Plugin;
use Phpfox_Request;
defined('PHPFOX') or exit('NO DICE!');

class Form2 extends Phpfox_Component {
	public function process() {
		if (!Phpfox::isUser()) {
			return false;
		}

		$this->template()->assign([
			'bShowMenu' => $this->getParam('menu', false)
		]);
        return null;
	}

	public function clean() {
		$this->template()->clean('bShowMenu');
		$this->clearParam('menu');
	}
}