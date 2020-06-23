<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright       [YOUNET_COPYRIGHT]
 * @author          AnNT
 * @package         Module_jobposting
 */

class JobPosting_Component_Block_Company_Photo extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        if(!$aCompany = $this->getParam('aCompany'))
        {
            return false;
        }

        $aImages = Phpfox::getService('jobposting.company')->getImages($aCompany['company_id']);
        $iTotalImages = count($aImages);
        
        $this->template()->assign(array(
            'aImages' => $aImages,
            'iTotalImages' => $iTotalImages,
            'aParamsUpload' => array('iRemain' => 6 - $iTotalImages),
            'aCompany' => $aCompany,
        ));
    }
    
    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('jobposting.component_block_company_photo_clean')) ? eval($sPlugin) : false);
    }
}

?>