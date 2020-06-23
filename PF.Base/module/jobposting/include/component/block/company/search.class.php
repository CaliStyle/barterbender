<?php

defined('PHPFOX') or exit('NO DICE!');

class jobposting_component_block_Company_search extends Phpfox_Component{

	public function process ()
	{
		$aIndustryBlock = PHpfox::getService('jobposting.category')->get(2);
		$this->template()->assign(array(
				'aIndustryBlock' => $aIndustryBlock,
				'sFormUrl' => $this->search()->getFormUrl().'/bIsAdvSearch_1',
			)
		);
		
		return 'block';
	}
}