<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Block_Add_New_Role extends Phpfox_Component
{

    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $role_id = $this->getParam('role_id');
        $business_id = $this->getParam('business_id');

        $aRole = array();
        if((int)$role_id){
            $aRole = Phpfox::getService('directory')->getRoleMemberById($role_id);
        }

        $this->template()->assign(array(
                'aRole'             => $aRole,
                'role_id'           => $role_id,
                'business_id'           => $business_id,
                'sCorePath' => Phpfox::getParam('core.path'),
                'sCustomClassName' => 'ync-block'
            )
        );

        return 'block';
    }

}

?>