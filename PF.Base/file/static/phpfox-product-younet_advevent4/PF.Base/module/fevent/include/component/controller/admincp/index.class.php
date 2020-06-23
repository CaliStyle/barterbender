<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');
/**
 * 
 * 
 * @copyright       [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Event
 */
class Fevent_Component_Controller_Admincp_Index extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{
		$bSubCategory = false;
        $aParent = [];
		if (($iId = $this->request()->getInt('sub')))
		{
			$bSubCategory = true;
            $aParent = Phpfox::getService('fevent.category')->getForEdit($iId);
		}

		if ($iDelete = $this->request()->getInt('delete'))
		{

			if (Phpfox::getService('fevent.category.process')->delete($iDelete))
			{
				$this->url()->send('admincp.fevent', null, _p('category_successfully_deleted'));
			}

		}

		$aCategories = ($bSubCategory ? Phpfox::getService('fevent.category')->getForAdmin($iId) : Phpfox::getService('fevent.category')->getForAdmin());

		$this->template()->setTitle(($bSubCategory ?  _p('manage_sub_categories') : _p('manage_categories')))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p("module_fevent"), $this->url()->makeUrl('admincp.app',['id' => '__module_fevent']))
            ->setBreadCrumb(_p('manage_categories') . (($bSubCategory && isset($aParent['name'])) ? ': '. _p($aParent['name']) : ''))
			->setHeader(array(
							'admin.js' => 'module_fevent',
							'drag.js' => 'static_script',
							'<script type="text/javascript">$Behavior.coreDragInit = function() { Core_drag.init({table: \'#js_drag_drop\', ajax: \'fevent.categoryOrdering\'}); }</script>'
						)
			)
			->assign(array(
						 'bSubCategory' => $bSubCategory,
						 'aCategories' => $aCategories
					 )
			);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('fevent.component_controller_admincp_index_clean')) ? eval($sPlugin) : false);
	}
}

?>
