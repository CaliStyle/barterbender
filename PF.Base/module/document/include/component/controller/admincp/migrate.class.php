<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');
/**
 * 
 * 
 * @copyright        [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Document
 */

class Document_Component_Controller_Admincp_Migrate extends Phpfox_Component
{
     /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
		$this->template()->setTitle(_p('admin_menu_migrate_data'))
                        ->setBreadcrumb((_p('admin_menu_migrate_data')), $this->url()->makeUrl('admincp.document.migrate'))
                       ;
    }
}
?>
