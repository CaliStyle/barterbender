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
class Document_Component_Controller_Admincp_Manage extends Phpfox_Component
{
    public function process()
    {
        Phpfox::isAdmin(true);

        // ACTION DELETE APPROVE FEATURE
        if ($this->request()->get('delete') && ($aDeleteIds = $this->request()->getArray('id')))
        {
            if (Phpfox::getService('document.process')->deleteMultiple($aDeleteIds))
            {
                $this->url()->send('admincp.document.manage', null, _p('document_successfully_deleted'));
            }
        }

        if ($this->request()->get('feature') && ($aId = $this->request()->getArray('id')))
        {
            if (Phpfox::getService('document.process')->featureMultiple($aId))
            {
                $this->url()->send('admincp.document.manage', null, _p('documents_successfully_featured'));
            }
        }

        if ($this->request()->get('approve') && ($aId = $this->request()->getArray('id')))
        {
            if (Phpfox::getService('document.process')->approveMultiple($aId))
            {
                $this->url()->send('admincp.document.manage', null, _p('documents_successfully_approved'));
            }
        }
        // END ACTION



        $iPage = $this->request()->getInt('page');
        $aPages = array(20, 20, 30, 40);

        $aDisplays = array();
        foreach ($aPages as $iPageCnt)
        {
            $aDisplays[$iPageCnt] = _p('core.per_page', array('total' => $iPageCnt));
        }
        $aStatus = array('%' => 'Any',
            '%1%' => _p('approved'),
            '%0%' => _p('not_approved',null,false,"Not Approved"),
        );
        $aFilters = array(
            'search' => array(
                'type' => 'input:text',
                'search' => "AND ca.title LIKE '%[VALUE]%'"
            ),
            'user' => array(
                'type' => 'input:text',
                'search' => "AND u.full_name LIKE '%[VALUE]%'"
            ),
            'status' => array(
                'type' => 'select',
                'options' => $aStatus,
                'search' => "AND is_approved LIKE '[VALUE]'"
            ),
        );

        $oSearch = Phpfox::getLib('search')->set(array(
                'type' => 'document',
                'filters' => $aFilters,
                'search' => 'search'
            )
        );
        //$iLimit = $oSearch->getDisplay();
        $iLimit = 20;

        $aConds = $oSearch->getConditions();

        list($iCnt, $aDocuments) =
            Phpfox::getService('document.document')->searchDocument($aConds, 'ca.time_stamp desc' , $oSearch->getPage(), $iLimit);

        Phpfox::getLib('pager')->set(array('page' => $iPage, 'size' => $iLimit, 'count' => $oSearch->getSearchTotal($iCnt)));

        $documents=array();
        foreach($aDocuments as $aDocument)
        {

                $aDocument['link'] = Phpfox::permalink('document', $aDocument['document_id'], $aDocument['title']);
                $aDocument['edit_link'] = $document['edit_link'] = $this->url()->makeUrl('document.add', array('id_' . $aDocument['document_id'],'back_admincp'));
                $aDocument['short_title'] = Phpfox::getLib('parse.input')->clean($aDocument['title'],20);
                $aDocument['created_date'] = date("M j, Y ",$aDocument['time_stamp']);
                $documents[] = $aDocument;

        }

        $this->template()->setTitle(_p('manage_documents'))
            ->setBreadcrumb((_p('manage_documents')), $this->url()->makeUrl('admincp.document.manage'))
            ->assign(array(
                    'aDocuments' => $documents,
                    'can_feature' => Phpfox::getUserParam('document.can_feature_documents'),
                    'can_approve' => Phpfox::getUserParam('document.can_approve_documents'),
                )
            );
        return;






        // START CODE V3
        if ($this->request()->get('delete') && ($aDeleteIds = $this->request()->getArray('id')))
        {
            if (Phpfox::getService('document.process')->deleteMultiple($aDeleteIds))
            {
                $this->url()->send('admincp.document.manage', null, _p('document_successfully_deleted'));
            }
        }
        
        if ($this->request()->get('feature') && ($aId = $this->request()->getArray('id')))
        {
            if (Phpfox::getService('document.process')->featureMultiple($aId))
            {
                $this->url()->send('admincp.document.manage', null, _p('documents_successfully_featured'));
            }
        }
        
        if ($this->request()->get('approve') && ($aId = $this->request()->getArray('id')))
        {
            if (Phpfox::getService('document.process')->approveMultiple($aId))
            {
                $this->url()->send('admincp.document.manage', null, _p('documents_successfully_approved'));
            }
        }
        
        $aDocuments = Phpfox::getService('document.process')->getDocuments(0,true);
        $iPage = $this->request()->get('page');
        $iPage = (isset($iPage) && is_numeric($iPage)) ? $iPage : 1;
        $documents_count = count($aDocuments);
        $documents_per_page = 30;
        Phpfox::getLib('pager')->set(array('page' => $iPage, 'size' => $documents_per_page, 'count' => $documents_count));
        $start_index = (int) ($iPage - 1)* $documents_per_page;
        $last_index =  ($documents_count < ($iPage) * $documents_per_page)  ? $documents_count : (int)(($iPage) * $documents_per_page);
        $count = 0;  $documents = array();
        foreach($aDocuments as $aDocument)
        {
           if ( $start_index <= $count && $count < $last_index)
           {
               $aDocument['link'] = Phpfox::permalink('document', $aDocument['document_id'], $aDocument['title']); 
               $aDocument['edit_link'] = $document['edit_link'] = $this->url()->makeUrl('document.add', array('id_' . $aDocument['document_id'],'back_admincp')); 
               $aDocument['short_title'] = Phpfox::getLib('parse.input')->clean($aDocument['title'],20);
               $aDocument['created_date'] = date("M j, Y ",$aDocument['time_stamp']);
               $documents[] = $aDocument;    
           }
            $count++;
        }


        $this->template()->setTitle(_p('manage_documents'))
                        ->setBreadcrumb((_p('manage_documents')), $this->url()->makeUrl('admincp.document.manage'))
                        ->assign(array(

                                'aDocuments' => $documents,
                                'can_feature' => Phpfox::getUserParam('document.can_feature_documents'),
                                'can_approve' => Phpfox::getUserParam('document.can_approve_documents'),
                            )
                        );
    }
}
?>
