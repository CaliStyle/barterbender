<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 * @copyright      YouNet Company
 * @author         TienNPL
 * @package        Module_FoxFeedsPro
 * @version        3.02
 * 
 */
class FoxFeedsPro_Component_Controller_Addnews extends Phpfox_Component
{
	/**
	 * validate the form data input
	 * @param array $aVals is the array of input data
	 * @param boolean $bIsEdit is the edit mode
	 * @return true|false
	 */
	private function validate($aVals, $bIsEdit = FALSE)
	{	
		// Check news url and not empty news
		if(empty($aVals['item_title']))
		{
			Phpfox_Error::set(_p('foxfeedspro.news_title_cannot_be_empty'));
			return false;
		}
		if(empty($aVals['item_url_detail']))
		{
			Phpfox_Error::set(_p('foxfeedspro.news_url_cannot_be_empty'));
			return false;
		}
		if(!preg_match('/^(http|https)?:\/\/[a-zA-Z0-9\.\-_]+\.[a-zA-Z]{2,6}[^\s]+$/i', $aVals['item_url_detail']))
		{
			Phpfox_Error::set(_p('foxfeedspro.the_news_url_is_not_valid'));
			return FALSE;
		}
		
		// Check logo file
		if (isset($aVals['thumbnail']) && !empty($aVals['thumbnail']['name']))
		{
			if($aVals['thumbnail']['size'] == 0)
			{
				Phpfox_Error::set(_p('foxfeedspro.the_uploaded_image_file_size_must_be_greater_than_zero'));
				return FALSE;
			}
		}

		// Check exists feed name and feed url
		if ((!empty($aVals['item_title']) || !empty($aVals['item_url_detail'])))
		{
			$aNews = phpfox::getService('foxfeedspro')->getNewsByNameOrURL($aVals);

			if( count($aNews) > 0 )
			{
				Phpfox_Error::set(_p('foxfeedspro.the_news_name_or_news_url_has_already_existed'));
				return FALSE;
			}
		}
		
		return TRUE;
	}
	
	/*
	 * Process method which is used to process this component
	 */
	public function process ()
	{
		//Checking add feed permission
		Phpfox::isUser(TRUE);
		Phpfox::getUserParam("foxfeedspro.can_add_news_items",TRUE);
		
		// Build filter section menu on left side
		$aFilterMenu = array();
		if (!defined('PHPFOX_IS_USER_PROFILE')) 
		{
			$aFilterMenu = array(
				_p('foxfeedspro.browse_all') => '',
				TRUE,
				_p('foxfeedspro.my_rss_providers') 	 => 'foxfeedspro.feeds',
				_p('foxfeedspro.my_news') 			 => 'foxfeedspro.news',
				_p('foxfeedspro.my_favorited_news') 	 => 'foxfeedspro.view_favorite',
			);
		}
		$this -> template() -> buildSectionMenu('foxfeedspro', $aFilterMenu);
		
		$bIsEdit = false;
		$bNewsNotFound = FALSE;
		$oFoxFeedsPro = Phpfox::getService('foxfeedspro');
		$oFoxFeedsProProcess = Phpfox::getService('foxfeedspro.process');
		
		// Add validation for form elements
	 	$aValidation = array(
			'title' => array(
				'def' => 'required',
				'title'=> _p('foxfeedspro.news_title_cannot_be_empty')
			),
			'url' => array(
				'def' => 'required',
				'title'=> _p('foxfeedspro.news_url_cannot_be_empty')
			),
		);	
		$oValid = Phpfox::getLib('validator')->set(array(
				'sFormName' => 'js_add_feed_form', 
				'aParams'   => $aValidation
		));
		
		// Get available RSS Feeds
		$aFeeds = $oFoxFeedsPro -> getFeedsByUserId(Phpfox::getUserId());
		
		// On Edit Mode or not
		if($iEditId = $this->request()->getInt('item'))
		{
			$bIsEdit = true;
			
			$aNews   = $oFoxFeedsPro->getNewsById($iEditId, false);
            if (!empty($aNews['item_image'])) {
                $aNews['current_image'] = Phpfox::getLib('image.helper')->display(
                    array(
                        'server_id' => $aNews['server_id'],
                        'path' => 'core.url_pic',
                        'file' => $aNews['item_image'],
                        'return_url' => true
                    )
                );
            }

                if(!$aNews)
			{
				$bNewsNotFound = TRUE;
			}
			
			if (Phpfox::isModule('tag'))
			{
				$aNews['tag_list'] = '';					

				$aTags = Phpfox::getService('tag')->getTagsById('foxfeedspro_news', $iEditId);
				if (isset($aTags[$aNews['item_id']]))
				{
					foreach ($aTags[$aNews['item_id']] as $aTag)
					{
						$aNews['tag_list'] .= ' ' . $aTag['tag_text'] . ',';	
					}
					$aNews['tag_list'] = trim(trim($aNews['tag_list'], ','));
				}

			}

			if(isset($aNews['item_id'])){
				if($aNews['user_id'] != Phpfox::getUserId()){
					$this->url()->send('foxfeedspro.news', null, _p('foxfeedspro.you_cannot_edit_item_of_other_user'));
				}
				if($aNews['is_approved'] == 1){
					$this->url()->send('foxfeedspro.news', null, _p('foxfeedspro.you_cannot_edit_item_which_is_approved'));
				}				
			}
			$this->template() ->assign(array(
				'aForm' => $aNews
			));
		}
		
		// Set Header, BreadCrumb and Variable
		$this->template()->setHeader(array(
			'front_end.js'	=> 'module_foxfeedspro',
		));
		
		$this->template()->setBreadcrumb(_p('foxfeedspro.news'), $this->url()->makeUrl('foxfeedspro'));
		
		$this->template()->setBreadCrumb(
			(!$bIsEdit ? _p('foxfeedspro.add_news') : _p('foxfeedspro.edit_news')), 
			(!$bIsEdit ? $this->url()->makeurl('foxfeedspro.addnews') : $this->url()->makeurl('foxfeedspro.addnews.item_'.$iEditId)),
			TRUE
		);
		
		$this->template()->assign(array(
			'sCreateJs'   => $oValid -> createJS(),
			'sGetJsForm'  => $oValid -> getJsForm(),
			'aFeeds'	  => $aFeeds,
			'bIsEdit'	  => $bIsEdit,
			'bNewsNotFound'=> $bNewsNotFound
		));
		
		// Form Submit Process
		if($aVals = $this->request()->get('val'))
		{
			// Get logo file if it is added
			if(isset($_FILES['thumbnail']) && !empty ($_FILES['thumbnail']['name']))
			{
				$aVals['thumbnail'] = $_FILES['thumbnail'];
			}
			else
			{
				$aVals['thumbnail'] = "";
			}

			if($bIsEdit)
			{
				$aVals['item_id'] = $iEditId;
			}
			
			// Validate input data and process add/edit RSS Provider
			if( $this->validate($aVals, $bIsEdit) )
			{
				
				$aFeed = $oFoxFeedsPro -> getFeedById($aVals['feed_id']);
				$iAutoApproved = (int) Phpfox::getUserParam('foxfeedspro.auto_approve_posted_news');
				
				// Prepare data for add/edit news
				$oParseInput = Phpfox::getLib('parse.input');
				$oParseOutput = Phpfox::getLib('parse.output');
				$aNews = array(
					'feed_id'			 	=> $aFeed['feed_id'],
					'owner_type'		 	=> "user",
					'user_id'			 	=> $aFeed['user_id'],
					'item_title'		 	=> $oParseInput->clean($aVals['item_title']),
					'item_alias'		 	=> $oParseInput->clean($aVals['item_title']),
					'item_description'   	=> $oParseOutput->parse($aVals['item_description']),
					'item_description_parse'=> $oParseInput->prepare($aVals['item_description']),
					'item_content'		 	=> $oParseOutput->parse($aVals['item_content']),
					'item_content_parse' 	=> $oParseInput->prepare($aVals['item_content']),
					'item_url_detail'	 	=> $aVals['item_url_detail'],
					'item_author'		 	=> $aVals['item_author'],
					'is_edited' 		 	=> ($bIsEdit ? 1 : 0),
                    'server_id'             => Phpfox::getLib('request')->getServer('PHPFOX_SERVER_ID'),
                    'is_download_image'     => 1,
				);
				if(!$bIsEdit)
				{
					$aNews['item_pubDate'] 		 = PHPFOX_TIME;
					$aNews['item_pubDate_parse'] = date("D,d M Y h:i:s e",PHPFOX_TIME);
					$aNews['added_time'] 		 = PHPFOX_TIME;
					$aNews['is_active']	  		 = $aFeed['is_active'];
					$aNews['is_approved'] 		 = $iAutoApproved;
				}
				
				// Generate image url from uploaded file
				if($aVals['thumbnail'])
				{
					$sThumbNail = Phpfox::getService('foxfeedspro') -> uploadImage('thumbnail');
					if(!$sThumbNail)
					{
						return FALSE;
					}
					$aNews['item_image'] = $sThumbNail;
				}

				if($bIsEdit)
				{
					$aNews['item_id'] = $iEditId;
					
					//when editing,don't care tag list of provider

					if (Phpfox::isModule('tag'))
					{
						Phpfox::getService('tag.process')->update('foxfeedspro_news', $iEditId, $aNews['user_id'], (!Phpfox::getLib('parse.format')->isEmpty($aVals['tag_list']) ? $aVals['tag_list'] : null));
					}

					// Delete the old thumbnail
					if($aVals['thumbnail'])
					{
						$aRelatedNews = $oFoxFeedsPro->getNewsById($iEditId);
						if($aRelatedNews && $aRelatedNews['item_server_id'] == '0')
						{
							$sImageLink = str_replace(Phpfox::getParam('core.url_file')."foxfeedspro/", Phpfox::getParam('core.dir_file')."foxfeedspro/", $aRelatedNews['item_image']);
							@unlink($sImageLink);		
						}
					}
					// Edit Process
					if($oFoxFeedsProProcess->editNews($aNews))
					{
						$this->url()->send('foxfeedspro.news', null, _p('foxfeedspro.the_news_news_name_was_edited_successfully',array('news_name' =>substr($aVals['item_title'], 0, 200))));
					}	
					else
					{
						Phpfox_Error::set(_p('foxfeedspro.cannot_edit_news'));
					}
				}
					// Add Process
				elseif($iId = $oFoxFeedsProProcess->addNews($aNews, $aVals['temp_file']))
				{
					$aTagsFromFeeds = Phpfox::getService('tag')->getTagsById('foxfeedspro_feeds', $aNews['feed_id']);
					$sTagsFeeds = '';					
					
					if (isset($aTagsFromFeeds[$aNews['feed_id']]))
					{
						foreach ($aTagsFromFeeds[$aNews['feed_id']] as $aTag)
						{
							$sTagsFeeds .= ' ' . $aTag['tag_text'] . ',';	
						}
						$sTagsFeeds = trim(trim($sTagsFeeds, ','));
					}

					if($aVals['tag_list'] == ''){
						$aVals['tag_list'] =  $sTagsFeeds;
					}
					else
					if($aVals['tag_list'] != '' && $sTagsFeeds != ''){
						$aVals['tag_list'] .= ','.$sTagsFeeds;
					}
					if (Phpfox::isModule('tag') && isset($aVals['tag_list']) && ((is_array($aVals['tag_list']) && count($aVals['tag_list'])) || (!empty($aVals['tag_list']))))
					{
						Phpfox::getService('tag.process')->add('foxfeedspro_news', $iId, Phpfox::getUserId(), $aVals['tag_list']);
					}

					$this->url()->send('foxfeedspro.news', null,_p('foxfeedspro.the_news_news_name_was_created_successfully',array('news_name'=>substr($aVals['item_title'], 0, 200))));
				}
				else
				{
					Phpfox_Error::set(_p('foxfeedspro.cannot_add_news'));
				}
			}
			else 
			{
				$this->template() ->assign(array(
					'aForm' => $aVals
				));
			}
		}	
	}

	/*
	 * Clean method used to generate the top menu of the plugin according to the privacy settings in user group setting
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('foxfeedspro.component_controller_index_clean')) ? eval($sPlugin) : false);
	}
}

?>