<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

class Directory_Component_Controller_Add_Field_Contact_Us extends Phpfox_Component
{
    /**
     * Class process method which is used to execute this component.
     */
    public function process()
    {
		Phpfox::getService('directory.helper')->buildMenu();
        $bHideOptions = true;
        $iDefaultSelect = 4;
        $bIsEdit = false;
        $aForms = array('is_required' => '1');
        $iObjType = $this->request()->get('objtype');
        $sAction = $this->request()->get('action');
		
        $iGroupId = $this->getParam('iGroupId');
        $iContactUsId = $this->request()->get('contact_us_id');
        if($sAction == 'add')
        {
            $iType = $this->request()->get('type');
        }
        elseif($sAction == 'edit')
        {
            $iId = $this->request()->get('id');
            $aField = Phpfox::getService('directory.customcontactus.custom')->getForCustomEdit($iId);
            if (isset($aField['field_id']))
            {                
                $bIsEdit = true;
                $aForms = $aField;
                
                if (isset($aField['option']) && $aField['var_type'] == 'select')
                {
                    $bHideOptions = false;                
                }
            }
        }
		elseif($sAction == 'delete')
		{
			$iId = $this->request()->get('id');
		}
        else
        {
            return Phpfox_Error::set('Invalid action.');
        }
		
        $phrase = _p('directory.are_you_sure_you_want_to_delete_this_custom_option');
		$phrase = str_replace("\r\n", "\\n", $phrase);
		
        $this->template()
        	->assign(array(
                'iContactUsId'  => $iContactUsId,
                'aLanguages' => Phpfox::getService('language')->getAll(),
                'urlModule' => Phpfox::getParam('core.url_module'),
                'bHideOptions' => $bHideOptions,
                'iDefaultSelect' => $iDefaultSelect,
                'aForms' => $aForms,
                'bIsEdit' => $bIsEdit,
                'iCompanyId' => 0,
                'iObjType' => $iObjType,
                'iId' => isset($iId) ? $iId : '',
                'iGroupId' => isset($iGroupId) ? $iGroupId : '',
                'phrase' => $phrase,
                'corepath' => phpfox::getParam('core.path'),
            ))
			;
    }

    /**
     * Garbage collector. Is executed after this class has completed
     * its job and the template has also been displayed.
     */
    public function clean()
    {
        
    }

}
