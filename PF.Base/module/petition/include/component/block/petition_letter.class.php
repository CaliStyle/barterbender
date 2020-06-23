<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Petition_Component_Block_Petition_Letter extends Phpfox_Component
{

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
		$id = $this->request()->get('id');
		if ($id >0)
		{
			 if (Phpfox::isModule('attachment')) {
				 $this->setParam('attachment_share', array(
								'type' => 'petition',
								'id' => 'core_js_petition_form',
								'edit_id' => $id
							)
            );
		}
        
}
        return 'block';

         
    }

}

?>