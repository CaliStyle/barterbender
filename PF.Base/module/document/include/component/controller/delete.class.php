<?php
defined('PHPFOX') or exit('NO DICE!');
/**
 * 
 * 
 * @copyright        [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Document
 */
class Document_Component_Controller_Delete extends Phpfox_Component
{
    public function process()
    {
        if($iDocumentId = $this->request()->getInt('id'))
        {
            $aDocument = Phpfox::getService('document.process')->getDocument($iDocumentId);
            if($aDocument)
            {
                if($aDocument['user_id'] == Phpfox::getUserId())
                {
                    Phpfox::getUserParam('document.can_delete_own_document', true);
                }
                else
                {
                    Phpfox::getUserParam('document.can_delete_other_document', true);
                }

                Phpfox::getService('document.process')->delete($iDocumentId);
                $this->url()->send('document', null, _p('document_successfully_deleted'));
            }
        } else {
            $this->url()->send('document');
        }
    }
}