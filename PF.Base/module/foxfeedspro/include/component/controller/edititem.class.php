<?php
/*
 * @copyright        [YouNet_COPYRIGHT]
 * @author           YouNet Company
 * @package          Module_FoxFeedsPro
 * @version          3.01
 *
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<?php

class FoxFeedsPro_Component_Controller_Edititem extends Phpfox_Component
{
	private function isValidData($value)
	{

		$strErr = "";
		if (empty($value['item_title']))
		{
			$strErr .= _p('foxfeedspro.news_title_cannot_be_empty').".<br/>";
		}
		if (empty($value['item_description']))
		{
			$strErr .= _p('foxfeedspro.description_can_not_be_empty').".<br/>";
		}
		if (!empty($value['item_url_detail']))
		{
			if(preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $value['item_url_detail']) === 0)
			$strErr .= _p('foxfeedspro.url_is_not_valid').".<br/>";
		}
		if (isset($value['file'])&& !empty($value['file']))
		{
			$file = $value['file'];
			$imglist = array('jpg','gif','png','jpeg');
			$info = $this->getExtention($file['name']);
			if (!in_array($info,$imglist))
			{
				$strErr .= _p('foxfeedspro.invalid_file_type').".<br/>";
			}
		}
		return $strErr;


	}

	public function process()
	{
		$aFilterMenu = array();
		if (!defined('PHPFOX_IS_USER_PROFILE'))
		{
			$aFilterMenu = array(
			_p('foxfeedspro.browse_all')  => '',
			_p('foxfeedspro.browse_all_by_recent_news') => 'date',
			_p('foxfeedspro.browse_all_by_most_viewed') => 'most-view',
			_p('foxfeedspro.browsed_by_most_discussed') => 'most-comment',
			_p('foxfeedspro.browse_all_by_most_favorited') => 'most-favorite',
			true,
			_p('foxfeedspro.my_rss_provider') => 'foxfeedspro.feeds',
			_p('foxfeedspro.my_news') => 'foxfeedspro.news',
			_p('foxfeedspro.my_favorite_news')=> 'favourite',
			);
		}
		$this->template()->buildSectionMenu('foxfeedspro', $aFilterMenu);

		if($this->request()->get('edit'))
		{
			$item_edit = $this->request()->get('val');
			if(!empty($item_edit['item_title']))
			{
				$item_edit['item_alias'] = phpfox::getService('foxfeedspro')->getAliasFromString(htmlspecialchars($item_edit['item_title']));
			}
			$arrSearch[] = " ni.item_id  = ".$item_edit['item_id'];
			list($iCnt, $items_edit) = Phpfox::getService('foxfeedspro')->getItems($arrSearch,"",0, 10,true);
			if(count($items_edit) > 0)
			{
				$edit = $items_edit[0];
			}

			if (isset($_FILES['item_photo_thumb']) && !empty($_FILES['item_photo_thumb']['name'])){
				$url = phpfox::getService('foxfeedspro')->uploadLogo('item_photo_thumb');
				$type = substr($url,  strrpos($url, '.'));
				$thumb_url = substr($url,0,  strrpos($url,'.')).'_thumb'.$type;
				$path = PHPFOX_DIR_FILE.'pic'.PHPFOX_DS.'foxfeedspro'.date('Y');
				if (!is_dir($path))
				{
					if(!@mkdir($path,0777,1))
					{
					}
				}
				$thumb_url = str_replace(phpfox::getParam('core.path').'file'.'/',PHPFOX_DIR_FILE,$thumb_url);
				$url = str_replace('_thumb','',$thumb_url);
				$oImage = Phpfox::getLib('image');
				$oImage->createThumbnail($url, $thumb_url, 112, 150);
				$thumb_url = str_replace(PHPFOX_DIR_FILE,phpfox::getParam('core.path').'file'.'/',$thumb_url);
				$url = $thumb_url;

			}
			else
			{
				$url = $edit['item_image'];
			}

			$item_edit['item_image'] = $url;
			$strErr = $this->isValidData($item_edit);
			if($strErr != "")
			{
				$feeds = Phpfox::getService('foxfeedspro')->getFeedsOfUser();
				$this->template()->assign(array(
                    		'title'=>$item_edit['item_title'],
                    		'url_source'=>$item_edit['item_url_detail'],
                    		'description'=>$item_edit['item_description'],
                    		'content'=>$item_edit['item_content'],
							'item_id'=>$item_edit['item_id'],
							'owner_id' => $item_edit['owner_id'],
							'item_edit'=>$item_edit,
							'feeds' => $feeds

				));
				$this->template()->setBreadcrumb(_p('foxfeedspro.news'), $this->url()->makeUrl('foxfeedspro'));
				$this->template()->setBreadcrumb('Edit a News', $this->url()->makeUrl('foxfeedspro.edititem.item_'.$item_edit['item_id']), true);
				return Phpfox_Error::set($strErr);
			}
			if(Phpfox_Error::isPassed())
			{
				phpfox::getService('foxfeedspro.process')->updateNews($item_edit);
				$this->url()->send('foxfeedspro.news', null, _p('foxfeedspro.news_successfully_updated'));
			}
		}
		$item_id  = $this->request()->get('item');
		if($item_id == null || $item_id <=0 )
		{
			$this->url()->send('foxfeedspro.items', null,'You must select News item to edit');
		}
		else
		{
			$arrSearch[] = " ni.item_id  = ".$item_id;
			list($iCnt, $items) = Phpfox::getService('foxfeedspro')->getItems($arrSearch,"",0, 10,true);
			phpfox::isUser(true);
			if(!phpfox::isAdmin())
			{
				if($items[0]['owner_id'] != phpfox::getUserId() || $items[0]['is_approved'] == 1)
				{
					$this->url()->send('subscribe');

				}
			}
			$feeds = Phpfox::getService('foxfeedspro')->getFeedsOfUser();
			$item = null;
			if(count($items)>0)
			{
				$item = $items[0];
				$this->template()->assign(
				array(
                                'item_edit' => $item,
                                'feeds' =>$feeds,
								'aForms' => $item
				)
				);
			}
			if($item == null)
			{
				$this->url()->send('foxfeedspro.items', null,'Invalid News Item');
			}
		}

		if (Phpfox::isModule('attachment'))
		{
			$this->setParam('attachment_share', array(
					'type' => 'foxfeedspro',
					'id' => 'core_js_news_form',
					'edit_id' => $item_id
			)
			);
		}
		$this->template()->assign(array(
							'item_id' => $item_id,
		)
		)
		->setHeader('cache', array(
				'jquery/plugin/jquery.highlightFade.js' => 'static_script',
				'switch_legend.js' => 'static_script',
				'switch_menu.js' => 'static_script',
				'quick_edit.js' => 'static_script',
				'pager.css' => 'style_css'
				))
				->setEditor(array('wysiwyg' => 1));
				$this->template()->setBreadcrumb(_p('foxfeedspro.news'), $this->url()->makeUrl('foxfeedspro'));
				$this->template()->setBreadcrumb('Edit a News', $this->url()->makeUrl('foxfeedspro.edititem.item_'.$item_id), true);

	}
}
?>