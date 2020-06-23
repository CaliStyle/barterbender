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
class FoxFeedsPro_Component_Controller_Admincp_Categories extends Phpfox_Component
{
	/*
	 * Process method which is used to process this component
	 */
	 public function process()
	 {
		 $bSubCategory = false;

		 if (($iId = $this->request()->getInt('sub')))
		 {
			 $bSubCategory = true;
		 }

		 if ($iDelete = $this->request()->getInt('delete'))
		 {

			 if (Phpfox::getService('foxfeedspro.category.process')->delete($iDelete))
			 {
				 $this->url()->send('admincp.foxfeedspro.categories', null, _p('category_successfully_deleted'));
			 }

		 }

		 $aCategories = ($bSubCategory ? Phpfox::getService('foxfeedspro.category')->getForAdmin($iId) : Phpfox::getService('foxfeedspro.category')->getForAdmin());

		 $this->template()->setTitle(($bSubCategory ?  _p('manage_sub_categories') : _p('manage_categories')))
			 ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
			 ->setBreadCrumb(($bSubCategory ?  _p('manage_sub_categories') : _p('manage_categories')))
			 ->setHeader(array(
							 'drag.js' => 'static_script',
							 '<script type="text/javascript">$Behavior.coreDragInit = function() { Core_drag.init({table: \'#js_drag_drop\', ajax: \'foxfeedspro.categoryOrdering\'}); }</script>',
							 'jquery/ui.js' => 'static_script',
							 'admin.js' => 'module_foxfeedspro',
							 '<script type="text/javascript">$Behavior.setUrlFoxFeedsPro = function(){$Core.foxfeedspro.url(\'' . $this->url()->makeUrl('admincp.foxfeedspro.categories') . '\');}</script>'
						 )
			 )
			 ->setPhrase(array(
							 'are_you_sure'
						 )
			 )
			 ->assign(array(
						  'bSubCategory' => $bSubCategory,
						  'aCategories' => $aCategories
					  )
			 );
	 }
}