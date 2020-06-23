<?php

defined('PHPFOX') or exit('NO DICE!');

class jobposting_component_block_job_search extends Phpfox_Component{

	public function process ()
	{
		$aCategoriesBlock = PHpfox::getService('jobposting.catjob')->get();
		$sCountry = $this->search()->get('country_iso');
        $sCountries = Phpfox::getService('jobposting.helper')->getSelectCountriesForSearch($sCountry);
		
		if(isset($_SESSION['ynjobposting_country_child_id']))
		{
			$this->setParam('country_child_id', $_SESSION['ynjobposting_country_child_id']);			
		}
		
		$this->setParam('country_child_value', $sCountry);
		
		$this->setParam('country_child_filter',true);
		
		$this->template()->assign(array(
				'aCategoriesBlock' => $aCategoriesBlock,
				'sCountries' => $sCountries
			)
		);
		
		return 'block';
	}
}