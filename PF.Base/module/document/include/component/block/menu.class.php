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
class Document_Component_Block_Menu extends Phpfox_Component
{
    public function process()
    {
        $show_edit_link = false;
        if ($this->request()->get('req2') == 'view')
        {
            
            $sTitle = $this->request()->get('req3');
            $document = Phpfox::getService('document.process')->getDocumentFromTitleUrl($sTitle);
            $show_edit_link = ($document['user_id'] == Phpfox::getUserId() && Phpfox::getUserParam('document.can_edit_own_document')|| Phpfox::getUserParam('document.can_edit_other_document')) ? true: false;    
            $this->template()->assign(array(
                       'edit_document' => $this->url()->makeUrl('document.add',array('id_' . $document['document_id']))));    
        }
        $this->template()
            ->assign(array(
            'show_edit_link' => $show_edit_link,
            'add_document' => $this->url()->makeUrl('document.add'),
            'my_documents' => $this->url()->makeUrl(Phpfox::getUserBy('user_name'),'document'),
            'all_documents' => $this->url()->makeUrl('document.index')
            ));
        return 'block';
    }
}
?>
