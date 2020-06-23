<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 * @copyright      YouNet Company
 * @author         TienNPL
 * @package        Module_NewsFeed
 * @version        3.02p5
 * 
 */
class FoxFeedsPro_Component_Controller_Admincp_Edititem extends Phpfox_Component
{
	/**
	 * validate the form data input
	 * @param array $aVals is the array of input data
	 * @param boolean $bIsEdit is the edit mode
	 * @return true|false
	 */
	private function validate($aVals, $bIsEdit =false)
	{
        if (empty($aVals['item_title']))
        {
            Phpfox_Error::set(_p('foxfeedspro.news_title_cannot_be_empty'));
            return false;
        }
        if (empty($aVals['item_description']))
        {
            Phpfox_Error::set(_p('foxfeedspro.description_can_not_be_empty'));
            return false;
        }
		// Check news url and not empty news
		if(!preg_match('/^(http|https)?:\/\/[a-zA-Z0-9\.\-_]+\.[a-zA-Z]{2,6}[^\s]+$/i', $aVals['item_url_detail']))
		{
			Phpfox_Error::set(_p('foxfeedspro.the_news_url_is_not_valid'));
			return false;
		}
		
		// Check logo file
		if (isset($aVals['thumbnail']) && !empty($aVals['thumbnail']['name']))
		{
			if($aVals['thumbnail']['size'] == 0)
			{
				Phpfox_Error::set(_p('foxfeedspro.the_uploaded_image_file_size_must_be_greater_than_zero'));
				return false;
			}
		} 	

		// Check exists feed name and feed url
		if ((!empty($aVals['item_title']) || !empty($aVals['item_url_detail'])))
		{
			$aNews = phpfox::getService('foxfeedspro')->getNewsByNameOrURL($aVals);

			if( count($aNews) > 0 )
			{
				Phpfox_Error::set(_p('foxfeedspro.the_news_name_or_news_url_has_already_existed'));
				return false;
			}
		}
		
		return true;
	}
	
	/*
	 * Process method which is used to process this component
	 */
	public function process ()
	{
		$bIsEdit = true;
		$bNewsNotFound = FALSE;
		$iEditId = $this->request()->getInt('item');
		
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
			'description' => array(
				'def' => 'required',
				'title'=> _p('foxfeedspro.news_description_cannot_be_empty')
			),
		);	
		$oValid = Phpfox::getLib('validator')->set(array(
				'sFormName' => 'js_add_feed_form', 
				'aParams'   => $aValidation
		));
		
		// Get available RSS Feeds
		$aFeeds = $oFoxFeedsPro -> getFeedsByUserId(0);
		
		$aNews   = $oFoxFeedsPro->getNewsById($iEditId, false);
		
		//edit tag
		if (Phpfox::isModule('tag'))
		{
			$aTags = Phpfox::getService('tag')->getTagsById('foxfeedspro_news', $iEditId);
			if (isset($aTags[$aNews['item_id']]))
			{
				$aNews['tag_list'] = '';					
				foreach ($aTags[$aNews['item_id']] as $aTag)
				{
					$aNews['tag_list'] .= ' ' . $aTag['tag_text'] . ',';	
				}
				$aNews['tag_list'] = trim(trim($aNews['tag_list'], ','));
			}
		}
		if(!$aNews)
		{
			$bNewsNotFound = TRUE;
		}

		$this->template()->setBreadCrumb(_p('foxfeedspro.edit_news'), $this->url()->makeurl('admincp.foxfeedspro.addnews.item_' . $iEditId));

		$this->template() ->assign(array(
			'aForm' 	  => $aNews,
			'sCreateJs'   => $oValid -> createJS(),
			'sGetJsForm'  => $oValid -> getJsForm(),
			'aFeeds'	  => $aFeeds,
			'bIsEdit'	  => $bIsEdit,
			'bNewsNotFound' => $bNewsNotFound,
            'sCorePath' => Phpfox::getService('foxfeedspro')->getStaticPath().'file/pic/',
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
					'item_id'			 	=> $iEditId,
					'feed_id'				=> $aFeed['feed_id'],
					'owner_type'		 	=> "user",
					'user_id'				=> !$bIsEdit?$aFeed['user_id']:$aNews['user_id'],
					'item_title'			=> $oParseInput->clean($aVals['item_title']),
					'item_alias'		 	=> $oParseInput->clean($aVals['item_title']),
					'item_description'   	=> $oParseOutput->parse($aVals['item_description']),
					'item_description_parse'=> $oParseInput->prepare($aVals['item_description']),
					'item_content'		 	=> $oParseOutput->parse($aVals['item_content']),
					'item_content_parse' 	=> $oParseInput->prepare($aVals['item_content']),
					'item_url_detail'	 	=> $aVals['item_url_detail'],
					'item_author'		 	=> $aVals['item_author'],
					'is_active'  		 	=> $aVals['is_active'],
					'is_featured'  		 	=> $aVals['is_featured'],
					'is_edited' 		 	=>	1
				);
				
				// Generate image url from uploaded file
				if($aVals['thumbnail'])
				{
					$sThumbNail = Phpfox::getService('foxfeedspro') -> uploadImage('thumbnail');
					
					if(!$sThumbNail)
					{
						return FALSE;
					}
					
					$aNews['item_image'] = $sThumbNail;
					
					// Delete the old thumbnail
					$aRelatedNews = $oFoxFeedsPro->getNewsById($iEditId);
					
					if($aRelatedNews && $aRelatedNews['item_server_id'] == '0')
					{
						$sImageLink = str_replace(Phpfox::getParam('core.url_pic')."foxfeedspro/", Phpfox::getParam('core.dir_pic')."foxfeedspro/", $aRelatedNews['item_image']);
						@unlink($sImageLink);		
					}
				}
				
				// Edit Process
				if($oFoxFeedsProProcess->editNews($aNews))
				{
					//when editing,don't care tag list of provider
					if (Phpfox::isModule('tag'))
					{
						Phpfox::getService('tag.process')->update('foxfeedspro_news', $iEditId, $aNews['user_id'], (!Phpfox::getLib('parse.format')->isEmpty($aVals['tag_list']) ? $aVals['tag_list'] : null));
					}
					
					$this->url()->send('admincp.foxfeedspro.items', null, _p('foxfeedspro.the_news_news_name_was_edited_successfully',array('news_name' =>substr($aVals['item_title'], 0, 200))));
				}	
				else
				{
					Phpfox_Error::set(_p('foxfeedspro.cannot_edit_news'));
				}
			}
			else 
			{
                $aVals['item_content_parse'] = $aVals['item_content'];
                $aVals['item_description_parse'] = $aVals['item_description'];
				$this->template() ->assign(array(
					'aForm' => $aVals
				));
			}
		}
	}
}
		
?>