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
 
defined('YOUNET_NEWS_FEED_PARSER') or define('YOUNET_NEWS_FEED_PARSER', "http://newsservice.younetco.com/v1.1/getfeed.php");
 
class FoxFeedsPro_Component_Controller_Admincp_Addfeed extends Phpfox_Component
{
	/**
	 * validatet the form data input
	 * @param array $aVals is the array of input data
	 * @param boolean $bIsEdit is the edit mode
	 * @return true|false
	 */
	private function validate($aVals, $bIsEdit =false)
	{
		// Check logo url
		if(!empty($aVals['feed_logo']))
		{
			if(!preg_match('/^(http|https)?:\/\/[a-zA-Z0-9\.\-_]+\.[a-zA-Z]{2,6}[^\s]+$/i', $aVals['feed_logo']))
			{
				Phpfox_Error::set(_p('foxfeedspro.rss_provider_logo_url_is_not_valid'));
				return false;
			}
		}
		if(empty($aVals['feed_name'])){
			Phpfox_Error::set(_p('foxfeedspro.name_of_feed_cannot_be_empty'));
			return false;
		}
		// Check feed url and not empty feed
		if(!preg_match('/^(http|https)?:\/\/[a-zA-Z0-9\.\-_]+\.[a-zA-Z]{2,6}[^\s]+$/i', $aVals['feed_url']))
		{
			Phpfox_Error::set(_p('foxfeedspro.rss_provider_url_is_not_valid'));
			return false;
		}
		else
		{
		}
		
		// Check logo file
		if (isset($aVals['logo_file']) && !empty($aVals['logo_file']['name']))
		{
			if($aVals['logo_file']['size'] == 0)
			{
				Phpfox_Error::set(_p('foxfeedspro.the_uploaded_logo_file_size_must_be_greater_than_zero'));
				return false;
			}
		} 	



		// Check favicon file
		if (isset($aVals['logo_mini_logo']) && !empty($aVals['logo_mini_logo']['name']))
		{
			if($aVals['logo_mini_logo']['size'] == 0)
			{
				Phpfox_Error::set(_p('foxfeedspro.the_uploaded_favicon_file_size_must_be_greater_than_zero'));
				return false;
			}
		} 	


		// Check exists feed name and feed url
		if ((!empty($aVals['feed_name']) || !empty($aVals['feed_url'])))
		{
			$aFeeds = phpfox::getService('foxfeedspro')->getFeedsByNameOrURL($aVals);

			if( count($aFeeds) > 0 )
			{
				Phpfox_Error::set(_p('foxfeedspro.the_rss_provider_name_or_rss_provider_url_already_existed'));
				return false;
			}
		}
		if(!is_numeric($aVals['feed_item_import']) || $aVals['feed_item_import']<0)
		{
			Phpfox_Error::set(_p('foxfeedspro.the_number_of_items_peer_rss_provider_is_invalid'));
			return false;
		}
		
		return true;
	}
	/*
	 * Process method which is used to process this component
	 */
	public function process ()
	{
		$bIsEdit = false;
		$bFeedNotFound = FALSE;
		$oFoxFeedsPro = Phpfox::getService('foxfeedspro');
		
		// Parse Mode 
		$bParseDirectly = Phpfox::getParam('foxfeedspro.parse_news_directly');
		$sParserLink = YOUNET_NEWS_FEED_PARSER;
		if($bParseDirectly)
		{
			$sParserLink = $oFoxFeedsPro->getStaticPath()."module/foxfeedspro/static/parser/getfeed.php";			
		}
		
		// Get category and language list
		$sCategories = Phpfox::getService('foxfeedspro.category')->get();
		
		$aLanguages = $oFoxFeedsPro->getLanguages();

		// Add validation for form elements
	 	$aValidation = array(
			'name' => array(
				'def' => 'required',
				'title'=> _p('foxfeedspro.name_of_feed_cannot_be_empty')
			),
			'url' => array(
				'def' => 'required',
				'title'=> _p('foxfeedspro.feed_url_cannot_be_empty')
			),
			'item_import' => array(
				'def' => 'required',
				'title' => _p('foxfeedspro.number_of_item_import_cannot_be_empty')
			)
		);	
		$oValid = Phpfox::getLib('validator')->set(array(
				'sFormName' => 'js_add_feed_form', 
				'aParams'   => $aValidation
		));
				
		// On Edit Mode or not
		if($iEditId = $this->request()->getInt('feed'))
		{
			$bIsEdit = true;
			$aFeed   = $oFoxFeedsPro->getFeedById($iEditId);
			
			if (Phpfox::isModule('tag'))
			{
				$aTags = Phpfox::getService('tag')->getTagsById('foxfeedspro_feeds', $iEditId);
				if (isset($aTags[$aFeed['feed_id']]))
				{
					$aFeed['tag_list'] = '';					
					foreach ($aTags[$aFeed['feed_id']] as $aTag)
					{
						$aFeed['tag_list'] .= ' ' . $aTag['tag_text'] . ',';	
					}
					$aFeed['tag_list'] = trim(trim($aFeed['tag_list'], ','));
				}
			}

			if(!$aFeed)
			{
				$bFeedNotFound = TRUE;
			}	
			else 
			{
				// Get Feed Category List
				$aFeedCats = Phpfox::getService("foxfeedspro.category")->getFeedCategoryList($aFeed['feed_id']);
				$aFeedCats = implode(',', $aFeedCats);
				
				$this-> template()->setHeader(array(
					'<script type="text/javascript">$Behavior.foxfeedsproCategoryEdit = function(){ var aCategories = explode(\',\', \'' . $aFeedCats . '\'); for (i in aCategories) { $(\'#js_mp_holder_\' + aCategories[i]).show(); $(\'#js_mp_category_item_\' + aCategories[i]).attr(\'selected\', true); } }</script>'
				));
				
				// Show real url logo
				if($aFeed['feed_logo'])
				{
					$aFeed['feed_logo'] = Phpfox::getParam('core.url_pic').$aFeed['feed_logo'];
				}

				// Show real favicon

				if(strpos($aFeed['logo_mini_logo'], "http") !== false){
					$aFeed['logo_mini_logo'] = $aFeed['logo_mini_logo'];
				}
				else{
					$aFeed['logo_mini_logo'] = Phpfox::getParam('core.url_pic') . "foxfeedspro/" . $aFeed['logo_mini_logo'];
				}

			}
			
			$this->template() ->assign(array(
				'aForm' => $aFeed
			));	
		}
		
		// Set BreadCrumb
		$this->template()->setHeader(array(
			'category.js' => 'module_foxfeedspro'
		));
		$this->template()->setBreadCrumb(
			(!$bIsEdit ? _p('foxfeedspro.add_a_new_rss_provider') : _p('foxfeedspro.edit_rss_provider')), 
			(!$bIsEdit ? $this->url()->makeurl('admincp.foxfeedspro.addfeed') : $this->url()->makeurl('admincp.foxfeedspro.addfeed.id_'.$iEditId))
		);
		
		$this->template()->assign(array(
			'sCreateJs'   => $oValid -> createJS(),
			'sGetJsForm'  => $oValid -> getJsForm(),
			'sCategories' => $sCategories,
			'aLanguages'  => $aLanguages,
			'bIsEdit'	  => $bIsEdit,
			'bFeedNotFound' => $bFeedNotFound
		));
		
		// Form Submit Process
		if($aVals = $this->request()->get('val'))
		{ 
			if($bIsEdit)
			{
				$aVals['feed_id'] = $iEditId;
			}
			// Validate input data and process add/edit RSS Provider
			if( $this->validate($aVals, $bIsEdit) )
			{
				// Parse feed url for information
				$aFeedOption = array('uri' => urlencode($aVals['feed_url']));
				
				$sContent = null;
				try
				{
                    $sUrl = $sParserLink . '?' . http_build_query($aFeedOption, null, '&');
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $sUrl);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                    curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
                    $sContent = curl_exec($ch);
                    curl_close($ch);
				}
				catch (exception $e)
				{
					
				}	
				
				if (null !== $sContent) {
					$aFeedInfo = json_decode($sContent, 1);
					$aVals['item_count'] = $aFeedInfo['item_count'];
					$aVals['logo'] 	 	 = $aFeedInfo['logo'];
					$aVals['favicon']    = $aFeedInfo['favicon'];
				}
				else {
					$aVals['item_count'] = 0;
					$aVals['logo']  	 = '';
					$aVals['favicon']    = '';
				}
				// Get logo file if it is added
				if(isset($_FILES['logo_file']) && !empty ($_FILES['logo_file']['name']))
				{
					$aVals['logo_file'] = $_FILES['logo_file'];
				}
				else
				{
					$aVals['logo_file'] = "";
				}

				// Get favicon file if it is added
				if(isset($_FILES['logo_mini_logo']) && !empty ($_FILES['logo_mini_logo']['name']))
				{
					$aVals['logo_mini_logo'] = $_FILES['logo_mini_logo'];
				}
				else
				{
					$aVals['logo_mini_logo'] = "";
				}


				if($bIsEdit)
				{
					$aVals['feed_id'] = $iEditId;
				}
			
				if($bIsEdit)
				{
					if (Phpfox::isModule('tag'))
					{
						Phpfox::getService('tag.process')->update('foxfeedspro_feeds', $iEditId, $aFeed['user_id'], (!Phpfox::getLib('parse.format')->isEmpty($aVals['tag_list']) ? $aVals['tag_list'] : null));
					}

					// Edit Process
					if(Phpfox::getService('foxfeedspro.process')->editFeed($aVals))
					{
						$this->url()->send('admincp.foxfeedspro.feeds', null, _p('foxfeedspro.the_feed_name_was_edited_successfully',array('feed_name' =>substr($aVals['feed_name'], 0, 200))));
					}	
					else
					{
						Phpfox_Error::set(_p('foxfeedspro.cannot_edit_rss_provider'));
					}
				}
					// Add Process
				elseif($iId =  Phpfox::getService('foxfeedspro.process')->addFeed($aVals))
				{
					if (Phpfox::isModule('tag') && isset($aVals['tag_list']) && ((is_array($aVals['tag_list']) && count($aVals['tag_list'])) || (!empty($aVals['tag_list']))))
					{
						Phpfox::getService('tag.process')->add('foxfeedspro_feeds', $iId, Phpfox::getUserId(), $aVals['tag_list']);
					}

					$this->url()->send('admincp.foxfeedspro.feeds', null,_p('foxfeedspro.the_feed_name_was_created_successfully',array('feed_name'=>substr($aVals['feed_name'], 0, 200))));
				}
				else
				{
					Phpfox_Error::set(_p('foxfeedspro.cannot_add_rss_provider'));
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
}


?>