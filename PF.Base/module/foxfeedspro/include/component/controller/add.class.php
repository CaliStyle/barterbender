<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond Benc
 * @package 		Phpfox_Component
 * @version 		$Id: add.class.php 1522 2010-03-11 17:56:49Z Miguel_Espinoza $
 */

/**
 * Add advanced category for each user
 */
class FoxFeedsPro_Component_Controller_Add extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		phpfox::isUser(true);
		if (!Phpfox::getParam('foxfeedspro.is_using_advanced_category'))
		{
			$this->url()->send('foxfeedspro');
		}
		Phpfox::isUser(true);
		$oDb = Phpfox::getLib('phpfox.database');
		$this->template()->setTitle(_p('foxfeedspro.news'));
		//$allow = Phpfox::getUserParam('foxfeedspro.allow_users_to_add_feed', true);
		$bIsAddNews = phpfox::getUserParam('foxfeedspro.can_add_news_items');
		$bIsAddFeed = phpfox::getUserParam('foxfeedspro.can_add_rss_provider');
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
		$this -> template() -> assign(array(
			'bIsAddNews' => $bIsAddNews,
			'bIsAddFeed' => $bIsAddFeed
		));
		$bIsEdit = false;
		if ($iEditId = $this->request()->getInt('id'))
		{
			if ($aCategory = Phpfox::getService('foxfeedspro.category')->getForEdit($iEditId))
			{
				$bIsEdit = true;
				
				$this->template()->setHeader('<script type="text/javascript">$Behavior.onLoadEditFormCategory = function(){$(\'#js_mp_category_item_' . $aCategory['parent_id'] . '\').prop(\'selected\', \'selected\');};</script>')->assign('aForms', $aCategory);
			}
		}		
		if ($aVals = $this->request()->getArray('val'))
		{
                    
			if ($bIsEdit)
			{
				if (Phpfox::getService('foxfeedspro.category.process')->updateMyCategory($aCategory['category_id'], $aVals))
				{
					$this->url()->send('foxfeedspro.add', array('id' => $aCategory['category_id']), _p('foxfeedspro.category_successfully_updated'));
				}
			}
			else 
			{
				if (Phpfox::getService('foxfeedspro.category.process')->addMyCategory($aVals))
				{
					$this->url()->send('foxfeedspro.add', null, _p('foxfeedspro.category_successfully_added'));
				}
			}
		}
		// Set header
		$this -> template() -> setHeader(array(
			'front_end.js'	=> 'module_foxfeedspro', 
		));
		
		$this->template() -> setBreadcrumb(_p('foxfeedspro.news'), $this -> url() -> makeUrl('foxfeedspro'));
		$this->template() -> setBreadCrumb(_p('foxfeedspro.add_category'), $this -> url() -> makeurl('foxfeedspro.addfeed'), true);

		$this->template()->assign(array(
					'sOptions' => Phpfox::getService('foxfeedspro.category')->display('option')->getMyCategories($bIsEdit ? $iEditId : 0),
					'bIsEdit' => $bIsEdit
				)
			);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('foxfeedspro.component_controller_add_clean')) ? eval($sPlugin) : false);
		(($sPlugin = Phpfox_Plugin::get('foxfeedspro.component_controller_index_clean')) ? eval($sPlugin) : false);
	}
}

?>