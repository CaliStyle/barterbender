<?php

defined('PHPFOX') or exit('NO DICE!');

class JobPosting_Component_Block_Showpopupconfirmyesno extends Phpfox_Component {

    public function process() {
     
	 $function  = $this->getParam('function');
	 
	 switch ($function) {
		 case 'workingcompany':
			 	 $this->template()->assign(array(
		            'function' => 'jobposting.'. $function,
		            'phare' => _p($this->getParam('phare')),
					'value' => 'company_id='.$this->getParam('company_id').'&working='.$this->getParam('working')
				));
			 break;
		 case 'removeWorkingCompany':
			 	 $this->template()->assign(array(
		            'function' => 'jobposting.'. $function,
		            'phare' => _p($this->getParam('phare')),
		            'value' => 'type='.$this->getParam('type').'&companyID='.$this->getParam('company_id').'&userID='.$this->getParam('user_id')
				));
			 break;
		case 'rejectWorkingCompany':
			 	 $this->template()->assign(array(
		            'function' => 'jobposting.'. $function,
		            'phare' => _p($this->getParam('phare')),
		            'value' => 'type='.$this->getParam('type').'&companyID='.$this->getParam('company_id').'&userID='.$this->getParam('user_id')
				));
			 break;
		
	 }
        return 'block';
    }

    public function clean() {
    	
    }

}