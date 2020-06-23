<?php

defined('PHPFOX') or exit('NO DICE!');

class Contest_Component_Block_Entry_Entry_Vote extends Phpfox_Component {

    public function process() {
    	$aEntry = $this->getParam('aEntry');
		$hide_vote = 0;
        if($aEntry['status_entry']!=1)
		{
			$hide_vote = 1;	
		}

		$this->template()->assign(array(
			'hide_vote' => $hide_vote,
		));
    }

}
?>