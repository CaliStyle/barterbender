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
class Document_Component_Controller_Profile extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
      
        $this->setParam('bIsProfile', true);
        $aUser = $this->getParam('aUser');        
        Phpfox::getComponent('document.index', array('bNoTemplate' => true), 'controller');
    }
    
    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('document.component_controller_profile_clean')) ? eval($sPlugin) : false);
    }
}

?>