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
class FoxFeedsPro_Component_Controller_Admincp_Addcategory extends Phpfox_Component
{
	/*
	 * Process method which is used to process this component
	 */
	 public function process()
	 {
	 	// Add validation for form elements
		$aLanguages = Phpfox::getService('language')->getAll();
		// In edit category mode or in add category mode
		$bIsEdit = false;
		
		if ($iEditId = $this->request()->getInt('id'))
		{
			
			if ($aCategory = Phpfox::getService('foxfeedspro.category')->getForEdit($iEditId))
			{
				$bIsEdit = true;
				$this->template()
				     ->setHeader('<script type="text/javascript">(function(){$(\'#js_mp_category_item_' . $aCategory['parent_id'] . '\').attr(\'selected\', true);});</script>')
					 ->assign('aForms', $aCategory);
			}
		}

		 if ($aVals = $this->request()->getArray('val'))
		 {
			 if ($bIsEdit)
			 {
				 if (Phpfox::getService('foxfeedspro.category.process')->update($aCategory['category_id'], $aVals))
				 {
					 $this->url()->send('admincp.foxfeedspro.addcategory', array('id' => $aCategory['category_id']), _p('category_successfully_updated'));
				 }
			 }
			 else
			 {
				 if (Phpfox::getService('foxfeedspro.category.process')->add($aVals))
				 {
					 $this->url()->send('admincp.foxfeedspro.addcategory', null, _p('category_successfully_added'));
				 }
			 }
		 }
		
		// Add page title, breadcrumb and variable to layout view 
		$this->template()->setTitle( $bIsEdit ? _p('foxfeedspro.edit_category') : _p('foxfeedspro.add_category'))
			->setBreadCrumb( $bIsEdit ? _p('foxfeedspro.edit_category') : _p('foxfeedspro.add_category'), $this->url()->makeUrl('admincp.foxfeedspro.addcategory'))
			->assign(array(
				'sOptions' 	 => Phpfox::getService('foxfeedspro.category')->display('option')->get($bIsEdit ? $iEditId : 0),
				'bIsEdit' 	 => $bIsEdit,
				'aLanguages' => $aLanguages
			)
		);		
 	}
}
?>