<?php

defined('PHPFOX') or exit('NO DICE!');

class Contest_Component_Block_Entry_Content_Video extends Phpfox_Component{

	public function process ()
	{
		$aEntry = $this->getParam('aYnEntry');

		$this->template()->assign(array(
				'aVideoEntry' => $aEntry
				// 'sHeader' => _p('contest.categories')
			)
		);	
			
	}
}