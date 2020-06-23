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
class Document_Component_Controller_Index extends Phpfox_Component
{
    public function process()
    {
        //added for beta 5
        if (defined('PHPFOX_IS_USER_PROFILE') && ($sLegacyTitle = $this->request()->get('req3')) && !empty($sLegacyTitle))
        {
            Phpfox::getService('core')->getLegacyItem(array(
                'field' => array('document_id', 'title'),
                'table' => 'document',
                'redirect' => 'document',
                'title' => $sLegacyTitle));
        }
        Phpfox::getUserParam('document.can_view_documents', true);
        $aParentModule = $this->getParam('aParentModule');

        $search_id = $this->request()->get('search-id');
        if (isset($search_id) && $search_id != "")
        {
            $this->setParam('is_document_search', true);
        }
        else
        {
            $this->setParam('is_document_search', false);
        }
        if ($iDocumentId = $this->request()->get('redirect'))
        {
            $aDocument = Phpfox::getService('document.process')->getDocumentById($iDocumentId);
            if (count($aDocument))
            {
                $this->url()->send('document', array('view', $aDocument['title_url']));
            }
            else
            {
                Phpfox_Error::set(_p('invalid_document'));
            }
        }
        //if not in Pages module and the req2 is number (document_id) then redirect into document.view page
        if ($aParentModule === null && $this->request()->getInt('req2'))
        {
            return Phpfox::getLib('module')->setController('document.view');
        }

        $bIsUserProfile = false;
        if (defined('PHPFOX_IS_AJAX_CONTROLLER'))
        {
            $bIsUserProfile = true;
            $aUser = Phpfox::getService('user')->get($this->request()->get('profile_id'));
            $this->setParam('aUser', $aUser);
        }

        if (defined('PHPFOX_IS_USER_PROFILE'))
        {
            $bIsUserProfile = true;
            $aUser = $this->getParam('aUser');
        }
        $oServiceDocumentBrowse = Phpfox::getService('document.browse');

        $sView = $this->request()->get('view');
        $sCategory = null;
        $aCallback = $this->getParam('aCallback', false);
        $this->setParam('sTagType', 'document');

        $bIsProfile = $this->getParam('bIsProfile');
        $this->search()->set(array(
            'type' => 'document',
            'field' => 'm.document_id',
            'search_tool' => array(
                'table_alias' => 'm',
                'search' => array(
                    'action' => ($aParentModule === null ? ($bIsProfile === true ? $this->url()->makeUrl($aUser['user_name'],
                        array('document', 'view' => $this->request()->get('view'))) : $this->url()->makeUrl('document',
                        array('view' => $this->request()->get('view')))) : $aParentModule['url'] . 'document/' . $this->request()->get('view')),
                    'default_value' => _p('search_documents'),
                    'name' => 'search',
                    'field' => 'm.title'),
                'sort' => array(
                    'latest' => array('m.time_stamp', _p('latest')),
                    'featured' => array('m.is_featured', _p('featured')),
                    'most-viewed' => array('m.total_view', _p('most_viewed')),
                    'most-talked' => array('m.total_comment', _p('most_discussed'))),
                'show' => array(
                    9,
                    12,
                    15))));

        $aBrowseParams = array(
            'module_id' => 'document',
            'alias' => 'm',
            'field' => 'document_id',
            'table' => Phpfox::getT('document'),
            'hide_view' => array('pending', 'my'));

        switch ($sView)
        {
            case 'pending':
                if (Phpfox::getUserParam('document.can_approve_documents'))
                {
                    $this->search()->setCondition('AND m.is_approved = 0');
                }
                break;
            case 'my':
                Phpfox::isUser(true);
                $this->search()->setCondition('AND m.user_id = ' . Phpfox::getUserId());
                break;
            default:
                $this->search()->setCondition('AND m.is_approved = 1');
                if ($bIsProfile)
                {
                    $this->search()->setCondition('AND m.in_process = 0 AND m.view_id ' . ($aUser['user_id'] == Phpfox::getUserId() ? 'IN(0,2)' : '= 0') . ' AND m.item_id = 0 AND m.privacy IN(' . (Phpfox::getParam('core.section_privacy_item_browsing') ? '%PRIVACY%' : Phpfox::getService('privacy')->getForBrowse($aUser)) . ') AND m.user_id = ' . (int)$aUser['user_id']);
                }
                else
                {
                    if (defined('PHPFOX_IS_PAGES_VIEW'))
                    {
                        $this->search()->setCondition('AND m.in_process = 0 AND m.view_id = 0 AND m.module_id = \'' . Phpfox::getLib('database')->escape($aParentModule['module_id']) . '\' AND m.item_id = ' . (int)$aParentModule['item_id'] . ' AND m.privacy IN(%PRIVACY%)');
                    }
                    else
                    {
                        $this->search()->setCondition(Phpfox::getService('document')->getConditionsForSettingPageGroup());
                    }
                }
                break;
        }

        $sCategory = null;
        $sTagSearchValue = null;


        if ($this->request()->get(($bIsUserProfile === true ? 'req3' : 'req2')) == 'tag')
        {
            $tagText = urldecode($this->request()->get(($bIsUserProfile === true ? 'req4' : 'req3')));
            if (($aTag = Phpfox::getService('tag')->getTagInfo('document', $tagText)))
            {
                $this->template()->setBreadCrumb('Topic: ' . $aTag['tag_text'] . '', $this->url()->makeUrl('current'), true);

                $this->search()->setCondition('AND tag.tag_text = \'' . Phpfox::getLib('database')->escape($this->request()->get(($bIsUserProfile === true ? 'req4' : 'req3'))) . '\' AND tag.tag_type = 0 AND tag.added > ' . (PHPFOX_TIME - (86400 * Phpfox::getParam('tag.tag_days_treading'))) . '');
            }
        }

        if ($this->request()->get('req2') == 'category')
        {
            $sCategory = $this->request()->getInt('req3');
            $this->search()->setCondition('AND mcd.category_id = ' . (int)$sCategory);
        }

        $this->setParam('sView', $sView);
        $this->setParam('sCategory', $sCategory);
        $this->template()->setBreadcrumb((defined('PHPFOX_IS_USER_PROFILE') ?
            _p('full_name_s_documents',
                array('full_name' => $aUser['full_name'])) :
            _p('documents')),
            (defined('PHPFOX_IS_USER_PROFILE') ? $this->url()->makeUrl($aUser['user_name'], 'document') : ($aCallback === false ? $this->url()->makeUrl('document') : $this->url()->makeUrl($aCallback['url_home'][0], array_merge($aCallback['url_home'][1], array('document'))))));
        $oServiceDocumentBrowse->category($sCategory);

        $this->search()->setContinueSearch(true);
        $this->search()->browse()->setPagingMode(Phpfox::getParam('document.document_paging_mode', 'loadmore'));
        $this->search()->browse()->params($aBrowseParams)->execute();

        $aFilterMenu = array();
        if (!defined('PHPFOX_IS_USER_PROFILE') && !defined('PHPFOX_IS_PAGES_VIEW'))
        {
            $aFilterMenu = array(_p('all_documents_link_title') => '');

            if (Phpfox::isUser()) {
                $iMyDocumentTotal = Phpfox::getService('document')->getMyDocumentsTotal();
                if ($iMyDocumentTotal) {
                    $aFilterMenu[_p('my_documents_link_title') . '<span class="my count-item">' . ($iMyDocumentTotal > 99 ? '99+' : $iMyDocumentTotal) . '</span>'] = 'my';
                } else {
                    $aFilterMenu[_p('my_documents_link_title') . '<span class="my count-item">' . 0 . '</span>'] = 'my';
                }
                if (!Phpfox::getParam('core.friends_only_community'))
                {
                    $aFilterMenu[_p('friend_documents')] = 'friend';
                }

                if (Phpfox::getUserParam('document.can_approve_documents'))
                {
                    $iPendingTotal = Phpfox::getService('document')->getPendingTotal();

                    if ($iPendingTotal)
                    {
                        $aFilterMenu[_p('pending') .
                        (Phpfox::getUserParam('document.can_approve_documents') ? ' <span class="pending count-item"> ' . $iPendingTotal . ' </span>' : 0)] = 'pending';
                    }
                }
            }
        }
        if (defined('PHPFOX_IS_PAGES_VIEW'))
        {
            $this->template()->assign(array('isPagesView' => true));
        }
        else
        {
            $this->template()->assign(array('isPagesView' => false));
        }
        $this->template()->buildSectionMenu('document', $aFilterMenu);

        $documents = $this->search()->browse()->getRows();
        Phpfox::getLib('pager')->set(array(
            'page' => $this->search()->getPage(),
            'size' => $this->search()->getDisplay(),
            'count' => $this->search()->browse()->getCount()
        ));
        $aDocuments = array();
        $count = 0;
        $new_documents_period = phpfox::getParam('document.new_documents_period');
        $previous_time = strtotime('-' . $new_documents_period . ' day');
        foreach ($documents as $document)
        {
            if(($document['module_id'] == 'groups' && !(Phpfox::getParam('document.display_document_created_in_group') && Phpfox::isAppActive('PHPfox_Groups')  && Phpfox::getService('groups')->hasPerm($document['item_id'],
                        'document.view_browse_documents'))) || ($document['module_id'] == 'pages' && !(Phpfox::getParam('document.display_document_created_in_page') && Phpfox::isAppActive('Core_Pages')  && Phpfox::getService('pages')->hasPerm($document['item_id'],
                            'document.view_browse_documents')))) {
                continue;
            }

            Phpfox::getService('document.process')->initScribd(Phpfox::getParam('document.api_key'), $document['user_id']);
            #process_status
            $process_status = $document['process_status'];
            if ($process_status != 'DONE' && $process_status != 'DISPLAYABLE')
            {
                $conversion_status = Phpfox::getService('document.process')->getConversionStatus($document['doc_id']);
                if($conversion_status != '')
                {
                    $process_status = $conversion_status;
                    Phpfox::getService('document.process')->updateConversionStatus($document['document_id'], $conversion_status);
                }
            }
            
            #image_url
            if (($process_status == 'DONE' || $process_status == 'DISPLAYABLE') && empty($document['image_url']))
            {
                //5 minutes or blank image_url
                if ((int)(PHPFOX_TIME - $document['image_url_updated_time']) > 300 || $document['image_url'] == "")
                {
                    $image_url = Phpfox::getService('document.process')->getThumbnail($document['doc_id']);
                    if($image_url != '')
                    {
                        $document['image_url'] = $image_url;

                        Phpfox::getService('document.process')->updateImageUrl($document['document_id'], $image_url);
                    }
                }
            }
            elseif ($process_status == 'PROCESSING' && empty($document['image_url']))
            {

                $document['image_url'] = Phpfox::getParam('core.path_file') . 'module/document/static/image/processing.png';
                if (empty($document['access_key']))
                {
                    $document['image_url'] = Phpfox::getParam('core.path_file') . 'module/document/static/image/google_cover.png';
                }
            }
            elseif ($process_status == 'ERROR' && empty($document['image_url']))
            {
                $document['image_url'] = Phpfox::getParam('core.path_file') . 'module/document/static/image/error.png';
            }
            if ($document['image_url'] == "")
            {
                $document['image_url'] = Phpfox::getParam('core.path_file') . 'module/document/static/image/error.png';
            }
            
            #page_count
            if ($document['page_count'] == 0 && ($process_status == 'DONE' || $process_status == 'DISPLAYABLE'))
            {
                $page_count = Phpfox::getService('document.process')->getPageCount($document['doc_id']);
                if($page_count != '')
                {
                    $document['page_count'] = $page_count;
                    Phpfox::getService('document.process')->updatePageCount($document['document_id'], $document['page_count']);
                }
            }

            $document['breadcrumb'] = Phpfox::getService('document.category')->getCategoriesById($document['document_id']);
            $document['is_long_name'] = (strlen($document['full_name']) > 20) ? true : false;
            $document['full_name_link'] = Phpfox::getLib('url')->makeUrl($document['user_name']);
            $document['date'] = Phpfox::getTime(Phpfox::getParam('core.global_update_time'));
            $document['link'] = Phpfox::permalink('document', $document['document_id'], $document['title']);
            
            if ($this->request()->get('req1') == 'pages' && $this->request()->getInt('req2'))
            {
                $pages_id = $this->request()->getInt('req2');
                $document['edit_link'] = $this->url()->makeUrl('document.add', array(
                    'module' => 'pages',
                    'item' => $pages_id,
                    'id' => $document['document_id']));
            }
            else
            {
                $document['edit_link'] = $this->url()->makeUrl('document.add', array('id_' . $document['document_id']));
            }
            
            $document['is_edit'] = ($document['user_id'] == Phpfox::getUserId() && Phpfox::getUserParam('document.can_edit_own_document')) || Phpfox::getUserParam('document.can_edit_other_document');
            $document['is_delete'] = ($document['user_id'] == Phpfox::getUserId() && Phpfox::getUserParam('document.can_delete_own_document')) || Phpfox::getUserParam('document.can_delete_other_document');

            //code to show 3 documents on a row
            $document['first_item'] = false;
            $document['last_item'] = false;
            if (($count % 3) == 0)
            {
                $document['first_item'] = true;
            }
            if (($count % 3) == 2)
            {
                $document['last_item'] = true;
            }
            if ($count == count($documents) - 1)
            {
                $document['last_item'] = true;
            }
            $document['index'] = $count;
            $count++;

            //determine the new document or not
            $document['is_new'] = false;
            if ($new_documents_period && $document['time_stamp'] > $previous_time)
            {
                $document['is_new'] = true;
            }

            $document['total_view'] = Phpfox::getService('document.process')->numberAbbreviation($document['total_view']);
            $document['total_comment'] = Phpfox::getService('document.process')->numberAbbreviation($document['total_comment']);
            $document['total_like'] = Phpfox::getService('document.process')->numberAbbreviation($document['total_like']);
            $document['date'] = Phpfox::getTime(Phpfox::getParam('core.global_update_time'), $document['time_stamp']);

            $aDocuments[] = $document;
        }
        $bInHomepage = 1;
        if ($this->search()->getPage() >1)
        {
            $bInHomepage = $this->search()->getPage();
        }
        $this->template()->setTitle((defined('PHPFOX_IS_USER_PROFILE') ? _p('full_name_s_documents', array('full_name' => $aUser['full_name'])) : _p('documents')))->setHeader('cache', array(
            'pager.css' => 'style_css',
            'jquery.rating.css' => 'static_css',
            'jquery/plugin/jquery.scrollTo.js' => 'static_script',
            'document.js' => 'module_document',
            'jquery.tipTip.js' => 'module_document',
            'tipTip.css' => 'module_document' //'jquery.tipTip.minified.js'=>'module_document',
                ))
            ->assign(array(
                'bInHomepage'=> $bInHomepage,
            'aDocuments' => $aDocuments,
            'sParentLink' => ($aCallback !== false ? $aCallback['url_home'][0] . '.' . implode('.', $aCallback['url_home'][1]) . '.document' : 'document'),
            'aCallback' => $aCallback,
            'sLinkPendingDocuments' => $this->url()->makeUrl('document.view_pending'),
            'sView' => $sView,
            'iPage' => $this->search()->getPage(),
            'core_path' => Phpfox::getParam('core.path_file'),
            'no_image_url' => Phpfox::getParam('core.path_file') . 'module/document/static/image/no_image.png',
                'isPage' => defined('PHPFOX_IS_PAGES_VIEW'),
            ));

        if ($sCategory !== null)
        {
            $aCategories = Phpfox::getService('document.category')->getParentBreadcrumb($sCategory);
            $iCnt = 0;
            foreach ($aCategories as $aCategory)
            {
                $iCnt++;

                $this->template()->setTitle($aCategory[0]);

                if ($aCallback !== false)
                {
                    $sHomeUrl = '/' . $aCallback['url_home'][0] . '/' . implode('/', $aCallback['url_home'][1]) . '/document/';
                    $aCategory[1] = preg_replace('/^http:\/\/(.*?)\/document\/(.*?)$/i', 'http://\\1' . $sHomeUrl . '\\2', $aCategory[1]);

                }

                $this->template()->setBreadcrumb($aCategory[0], $aCategory[1], ($iCnt === count($aCategories) ? true : false));
            }
        }

        if ($sView == 'pending')
        {
            $this->setParam('global_moderation', array(
                'name' => 'document',
                'ajax' => 'document.moderation',
                'menu' => array(array('phrase' => _p('delete'), 'action' => 'delete'), array('phrase' => _p('approve'), 'action' => 'approve'))));
        }
        else
        {
            $this->setParam('global_moderation', array(
                'name' => 'document',
                'ajax' => 'document.moderation',
                'menu' => array(array('phrase' => _p('delete'), 'action' => 'delete'))));
        }
        $aPager = array(
            'page' => $this->search()->getPage(),
            'size' => $this->search()->getDisplay(),
            'count' => $this->search()->browse()->getCount(),
            'paging_mode' => $this->search()->browse()->getPagingMode()
        );
        \Phpfox_Pager::instance()->set($aPager);
    }
}

?>
