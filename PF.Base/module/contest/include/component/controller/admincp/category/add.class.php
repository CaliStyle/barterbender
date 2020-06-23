<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Contest_Component_Controller_Admincp_Category_Add extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{		
		$bIsEdit = false;
		$aLanguages = Phpfox::getService('language')->getAll();

		if ($iEditId = $this->request()->getInt('id'))
		{
			if ($aCategory = Phpfox::getService('contest.category')->getForEdit($iEditId))
			{
				$bIsEdit = true;

				$this->template()->setHeader('<script type="text/javascript"> $Behavior.onLoadParentCategory = function(){$(\'#js_mp_category_item_' . $aCategory['parent_id'] . '\').attr(\'selected\', true);};</script>')->assign('aForms', $aCategory);
			}
		}		
		
		if ($aVals = $this->request()->getArray('val'))
		{
			if ($bIsEdit)
			{
				if (Phpfox::getService('contest.category.process')->update($aCategory['category_id'], $aVals))
				{
					$this->url()->send('admincp.contest.category.add', array('id' => $aCategory['category_id']), _p('contest.category_successfully_updated'));
				}
			}
			else 
			{
				if (Phpfox::getService('contest.category.process')->add($aVals))
				{
					$this->url()->send('admincp.contest.category.add', null, _p('contest.category_successfully_added'));
				}
			}
		}
		
		$this->template()->setTitle(($bIsEdit ? _p('edit_a_category') : _p('create_a_new_category')))
            ->setBreadCrumb(_p("Apps"), $this->url()->makeUrl('admincp.apps'))
            ->setBreadCrumb(_p('module_contest'), $this->url()->makeUrl('admincp.app').'?id=__module_contest')
			->setBreadcrumb(($bIsEdit ? _p('edit_a_category') : _p('create_a_new_category')), $this->url()->makeUrl('admincp.contest'))
			->assign(array(
					'sOptions' => Phpfox::getService('contest.category')->display('option')->get(),
					'bIsEdit' => $bIsEdit,
					'aLanguages' => $aLanguages
				)
			);
	}
	
	/**
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
		(($sPlugin = Phpfox_Plugin::get('fundraising.component_controller_admincp_category_add_clean')) ? eval($sPlugin) : false);
	}
}

?>