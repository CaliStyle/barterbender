<?php

/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');

class Videochannel_Component_Block_Url extends Phpfox_Component
{
    /**
     *
     * @var Videochannel_Service_Category_Category 
     */
    public $oSerVideoChannelCategory;
    
    public function __construct($aParams)
    {
        parent::__construct($aParams);
        
        $this->oSerVideoChannelCategory = Phpfox::getService('videochannel.category');
    }


    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $this->template()->assign(array(
            'sCategories' => $this->oSerVideoChannelCategory->get(),
            'sEditorId' => $this->request()->get('editor_id')
                )
        );
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('videochannel.component_block_url_clean')) ? eval($sPlugin) : false);
    }

}

?>