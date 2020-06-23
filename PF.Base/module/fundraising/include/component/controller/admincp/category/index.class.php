<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Fundraising_Component_Controller_Admincp_Category_Index extends Phpfox_Component
{
	/**
	 * Class process method which is used to execute this component.
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
            if (Phpfox::getService('fundraising.category')->getAllItemBelongToCategory($iDelete) > 0)
            {
                Phpfox::addMessage(_p('you_can_not_delete_this_category_because_there_are_many_items_related_to_it'));
            } else {
                if (Phpfox::getService('fundraising.category.process')->delete($iDelete)) {
                    $this->url()->send('admincp.fundraising.category', null, _p('category_successfully_deleted'));
                }
            }
        }

        $aCategories = ($bSubCategory ? Phpfox::getService('fundraising.category')->getForAdmin($iId) : Phpfox::getService('fundraising.category')->getForAdmin());

        $this->template()->setTitle(($bSubCategory ?  _p('manage_sub_categories') : _p('manage_categories')))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('module_fundraising'), $this->url()->makeUrl('admincp.app').'?id=__module_fundraising')
            ->setBreadCrumb(($bSubCategory ?  _p('manage_sub_categories') : _p('manage_categories')))
            ->setHeader(array(
                    'drag.js' => 'static_script',
                    '<script type="text/javascript">$Behavior.coreDragInit = function() { Core_drag.init({table: \'#js_drag_drop\', ajax: \'fundraising.categoryOrdering\'}); }</script>'
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
		(($sPlugin = Phpfox_Plugin::get('fundraising.component_controller_admincp_category_index_clean')) ? eval($sPlugin) : false);
	}
}

?>
