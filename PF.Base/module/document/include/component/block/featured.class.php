<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Document_Component_Block_Featured extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$sLink = 'document';
        $sView = $this->getParam('sView');
        if ($sView == 'pending')
        {
            return false;
        }
        
        if ($this->request()->get('req2') == 'tag')
        {
            return false;
        }
        
        if ($this->getParam('is_document_search'))
        {
            return false;
        }
        
        $sCondition = '';
        switch ($sView)
        {
            case 'my':
                Phpfox::isUser(true);
                $sCondition .= ' AND d.user_id = ' . Phpfox::getUserId() . ' AND d.privacy IN(0,1,2,3,4)';
                break;
            case 'friend':
                Phpfox::getLib('database')->join(Phpfox::getT('friend'), 'friends', 'friends.user_id = d.user_id AND friends.friend_user_id = ' . Phpfox::getUserId());
                $sCondition .= ' AND d.view_id = 0 AND d.privacy IN(0,1,2) ';
                break;
            default:
                if (defined('PHPFOX_IS_PAGES_VIEW'))
                {
                    $aParentModule = $this->getParam('aParentModule');
					$sLink = 'pages/'.$aParentModule['item_id'].'/document';
                    $sCondition .= ' AND d.view_id = 0 AND d.module_id = \'' . Phpfox::getLib('database')->escape($aParentModule['module_id']) . '\' AND d.item_id = ' . (int) $aParentModule['item_id'] . ' AND d.privacy = 0';
                }
                else
                {
                    $sCondition .= ' AND d.view_id = 0 AND d.privacy = 0 ' . Phpfox::getService('document')->getConditionsForSettingPageGroup('d');
                }
        }
        $limit = $this->getParam('limit', 5);
        if ($limit < 0) {
            return false;
        }
        $aRows = Phpfox::getService('document')->getFeatured($sCondition, $limit);
		$aDocuments = array();
                  
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
                    if ((int)(PHPFOX_TIME - $document['image_url_updated_time']) > 300
                        || $document['image_url'] == "")
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
        }
        else
        {
            //no data, disable this block
            return false;
        }
		
		$this->template()->assign(array(
				'sHeader' => _p('featured_documents'),
				'aDocuments_featured' => $aDocuments,
				'sLink' => $sLink
			)
		);

        if (count($aDocuments) >= $limit) {
            $this->template()->assign(array(
                'aFooter' => array(
                    _p('view_all') => $this->url()->makeUrl($sLink, array('sort' => 'featured'))
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
                'info' => _p('Featured Documents Limit'),
                'description' => _p('Define the limit of how many featured documents can be displayed when viewing the blog section. Set 0 will hide this block.'),
                'value' => 5,
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
                'title' => '"Featured Documents Limit" must be greater than or equal to 0'
            ]
        ];
    }
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('document.component_block_featured_clean')) ? eval($sPlugin) : false);
	}
}

?>