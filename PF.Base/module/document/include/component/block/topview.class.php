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
class Document_Component_Block_Topview extends Phpfox_Component
{
    /**
     * Class process method wnich is used to execute this component.
     */
    public function process()
    {
		$sLink = 'document';
        $sView = $this->getParam('sView');
        if ($this->request()->get('req2') == 'tag')
        {
            return false;
        }
        if ($sView == 'pending')
        {
            return false;
        }
        $is_search = $this->getParam('is_document_search');
        if ($is_search)
        {
            return false;
        }
        $sCondition = " m.in_process = 0 AND m.is_approved=1 ";
        if (!isset($sView)) { $sView = "";}
        
        $sQuery = Phpfox::getLib('database');
        $sQuery->select('*');
        
        $sQuery->from(PHpfox::getT('document'),'m');
        $sQuery->join(phpfox::getT('user'),'u', 'm.user_id = u.user_id');
        $bIsProfile = $this->getParam('bIsProfile');
        $aUser = $this->getParam('aUser');
        switch ($sView)
        {
            case 'pending':
                    //in Pending view, disable this block
                   return false;
            case 'my':
                Phpfox::isUser(true);
                $sCondition .= " AND m.user_id = " . Phpfox::getUserId() . ' AND m.privacy IN(%PRIVACY%)';
                break;
            case 'friend':
                $sQuery->join(Phpfox::getT('friend'), 'friends', 'friends.user_id = m.user_id AND friends.friend_user_id = ' . Phpfox::getUserId());
                $sCondition .= '  AND m.view_id = 0 AND m.privacy IN(%PRIVACY%) ';
                break;
            default:
                if ($bIsProfile)
                {
                    $sCondition .= ' AND m.view_id ' . ($aUser['user_id'] == Phpfox::getUserId() ? 'IN(0,2)' : '= 0') . ' AND m.item_id = 0 AND m.privacy IN(' . (Phpfox::getParam('core.section_privacy_item_browsing') ? '%PRIVACY%' : Phpfox::getService('privacy')->getForBrowse($aUser)) . ') AND m.user_id = ' . (int) $aUser['user_id'];
                }else
                {
                    if (defined('PHPFOX_IS_PAGES_VIEW'))
                    {
                        $aParentModule = $this->getParam('aParentModule');
						$sLink = 'pages/'.$aParentModule['item_id'].'/document';
                        $sCondition .= ' AND m.view_id = 0 AND m.module_id = \'' . Phpfox::getLib('database')->escape($aParentModule['module_id']) . '\' AND m.item_id = ' . (int) $aParentModule['item_id'] . ' AND m.privacy IN(%PRIVACY%)';
                       // $this->search()->setCondition('AND m.in_process = 0 AND m.view_id = 0 AND m.module_id = \'' . Phpfox::getLib('database')->escape($aParentModule['module_id']) . '\' AND m.item_id = ' . (int) $aParentModule['item_id'] . ' AND m.privacy IN(%PRIVACY%)');
                    }
                    else
                    {
                        $sCondition .= ' AND m.view_id = 0 AND m.privacy IN(%PRIVACY%) ' . Phpfox::getService('document')->getConditionsForSettingPageGroup();
                    }
                }
                break;
        }
        switch ($sView)
        {
            case 'friend':
                $sCondition = str_replace('%PRIVACY%', '0,1,2', $sCondition);
                break;
            case 'my':
                $sCondition = str_replace('%PRIVACY%', '0,1,2,3,4', $sCondition);
                break;                
            default:
                $sCondition = str_replace('%PRIVACY%', '0', $sCondition);
                break;
        }
        $sCategory = $this->getParam('sCategory');
        if (isset($sCategory) && $sCategory)
        {
            $sQuery->join(phpfox::getT('document_category_data'),'dcd', 'm.document_id = dcd.document_id');
            $sCondition .= " AND dcd.category_id = " . $sCategory;
        }
        
        $document_limit = $this->getParam('limit', 3);
        if ($document_limit <= 0)
        {
            return false;
        }else
        {
            $sQuery->limit(0,$document_limit+1); 
        }
        $sQuery->order('m.total_view DESC');
        $aRows = $sQuery->where($sCondition)
                       ->execute('getRows');
        $count_rows = count($aRows);
		if($count_rows > $document_limit){
			unset($aRows[$count_rows-1]);
		}
        $aDocuments = array(); 
        $count = 0;            
        if (count($aRows))
        {
            foreach ($aRows as $document)
            {
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
                if (($process_status == 'DONE' || $process_status == 'DISPLAYABLE')  && empty($document['image_url']))
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
                elseif ($process_status == 'PROCESSING'  && empty($document['image_url']))
                {
                    $document['image_url'] = Phpfox::getParam('core.path_file') . 'module/document/static/image/processing_thumbnail.png';
                    if (empty($document['access_key']))
                    {
                        $document['image_url'] = Phpfox::getParam('core.path_file') . 'module/document/static/image/google_cover_thumbnail.png';
                    }
                }
                elseif ($process_status == 'ERROR'  && empty($document['image_url']))
                {
                    $document['image_url'] = Phpfox::getParam('core.path_file') . 'module/document/static/image/error_thumbnail.png';
                }
                if ($document['image_url'] == "")
                {
                    $document['image_url'] = Phpfox::getParam('core.path_file') . 'module/document/static/image/error_thumbnail.png';
                }
                
                $document['is_long_name'] = (strlen($document['full_name']) > 20) ? true : false;
                $document['full_name_link'] = Phpfox::getLib('url')->makeUrl($document['user_name']);
                $document['link'] = Phpfox::permalink('document', $document['document_id'], $document['title']);
                    
                $aDocuments[] = $document;
            }
        }else
        {
            //no data, disable this block
            return false;
        }
     
        $this->template()->assign(array(
                'sHeader' => _p('top_viewed_documents'),
                'aDocuments' => $aDocuments,
                'sLink' => $sLink
            )
        );

        if ($count_rows > $document_limit) {
            $this->template()->assign(array(
                'aFooter' => array(
                    _p('view_all') => $this->url()->makeUrl($sLink, array('sort' => 'most-viewed'))
                )
            ));
        }
        
        return 'block';        
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return [
            [
                'info' => _p('Most Viewed Documents Limit'),
                'description' => _p('Define the limit of how many most viewed documents can be displayed when viewing the document section. Set 0 will hide this block.'),
                'value' => 3,
                'type' => 'integer',
                'var_name' => 'limit',
            ]
        ];
    }

    public function getValidation()
    {
        return [
            'limit' => [
                'def' => 'int',
                'min' => 0,
                'title' => '"Most Viewed Documents Limit" must be greater than or equal to 0'
            ]
        ];
    }
    
    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        // Lets clear it from memory
        $this->template()->clean(array(
                'limit',
            )
        );

        (($sPlugin = Phpfox_Plugin::get('document.component_block_filter_clean')) ? eval($sPlugin) : false);
    }
}

?>
