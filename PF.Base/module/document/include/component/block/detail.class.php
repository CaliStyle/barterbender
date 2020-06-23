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
class Document_Component_Block_Detail extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
        $aDocument = $this->getParam('aDocument');
        $sGroup = $this->getParam('sGroup', '');
        $this->template()->assign(array(
                'aDocumentDetails' => array(
                    _p('added') => Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $aDocument['time_stamp']),
                    _p('comments') => $aDocument['total_comment']
                ),
                'allow_rating' => $aDocument['allow_rating'],
                'sGroup' => $sGroup
            )
        );
        return 'block';
    }
    
    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        (($sPlugin = Phpfox_Plugin::get('document.component_block_detail_clean')) ? eval($sPlugin) : false);
    }
}
  
?>
