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
class Document_Service_Process extends Phpfox_Service
{
    /**
     * Class constructor
     */ 
    private $my_user_id;
    private $api_key;
    private $secret;
    private $url;
    private $session_key;
    private $_aCallback = false;

    // cache time in minute
    private $_cacheTime = 5;   
    
    // statically cache documents 
    private $_aDocuments = array();
    
    // List of post cached method
    private $_aCachedMethod = array( 
        'docs.getConversionStatus',
        'thumbnail.get',
        'docs.getSettings'
    );
       
    public function __construct()
    {
        $this->_sTable = Phpfox::getT('document');
    } 
    public function addDocument($aVals)
    {
        (($sPlugin = Phpfox_Plugin::get('document.service_process_add')) ? eval($sPlugin) : false);
        
        $aInsert  = array();
        if (count($aVals))
        {
            $oFilter = Phpfox::getLib('parse.input');
            $aInsert['document_file_name'] = $aVals['document_file_name'];
            //$aInsert['document_file_type'] = $aVals['document_file_type'];
            $aInsert['document_file_path'] = $aVals['document_file_path'];
            $aInsert['doc_id'] = isset($aVals['doc_id'])?$aVals['doc_id']:0;

            $aInsert['access_key'] =isset($aVals['access_key'])?$aVals['access_key']:"";
            $aInsert['visibility'] = (isset($aVals['visibility']) && !empty($aVals['visibility'])) ? $aVals['visibility'] : 0;
            $aInsert['document_privacy'] = $aVals['document_privacy'];
            $aInsert['document_license'] = $aVals['document_license'];
            $aInsert['privacy'] = $aVals['privacy'];

            $aInsert['user_id'] = Phpfox::getUserId();

            $aInsert['is_approved'] = Phpfox::getUserParam('document.approve_document_before_display') ? 1 : 0;
            $aInsert['is_featured'] = 0;



            $aInsert['module_id'] = $aVals['module_id'];
            $aInsert['item_id'] = $aVals['item_id'];
            $aInsert['title'] = $oFilter->clean(strip_tags($aVals['title']), 255);
            $aInsert['title_url'] = $this->preParse()->prepareTitle('document', $aInsert['title'],'title_url', null, $this->_sTable);
            $aInsert['image_url'] = $aVals['image_url'];
            $aInsert['image_url_updated_time'] = PHPFOX_TIME;
            $aInsert['description'] = $aVals['text'];
            $aInsert['allow_comment'] = $aVals['allow_comment'];
            $aInsert['privacy_comment'] = $aVals['privacy_comment'];
            $aInsert['total_comment'] = 0;
            $aInsert['total_score']  = 0;
            $aInsert['total_like']  = 0;
            $aInsert['total_rating'] = 0;
            $aInsert['time_stamp'] = PHPFOX_TIME;
            $aInsert['total_view'] = 1;
            $aInsert['allow_rating'] = $aVals['allow_rating'];
            $aInsert['allow_download'] = $aVals['allow_download'];
            $aInsert['allow_attach'] = $aVals['allow_attach'];
            $aInsert['view_id'] = $aVals['view_id'];
            $aInsert['in_process'] = 0;  //default value
            $aInsert['process_status'] = 'PROCESSING'; //default value
            $aInsert['page_count'] = 0; //default value
            $aInsert['image_url'] = '';
            $aInsert['server_id'] = Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID');
            $this->_processUploadForm($aVals, $aInsert);
            $iDocumentId = $this->database()->insert($this->_sTable,$aInsert);
            
            $aVals['text'] = isset($aVals['text']) ? $aVals['text'] : "";
            
            $this->database()->insert(Phpfox::getT('document_text'), array(
                    'document_id' => $iDocumentId,
                    'text' => Phpfox::getLib('parse.input')->clean($aVals['text']),
                    'text_parsed' => Phpfox::getLib('parse.input')->prepare($aVals['text'])        
                )
            );
            
            #Update to Scribd
            $aScribdUpdate = array(
                'doc_ids' => $aVals['doc_id'],
                'title' => html_entity_decode($aVals['title'], ENT_COMPAT, 'utf-8'),
                'description' => Phpfox::getLib('parse.input')->prepare($aVals['text']),
                'access' => $aVals['visibility'] ? 'public' : 'private',
                'link_back_url' => Phpfox::getLib('url')->permalink('document', $iDocumentId, $aVals['title'])
            );
            $this->changeSettings($aScribdUpdate);

            if (Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support')) {
                Phpfox::getService('tag.process')->add('document', $iDocumentId, Phpfox::getUserId(), $aVals['text'], true);
            }
            if (Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_tag_support') && isset($aVals['tag_list']) && ((is_array($aVals['tag_list']) && count($aVals['tag_list'])) || (!empty($aVals['tag_list'])))) {
                Phpfox::getService('tag.process')->add('document', $iDocumentId, Phpfox::getUserId(), $aVals['tag_list']);
            }

            if ($aInsert['is_approved'])
            {
                if (isset($aVals['module_id']) && ($aVals['module_id'] != 'document') && Phpfox::isModule($aVals['module_id']) && Phpfox::hasCallback($aVals['module_id'],
                        'getFeedDetails')) {
                    (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->callback(Phpfox::callback($aVals['module_id'] . '.getFeedDetails',
                        $aVals['item_id']))->add('document', $iDocumentId, $aVals['privacy'],
                        (isset($aVals['privacy_comment']) ? (int)$aVals['privacy_comment'] : 0), $aVals['item_id']) : null);
                } else {
                    (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->add('document', $iDocumentId, $aVals['privacy'],
                        (isset($aVals['privacy_comment']) ? (int)$aVals['privacy_comment'] : 0)) : null);
                }

                //support add notification for parent module
                if (Phpfox::isModule('notification') && isset($aVals['module_id']) && Phpfox::isModule($aVals['module_id']) && Phpfox::hasCallback($aVals['module_id'],
                        'addItemNotification')) {
                    Phpfox::callback($aVals['module_id'] . '.addItemNotification', [
                        'page_id' => $aVals['item_id'],
                        'item_perm' => 'document.view_browse_documents',
                        'item_type' => 'document',
                        'item_id' => $iDocumentId,
                        'owner_id' => Phpfox::getUserId(),
                        'items_phrase' => _p('documents__l')
                    ]);
                }
                
                
                // Update user activity
                Phpfox::getService('user.activity')->update(Phpfox::getUserId(), 'document');
            }
            $aCategories = array();
            if ($iDocumentId)
            {
                foreach ($aVals['category'] as $iCategory)
                {        
                    if (empty($iCategory))
                    {
                        continue;
                    }
                    
                    if (!is_numeric($iCategory))
                    {
                        continue;
                    }            
                    
                    $aCategories[] = $iCategory;
                }
            }
			else
            {
                return false; // show error message here
            }
            foreach ($aCategories as $categoryId)
            {
                if (!empty($categoryId) && is_numeric($categoryId)) $this->database()->insert(Phpfox::getT('document_category_data'), array ('document_id' => $iDocumentId, 'category_id' => $categoryId));
            }
			if ($aVals['privacy'] == '4')
			{
				Phpfox::getService('privacy.process')->add('document', $iDocumentId, (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : array()));			
			}
            
            (($sPlugin = Phpfox_Plugin::get('document.service_process_add__end')) ? eval($sPlugin) : false);
            
            return $iDocumentId;
        }
		else
        {
            return false; //show error message here
        }
    }
    public function updatePhoto()
    {
        $sDirPath = PHPFOX_DIR_FILE . 'pic/document/';
        if (!is_dir($sDirPath))
        {
            @mkdir($sDirPath, 0777);
            @chmod($sDirPath, 0777);
        }
        $oImage = Phpfox::getLib('image');
        $sNewFileName = Phpfox::getLib('file')->upload('image', Phpfox::getParam('core.dir_pic') . 'document/', PHPFOX_TIME);

        $aSizes = array(100, 200, 400);
        foreach ($aSizes as $iSize)
        {
            $oImage->createThumbnail(Phpfox::getParam('core.dir_pic') . "document/" . sprintf($sNewFileName, ''), Phpfox::getParam('core.dir_pic') . "document/" . sprintf($sNewFileName, '_' . $iSize), $iSize, $iSize);
            $oImage->createThumbnail(Phpfox::getParam('core.dir_pic') . "document/" . sprintf($sNewFileName, ''), Phpfox::getParam('core.dir_pic') . "document/" . sprintf($sNewFileName, '_' . $iSize . '_square'), $iSize, $iSize, false);
        }
        return $sNewFileName;
    }
    public function updateDocument($aVals)
    {
        $aUpdate = array();
	
        if (count($aVals))
        {
            $aUpdate['visibility'] = ($aVals['visibility'] ? $aVals['visibility'] : 0);
            $aUpdate['privacy'] = $aVals['privacy']; 
            $aUpdate['document_privacy'] = $aVals['document_privacy'];
            $aUpdate['privacy'] = $aVals['privacy'];
            $aUpdate['document_license'] = $aVals['document_license'];
            $aUpdate['title'] = $aVals['title'];
            $aUpdate['title_url'] = $this->preParse()->prepareTitle('document', $aUpdate['title'],'title_url', null, $this->_sTable);
            $aUpdate['description'] = $aVals['text'];
            $aUpdate['allow_comment'] = $aVals['allow_comment'];
            $aUpdate['privacy_comment'] = $aVals['privacy_comment'];
            $aUpdate['time_stamp'] = PHPFOX_TIME;
            $aUpdate['allow_rating'] = $aVals['allow_rating'];
            $aUpdate['allow_download'] = $aVals['allow_download'];
            $aUpdate['allow_attach'] = $aVals['allow_attach'];
            $this->_processUploadForm($aVals, $aUpdate);
            $this->database()->update($this->_sTable,$aUpdate,'document_id = ' . $aVals['document_id']);
            
            if (isset($aVals['text']))
            {
                $textUpdate['text'] =   Phpfox::getLib('parse.input')->clean($aVals['text']);
                $textUpdate['text_parsed'] = Phpfox::getLib('parse.input')->prepare($aVals['text']);     
            }else
            {
                $textUpdate['text_parsed'] = $textUpdate['text'] =  "";
            }
          
            $this->database()->update(Phpfox::getT('document_text'), $textUpdate, 'document_id =' . $aVals['document_id']);
            
            #Update to Scribd
            $aScribdUpdate = array(
                'doc_ids' => Phpfox::getService('document')->getScribdDocId($aVals['document_id']),
                'title' => html_entity_decode($aVals['title'], ENT_COMPAT, 'utf-8'),
                'description' => $textUpdate['text_parsed'],
                'access' => $aVals['visibility'] ? 'public' : 'private',
                'link_back_url' => Phpfox::getLib('url')->permalink('document', $aVals['document_id'], $aVals['title'])
            );
            $this->changeSettings($aScribdUpdate);

            // Add hastag
            if (Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_hashtag_support')) {
                Phpfox::getService('tag.process')->update('document', $aVals['document_id'], $aVals['user_id'], $aVals['text'], true);
            }
            if (Phpfox::isModule('tag') && Phpfox::getParam('tag.enable_tag_support')) {
                Phpfox::getService('tag.process')->update('document', $aVals['document_id'], $aVals['user_id'],
                    (!Phpfox::getLib('parse.format')->isEmpty($aVals['tag_list']) ? $aVals['tag_list'] : null));
            }
            
            (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->update('document', $aVals['document_id'], $aVals['privacy'], (isset($aVals['privacy_comment']) ? (int) $aVals['privacy_comment'] : 0)) : null);        
            
            $this->database()->delete(Phpfox::getT('document_category_data'),'document_id = ' . $aVals['document_id']);
            foreach ($aVals['category'] as $categoryId)
            {
                if(!empty($categoryId) && is_numeric($categoryId)) $this->database()->insert(Phpfox::getT('document_category_data'), array ('document_id' => $aVals['document_id'], 'category_id' => $categoryId));
            }
			if (Phpfox::isModule('privacy'))
			{
				if ($aVals['privacy'] == '4')
				{
					Phpfox::getService('privacy.process')->update('document', $aVals['document_id'], (isset($aVals['privacy_list']) ? $aVals['privacy_list'] : array()));
				}
				else 
				{
					Phpfox::getService('privacy.process')->delete('document', $aVals['document_id']);
				}			
			}
            return true;
        }
		else
        {
            return false;
        }
    }
    public function getDocumentTitleUrl($iDocumentId)
    {
        if ($iDocumentId)
        {
            return $this->database()->select('title_url')
                            ->from($this->_sTable)
                            ->where('document_id =' . $iDocumentId)
                            ->execute('getField');
        }
    }
    public function getDocumentFromTitleUrl($sTitle)
    {
        $document = $this->database()->select('*')
                            ->from($this->_sTable)
                            ->where('title_url =\'' . $this->database()->escape($sTitle) . '\'')  
                            ->execute('getRow');
        if (count($document))
        {
            return $document;
        }else
        {
            return false;
        } 
    }
    public function getDocumentById($iDocumentId)
    {
        if ($iDocumentId)
        {
            return $this->database()->select('*, server_id as doc_server_id')
                    ->from($this->_sTable)
                    ->where('document_id =' . $iDocumentId)
                    ->execute('getRow');    
        }else
        {
            return false;
        }
        
    }
    public function getDocuments($iUserId = 0, $iAll = false, $aParams=null)
    {
        $sConditions = "";
        if ($iUserId && !$iAll)
        {
            $sConditions .= "user_id =" . $iUserId;
        }
        $aDocuments = $this->database()->select('*, m.view_id as document_view_id')
                        ->from($this->_sTable,'m')
                        ->join(Phpfox::getT('user'),'u','m.user_id = u.user_id')
                        ->where($sConditions)
                        ->order('time_stamp DESC')
                        ->execute('getRows');
        return $aDocuments;
    }
    public function callback($aCallback)
    {
        $this->_aCallback = $aCallback;

        return $this;
    }
    public function getDocument($iDocument)
    {
        if ($iDocument)
        {
            if (Phpfox::isModule('track'))
            {
                $this->database()->select("document_track.item_id AS document_is_viewed, ")->leftJoin(Phpfox::getT('document_track'), 'document_track', 'document_track.item_id = d.document_id ');
            }
            if (Phpfox::isModule('friend'))
            {
                $this->database()->select('f.friend_id AS is_friend, ')->leftJoin(Phpfox::getT('friend'), 'f', "f.user_id = d.user_id AND f.friend_user_id = " . Phpfox::getUserId());                    
            }        
            
            if (Phpfox::isModule('like'))
            {
                $this->database()->select('l.like_id AS is_liked, ')->leftJoin(Phpfox::getT('like'), 'l', 'l.type_id = \'document\' AND l.item_id = d.document_id AND l.user_id = ' . Phpfox::getUserId());
            }
            $aDocument = $this->database()->select('d.*,d.server_id as file_server_id, dt.*, u.*, d.view_id as document_view_id, dr.rate_id AS has_rated, dl.license_id, dl.license_name, dl.reference_url, dl.image_url as license_image_url')
                        ->from($this->_sTable,'d')
                        ->join(Phpfox::getT('user'), 'u', 'u.user_id = d.user_id')
                        ->join(Phpfox::getT('document_text'), 'dt' ,'dt.document_id = d.document_id')
                        ->leftJoin(Phpfox::getT('document_rating'), 'dr', 'dr.item_id = d.document_id AND dr.user_id =' . Phpfox::getUserId())
                        ->leftJoin(Phpfox::getT('document_license'), 'dl', 'dl.license_id = d.document_license')
                        ->where('d.document_id =' . $iDocument . ' ')
                        ->execute('getRow');
            if (count($aDocument))
            {
            	$aDocument['license_image_url'] = Phpfox::getParam('core.path_file') . 'module/' . $aDocument['license_image_url'] ;

                //$aDocument['license_image_url'] = PHpfox::getParam('core.folder_original').'PF.Base/module/' . $aDocument['license_image_url'] ;


            	$aDocument['download_link'] = Phpfox::getLib('url')->makeUrl('document.download', array('id_' . $aDocument['document_id']));
				$aDocument['breadcrumb'] = Phpfox::getService('document.category.category')->getCategoriesById($aDocument['document_id']);
				//if(Phpfox::getParam('document.api_viewer'))
                if ($aDocument['doc_id'] > 0)
				{
                	$this->initScribd(Phpfox::getParam('document.api_key'),$aDocument['user_id']);
                	$aDocument['scribd_display'] = $this->displayDocument($aDocument['doc_id'],$aDocument['access_key']);
                	$aDocument['bookmark'] = "<script type='text/javascript' src='http://www.scribd.com/javascripts/scribd_api.js'></script>
<div id='embedded_flash'><a href=\"http://www.scribd.com\">Scribd</a></div>
<script type=\"text/javascript\">
var scribd_doc = scribd.Document.getDoc( ".$aDocument['doc_id'] . ", '" . $aDocument['access_key'] . "' );
var oniPaperReady = function(e){} 
scribd_doc.addParam( 'jsapi_version', 2 );
scribd_doc.addEventListener( 'iPaperReady', oniPaperReady ); 
scribd_doc.write( 'embedded_flash' );</script>";
				}
				else
				{
					$file_path = Phpfox::getParam('core.path_file') . 'file/document' . PHPFOX_DS .$aDocument['document_file_path'];

					if(Phpfox::getParam('core.allow_cdn') && $aDocument['file_server_id'])
                    {
                        $file_path = Phpfox::getLib('cdn')->getUrl($file_path,$aDocument['file_server_id']);
                    }
                    /*
                    $domain = Phpfox::getLib('url') -> makeUrl('') ;
                    $domain = str_replace('/index.php/', '/',$domain);
                    $file_path =  $domain.'PF.Base/file/document/' . $aDocument['document_file_path'];
                    */

                    //$file_path = "http://infolab.stanford.edu/pub/papers/google.pdf";
                    //$file_path = "http://product-dev.younetco.com/longvx/test/google.pdf";

					 $aDocument['google_display'] = '<iframe src="https://docs.google.com/viewer?url='.$file_path.'&embedded=true" class="yndocument_google_viewer" frameborder="0"></iframe>';
					 $aDocument['bookmark'] = '<iframe src="https://docs.google.com/viewer?url='.$file_path.'&embedded=true" style="width:100%; height:600px;" frameborder="0"></iframe>';
				}
                $aDocument['embed'] = '';
                if (Phpfox::isModule('tag'))
                {
                    $aTags = Phpfox::getService('tag')->getTagsById(defined('PHPFOX_IS_GROUP_VIEW') ?'document_group': 'document', $aDocument['document_id']);
                    if (isset($aTags[$aDocument['document_id']]))
                    {
                        $aDocument['tag_list'] = $aTags[$aDocument['document_id']];
                    }
                }
                if (Phpfox::isModule('track'))
                {
                    $viewed_document = $this->database()->select("item_id")
                                            ->from(Phpfox::getT('document_track'))
                                            ->where('item_id = ' . $aDocument['document_id'] . ' AND user_id = ' . Phpfox::getUserBy('user_id'))
                                            ->execute('getField');
                    if (!empty($viewed_document)){
                        $aDocument['document_is_viewed'] = true;
                    }else
                    {
                        $aDocument['document_is_viewed'] = false;
                    }
                }
                return $aDocument;
                
            }else
            {
                return false;
            }
               
        }else
        {
            return false;
        }
      
    }
    public function displayDocument($doc_id, $access_key)
    {
        if ($doc_id != "" && $access_key != "")
        {
             $str = "<div id='embedded_flash' ><a href=\"http://www.scribd.com\">Scribd</a></div>";
             $str .="<script>var doc_id = ".$doc_id.";var access_key = '".$access_key."';</script>";
           
             return $str;   
        }
    }
     public function initScribd($api_key,$my_user_id)
     {
         $this->api_key = $api_key;
         $this->my_user_id = $my_user_id;
         $this->url = "http://api.scribd.com/api?api_key=" . $api_key;
         return;
     }
     public function login($username, $password){
        $method = "user.login";
        $params['username'] = $username;
        $params['password'] = $password;

        $result = $this->postRequest($method, $params);
        $this->session_key = $result['session_key'];
        return $result;
    }
    
    public function uploadFromUrl($url, $doc_type = null, $access = null, $rev_id = null)
    {
        $method = "docs.uploadFromUrl";
        
        $params['url'] = $url;
        $params['access'] = $access;
        $params['rev_id'] = $rev_id;
        $params['doc_type'] = $doc_type;
        $params['download_and_drm'] = "view-only";
        
        $data_array = $this->postRequest($method, $params);

        if ($data_array['stat'] = 'ok')
        {
            $aReturn['doc_id'] = (string)$data_array->doc_id;
            $aReturn['access_key'] = (string)$data_array->access_key;
            $aReturn['secret_password'] = isset($data_array->secret_password) ? (string)$data_array->secret_password : "";
            $aReturn['thumbnail_url'] = $this->getThumbnail($aReturn['doc_id']);
        }
        else
        {
            return false;
        }
        
        return $aReturn;
    }
    
    public function changeSettings($params)
    {
        $method = "docs.changeSettings";
        $oResult = $this->postRequest($method, $params); 
        if ($oResult['stat'] == 'ok')
        {
            return true;
        }
        return false;
    }
    
    public function getThumbnail($doc_id)
    {
      //  $sUrl = 'http://api.scribd.com/api?method=thumbnail.get&api_key=' . $this->api_key . '&doc_id=' . $doc_id . '&my_user_id=' . $this->my_user_id;
        
				if(isset($this->_aDocuments[$doc_id]['thumbnail'])) {
					return $this->_aDocuments[$doc_id]['thumbnail'];
				}
				
        $method = 'thumbnail.get';
        $params = array ('api_key' => $this->api_key,
                         'doc_id' => $doc_id,
                         'my_user_id' => $this->my_user_id);
        $oResult = $this->postRequest($method,$params);



        if ($oResult['stat'] == 'ok')
        {
            $this->_aDocuments[$doc_id]['thumbnail'] = (string) $oResult->thumbnail_url;

            return (string)$oResult->thumbnail_url;
        }else
        {
            return "";
        }
    }
    public function getPageCount($doc_id)
    {
				if(isset($this->_aDocuments[$doc_id]['page_count'])) {
					return $this->_aDocuments[$doc_id]['page_count'];
				}
				
        $method = 'docs.getSettings';
        $params = array ('api_key' => $this->api_key,
                         'doc_id' => $doc_id,
                         'my_user_id' => $this->my_user_id);
        $oResult = $this->postRequest($method,$params); 
        if ($oResult['stat'] == 'ok')
        {
						$this->_aDocuments[$doc_id]['page_count'] = (string) $oResult->page_count;
            return (string)$oResult->page_count;
        }else
        {
            return "";
        }
    }
    public function getConversionStatus($doc_id)
    {
        
				if(isset($this->_aDocuments[$doc_id]['conversion_status'])) {
					return $this->_aDocuments[$doc_id]['conversion_status'];
				}

        $method = 'docs.getConversionStatus';
        $params = array ('api_key' => $this->api_key,
                         'doc_id' => $doc_id,
                         'my_user_id' => $this->my_user_id);
        $oResult = $this->postRequest($method,$params); 
        if ($oResult['stat'] == 'ok')
        {
						// cache it here
			$this->_aDocuments[$doc_id]['conversion_status'] = $oResult->conversion_status;
            return (string)$oResult->conversion_status;
        }else
        {
            return "";
        }    
    }

		//minhta , cache function 
    public function saveToCache($doc_id, $needle, $data) {

			$cacheId = $this->getCache($doc_id, $needle);
			return Phpfox::getLib('cache')->save($cacheId, $data);
		}

		public function getCache($doc_id, $needle) {
			$cacheName = $doc_id . '_' . $needle;
			$cacheArray = array('yndocument', $cacheName);

			$cacheId = Phpfox::getLib('cache')->set($cacheArray); 

			return $cacheId;
		}

		public function getFromCache($doc_id, $needle) {
			$cacheId = $this->getCache($doc_id, $needle);

			return Phpfox::getLib('cache')->get($cacheId, $this->_cacheTime);
		}

    public function deleteOnScribd($doc_id)
    {
        $method = 'docs.delete';
        $params = array ('api_key' => $this->api_key,
                         'doc_ids' => $doc_id,
                         'my_user_id' => $this->my_user_id,
                       );
        $oResult = $this->postRequest($method,$params); 
        if ($oResult['stat'] == 'ok')
        {
            return true;
        }else
        {
            return false;
        }                  
    }
    public function getList(){
        $sUrl = 'http://api.scribd.com/api?method=docs.getList&api_key=' . $this->api_key . '&my_user_id=' . $this->my_user_id;
        $oResult = simplexml_load_file($sUrl);
        if ($oResult['stat'] == 'ok')
        {
            return $oResult->resultset;
        } 
        return false;
    }
     
    private function postRequest($method, $params){
        $params['method'] = $method;
        $params['session_key'] = $this->session_key;
        $params['my_user_id'] = $this->my_user_id;
        $doc_id = isset($params['doc_id']) ? $params['doc_id'] : false;

				if($doc_id && ($cacheResult = $this->getFromCache($doc_id, $method))) {
					return simplexml_load_string($cacheResult);
				}

        $post_params = array();
        foreach ($params as $key => &$val) {
            if(!empty($val)){
                
                if (is_array($val)) $val = implode(',', $val);
                if($key != 'file' && substr($val, 0, 1) == "@"){
                    $val = chr(32).$val;
                }
                    
                $post_params[$key] = $val;
            }
        }  
        $secret = $this->secret;
       // $post_params['api_sig'] = $this->generate_sig($params, $secret);
        $request_url = $this->url;
		if(isset($post_params['url']))
        {
			$post_params['url'] = str_replace("https:", "http:", $post_params['url']);
		}
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url );       
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_POST, 1 );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_params );
        $xml = curl_exec( $ch );
        $result = simplexml_load_string($xml); 

        if (in_array($method, $this->_aCachedMethod) && !empty($result))
        {
            $this->saveToCache($doc_id, $method, $result->asXML());
        }
        
        curl_close($ch);
        return $result;
    }
    public function makeUrl($sUser, $sUrl, $aCallback = null)
    {
        return Phpfox::getLib('url')->makeUrl($sUser, array('document', $sUrl));
    }
    public function getDocumentForEdit($iEditId)
    {
        $aDocument = $this->database()->select('d.*, d.description as text, u.user_name')
            ->from($this->_sTable, 'd')
            ->join(Phpfox::getT('user'), 'u', 'u.user_id = d.user_id')
            ->where('d.document_id = ' . (int) $iEditId)
            ->execute('getSlaveRow');
        if (isset($aDocument['document_id']))
        {
            if (($aDocument['user_id'] == Phpfox::getUserId() && Phpfox::getUserParam('document.can_edit_own_document')) || Phpfox::getUserParam('document.can_edit_other_document'))
            {
                $aDocument['categories'] = Phpfox::getService('document.category')->getCategoryIds($aDocument['document_id']);
                $aDocument['document_url'] = $this->makeUrl($aDocument['user_name'], $aDocument['title_url']);
                if (!empty($aDocument['image_url'])) {
                    $aDocument['current_image'] = Phpfox::getLib('image.helper')->display([
                        'server_id' => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID'),
                        'path' => 'core.url_pic',
                        'file' => 'document/'.$aDocument['image_url'],
                        'suffix' => '_240_square',
                        'return_url' => true
                    ]);
                }

                if (Phpfox::isModule('tag'))
                {
                    $aTags = Phpfox::getService('tag')->getTagsById($aDocument['module_id'] != 'document' ? 'document_group' : 'document', $aDocument['document_id']);
                    if (isset($aTags[$aDocument['document_id']]))
                    {
                        $aDocument['tag_list'] = '';
                        foreach ($aTags[$aDocument['document_id']] as $aTag)
                        {
                            $aDocument['tag_list'] .= ' ' . $aTag['tag_text'] . ',';
                        }
                        $aDocument['tag_list'] = trim(trim($aDocument['tag_list'], ','));
                    }
                }

                return $aDocument;
            }
        }

        return Phpfox_Error::set(_p('unable_to_find_the_document_you_plan_to_edit'));
       
    }
    public function delete($iDocumentId)
    {
       $aDocument = $this->database()->select('d.document_id, d.doc_id, d.user_id, d.document_file_path')
                ->from($this->_sTable, 'd')
                ->where(($iDocumentId === null ? 'd.user_id = ' . Phpfox::getUserId() : 'd.document_id = ' . (int) $iDocumentId))
                ->execute('getRow');
                
            if (!isset($aDocument['document_id']))
            {
                return Phpfox_Error::set(_p('unable_to_find_the_document_you_plan_to_delete'));
            }
       
        if (($aDocument['user_id'] == Phpfox::getUserId() && Phpfox::getUserParam('document.can_delete_own_document')) || Phpfox::getUserParam('document.can_delete_other_document'))
        {  
            $documentFileLocation = Phpfox::getParam('core.dir_file') . 'document' . PHPFOX_DS . $aDocument['document_file_path'] ;
            phpfox::getLib('file')->unlink($documentFileLocation);
           
            $this->initScribd(Phpfox::getParam('document.api_key'),$aDocument['user_id']); 
            $this->deleteOnScribd($aDocument['doc_id']);
            
            (Phpfox::isModule('comment') ? Phpfox::getService('comment.process')->deleteForItem(null, $aDocument['document_id'], 'document') : null);        
            (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->delete('document', $aDocument['document_id']) : null);
            (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->delete('comment_document', $aDocument['document_id']) : null);            
            
            (Phpfox::isModule('notification') ? Phpfox::getService('notification.process')->delete('comment_document', $aDocument['document_id'], $aDocument['user_id']) : null);            
            (Phpfox::isModule('notification') ? Phpfox::getService('notification.process')->delete('document_approved', $aDocument['document_id'], $aDocument['user_id']) : null);            
            
          
            (Phpfox::isModule('tag') ? Phpfox::getService('tag.process')->deleteForItem($aDocument['user_id'], $aDocument['document_id'], 'document') : null);
            
            $this->database()->delete(Phpfox::getT('document'), 'document_id = ' . $aDocument['document_id']);
            $this->database()->delete(Phpfox::getT('document_category_data'), 'document_id = ' . $aDocument['document_id']);
            $this->database()->delete(Phpfox::getT('document_rating'), 'item_id = ' . $aDocument['document_id']);
            $this->database()->delete(Phpfox::getT('document_text'), 'document_id = ' . $aDocument['document_id']);
            $this->database()->delete(Phpfox::getT('document_embed'), 'document_id = ' . $aDocument['document_id']);
            
            // Update user activity
            Phpfox::getService('user.activity')->update($aDocument['user_id'], 'document', '-');
            
            return true;
        }
        
        return Phpfox_Error::set(_p('invalid_permissions'));
  
    }
    public function deleteMultiple($aIds)
    {
        foreach($aIds as $iId)
        {
            $this->delete($iId);
        }
        return true;
    }
    public function getPendingTotal()
    {
        return $this->database()->select('COUNT(*)')
            ->from($this->_sTable)
            ->where('view_id = 2')
            ->execute('getSlaveField');
    }
    public function test_send()
    {
        $result = Phpfox::getLib('mail')->to('baotnq@younetco.com')->subject('hello')->message('test message')->send();
        return $result;
    }
    public function sendWithAttachment($aVals)
    {
        require_once(PHPFOX_DIR_MODULE . 'document' . PHPFOX_DS . 'static' . PHPFOX_DS . 'libs' . PHPFOX_DS . 'attach.class.php');

        Phpfox::isUser(true);
        //Phpfox::getUserParam('share.can_send_emails', true);
        // Phpfox::getService('ban')->checkAutomaticBan($aVals['subject'] . ' ' . $aVals['message']);
        $aPassed = array();
        $aEmails = explode(',', $aVals['to'] . ',');
        $iCnt = 0;

        foreach ($aEmails as $sEmail)
        {
            $sEmail = trim($sEmail);
            
            if (empty($sEmail))
            {
                continue;
            }

            if (Phpfox::getLib('mail')->checkEmail($sEmail))
            {
                $iCnt++;

                $aPassed[] = $sEmail;

            }
        }

        if (!count($aPassed))
        {
            return Phpfox_Error::set(_p('share.none_of_the_emails_entered_were_valid'));
        }

        $aDocument = Phpfox::getService('document.process')->getDocumentById($aVals['id']);

        $_SESSION['document_attachment'] = Phpfox::getParam('core.dir_file') . 'document' . PHPFOX_DS . $aDocument['document_file_path'];
        $_SESSION['document_name'] = $aDocument['document_file_name'];
        $ext = pathinfo($aDocument['document_file_path'], PATHINFO_EXTENSION);
        $sTempFile = Phpfox::getParam('core.dir_file') . 'document' . PHPFOX_DS . md5($aDocument['document_file_name'].PHPFOX_TIME.uniqid()) . '.' . $ext;

        if (Phpfox::getParam('core.allow_cdn') && $aDocument['doc_server_id'] > 0)
        {

            $sFileUrl = Phpfox::getLib('cdn')->getUrl(Phpfox::getParam('core.url_file').'document/'.$aDocument['document_file_path'], $aDocument['doc_server_id']);
            $sContents = file_get_contents($sFileUrl);
            @file_put_contents($sTempFile, $sContents);
            if (filesize($sTempFile) > 0)
            {
                $_SESSION['document_attachment'] = $sTempFile;
            }

        }

        $email = new Phpmailer_Attach();
        $email->send($aPassed, $aVals['subject'], $aVals['message'], $aVals['message']);

        //Phpfox_Error::skip(false);

        unset($_SESSION['document_attachment']);
        unset($_SESSION['document_name']);
        if (file_exists($sTempFile))
        {
            @unlink($sTempFile);
        }
        
        return true;
    }
    public function updateConversionStatus($document_id, $sStatus = "")
    {
        if ($document_id)
        {
            $this->database()->update(Phpfox::getT('document'),array('process_status' => $sStatus), 'document_id =' . $document_id);   
        }
    }  
    public function updatePageCount($document_id, $iPageCount)
    {
        if ($document_id && $iPageCount)
        {
            $this->database()->update(Phpfox::getT('document'),array('page_count' => $iPageCount), 'document_id =' . $document_id);   
        }
    }
    public function updateImageUrl($document_id, $sImageUrl)
    {
        if ($document_id && $sImageUrl != "")
        {
            $this->database()->update(Phpfox::getT('document'),array('image_url' => $sImageUrl, 'image_url_updated_time' => PHPFOX_TIME), 'document_id =' . $document_id);
        }
    }
    public function getFileType($sFileName)
    {
        $aFile = explode('.',$sFileName);
        return  $aFile[(count($aFile) - 1)];
    }
	
	// Data Migration
	public function getColumnInfo($table){
		$columns = array();
		$oDBLib = PHPFOX::getLib("database");
		$result = $oDBLib->query("SHOW COLUMNS FROM `$table`");
		while($aRes = mysqli_fetch_assoc($result)){
			$columns[$aRes["Field"]] = $aRes;
		}
		return $columns;
	}
	
	public function alterMissingColumn($table = NULL, $columns = array()) {
		if(count($columns) === 0 || $table === NULL)return false;
		$oDBLib = PHPFOX::getLib("database");
		$_columns = $this->getColumnInfo($table);
		$columnNames = array_keys($_columns);
		
		foreach($columns as $index=>$description){
			if(!in_array($index, $columnNames)){
				// missing col :D
				$oDBLib->query(sprintf("ALTER TABLE `%s` ADD COLUMN `%s` %s;", $table, $index, $description));
			} else {
				// exist
			}
		}
	}
	
	public function updateTable($updateInfors = array()) {
		$oDBLib = PHPFOX::getLib("database");
		foreach($updateInfors as $table=>$aUpdate){
			$oDBLib->query(sprintf("UPDATE `%s` set %s WHERE %s;", $aUpdate['table'], $aUpdate['vals'], $aUpdate["cond"]));
		}
	}
	
	public function getRawDocuments() {
		$oDBLib = PHPFOX::getLib("database");
		return $oDBLib
			->select("*")
			->from(PHPFOX::getT("document"), "doc")
			// ->join(PHPFOX::getT("document_text"), "doctext", "doc.document_id = doctext.document_id")
			->where("doc.document_id not in (SELECT doctex.document_id FROM " . PHPFOX::getT("document_text") . " AS doctex);")
			->execute("getSlaveRows");
	}
	
	public function getRawTable($table) {
		$oDBLib = PHPFOX::getLib("database");
		return $oDBLib
			->select("*")
			->from($table)
			->execute("getSlaveRows");
	}
	// End Data Migration
    
    public function updateApprove($iId, $iValue)
    {
        Phpfox::isUser(true);
        Phpfox::getUserParam('document.can_approve_documents', true);
        
        if($iValue == 1)
        {
            $aDocument = $this->database()->select('d.*, ' . Phpfox::getUserField())
                ->from($this->_sTable, 'd')
                ->join(Phpfox::getT('user'), 'u', 'u.user_id = d.user_id')
                ->where('d.document_id = ' . (int)$iId)
                ->execute('getRow');
            
            $rs = $this->database()->update($this->_sTable, array('is_approved' => 1), 'document_id = '.(int)$iId);
        
            if($aDocument['is_approved']==0 && $rs)
            {
                #Add feed
                if (isset($aDocument['module_id']) && ($aDocument['module_id'] != 'document') && Phpfox::isModule($aDocument['module_id']) && Phpfox::hasCallback($aDocument['module_id'],
                        'getFeedDetails')) {
                    (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->callback(Phpfox::callback($aDocument['module_id'] . '.getFeedDetails',
                        $aDocument['item_id']))->add('document', $iId, $aDocument['privacy'],
                        (isset($aDocument['privacy_comment']) ? (int)$aDocument['privacy_comment'] : 0), $aDocument['item_id']) : null);
                } else {
                    (Phpfox::isModule('feed') ? Phpfox::getService('feed.process')->add('document', $iId, $aDocument['privacy'],
                        (isset($aDocument['privacy_comment']) ? (int)$aDocument['privacy_comment'] : 0), 0, $aDocument['user_id']) : null);
                }
                
                #Update user activity
                Phpfox::getService('user.activity')->update($aDocument['user_id'], 'document');

                if (Phpfox::isModule('notification')) {
                    Phpfox::getService('notification.process')->add('document_approved', $aDocument['document_id'], $aDocument['user_id']);
                }

                #Send mail to user
                $sLink = Phpfox::getLib('url')->makeUrl($aDocument['user_name'], array('document', $aDocument['title_url']));
                
                Phpfox::getLib('mail')->to($aDocument['user_id'])
                    ->subject(array('document.your_document_has_been_approved_on_site_title', array('site_title' => Phpfox::getParam('core.site_title'))))
                    ->message(array('document.your_document_has_been_approved_on_site_title_n_nto_view_this_video_follow_the_link_below_n_a_href', array('site_title' => Phpfox::getParam('core.site_title'), 'sLink' => $sLink)))
                    ->notification('document.document_is_approved')
                    ->send();
            }
        }
        else
        {
            $rs = $this->database()->update($this->_sTable, array('is_approved' => 0), 'document_id = '.(int)$iId);
        }
        
        return $rs;
    }

    public function updateFeature($iId, $iValue)
    {
        Phpfox::isUser(true);
        Phpfox::getUserParam('document.can_feature_documents', true);
        
        $rs = $this->database()->update($this->_sTable, array('is_featured' => $iValue), 'document_id = '.(int)$iId);
        
        return $rs;
    }
    
    public function approveMultiple($aId)
    {
        foreach($aId as $iId)
        {
            $this->updateApprove($iId, 1);
        }
        
        return true;
    }
    
    public function featureMultiple($aId)
    {
        foreach($aId as $iId)
        {
            $this->updateFeature($iId, 1);
        }
        
        return true;
    }

    public function updateAllowDownload($iId, $iValue)
    {
        Phpfox::isUser(true);
        Phpfox::getUserParam('document.can_set_allow_download', true);
        
        $rs = $this->database()->update($this->_sTable, array('allow_download' => $iValue), 'document_id = '.(int)$iId);
        
        return $rs;
    }

    function numberAbbreviation($number) {
        if($number == 0)
            return $number;
        $abbrevs = array(12 => "T", 9 => "B", 6 => "M", 3 => "K", 0 => "");
        foreach($abbrevs as $exponent => $abbrev) {
            if($number >= pow(10, $exponent)) {
                $display_num = $number / pow(10, $exponent);
                $decimals = ($exponent >= 3 && round($display_num) < 100) ? 1 : 0;
                return number_format($display_num, $decimals) . $abbrev;
            }
        }
    }

    public function deleteImage($iId, $iUserId)
    {
        $iUserId = (int)$iUserId;
        $iId = (int)$iId;

        $aDocument = db()->select('image_url, user_id')
            ->from(':document')
            ->where('document_id ='.$iId)
            ->execute('getRow');

        if (!empty($aDocument['image_url'])) {
            $aParams = Phpfox::getService('document.callback')->getUploadParams();
            $aParams['type'] = 'document';
            $aParams['path'] = $aDocument['image_url'];
            $aParams['user_id'] = $iUserId;
            if (Phpfox::getService('user.file')->remove($aParams)) {
                $this->database()->update(':document', array('image_url' => null,'server_id' => 0),
                    'document_id = ' . $iId);
            }
            else {
                return false;
            }
        }
        return true;
    }

    private function _processUploadForm($aVals, &$aInsert)
    {
        if (!empty($aVals['image_url']) && (!empty($aVals['temp_file']) || !empty($aVals['remove_photo']))) {
            if ($this->_deleteImage($aVals['image_url'], 'document', $aVals['image_server_id'])) {
                $aInsert['image_url'] = null;
                $aInsert['server_id'] = 0;
            }
        }

        if (!empty($aVals['temp_file'])) {
            $aFile = Phpfox::getService('core.temp-file')->get($aVals['temp_file']);
            if (!empty($aFile)) {
                $aInsert['image_url'] = $aFile['path'];
                $aInsert['image_server_id'] = $aFile['server_id'];
                Phpfox::getService('core.temp-file')->delete($aVals['temp_file']);
            }
        }
    }

    private function _deleteImage($sName, $sType, $iServerId = 0)
    {
        $aParams = Phpfox::callback($sType . '.getUploadParams');
        $aParams['type'] = $sType;
        $aParams['path'] = $sName;
        $aParams['server_id'] = $iServerId;

        return Phpfox::getService('user.file')->remove($aParams);
    }
}
?>
