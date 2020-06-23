<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[YOUNET_COPPYRIGHT]
 * @author  		AnNT
 * @package  		Module_jobposting
 */

class Jobposting_Component_Block_Job_Promote extends Phpfox_Component 
{
    /**
	 * Class process method wnich is used to execute this component.
	 */
    public function process()
    {
        $iId = $this->request()->get('id');
        
        $sPromoteCode = Phpfox::getService('jobposting.job')->getPromoteCode($iId, 1, 1);
        
        $this->template()->assign(array(
            'iId' => $iId,
            'sPromoteCode' => $sPromoteCode
        ));
    }

}

?>